<?php
/**
 * Digiwallet Payment Module for osCommerce
 *
 * @copyright Copyright 2013-2014 Yellow Melon
 * @copyright Portions Copyright 2013 Paul Mathot
 * @copyright Portions Copyright 2003 osCommerce
 * @license see LICENSE.TXT
 */
require('includes/application_top.php');
require 'includes/modules/payment/digiwallet/compatibility.php';

if(!defined('TABLE_ORDERS')) {
    require_once 'includes/modules/payment/digiwallet/database_tables.php';
}

$ywincludefile = realpath(dirname(__FILE__) . '/includes/languages/dutch/digiwallet_transactions.php');
require_once($ywincludefile);

$ywincludefile = realpath(dirname(__FILE__) . '/../includes/modules/payment/digiwallet/digiwallet.class.php');
require_once($ywincludefile);

$ywincludefile = realpath(dirname(__FILE__) . '/includes/extra_datafiles/digiwallet_transactions_filenames.php');
require_once($ywincludefile);

/**
 */
function digiwallet_get_directorylist()
{
    $issuerList = array();
    
    $objDigiCore = new DigiWalletCore("AUTO");
    
    $bankList = $objDigiCore->getBankList();
    foreach ($bankList as $issuerID => $issuerName) {
        $i = new stdClass();
        $i->issuerID = $issuerID;
        $i->issuerName = $issuerName;
        $i->issuerList = 'short';
        array_push($issuerList, $i);
    }
    return $issuerList;
}

/**
 *
 * @param
 *            $date
 */
function get_unix_timestamp($date)
{
    return strtotime($date);
}

function timeDiff($timestamp)
{
    $now = time();
    if ($now < $timestamp) {
        $sign = '-';
        $elapsed = $timestamp - $now;
    } else {
        $sign = '';
        $elapsed = ($now - $timestamp);
    }
    
    if ($elapsed > 604800) {
        $return = ' meer dan een week';
    } elseif ($elapsed > 172800) {
        $return = gmdate('z', $elapsed) . ' dagen';
    } elseif ($elapsed > 86400) {
        $return = gmdate('z', $elapsed) . ' dag';
    } else {
        $return = gmdate('G:i:s', $elapsed);
    }
    
    if (($now > $timestamp) && ($elapsed > (int) MODULE_PAYMENT_DIGIWALLET_EXPIRATION_PERIOD)) {
        $return = '<strong class="digiwalletExpiredTransaction">' . $return . '</strong>';
    }
    return $sign . $return;
}

/**
 *
 * @param integer $transactionID
 */
function digiwallet_lookup_transaction($transactionID = '')
{
    global $languages_id;
    $selected_transaction_query = "SELECT it.*, o.orders_status, s.orders_status_name from (" . TABLE_DIGIWALLET_TRANSACTIONS . " it LEFT OUTER JOIN " . TABLE_ORDERS . " o ON (it.order_id = o.orders_id)) LEFT OUTER JOIN " . TABLE_ORDERS_STATUS . " s ON (o.orders_status = s.orders_status_id) WHERE (s.language_id = '" . (int) $languages_id . "' OR s.language_id IS NULL) AND (it.transaction_id = '" . $transactionID . "' OR it.order_id = '" . $transactionID . "') ORDER BY it.datetimestamp DESC";
    $selected_transaction = tep_db_query($selected_transaction_query);
    $selected_transaction = tep_db_fetch_array($selected_transaction);
    return $selected_transaction;
}

