<?php
/**
 * Digiwallet Payment Module for osCommerce
 *
 * @copyright Copyright 2013-2014 Yellow Melon
 * @copyright Portions Copyright 2013 Paul Mathot
 * @copyright Portions Copyright 2003 osCommerce
 * @license   see LICENSE.TXT
 */
chdir('../../../../');
require 'includes/application_top.php';
require_once 'includes/classes/order.php';
require_once 'includes/classes/payment.php';

// retrieve db-info from targetpay_transactions
$trxid = $_REQUEST["trxid"];
$pay_type = $_REQUEST["type"];
$finished = isset($_REQUEST["finished"]) ? $_REQUEST["finished"] : false;
$cancel = isset($_REQUEST["cancel"]) ? $_REQUEST["cancel"] : false;
$checksum = (isset($_REQUEST['checksum'])) ? $_REQUEST['checksum'] : false;
if(!isset($pay_type) || empty($pay_type)){
    echo "No payment method found!";
    die;
}
if($pay_type == "PYP"){
    if($finished || $cancel) {
        // Return/Cancel URL
        $trxid = $_REQUEST['paypalid'];
    }
    else {
        // Report URL
        $trxid = $_REQUEST['acquirerID'];
    }
}
else if($pay_type == "AFP"){
    // For Afterpay only
    $trxid = $_REQUEST['invoiceID'];
}
if(!isset($trxid) || empty($trxid)){
    echo "No transaction found!";
    die;
}

$pay_type = (empty($pay_type)) ? "_" : "_" . $pay_type . "_";

if (empty(constant('MODULE_PAYMENT_TARGETPAY' . $pay_type . 'STATUS')) || (constant('MODULE_PAYMENT_TARGETPAY' . $pay_type . 'STATUS') != 'True')) {
    exit();
}
$payment = new Payment('targetpay');

require_once 'includes/modules/payment/targetpay/targetpay.class.php';

require_once 'includes/extra_datafiles/targetpay.php';

$sql = "select * from " . TABLE_TARGETPAY_TRANSACTIONS . " where `transaction_id` = '" . $trxid . "'";
$transaction_query = tep_db_query($sql);

