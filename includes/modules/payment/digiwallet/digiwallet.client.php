<?php
/**
 * Digiwallet Payment Module for osCommerce
 *
 * @copyright Copyright 2013-2014 Yellow Melon
 * @copyright Portions Copyright 2013 Paul Mathot
 * @copyright Portions Copyright 2003 osCommerce
 * @license   see LICENSE.TXT
 */
$ywincludefile = realpath(dirname(__FILE__) . '/../../../extra_datafiles/digiwallet.php');
require_once $ywincludefile;

$ywincludefile = realpath(dirname(__FILE__) . '/../../../languages/dutch/modules/payment/digiwallet.php');
require_once $ywincludefile;

if(!defined('TABLE_ORDERS')) {
    require_once realpath(dirname(__FILE__) . '/database_tables.php');
}

require_once realpath(dirname(__FILE__) . '/compatibility.php');

$ywincludefile = realpath(dirname(__FILE__) . '/digiwalletpayment.class.php');
require_once $ywincludefile;

$ywincludefile = realpath(dirname(__FILE__) . '/client/ClientCore.php');
require_once $ywincludefile;


class digiwalletClient extends digiwalletpayment
{
    protected $tpMethods = array(
        'EPS' => 'EPS',
        'GIP' => 'GIP',
    );


    /**
     * Reformat URL if add wrong params
     * @param $url
     * @return string
     */
    public static function formatOscommerceUrl($url)
    {
        $separator = "?";
        if(substr_count($url, $separator) > 1) {
            $url_array = explode($separator, $url);
            $url = "";
            foreach ($url_array as $url_item) {
                $url = $url . $url_item . $separator;
                $separator = "&";
            }
            $url = substr($url, 0, strlen($url) - 1);
        }
        return $url;
    }

