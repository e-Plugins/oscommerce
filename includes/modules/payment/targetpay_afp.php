<?php
/**
 * Digiwallet Payment Module for osCommerce
*
* @copyright Copyright 2013-2014 Yellow Melon
* @copyright Portions Copyright 2013 Paul Mathot
* @copyright Portions Copyright 2003 osCommerce
* @license   see LICENSE.TXT
*/
$ywincludefile = realpath(dirname(__FILE__) . '/targetpay/targetpayment.class.php');
require_once $ywincludefile;

class targetpay_afp extends targetpayment
{
    /**
     * Tax applying percent
     * @var array
     */
    protected $array_tax = [
        1 => 21,
        2 => 6,
        3 => 0,
        4 => 'none'
    ];
    /***
     * Get product tax by Digiwallet
     * @param unknown $val
     * @return number
     */
    private function getTax($val)
    {
        if(empty($val)) return 4; // No tax
        else if($val >= 21) return 1;
        else if($val >= 6) return 2;
        else return 3;
    }
    /**
     *
     * @method targetpay inits the module
     */
    public function targetpay_afp()
    {
        $this->config_code = "AFP";
        parent::targetpayment();
    }

    /**
     * Format phonenumber by NL/BE
     *
     * @param unknown $country
     * @param unknown $phone
     * @return unknown
     */
    private static function format_phone($country, $phone) {
        $function = 'format_phone_' . strtolower($country);
        if(method_exists('targetpay_afp', $function)) {
            return self::$function($phone);
        }
        else {
            echo "unknown phone formatter for country: ". $function;
            exit;
        }
        return $phone;
    }
    /**
     * Format phone number
     *
     * @param unknown $phone
     * @return string|mixed
     */
    private static function format_phone_nld($phone) {
        // note: making sure we have something
        if(!isset($phone{3})) { return ''; }
        // note: strip out everything but numbers
        $phone = preg_replace("/[^0-9]/", "", $phone);
        $length = strlen($phone);
        switch($length) {
            case 9:
                return "+31".$phone;
                break;
            case 10:
                return "+31".substr($phone, 1);
                break;
            case 11:
            case 12:
                return "+".$phone;
                break;
            default:
                return $phone;
                break;
        }
    }

    /**
     * Format phone number
     *
     * @param unknown $phone
     * @return string|mixed
     */
    private static function format_phone_bel($phone) {
        // note: making sure we have something
        if(!isset($phone{3})) { return ''; }
        // note: strip out everything but numbers
        $phone = preg_replace("/[^0-9]/", "", $phone);
        $length = strlen($phone);
        switch($length) {
            case 9:
                return "+32".$phone;
                break;
            case 10:
                return "+32".substr($phone, 1);
                break;
            case 11:
            case 12:
                return "+".$phone;
                break;
            default:
                return $phone;
                break;
        }
    }
    /**
     * Breadown street address
     * @param unknown $street
     * @return NULL[]|string[]|unknown[]
     */
    private static function breakDownStreet($street)
    {
        $out = [];
        $addressResult = null;
        preg_match("/(?P<address>\D+) (?P<number>\d+) (?P<numberAdd>.*)/", $street, $addressResult);
        if(!$addressResult) {
            preg_match("/(?P<address>\D+) (?P<number>\d+)/", $street, $addressResult);
        }
        $out['street'] = array_key_exists('address', $addressResult) ? $addressResult['address'] : null;
        $out['houseNumber'] = array_key_exists('number', $addressResult) ? $addressResult['number'] : null;
        $out['houseNumberAdd'] = array_key_exists('numberAdd', $addressResult) ? trim(strtoupper($addressResult['numberAdd'])) : null;
        return $out;
    }