function digiwallet_check_transaction_status($transactionID = '')
{
    global $messageStack, $db;
    
    if (! tep_not_null($transactionID)) {
        return;
    }
    
    require_once(DIR_FS_CATALOG_MODULES . "payment/digiwallet/digiwallet.class.php");
    
    $selected_transaction = digiwallet_lookup_transaction($transactionID);
    
    $objDigiCore = new DigiWalletCore("AUTO", $selected_transaction["rtlo"]);
    $objDigiCore->setBankId($selected_transaction["issuer_id"]);
    
    @$objDigiCore->checkPayment($transactionID);
    
    $realstatus = "Open";
    if ($objDigiCore->getPaidStatus()) {
        $realstatus = "Success";
    } else {
        list ($errorcode, $void) = explode(" ", $objDigiCore->getErrorMessage());
        switch ($errorcode) {
            case "TP0010":
                $realstatus = "Open";
                break;
            case "TP0011":
                $realstatus = "Cancelled";
                break;
            case "TP0012":
                $realstatus = "Expired";
                break;
            case "TP0013":
                $realstatus = "Failure";
                break;
            case "TP0014":
                $realstatus = "Success";
                break;
            default:
                $realstatus = "Open";
        }
    }
    $customerInfo = $objDigiCore->getConsumerInfo();
    $consumerAccount = (((isset($customerInfo->consumerInfo["bankaccount"]) && ! empty($customerInfo->consumerInfo["bankaccount"])) ? $customerInfo->consumerInfo["bankaccount"] : ""));
    $consumerName = (((isset($customerInfo->consumerInfo["name"]) && ! empty($customerInfo->consumerInfo["name"])) ? $customerInfo->consumerInfo["name"] : ""));
    $consumerCity = (((isset($customerInfo->consumerInfo["city"]) && ! empty($customerInfo->consumerInfo["city"])) ? $customerInfo->consumerInfo["city"] : ""));
    
    if ($realstatus != "") {
        $status_update = tep_db_query("UPDATE " . TABLE_DIGIWALLET_TRANSACTIONS . " SET transaction_status = '" . $realstatus . "' , consumer_name = '" . $consumerName . "', consumer_account_number = '" . $consumerAccountNumber . "', consumer_city = '" . $consumerCity . "' WHERE transaction_id = '" . $transactionID . "' LIMIT 1");
        if ($status_update->resource) {
            $messageStack->add_session(DIGIWALLET_MESSAGE_SUCCESS_STATUS . " statuscode: ok", 'success');
        } else {
            $messageStack->add_session(DIGIWALLET_MESSAGE_WARNING_STATUS . " statuscode:" . $objDigiCore->getErrorMessage(), 'warning');
        }
    } else {
        $messageStack->add_session(MODULE_PAYMENT_DIGIWALLET_ERROR_TEXT_TRANSACTION_OPEN, 'warning');
    }
    // ~
    /* reload the page to display the messageStack, and pass params to show transaction details again */
    tep_redirect(tep_href_link(FILENAME_DIGIWALLET_TRANSACTIONS, 'action=lookup&transactionID=' . $transactionID));
}

/**
 * Set the action to be carried out on iDeal transactions that need further
 * processing.
 *
 * "open" transactions need to be queried again.
 * "Success" transactions need to be updated to the proper order status.
 */
function getSuggestedAction($transactionID, $transactionStatus, $orderID, $orderStatus, $session_id = '', $age = 0)
{
    if (strtolower($transactionStatus) == "open") {
        return array(
            "text" => DIGIWALLET_TEXT_CHECK_STATUS,
            "link" => tep_href_link(FILENAME_DIGIWALLET_TRANSACTIONS, "action=checkstatus&transactionID=" . $transactionID),
            'parameters' => ''
        );
    }
    if (($transactionStatus == "success") && ! ($orderID > 0) && ($age > 0) && ($age < (int) MODULE_PAYMENT_DIGIWALLET_EXPIRATION_PERIOD)) {
        return array(
            "text" => DIGIWALLET_WAIT_OR_CREATE_ORDER,
            "link" => HTTP_CATALOG_SERVER . DIR_WS_CATALOG . 'index.php?main_page=checkout_process&digiwallet_repair_order=true&digiwallet_transaction_id=' . $transactionID,
            'parameters' => 'target="_blank" onClick="return confirm(\'' . IDEAL_TEXT_ARE_YOU_SURE_WAIT . '\')"'
        );
    }
    if (($transactionStatus == "success") && ! ($orderID > 0)) {
        return array(
            "text" => DIGIWALLET_TEXT_CREATE_ORDER,
            "link" => HTTP_CATALOG_SERVER . DIR_WS_CATALOG . 'index.php?main_page=checkout_process&digiwallet_repair_order=true&digiwallet_transaction_id=' . $transactionID,
            'parameters' => 'target="_blank" onClick="return confirm(\'' . DIGIWALLET_TEXT_ARE_YOU_SURE . '\')"'
        );
    }
    if ($transactionStatus == "success" && ($orderStatus == MODULE_PAYMENT_DIGIWALLET_ORDER_STATUS_ID_OPEN || $orderStatus == 0)) {
        return array(
            "text" => DIGIWALLET_TEXT_CHANGE_STATUS,
            "link" => tep_href_link(FILENAME_ORDERS, "action=edit&oID=" . $orderID),
            'parameters' => 'target="_blank"'
        );
    }
    return array(
        "text" => DIGIWALLET_TEXT_NO_ACTION,
        "link" => "#",
        'parameters' => ''
    );
}