    /**
     * prepare the transaction and send user back on error or forward to bank
     */
    public function prepareTransaction()
    {
        global $order, $currencies, $customer_id, $db, $messageStack, $order_totals, $cart_digiwallet_id;

        list ($void, $customOrderId) = explode("-", $cart_digiwallet_id);

        $payment_purchaseID = time();
        $payment_issuer = $this->config_code;
        $payment_currency = "EUR"; // future use
        $payment_language = "nl"; // future use
        $payment_amount = round($order->info['total'] * 100, 0);
        $payment_entranceCode = tep_session_id();
        if ((strtolower($this->transactionDescription) == 'automatic') && (count($order->products) == 1)) {
            $product = $order->products[0];
            $payment_description = $product['name'];
        } else {
            $payment_description = 'Order:' . $customOrderId . ' ' . $this->transactionDescriptionText;
        }
        $payment_description = trim(strip_tags($payment_description));
        // This function has been DEPRECATED as of PHP 5.3.0. Relying on this feature is highly discouraged.
        // $payment_description = ereg_replace("[^( ,[:alnum:])]", '*', $payment_description);
        $payment_description = preg_replace("/[^a-zA-Z0-9\s]/", '', $payment_description);
        $payment_description = substr($payment_description, 0, 31); /* Max. 32 characters */
        if (empty($payment_description)) {
            $payment_description = 'nvt';
        }

        $iTest = false;//($this->getConstant("MODULE_PAYMENT_DIGIWALLET_" . $this->config_code . "_TESTACCOUNT") == "True") ? 1 : 0;
        $objDigiCore = new \Digiwallet\ClientCore($this->rtlo, $payment_issuer, 'nl');
        $formData = array(
            'amount' => $payment_amount,
            'inputAmount' => $payment_amount,
            'consumerEmail' => $order->customer['email_address'],
            'description' => $payment_description,
            'returnUrl' => self::formatOscommerceUrl(tep_href_link('ext/modules/payment/digiwallet/client_callback.php?type=' . $this->config_code, '', 'SSL') . "&finished=1"),
            'reportUrl' => self::formatOscommerceUrl(tep_href_link('ext/modules/payment/digiwallet/client_callback.php?type=' . $this->config_code, '', 'SSL')),
            'cancelUrl' => self::formatOscommerceUrl(tep_href_link('ext/modules/payment/digiwallet/client_callback.php?type=' . $this->config_code, '', 'SSL') . "&cancel=1"),
            'test' => 0
        );
        $apiToken = constant("MODULE_PAYMENT_DIGIWALLET_{$this->config_code}_DIGIWALLET_API_TOKEN");
        /** @var \Digiwallet\Packages\Transaction\Client\Response\CreateTransactionInterface $clientResult */
        $clientResult = $objDigiCore->createTransaction($apiToken, $formData);

        if (empty($clientResult)) {
            $messageStack->add_session('checkout_payment', $this->getConstant("MODULE_PAYMENT_DIGIWALLET_" . $this->config_code . "_ERROR_TEXT_ERROR_OCCURRED_PROCESSING") . "<br/>" . $objDigiCore->getErrorMessage());
            tep_redirect(tep_href_link(FILENAME_CHECKOUT_PAYMENT, 'error_message=' . urlencode($this->getConstant("MODULE_PAYMENT_DIGIWALLET_" . $this->config_code . "_ERROR_TEXT_ERROR_OCCURRED_PROCESSING") . " " . $objDigiCore->getErrorMessage()), 'SSL', true, false));
        }

        $this->transactionID = $clientResult->transactionId();
        $this->bankUrl = $clientResult->launchUrl();

        if ($this->getConstant("MODULE_PAYMENT_DIGIWALLET_" . $this->config_code . "_EMAIL_ORDER_INIT") == 'True') {
            $email_text = 'Er is zojuist een Digiwallet iDeal bestelling opgestart' . "\n\n";
            $email_text .= 'Details:' . "\n";
            $email_text .= 'customer_id: ' . $_SESSION['customer_id'] . "\n";
            $email_text .= 'customer_first_name: ' . $_SESSION['customer_first_name'] . "\n";
            $email_text .= 'Digiwallet transaction_id: ' . $this->transactionID . "\n";
            $email_text .= 'bedrag: ' . $payment_amount . ' (' . $payment_currency . 'x100)' . "\n";
            $max_orders_id = tep_db_query("select max(orders_id) orders_id from " . TABLE_ORDERS);
            $new_order_id = $max_orders_id->fields['orders_id'] + 1;
            $email_text .= 'order_id: ' . $new_order_id . ' (verwacht indien de bestelling wordt voltooid, kan ook hoger zijn)' . "\n";
            $email_text .= "\n\n";
            $email_text .= 'Digiwallet transactions lookup: ' . HTTP_SERVER_DIGIWALLET_ADMIN . FILENAME_DIGIWALLET_TRANSACTIONS . '?action=lookup&transactionID=' . $this->transactionID . "\n";

            tep_mail('', STORE_OWNER_EMAIL_ADDRESS, '[iDeal bestelling opgestart] #' . $new_order_id . ' (?)', $email_text, STORE_OWNER, STORE_OWNER_EMAIL_ADDRESS);
        }

        tep_db_query("INSERT INTO " . TABLE_DIGIWALLET_TRANSACTIONS . "
		    		(
		    		`transaction_id`,
		    		`rtlo`,
		    		`purchase_id`,
		    		`issuer_id`,
		    		`transaction_status`,
		    		`datetimestamp`,
		    		`customer_id`,
		    		`amount`,
		    		`currency`,
		    		`session_id`,
		    		`ideal_session_data`,
		    		`order_id`
		    		) VALUES (
		    		'" . $this->transactionID . "',
		    		'" . $this->rtlo . "',
		    		'" . $payment_purchaseID . "',
		    		'" . $payment_issuer . "',
		    		'open',
		    		NOW(),
		    		'" . $_SESSION['customer_id'] . "',
		    		'" . $payment_amount . "',
		    		'" . $payment_currency . "',
		    		'" . tep_db_input(tep_session_id()) . "',
		    		'" . base64_encode(serialize($_SESSION)) . "',
		    		'" . $customOrderId . "'
		    		);");
        tep_redirect(html_entity_decode($this->bankUrl));
    }


    /**
     * check payment status
     */
    public function checkStatus()
    {
        global $order, $db, $messageStack;

        if ($this->getConstant("MODULE_PAYMENT_DIGIWALLET_" . $this->config_code . "_REPAIR_ORDER") === true) {
            return false;
        }
        $this->transactionID = tep_db_input($_GET['trxid']);
        $method = tep_db_input($_GET['method']);
        if(empty($this->transactionID)) {
            $this->transactionID = tep_db_input($_GET['transactionID']);
        }

        if(empty($this->transactionID)) {
            $messageStack->add_session('checkout_payment', $this->getConstant("MODULE_PAYMENT_DIGIWALLET_" . $this->config_code . "_ERROR_TEXT_ERROR_OCCURRED_PROCESSING"));
            tep_redirect(tep_href_link(FILENAME_CHECKOUT_PAYMENT, 'error_message=' . urlencode($this->getConstant("MODULE_PAYMENT_DIGIWALLET_" . $this->config_code . "_ERROR_TEXT_ERROR_OCCURRED_PROCESSING")), 'SSL', true, false));
        }

        $iTest = false;//($this->getConstant("MODULE_PAYMENT_DIGIWALLET_" . $this->config_code . "_TESTACCOUNT") == "True") ? 1 : 0;

        $objDigiCore = new \Digiwallet\ClientCore($this->rtlo, $method, 'nl');
        $apiToken = constant("MODULE_PAYMENT_DIGIWALLET_{$this->config_code}_DIGIWALLET_API_TOKEN");

        $status = $objDigiCore->checkTransaction($apiToken, $this->transactionID);

        if ($status) {
            $realstatus = "success";
        } else {
            $realstatus = "open";
        }

        tep_db_query("UPDATE " . TABLE_DIGIWALLET_TRANSACTIONS . " SET `transaction_status` = '" . $realstatus . "',`datetimestamp` = NOW( ) ,`consumer_name` = '',`consumer_account_number` = '',`consumer_city` = '' WHERE `transaction_id` = '" . $this->transactionID . "' LIMIT 1");

        switch ($realstatus) {
            case "success":
                $order->info['order_status'] = $this->getConstant("MODULE_PAYMENT_DIGIWALLET_" . $this->config_code . "_ORDER_STATUS_ID");
                break;
            case "open":
                $messageStack->add_session('checkout_payment', $this->getConstant("MODULE_PAYMENT_DIGIWALLET_" . $this->config_code . "_ERROR_TEXT_TRANSACTION_OPEN"));
                tep_redirect(tep_href_link(FILENAME_CHECKOUT_PAYMENT, 'error_message=' . urlencode($this->getConstant("MODULE_PAYMENT_DIGIWALLET_" . $this->config_code . "_ERROR_TEXT_TRANSACTION_OPEN")), 'SSL', true, false));
                break;
            default:
                $messageStack->add_session('checkout_payment', $this->getConstant("MODULE_PAYMENT_DIGIWALLET_" . $this->config_code . "_ERROR_TEXT_TRANSACTION_OPEN"));
                tep_redirect(tep_href_link(FILENAME_CHECKOUT_PAYMENT, 'error_message=' . urlencode($this->getConstant("MODULE_PAYMENT_DIGIWALLET_" . $this->config_code . "_ERROR_TEXT_TRANSACTION_OPEN")), 'SSL', true, false));
                break;
        }
    }
}