    /**
     * prepare the transaction and send user back on error or forward to bank
     *
     * {@inheritDoc}
     * @see targetpayment::prepareTransaction()
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
        $customer_bod = "";
        $customer_gender = "";
        if (!empty($_SESSION['customer_id'])) {
            $sql = tep_db_query("select * from " . TABLE_CUSTOMERS . " where customers_id = " . (int) $_SESSION['customer_id']);
            $customer = tep_db_fetch_array($sql);
            if(!empty($customer)){
                $customer_gender = strtoupper($customer['customers_gender']);
                $customer_bod = ($customer['customers_dob'] == '0001-01-01 00:00:00') ? '' : substr($customer['customers_dob'], 0, 10);
            }
        }

        $iTest = ($this->getConstant("MODULE_PAYMENT_TARGETPAY_" . $this->config_code . "_TESTACCOUNT") == "True") ? 1 : 0;
        $objDigiCore = new TargetPayCore($payment_issuer, $this->rtlo, 'nl', $iTest);
        $objDigiCore->setAmount($payment_amount);
        $objDigiCore->setDescription($payment_description);
        $objDigiCore->bindParam('email', $order->customer['email_address']);
        $objDigiCore->bindParam('userip', $_SERVER["REMOTE_ADDR"]);

        $objDigiCore->setReturnUrl(tep_href_link('ext/modules/payment/targetpay/callback.php') . '?finished=1&type=' . $this->config_code, '', 'SSL');
        $objDigiCore->setReportUrl(tep_href_link('ext/modules/payment/targetpay/callback.php') . '?type=' . $this->config_code, '', 'SSL');
        $objDigiCore->setCancelUrl(tep_href_link('ext/modules/payment/targetpay/callback.php') . '?cancel=1&type=' . $this->config_code, '', 'SSL');
        // Add product infomation
        // Adding more information for Afterpay method
        $b_country = $order->billing['country']['iso_code_3'];
        $s_country = $order->delivery['country']['iso_code_3'];
        $b_country = (strtoupper($b_country) == 'BE' ? 'BEL' : 'NLD');
        $s_country = (strtoupper($s_country) == 'BE' ? 'BEL' : 'NLD');
        // Build billing address
        $streetParts = self::breakDownStreet($order->billing['street_address']);
        $objDigiCore->bindParam('billingstreet', empty($streetParts['street']) ? $order->billing['street_address'] : $streetParts['street']);
        $objDigiCore->bindParam('billinghousenumber', $streetParts['houseNumber'].$streetParts['houseNumberAdd']);
        $objDigiCore->bindParam('billingpostalcode', $order->billing['postcode']);
        $objDigiCore->bindParam('billingcity', $order->billing['city']);
        $objDigiCore->bindParam('billingpersonemail', $order->customer['email_address']);
        $objDigiCore->bindParam('billingpersoninitials', "");
        $objDigiCore->bindParam('billingpersongender', $customer_gender);
        $objDigiCore->bindParam('billingpersonsurname', trim($order->billing['lastname'] . (!empty($order->billing['firstname'])) ? " " . $order->billing['firstname'] : ""));
        $objDigiCore->bindParam('billingcountrycode', $b_country);
        $objDigiCore->bindParam('billingpersonlanguagecode', $b_country);
        $objDigiCore->bindParam('billingpersonbirthdate', $customer_bod);
        $objDigiCore->bindParam('billingpersonphonenumber', $order->customer['telephone']);
        // Build shipping address
        $streetParts = self::breakDownStreet($order->delivery['street_address']);
        $objDigiCore->bindParam('shippingstreet', empty($streetParts['street']) ? $order->delivery['street_address'] : $streetParts['street']);
        $objDigiCore->bindParam('shippinghousenumber', $streetParts['houseNumber'].$streetParts['houseNumberAdd']);
        $objDigiCore->bindParam('shippingpostalcode',  $order->delivery['postcode']);
        $objDigiCore->bindParam('shippingcity',  $order->delivery['city']);
        $objDigiCore->bindParam('shippingpersonemail',  $order->customer['email_address']);
        $objDigiCore->bindParam('shippingpersoninitials', "");
        $objDigiCore->bindParam('shippingpersongender', $customer_gender);
        $objDigiCore->bindParam('shippingpersonsurname',  trim($order->delivery['lastname'] . (!empty($order->delivery['firstname'])) ? " " . $order->delivery['firstname'] : ""));
        $objDigiCore->bindParam('shippingcountrycode', $s_country);
        $objDigiCore->bindParam('shippingpersonlanguagecode', $s_country);
        $objDigiCore->bindParam('shippingpersonbirthdate', $customer_bod);
        $objDigiCore->bindParam('shippingpersonphonenumber', $order->customer['telephone']);

        // Add products
        $invoice_lines = null;
        $total_amount_by_product = 0;
        if(!empty($order->products)){
            foreach ($order->products as $product){
                $invoice_lines[] = [
                    'productCode' => (string) $product['id'],
                    'productDescription' => $product['name'],
                    'quantity' => (int) $product['qty'],
                    'price' => (float) $product['final_price'],
                    'taxCategory' => ((float) $product['final_price'] > 0) ? $this->getTax(100 * $product['tax'] / ((float) $product['price'])) : 3
                ];
                $total_amount_by_product += (float) $product['final_price'];
            }
        }
        // Update to fix the total amount and item price
        if($total_amount_by_product < $order->info['total']){
            $invoice_lines[] = [
                'productCode' => "000000",
                'productDescription' => "Other fees (shipping, additional fees)",
                'quantity' => 1,
                'price' => $order->info['total'] - $total_amount_by_product,
                'taxCategory' => 1
            ];
        }
        // Add to invoice data
        if($invoice_lines != null && !empty($invoice_lines)){
            $objDigiCore->bindParam('invoicelines', json_encode($invoice_lines));
        }

        $result = @$objDigiCore->startPayment();

        if ($result === false) {
            $messageStack->add_session('checkout_payment', $this->getConstant("MODULE_PAYMENT_TARGETPAY_" . $this->config_code . "_ERROR_TEXT_ERROR_OCCURRED_PROCESSING") . "<br/>" . $objDigiCore->getErrorMessage());
            tep_redirect(tep_href_link(FILENAME_CHECKOUT_PAYMENT, 'error_message=' . urlencode($this->getConstant("MODULE_PAYMENT_TARGETPAY_" . $this->config_code . "_ERROR_TEXT_ERROR_OCCURRED_PROCESSING") . " " . $objDigiCore->getErrorMessage()), 'SSL', true, false));
            exit(0);
        }

        $this->transactionID = $objDigiCore->getTransactionId();


        if ($this->getConstant("MODULE_PAYMENT_TARGETPAY_" . $this->config_code . "_EMAIL_ORDER_INIT") == 'True') {
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
            $email_text .= 'Digiwallet transactions lookup: ' . HTTP_SERVER_TARGETPAY_ADMIN . FILENAME_TARGETPAY_TRANSACTIONS . '?action=lookup&transactionID=' . $this->transactionID . "\n";

            tep_mail('', STORE_OWNER_EMAIL_ADDRESS, '[iDeal bestelling opgestart] #' . $new_order_id . ' (?)', $email_text, STORE_OWNER, STORE_OWNER_EMAIL_ADDRESS);
        }

        tep_db_query("INSERT INTO " . TABLE_TARGETPAY_TRANSACTIONS . "
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
		    		`order_id`,
                    `more`
		    		) VALUES (
		    		'" . $this->transactionID . "',
		    		'" . $this->rtlo . "',
		    		'" . $payment_purchaseID . "',
		    		'" . $payment_issuer . "',
		    		'open',
		    		NOW( ),
		    		'" . $_SESSION['customer_id'] . "',
		    		'" . $payment_amount . "',
		    		'" . $payment_currency . "',
		    		'" . tep_db_input(tep_session_id()) . "',
		    		'" . base64_encode(serialize($_SESSION)) . "',
		    		'" . $customOrderId . "',
		    		'" . tep_db_input($objDigiCore->getMoreInformation()) . "'
		    		);");
        // Check the result of payment
        $result_str = $objDigiCore->getMoreInformation();
        // Process return message
        list ($trxid, $status) = explode("|", $result_str);

        if (strtolower($status) != "captured") {
            list ($trxid, $status, $ext_info) = explode("|", $result_str);
            if (strtolower($status) == "rejected") {
                // Show the error message
                $messageStack->add_session('checkout_payment', $this->getConstant("MODULE_PAYMENT_TARGETPAY_" . $this->config_code . "_ERROR_TEXT_ERROR_OCCURRED_PROCESSING") . "<br/>" . $ext_info);
                tep_redirect(tep_href_link(FILENAME_CHECKOUT_PAYMENT, 'error_message=' . urlencode($this->getConstant("MODULE_PAYMENT_TARGETPAY_" . $this->config_code . "_ERROR_TEXT_ERROR_OCCURRED_PROCESSING") . " " . $ext_info), 'SSL', true, false));
                exit(0);
            } else {
                // Redirect to enrichment URL
                tep_redirect($ext_info);
                exit(0);
            }
        } else {
            // Order OK, transfer to success page
            // Redirect to AFP's callback action to update order and relate data
            tep_redirect($objDigiCore->getReportUrl() . "&trxid=" . $this->transactionID);
            exit();
        }
    }

}