/**
 * cleanup non open or success transactions
 */
function digiwallet_cleanup()
{
    tep_db_query("DELETE FROM " . TABLE_DIGIWALLET_TRANSACTIONS . " WHERE (transaction_status='Expired' OR transaction_status='Cancelled' OR transaction_status='Failure')");
    tep_db_query("UPDATE " . TABLE_DIGIWALLET_TRANSACTIONS . " SET `ideal_session_data` = '' WHERE (`order_id` > 0 AND `ideal_session_data` != '')");
}

/**
 *
 * @param integer $transaction_id
 */
function digiwallet_delete_transaction($transaction_id = '')
{
    global $messageStack;
    if ($transaction_id != '') {
        tep_db_query("DELETE FROM " . TABLE_DIGIWALLET_TRANSACTIONS . " WHERE `transaction_id`='" . $transaction_id . "'");
        $messageStack->add('Removed transaction: ' . $transaction_id, 'success');
    }
}

if (is_file(DIR_WS_MODULES . 'digiwallet_csv_import_module.php')) {
    include(DIR_WS_MODULES . 'digiwallet_csv_import_module.php');
}

if ($_GET['action'] == 'remove') {
    digiwallet_delete_transaction($_GET['transactionID']);
}

$transFilter = DIGIWALLET_TEXT_FILTER_INCOMPLETE;

if (isset($_GET['transFilter'])) {
    $transFilter = $_GET['transFilter'];
}

if (isset($_GET['transactionID'])) {
    $transactionID = tep_db_input(tep_db_prepare_input($_GET['transactionID']));
}

$transaction_query_raw = "SELECT it.*, o.orders_status, s.orders_status_name from (" . TABLE_DIGIWALLET_TRANSACTIONS . " it left outer join " . TABLE_ORDERS . " o ON (it.order_id = o.orders_id)) left outer join " . TABLE_ORDERS_STATUS . " s ON (o.orders_status = s.orders_status_id) where (s.language_id = '" . (int) $languages_id . "' OR s.language_id IS NULL)";

switch ($transFilter) {
    case DIGIWALLET_TEXT_FILTER_COMPLETE:
        $transactions_query = $transaction_query_raw . " AND NOT ((it.transaction_status = 'success' AND (it.order_id IS NULL OR o.orders_status IS NULL OR o.orders_status='" . MODULE_PAYMENT_DIGIWALLET_ORDER_STATUS_ID_OPEN . "' OR o.orders_status='0')) OR it.transaction_status = 'open')";
        break;
    case DIGIWALLET_TEXT_FILTER_ALL:
        $transactions_query = $transaction_query_raw;
        break;
    case DIGIWALLET_TEXT_FILTER_INCOMPLETE:
    default:
        $transactions_query = $transaction_query_raw . " AND ((it.transaction_status = 'success' AND (it.order_id IS NULL OR o.orders_status IS NULL OR o.orders_status='" . MODULE_PAYMENT_DIGIWALLET_ORDER_STATUS_ID_OPEN . "' OR o.orders_status='0')) OR it.transaction_status = 'open')";
        break;
}
$transactions_query .= " order by it.datetimestamp desc";
$transactions_split = new splitPageResults($_GET['page'], MAX_DISPLAY_SEARCH_RESULTS, $transactions_query, $transactions_query_numrows);
$transactions = tep_db_query($transactions_query);