if (tep_db_num_rows($transaction_query) > 0) {
    $transaction_info = tep_db_fetch_array($transaction_query);

    $order_id = $transaction_info["order_id"];

    $sql = "select orders_status from " . TABLE_ORDERS . " where orders_id = '" . (int) $order_id . "'";
    $check_query = tep_db_query($sql);
    if (tep_db_num_rows($check_query)) {
        $check = tep_db_fetch_array($check_query);

        if ($check['orders_status'] == 1) {
            $sql_data_array = array(
                'orders_id' => $order_id,
                'orders_status_id' => '2',
                'date_added' => 'now()',
                'customer_notified' => '0',
                'comments' => 'callback.php'
            );
            tep_db_perform(TABLE_ORDERS_STATUS_HISTORY, $sql_data_array);
        }

        $iTest = false;//(constant("MODULE_PAYMENT_TARGETPAY" . $pay_type . "TESTACCOUNT") == "True") ? 1 : 0;
        $digiCore = new TargetPayCore(strtoupper(substr($transaction_info['issuer_id'], 0, 3)), $transaction_info['rtlo'], 'nl', $iTest);

        $params = [];
        if($checksum){
            $checksum_number = md5($trxid . $transaction_info['rtlo'] . $digiCore->getSalt());
            $params = ['checksum' => $checksum_number, 'once' => 0];
        }
        // Using order from cache for full properties
        $order = new order;
        require(DIR_WS_CLASSES . 'order_total.php');
        $order_total_modules = new order_total;
        $order_totals = $order_total_modules->process();

        // Re-init order info
        $order = new Order($order_id);
        // Check if the end-user is paid

        $testMode = false;
        $paymentIsPartial = false;
        $bw_paid_amount = 0;
        $isTestMode = false;//constant('MODULE_PAYMENT_TARGETPAY' . $pay_type . 'TESTACCOUNT') == "True";
        if ($isTestMode) { // Always OK
            $paidStatus = true;
            $testMode = true;
        } else {
            $result = @$digiCore->checkPayment($transaction_info['transaction_id'], $params);
            if ($pay_type == '_AFP_') {
                $result = substr($result, 7);
                list ($invoiceKey, $invoicePaymentReference, $status) = explode("|", $result);
                if (strtolower($status) == "captured") {
                    $paidStatus = true;
                } elseif (strtolower($status) == "incomplete") {
                    list ($invoiceKey, $invoicePaymentReference, $status, $enrichment_url) = explode("|", $result);
                    // Redirect to enrichment URL
                    tep_redirect($enrichment_url);
                    exit();
                } elseif (strtolower($status) == "rejected") {
                    list ($invoiceKey, $invoicePaymentReference, $status, $reject_reason, $reject_messages) = explode("|", $result);
                    // Show error message
                    tep_redirect(TargetPayCore::formatOscommerceUrl(tep_href_link(FILENAME_CHECKOUT_PAYMENT, 'error_message=' . urlencode(constant("MODULE_PAYMENT_TARGETPAY_" . $pay_type . "_ERROR_TEXT_ERROR_OCCURRED_PROCESSING") . " " . $reject_reason), 'SSL', true, false)));
                    exit(0);
                }
            }
            else {
                $paidStatus = $digiCore->getPaidStatus();
                if($pay_type == "_BW_") {
                    // Check paid amount with Bankwire method
                    $consumber_info = $digiCore->getConsumerInfo();
                    if (!empty($consumber_info) && $consumber_info['bw_paid_amount'] > 0) {
                        if ($consumber_info['bw_paid_amount'] < $consumber_info['bw_due_amount']) {
                            $paymentIsPartial = true;
                            $bw_paid_amount = $consumber_info['bw_paid_amount'];
                        }
                    }
                }
            }
        }

        if ($paidStatus) {
            $order_status = constant('MODULE_PAYMENT_TARGETPAY' . $pay_type . 'PREPARE_ORDER_STATUS_ID');
            if($paymentIsPartial) {
                // Set order status as Reviewing when paid by Bankwire method
                $order_status = constant('MODULE_PAYMENT_TARGETPAY' . $pay_type . 'PAYMENT_REVIEW');
                // Add history for partal paid amount
                $history_data = array(
                    'orders_id' => $order_id,
                    'orders_status_id' => $order_status,
                    'date_added' => 'now()',
                    'customer_notified' => '0',
                    'comments' => "Overschrijvingen partial paid: " . number_format($bw_paid_amount / 100, 2)
                );
                tep_db_perform(TABLE_ORDERS_STATUS_HISTORY, $history_data);
            }
            // Update Order status
            tep_db_query("update " . TABLE_ORDERS . " set orders_status = '" . $order_status . "', last_modified = now() where orders_id = '" . (int) $order_id . "'");

            $sql_data_array = array(
                'orders_id' => $order_id,
                'orders_status_id' => $order_status,
                'date_added' => 'now()',
                'customer_notified' => '0',
                'comments' => 'Digiwallet result: ' . ($testMode ? "Success (Test mode)" : $digiCore->getMoreInformation()) . ($paymentIsPartial ? " - Partially paid" : " - Fully paid")
            );
            tep_db_perform(TABLE_ORDERS_STATUS_HISTORY, $sql_data_array);

            for ($i = 0, $n = sizeof($order->products); $i < $n; $i ++) {
                // Stock Update - Joao Correia
                if (STOCK_LIMITED == 'true') {
                    if (DOWNLOAD_ENABLED == 'true') {
                        $stock_query_raw = "SELECT products_quantity, pad.products_attributes_filename
														FROM " . TABLE_PRODUCTS . " p
														LEFT JOIN " . TABLE_PRODUCTS_ATTRIBUTES . " pa
														ON p.products_id=pa.products_id
														LEFT JOIN " . TABLE_PRODUCTS_ATTRIBUTES_DOWNLOAD . " pad
														ON pa.products_attributes_id=pad.products_attributes_id
														WHERE p.products_id = '" . tep_get_prid($order->products[$i]['id']) . "'";
                        // Will work with only one option for downloadable products
                        // otherwise, we have to build the query dynamically with a loop
                        $products_attributes = $order->products[$i]['attributes'];
                        if (is_array($products_attributes)) {
                            $stock_query_raw .= " AND pa.options_id = '" . $products_attributes[0]['option_id'] . "' AND pa.options_values_id = '" . $products_attributes[0]['value_id'] . "'";
                        }
                        $stock_query = tep_db_query($stock_query_raw);
                    } else {
                        $stock_query = tep_db_query("select products_quantity from " . TABLE_PRODUCTS . " where products_id = '" . tep_get_prid($order->products[$i]['id']) . "'");
                    }
                    if (tep_db_num_rows($stock_query) > 0) {
                        $stock_values = tep_db_fetch_array($stock_query);
                        // do not decrement quantities if products_attributes_filename exists
                        if ((DOWNLOAD_ENABLED != 'true') || (! $stock_values['products_attributes_filename'])) {
                            $stock_left = $stock_values['products_quantity'] - $order->products[$i]['qty'];
                        } else {
                            $stock_left = $stock_values['products_quantity'];
                        }
                        tep_db_query("update " . TABLE_PRODUCTS . " set products_quantity = '" . $stock_left . "' where products_id = '" . tep_get_prid($order->products[$i]['id']) . "'");
                        if (($stock_left < 1) && (STOCK_ALLOW_CHECKOUT == 'false')) {
                            tep_db_query("update " . TABLE_PRODUCTS . " set products_status = '0' where products_id = '" . tep_get_prid($order->products[$i]['id']) . "'");
                        }
                    }
                }

                // Update products_ordered (for bestsellers list)
                tep_db_query("update " . TABLE_PRODUCTS . " set products_ordered = products_ordered + " . sprintf('%d', $order->products[$i]['qty']) . " where products_id = '" . tep_get_prid($order->products[$i]['id']) . "'");
                // Update Digiwallet
                tep_db_query("UPDATE " . TABLE_TARGETPAY_TRANSACTIONS . " SET `transaction_status` = 'success', `datetimestamp` = NOW()  WHERE `transaction_id` = '" . $transaction_info['transaction_id'] . "' LIMIT 1");
                // ------insert customer choosen option to order--------
                $attributes_exist = '0';
                $products_ordered_attributes = '';
                if (isset($order->products[$i]['attributes'])) {
                    $attributes_exist = '1';
                    for ($j = 0, $n2 = sizeof($order->products[$i]['attributes']); $j < $n2; $j ++) {
                        if (DOWNLOAD_ENABLED == 'true') {
                            $attributes_query = "select popt.products_options_name, poval.products_options_values_name, pa.options_values_price, pa.price_prefix, pad.products_attributes_maxdays, pad.products_attributes_maxcount , pad.products_attributes_filename
														from " . TABLE_PRODUCTS_OPTIONS . " popt, " . TABLE_PRODUCTS_OPTIONS_VALUES . " poval, " . TABLE_PRODUCTS_ATTRIBUTES . " pa
														left join " . TABLE_PRODUCTS_ATTRIBUTES_DOWNLOAD . " pad
														on pa.products_attributes_id=pad.products_attributes_id
														where pa.products_id = '" . $order->products[$i]['id'] . "'
														and pa.options_id = '" . $order->products[$i]['attributes'][$j]['option_id'] . "'
														and pa.options_id = popt.products_options_id
														and pa.options_values_id = '" . $order->products[$i]['attributes'][$j]['value_id'] . "'
														and pa.options_values_id = poval.products_options_values_id
														and popt.language_id = '" . $languages_id . "'
														and poval.language_id = '" . $languages_id . "'";
                            $attributes = tep_db_query($attributes_query);
                        } else {
                            $attributes = tep_db_query("select popt.products_options_name, poval.products_options_values_name, pa.options_values_price, pa.price_prefix from " . TABLE_PRODUCTS_OPTIONS . " popt, " . TABLE_PRODUCTS_OPTIONS_VALUES . " poval, " . TABLE_PRODUCTS_ATTRIBUTES . " pa where pa.products_id = '" . $order->products[$i]['id'] . "' and pa.options_id = '" . $order->products[$i]['attributes'][$j]['option_id'] . "' and pa.options_id = popt.products_options_id and pa.options_values_id = '" . $order->products[$i]['attributes'][$j]['value_id'] . "' and pa.options_values_id = poval.products_options_values_id and popt.language_id = '" . $languages_id . "' and poval.language_id = '" . $languages_id . "'");
                        }
                        $attributes_values = tep_db_fetch_array($attributes);
                        if(!empty($attributes_values)) {
                            $products_ordered_attributes .= "\n\t" . $attributes_values['products_options_name'] . ' ' . $attributes_values['products_options_values_name'];
                        } else {
                            if(!empty($order->products[$i]['attributes'][$j]['option'])) {
                                $products_ordered_attributes .= "\n\t" . $order->products[$i]['attributes'][$j]['option'] . ': ' . $order->products[$i]['attributes'][$j]['value'];
                                if(!empty($order->products[$i]['attributes'][$j]['prefix']) && !empty($order->products[$i]['attributes'][$j]['price'])) {
                                    $products_ordered_attributes .= " (" . $order->products[$i]['attributes'][$j]['prefix'] . $currencies->format($order->products[$i]['attributes'][$j]['price']) . ")";
                                }
                            }
                        }
                    }
                }
                // ------insert customer choosen option eof ----
                $total_weight += ($order->products[$i]['qty'] * $order->products[$i]['weight']);
                $total_tax += tep_calculate_tax($total_products_price, $products_tax) * $order->products[$i]['qty'];
                $total_cost += $total_products_price;

                $products_ordered .= $order->products[$i]['qty'] . ' x ' . $order->products[$i]['name'] . ' (' . $order->products[$i]['model'] . ') = ' . $currencies->display_price($order->products[$i]['final_price'], $order->products[$i]['tax'], $order->products[$i]['qty']) . $products_ordered_attributes . "\n";
            } // END FOR PRODUCTS IN ORDER LOOP


            // -------lets start with the email confirmation

            if (! defined('EMAIL_TEXT_SUBJECT')) {
                define('EMAIL_TEXT_SUBJECT', 'Bevestiging van uw bestelling');
                define('EMAIL_TEXT_ORDER_NUMBER', 'Order Nummer:');
                define('EMAIL_TEXT_INVOICE_URL', 'Factuurspecificatie:');
                define('EMAIL_TEXT_DATE_ORDERED', 'Besteldatum:');
                define('EMAIL_TEXT_PRODUCTS', 'Producten');
                define('EMAIL_TEXT_SUBTOTAL', 'Subtotaal:');
                define('EMAIL_TEXT_TAX', 'Belasting:        ');
                define('EMAIL_TEXT_SHIPPING', 'Verzendkosten: ');
                define('EMAIL_TEXT_TOTAL', 'Totaal:    ');
                define('EMAIL_TEXT_DELIVERY_ADDRESS', 'Afleveradres');
                define('EMAIL_TEXT_BILLING_ADDRESS', 'Factuuradres');
                define('EMAIL_TEXT_PAYMENT_METHOD', 'Betaalwijze');
                define('EMAIL_SEPARATOR', '------------------------------------------------------');
                define('TEXT_EMAIL_VIA', 'via');
            }

            $email_order = STORE_NAME . "\n" . EMAIL_SEPARATOR . "\n" . EMAIL_TEXT_ORDER_NUMBER . ' ' . $order_id . "\n" . EMAIL_TEXT_INVOICE_URL . ' ' . TargetPayCore::formatOscommerceUrl(tep_href_link(FILENAME_ACCOUNT_HISTORY_INFO, 'order_id=' . $order_id, 'SSL', false)) . "\n" . EMAIL_TEXT_DATE_ORDERED . ' ' . strftime(DATE_FORMAT_LONG) . "\n\n";
            if ($order->info['comments']) {
                $email_order .= tep_db_output($order->info['comments']) . "\n\n";
            }


            $email_order .= EMAIL_TEXT_PRODUCTS . "\n" . EMAIL_SEPARATOR . "\n" . $products_ordered . EMAIL_SEPARATOR . "\n";


            for ($i = 0, $n = sizeof($order_totals); $i < $n; $i ++) {
                $email_order .= strip_tags($order_totals[$i]['title']) . ' ' . strip_tags($order_totals[$i]['text']) . "\n";
            }

            if ($order->content_type != 'virtual') {
                $email_order .= "\n" . EMAIL_TEXT_DELIVERY_ADDRESS . "\n" . EMAIL_SEPARATOR . "\n" . $order->delivery['name'] . "\n" . $order->delivery['company'] . "\n" . $order->delivery['street_address'] . "\n" . $order->delivery['postcode'] . " " . $delivery['city'] . "\n" . $order->delivery['state'] . "\n" . $order->delivery['country']['title'] . "\n\n";
            }

            $email_order .= "\n" . EMAIL_TEXT_BILLING_ADDRESS . "\n" . EMAIL_SEPARATOR . "\n" . $order->billing['name'] . "\n" . $order->billing['company'] . "\n" . $order->billing['street_address'] . "\n" . $order->billing['postcode'] . " " . $billing['city'] . "\n" . $order->billing['state'] . "\n" . $order->billing['country']['title'] . "\n\n";

            $email_order .= EMAIL_TEXT_PAYMENT_METHOD . "\n" . EMAIL_SEPARATOR . "\n";
            $email_order .= $order->info['payment_method'] . "\n\n";
            if ($payment->email_footer) {
                $email_order .= $payment->email_footer . "\n\n";
            }

            tep_mail($order->customer['firstname'] . ' ' . $order->customer['lastname'], $order->customer['email_address'], EMAIL_TEXT_SUBJECT, $email_order, STORE_OWNER, STORE_OWNER_EMAIL_ADDRESS);

            // send emails to other people
            if (SEND_EXTRA_ORDER_EMAILS_TO != '') {
                tep_mail('', SEND_EXTRA_ORDER_EMAILS_TO, EMAIL_TEXT_SUBJECT, $email_order, STORE_OWNER, STORE_OWNER_EMAIL_ADDRESS);
            }
            // Clear Cart here
            $cart->remove_all();
            if($cancel){
                // Redirect to checkout page
                tep_redirect(TargetPayCore::formatOscommerceUrl(tep_href_link(FILENAME_CHECKOUT_SUCCESS, '', 'SSL', true, false)));
                exit(0);
            } else if($finished){
                // Redirect to checkout page
                tep_redirect(TargetPayCore::formatOscommerceUrl(tep_href_link('ext/modules/payment/targetpay/checkout.php', '', 'SSL') . ('?type=' . $_REQUEST["type"] . '&trxid=' . $trxid)));
                exit(0);
            }

        } // END IF SUCCESSFUL PAYMENT
        else {
            // Pending
            // ~ DW_SE_0020 Transaction has not been completed, try again later
            // Cancelled
            // ~ DW_SE_0021 Transaction has been cancelled
            // Error
            // ~ DW_SE_0022 Transaction has expired
            // ~ DW_SE_0023 Transaction could not be processed
            // ~ DW_SE_0001 No layoutcode
            // ~ DW_SE_0018 No valid identifiers
            // ~ DW_SE_0016 Transaction not found
            // ~ DW_SE_0019 Layoutcode does not match transaction
            // ~ DW_IE_0001 Unknown internal error
            // success
            // ~ DW_SE_0024 Transaction already checked
            $errorCode = substr($digiCore->getErrorMessage(), 0, 10);
            switch ($errorCode) {
                case 'DW_SE_0020':
                    $status = 1;
                    break;
                case 'DW_SE_0021':
                    $status = constant('MODULE_PAYMENT_TARGETPAY' . $pay_type . 'PAYMENT_CANCELLED');
                    break;
                case 'DW_SE_0022':
                case 'DW_SE_0023':
                case 'DW_SE_0001':
                case 'DW_SE_0018':
                case 'DW_SE_0016':
                case 'DW_SE_0019':
                case 'DW_IE_0001':
                    $status = constant('MODULE_PAYMENT_TARGETPAY' . $pay_type . 'PAYMENT_ERROR');
                    break;
                case 'DW_SE_0024':
                    $status = constant('MODULE_PAYMENT_TARGETPAY' . $pay_type . 'PREPARE_ORDER_STATUS_ID');
                    break;
            }

            $sql_data_array = array(
                'orders_id' => $order_id,
                'orders_status_id' => $status,
                'date_added' => 'now()',
                'customer_notified' => '0',
                'comments' => 'Digiwallet result ' . $digiCore->getErrorMessage() . ' used transaction id : ' . $transaction_info['transaction_id'] . ' | ' . $digiCore->getTransactionId() . '|' . $digiCore->getPayMethod() . '|'
            );
            tep_db_perform(TABLE_ORDERS_STATUS_HISTORY, $sql_data_array);
            // Update Digiwallet
            if($cancel && !$testMode){
                tep_db_query("UPDATE " . TABLE_TARGETPAY_TRANSACTIONS . " SET `transaction_status` = 'cancel',`datetimestamp` = NOW( )  WHERE `transaction_id` = '" . $transaction_info['transaction_id'] . "' LIMIT 1");
            }
            // Update order status
            tep_db_query("update " . TABLE_ORDERS . " set orders_status = '" . $status . "', last_modified = now() where orders_id = '" . (int) $order_id . "'");
        }
    }
} else {
    die('transaction not found');
}
if($cancel){
    // Redirect to checkout page
    tep_redirect(TargetPayCore::formatOscommerceUrl(tep_href_link(FILENAME_CHECKOUT_PAYMENT, '', 'SSL', true, false)));
    exit(0);
} else if($finished){
    // Redirect to checkout page
    tep_redirect(TargetPayCore::formatOscommerceUrl(tep_href_link('ext/modules/payment/targetpay/checkout.php', '', 'SSL') . ('?type=' . $_REQUEST["type"] . '&trxid=' . $trxid)));
    exit(0);
}
echo '45000';
require 'includes/application_bottom.php';