switch ($_GET['action']) {
    case 'checkstatus':
        digiwallet_check_transaction_status($transactionID);
        break;
    case 'lookup':
        if (isset($transactionID)) {
            $selected_transaction = digiwallet_lookup_transaction($transactionID);
        } elseif (isset($_GET['orderID'])) {
            $selected_transaction = digiwallet_lookup_transaction(tep_db_input(tep_db_prepare_input($_GET['orderID'])));
        }
        break;
    case 'cleanup':
        digiwallet_cleanup();
        break;
}
?>

<!doctype html public "-//W3C//DTD HTML 4.01 Transitional//EN">
<html <?php echo HTML_PARAMS; ?>>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title><?php echo TITLE; ?></title>
<meta name="robot" content="noindex, nofollow" />
<script language="JavaScript" src="includes/menu.js"
    type="text/JavaScript"></script>
<link href="includes/stylesheet.css" rel="stylesheet" type="text/css" />
<link rel="stylesheet" type="text/css"
    href="includes/cssjsmenuhover.css" media="all" id="hoverJS" />
<script language="javascript" src="includes/menu.js"></script>
<script language="javascript" src="includes/general.js"></script>
<script type="text/javascript">
  <!--
  function init()
  {
    cssjsmenu('navbar');
    if (document.getElementById)
    {
      var kill = document.getElementById('hoverJS');
      kill.disabled = true;
    }
  }
  // -->
</script>
</head>
<body onLoad="init()">
    <!-- header //-->
<?php
require(DIR_WS_INCLUDES . 'template_top.php');
?>
    <table border="0" width="100%" cellspacing="2" cellpadding="2">
        <tr>
            <td width="100%" valign="top">
                <table border="0" width="100%" cellspacing="0" cellpadding="2">
                    <tr>
                        <td width="100%">
                            <table border="0" width="100%" cellspacing="0" cellpadding="0">
                                <tr>
                                    <td class="pageHeading">Digiwallet iDEAL Transactions</td>
                                    <td>
                                        <table border="0" width="100%" cellspacing="0" cellpadding="0">
                                            <tr>
                                                <td class="smallText" align="center">
                                                    <?php echo tep_draw_form('transactions', FILENAME_DIGIWALLET_TRANSACTIONS, '', 'get'); ?>
                                                        <?php
                        
                                                        echo tep_draw_radio_field('transFilter', DIGIWALLET_TEXT_FILTER_ALL, false, 'onchange="this.form.submit();"') . DIGIWALLET_TEXT_FILTER_ALL . tep_draw_radio_field('transFilter', DIGIWALLET_TEXT_FILTER_INCOMPLETE, false, 'onchange="this.form.submit();"') . DIGIWALLET_TEXT_FILTER_INCOMPLETE . tep_draw_radio_field('transFilter', DIGIWALLET_TEXT_FILTER_COMPLETE, false, 'onchange="this.form.submit();"') . DIGIWALLET_TEXT_FILTER_COMPLETE;
                        ?> 
                                                        <input
                                                    type="submit" value="Filter" />
                                                    </form>
                                                </td>
                                            </tr>
                                            <tr>
                                                <?php echo tep_draw_form('transactions', FILENAME_DIGIWALLET_TRANSACTIONS, '', 'get'); ?>
                                                    <td class="smallText" align="center"><?php echo tep_draw_hidden_field('action', 'cleanup'); ?>
                                                        <input
                                                    onClick="return confirm('Weet u zeker dat u de transatie tabel nu wilt opschonen?')"
                                                    type="submit"
                                                    value="<?php echo DIGIWALLET_TEXT_FILTER_CLEANUP; ?>" />
                                                        <?php echo DIGIWALLET_TEXT_CLEANUP; ?>
                                                    </td>
                                                </form>
                                            </tr>
                                        </table>
                                    </td>
                                    <td align="right">
                                        <table border="0" width="100%" cellspacing="0" cellpadding="0">
                                            <tr>
                                                <?php echo tep_draw_form('transactions', FILENAME_DIGIWALLET_TRANSACTIONS, '', 'get'); ?>
                                                    <td
                                                    class="smallText" align="right">
                                                    <?php
                        
                                                    echo DIGIWALLET_TEXT_SEARCH_TRANSACTION_ID . ' ' . tep_draw_input_field('transactionID', '', 'size="12"') . tep_draw_hidden_field('action', 'lookup') . tep_draw_hidden_field('transFilter', $transFilter);
                        ?>
                                                        </td>
                                                </form>
                                            </tr>
                                            <tr>
                                                <?php echo tep_draw_form('transactions', FILENAME_DIGIWALLET_TRANSACTIONS, '', 'get'); ?>
                                                    <td
                                                    class="smallText" align="right">
                                                    <?php
                        
                                                    echo DIGIWALLET_TEXT_SEARCH_ORDER_ID . ' ' . tep_draw_input_field('orderID', '', 'size="12"') . tep_draw_hidden_field('action', 'lookup') . tep_draw_hidden_field('transFilter', $transFilter);
                        ?>
                                                    </td>
                                                </form>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <table border="0" width="100%" cellspacing="0" cellpadding="0">
                                <tr>
                                    <td valign="top">
                                        <table border="0" width="100%" cellspacing="0" cellpadding="2">
                                            <tr class="dataTableHeadingRow">
                                                <td class="dataTableHeadingContent"><?php echo DIGIWALLET_TABLE_HEADING_TRANSACTION_ID; ?></td>
                                                <td class="dataTableHeadingContent">RTLO</td>
                                                <td class="dataTableHeadingContent"><?php echo DIGIWALLET_TABLE_HEADING_PURCHASE_ID; ?></td>
                                                <td class="dataTableHeadingContent"><?php echo DIGIWALLET_TABLE_HEADING_ORDER_ID; ?></td>
                                                <td class="dataTableHeadingContent"><?php echo DIGIWALLET_TABLE_HEADING_TRANSACTION_STATUS; ?></td>
                                                <td class="dataTableHeadingContent"><?php echo DIGIWALLET_TABLE_HEADING_ORDER_STATUS; ?></td>
                                                <td class="dataTableHeadingContent" align="right"><?php echo DIGIWALLET_TABLE_HEADING_TRANSACTION_DATE_TIME; ?></td>
                                                <td class="dataTableHeadingContent" align="right"><?php echo DIGIWALLET_TABLE_HEADING_AGE; ?></td>
                                                <td class="dataTableHeadingContent" align="right"><?php echo DIGIWALLET_HEADING_CUSTOMER_ID; ?></td>
                                                <td class="dataTableHeadingContent" align="right"><?php echo DIGIWALLET_TABLE_HEADING_SUGGESTED_ACTION; ?></td>
                                                <td class="dataTableHeadingContent" align="right"><?php echo 'Details'; ?></td>
                                            </tr>
                                            <?php
        
                                            while ($transaction = tep_db_fetch_array($transactions)) {
                                                ?> 
                                                <tr class="dataTableRow"
                                                onmouseover="rowOverEffect(this)"
                                                onmouseout="rowOutEffect(this)">
                                                <td class="dataTableContent"><?php echo $transaction['transaction_id']; ?></td>
                                                <td class="dataTableContent"><?php echo $transaction['rtlo']; ?></td>
                                                <td class="dataTableContent"><?php echo $transaction['purchase_id']; ?></td>
                                                <td class="dataTableContent"><a
                                                    href="<?php echo tep_href_link(FILENAME_ORDERS)."?oID=".$transaction['order_id']; ?>&action=edit"><?php echo $transaction['order_id']; ?></a></td>
                                                <td class="dataTableContent"><?php echo $transaction['transaction_status']; ?></td>
                                                <td class="dataTableContent"><?php echo ($transaction['orders_status']=='0' ? '0' : $transaction['orders_status_name']); ?></td>
                                                <td class="dataTableContent" align="right"><?php echo tep_datetime_short($transaction['datetimestamp']); ?></td>
                                                <td class="dataTableContent" align="right">
                                                    <?php
                                                    $transaction_age = 0;
            
                                                    if (((! ($transaction['order_id'] > 0)) && (strtolower($transaction['transaction_status']) == 'success')) || (strtolower($transaction['transaction_status']) == 'open')) {
                                                        echo timeDiff(get_unix_timestamp($transaction['datetimestamp']));
                                                        $transaction_age = time() - get_unix_timestamp($transaction['datetimestamp']);
                                                    } else {
                                                        echo 'n.v.t';
                                                    }
                                                ?>
                                                    </td>

                                                <td class="dataTableContent" align="right"><a
                                                    target="_blank"
                                                    href="<?php echo tep_href_link(FILENAME_CUSTOMERS, 'selected_box=customers&cID=' . $transaction['customer_id'], 'NONSSL')?>"><?php echo $transaction['customer_id'];?></a>
                                                </td>
                                                <td class="dataTableContent" align="right">
                                                    <?php
                                                    $res = getSuggestedAction($transaction['transaction_id'], $transaction['transaction_status'], $transaction['order_id'], $transaction['orders_status'], $transaction['session_id'], $transaction_age);
                                                    if (($res['link'] != '#') && ! empty($res['link'])) {
                                                        ?> <a <?php echo $res['parameters']; ?>
													href="<?php echo $res['link']?>"><?php echo $res['text']; ?></a>
													<?php
                                                    } else {
                                                        echo '--';
                                                    }
                                                ?></td>
                                                <td class="dataTableContent" align="right"><a
                                                    href="<?php echo tep_href_link(FILENAME_DIGIWALLET_TRANSACTIONS, tep_get_all_get_params(array('action','transactionID')) . 'action=lookup&transactionID=' .$transaction['transaction_id'], 'NONSSL')?>"><?php echo DIGIWALLET_TEXT_DETAILS;?></a>&nbsp;</td>
                                            </tr>
                                            <?php
                                            }
        ?>
                                            <tr>
                                                <td colspan="5">
                                                    <table border="0" width="100%" cellspacing="0"
                                                        cellpadding="2">
                                                        <tr>
                                                            <td class="smallText" valign="top"><?php echo $transactions_split->display_count($transactions_query_numrows, MAX_DISPLAY_SEARCH_RESULTS, $_GET['page'], TEXT_DISPLAY_NUMBER_OF_ORDERS); ?></td>
                                                            <td class="smallText" align="right"><?php echo $transactions_split->display_links($transactions_query_numrows, MAX_DISPLAY_SEARCH_RESULTS, MAX_DISPLAY_PAGE_LINKS, $_GET['page'], tep_get_all_get_params(array('page', 'oID', 'action'))); ?></td>
                                                        </tr>
                                                    </table>
                                                </td>
                                            </tr>
                                        </table>

                                        <?php
                                        if (! empty($selected_transaction)) {
                                            $transaction_age = 0;
                                            if (! ($selected_transaction['order_id'] > 0)) {
                                                // echo timeDiff(get_unix_timestamp($transaction['datetimestamp']));
                                                $selected_transaction_age = time() - get_unix_timestamp($selected_transaction['datetimestamp']);
                                            }
                                            ?>
                                                                        <hr />
                                                                        <table border="0" width="100%" cellspacing="0" cellpadding="0">
                                            <tr>
                                                <td valign="top">
                                                    <table border="0" width="100%" cellspacing="0"
                                                        cellpadding="2">
                                                        <tr class="dataTableHeadingRow">
                                                            <td class="dataTableHeadingContent" colspan="2"><?php echo DIGIWALLET_TABLE_HEADING_TRANSACTION_DETAILS; ?></td>
                                                        </tr>

                                                        <tr class="dataTableRow" onmouseover="rowOverEffect(this)"
                                                            onmouseout="rowOutEffect(this)">
                                                            <td class="dataTableContent"><?php echo 'Verwijder deze transactie uit de tabel' ?></td>
                                                            <td class="dataTableContent"><a
                                                                onclick="return confirm('Weet u zeker dat u deze transactie wilt verwijderen uit de Digiwallet iDEAL tabel?')"
                                                                href="<?php echo tep_href_link(FILENAME_DIGIWALLET_TRANSACTIONS, tep_get_all_get_params(array('action','transactionID')) . 'action=remove&transactionID=' .$selected_transaction['transaction_id'], 'NONSSL')?>">Verwijder</a>
                                                            </td>
                                                        </tr>
                                                        <tr class="dataTableRow" onmouseover="rowOverEffect(this)"
                                                            onmouseout="rowOutEffect(this)">
                                                            <td class="dataTableContent"><?php echo DIGIWALLET_TABLE_HEADING_TRANSACTION_ID; ?></td>
                                                            <td class="dataTableContent"><?php echo $selected_transaction['transaction_id']; ?></td>
                                                        </tr>
                                                        <tr class="dataTableRow" onmouseover="rowOverEffect(this)"
                                                            onmouseout="rowOutEffect(this)">
                                                            <td class="dataTableContent">RTLO</td>
                                                            <td class="dataTableContent"><?php echo $selected_transaction['rtlo']; ?></td>
                                                        </tr>
                                                        <tr class="dataTableRow" onmouseover="rowOverEffect(this)"
                                                            onmouseout="rowOutEffect(this)">
                                                            <td class="dataTableContent"><?php echo DIGIWALLET_TABLE_HEADING_PURCHASE_ID; ?></td>
                                                            <td class="dataTableContent"><?php echo $selected_transaction['purchase_id']; ?></td>
                                                        </tr>
                                                        <tr class="dataTableRow" onmouseover="rowOverEffect(this)"
                                                            onmouseout="rowOutEffect(this)">
                                                            <td class="dataTableContent"><?php echo DIGIWALLET_TABLE_HEADING_ORDER_ID; ?></td>
                                                            <td class="dataTableContent"><a
                                                                href="<?php echo tep_href_link(FILENAME_ORDERS)."?action=edit&page=1&oID=".$selected_transaction['order_id']; ?>"><?php echo $selected_transaction['order_id']; ?></a></td>
                                                        </tr>
                                                        <tr class="dataTableRow" onmouseover="rowOverEffect(this)"
                                                            onmouseout="rowOutEffect(this)">
                                                            <td class="dataTableContent"><?php echo DIGIWALLET_TABLE_HEADING_TRANSACTION_STATUS; ?></td>
                                                            <td class="dataTableContent"><?php echo $selected_transaction['transaction_status']; ?><a
                                                                href="<?php echo tep_href_link(FILENAME_DIGIWALLET_TRANSACTIONS, tep_get_all_get_params(array('action')) . 'action=checkstatus&transactionID=' . $selected_transaction['transaction_id']); ?>">.</a></td>
                                                        </tr>
                                                        <tr class="dataTableRow" onmouseover="rowOverEffect(this)"
                                                            onmouseout="rowOutEffect(this)">
                                                            <td class="dataTableContent"><?php echo DIGIWALLET_TABLE_HEADING_ORDER_STATUS; ?></td>
                                                            <td class="dataTableContent"><?php echo (($selected_transaction['orders_status']!='0') ? $selected_transaction['orders_status_name'] : DIGIWALLET_NAME_ZERO_STATUS_ORDER); ?></td>
                                                        </tr>
                                                        <tr class="dataTableRow" onmouseover="rowOverEffect(this)"
                                                            onmouseout="rowOutEffect(this)">
                                                            <td class="dataTableContent"><?php echo DIGIWALLET_TABLE_HEADING_TRANSACTION_DATE_TIME; ?></td>
                                                            <td class="dataTableContent"><?php echo tep_datetime_short($selected_transaction['datetimestamp']); ?></td>
                                                        </tr>
                                                        <tr class="dataTableRow" onmouseover="rowOverEffect(this)"
                                                            onmouseout="rowOutEffect(this)">
                                                            <td class="dataTableContent"><?php echo DIGIWALLET_TABLE_HEADING_LAST_MODIFIED; ?></td>
                                                            <td class="dataTableContent"><?php echo tep_datetime_short($selected_transaction['last_modified']); ?></td>
                                                        </tr>
                                                        <tr class="dataTableRow" onmouseover="rowOverEffect(this)"
                                                            onmouseout="rowOutEffect(this)">
                                                            <td class="dataTableContent"><?php echo DIGIWALLET_HEADING_CUSTOMER_ID; ?></td>
                                                            <td class="dataTableContent"><a target="_blank"
                                                                href="<?php echo tep_href_link(FILENAME_CUSTOMERS, "selected_box=customers&cID=" . $selected_transaction['customer_id'], 'NONSSL')?>"><?php echo $selected_transaction['customer_id']; ?></a></td>
                                                        </tr>
                                                        <tr class="dataTableRow" onmouseover="rowOverEffect(this)"
                                                            onmouseout="rowOutEffect(this)">
                                                            <td class="dataTableContent"><?php echo DIGIWALLET_HEADING_TRANSACTION_AMOUNT; ?></td>
                                                            <td class="dataTableContent"><?php echo $selected_transaction['currency']; ?> <?php echo sprintf("%.02f", round($selected_transaction['amount']/100, 2)); ?></td>
                                                        </tr>
                                                        <!-- 
                                                        <tr class="dataTableRow" onmouseover="rowOverEffect(this)" onmouseout="rowOutEffect(this)">
                                                            <td class="dataTableContent"><?php echo DIGIWALLET_HEADING_CUSTOMER_ACCOUNT; ?></td>
                                                            <td class="dataTableContent"><?php echo $selected_transaction['consumer_account_number']; ?></td>
                                                        </tr>
                                                         -->
                                                        <!-- 
                                                        <tr class="dataTableRow" onmouseover="rowOverEffect(this)" onmouseout="rowOutEffect(this)">
                                                            <td class="dataTableContent"><?php echo DIGIWALLET_HEADING_CUSTOMER_NAME; ?></td>
                                                            <td class="dataTableContent"><?php echo $selected_transaction['consumer_name']; ?></td>
                                                        </tr>
                                                         -->
                                                        <tr class="dataTableRow" onmouseover="rowOverEffect(this)"
                                                            onmouseout="rowOutEffect(this)">
                                                            <td class="dataTableContent"><?php echo DIGIWALLET_TABLE_HEADING_SUGGESTED_ACTION; ?></td>
                                                                <?php $res = getSuggestedAction($selected_transaction['transaction_id'], $selected_transaction['transaction_status'], $selected_transaction['order_id'], $selected_transaction['orders_status'], $selected_transaction['session_id'], $selected_transaction_age);?>
                                                            <td class="dataTableContent"><a
                                                                <?php echo $res['parameters']; ?>
                                                                href="<?php echo $res['link']?>"><?php echo $res['text']; ?></a></td>
                                                        </tr>
                                                        <!-- 
                                                        <tr class="dataTableRow" onmouseover="rowOverEffect(this)" onmouseout="rowOutEffect(this)">
                                                            <td class="dataTableContent"><?php echo DIGIWALLET_HEADING_CUSTOMER_CITY; ?></td>
                                                            <td class="dataTableContent"><?php echo $selected_transaction['consumer_city']; ?></td>
                                                        </tr>
                                                         -->
                                                    </table>
                                                </td>
                                            </tr>
                                        </table>   
                                    <?php
                                        }
        ?>       
                                    <table>
                                            <tr>
                                                <td><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
                                            </tr>
                                            <tr class="dataTableHeadingRow">
                                                <td class="dataTableHeadingContent"><?php echo DIGIWALLET_HEADING_EXPLANATION; ?></td>
                                            </tr>
                                            <tr>
                                                <td class="dataTableContent"><?php echo DIGIWALLET_TEXT_EXPLANATION; ?></td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
    <div id="issuerlist" class="dataTableContent">
        <h4>Lijst met banken (uit de database)</h4>
    <?php
    $digiwallet_directorylist = digiwallet_get_directorylist();

    if ($digiwallet_directorylist) {
        ?>
        <ul>
        <?php
        foreach ($digiwallet_directorylist as $bankObj) {
            echo '<li>' . $bankObj->issuerName . ' (id: ' . $bankObj->issuerID . ', issuerlist: ' . $bankObj->issuerList . ')</li>';
        }
        ?>
        </ul>
        <?php
    } else {
        echo '<span style="color: red">De lijst met banken is op dit moment leeg, gebruik de Digiwallet iDEAL betaalmodule (als klant) om een nieuwe lijst met banken op te halen van de Digiwallet iDEAL server.</span>';
    }
?>
</div>
<?php require(DIR_WS_INCLUDES . 'footer.php'); ?>
<br>
</body>
</html>
<?php require(DIR_WS_INCLUDES . 'application_bottom.php'); ?>
