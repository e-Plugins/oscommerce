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

$ywincludefile = realpath(dirname(__FILE__) . '/digiwallet.class.php');
require_once $ywincludefile;

class digiwalletpayment
{

    const DEFAULT_RTLO = 156187;

    const DEFAULT_DIGIWALLET_TOKEN = 'bf72755a648832f48f0995454';

    public $code;

    public $title;

    public $public_title;

    public $payment_icon;

    public $description;

    public $enabled;

    public $sort_order = 0;

    public $rtlo;

    public $passwordKey;

    public $merchantReturnURL;

    public $expirationPeriod;

    public $transactionDescription;

    public $transactionDescriptionText;

    public $returnURL;

    public $reportURL;

    public $transactionID;

    public $purchaseID;

    public $directoryUpdateFrequency;

    public $error;

    public $bankUrl;

    public $config_code = "IDE";

    /**
     *
     * @method digiwallet inits the module
     */
    public function digiwalletpayment()
    {
        global $order;

        $this->code = 'digiwallet_' . strtolower($this->config_code);
        $this->title = $this->getConstant("MODULE_PAYMENT_DIGIWALLET_" . $this->config_code . "_TEXT_TITLE");
        $this->public_title = $this->getConstant("MODULE_PAYMENT_DIGIWALLET_" . $this->config_code . "_TEXT_PUBLIC_TITLE");
        $this->payment_icon = tep_image('images/icons/' . $this->config_code . '_50.png', '', '', '', 'align=absmiddle');
        $this->description = $this->getConstant("MODULE_PAYMENT_DIGIWALLET_" . $this->config_code . "_TEXT_DESCRIPTION") . $this->getConstant("MODULE_PAYMENT_DIGIWALLET_TESTMODE_WARNING_MESSAGE");
        $sort_order = $this->getConstant("MODULE_PAYMENT_DIGIWALLET_" . $this->config_code . "_SORT_ORDER");
        if(!empty($sort_order)) {
            $this->sort_order = $sort_order;
        }
        $this->enabled = (($this->getConstant("MODULE_PAYMENT_DIGIWALLET_" . $this->config_code . "_STATUS") == 'True') ? true : false);

        $this->rtlo = $this->getConstant("MODULE_PAYMENT_DIGIWALLET_" . $this->config_code . "_DIGIWALLET_RTLO");

        $this->transactionDescription = $this->getConstant("MODULE_PAYMENT_DIGIWALLET_" . $this->config_code . "_TRANSACTION_DESCRIPTION");
        $this->transactionDescriptionText = $this->getConstant("MODULE_PAYMENT_DIGIWALLET_" . $this->config_code . "_MERCHANT_TRANSACTION_DESCRIPTION_TEXT");

        if ($this->getConstant("MODULE_PAYMENT_DIGIWALLET_" . $this->config_code . "_REPAIR_ORDER") === true) {
            if ($_GET['digiwallet_transaction_id']) {
                $_SESSION['digiwallet_repair_transaction_id'] = tep_db_input($_GET['digiwallet_transaction_id']);
            }
            $this->transactionID = $_SESSION['digiwallet_repair_transaction_id'];
        }
    }

    /**
     * Get the defined constant values
     *
     * @param unknown $key
     * @return mixed|boolean
     */
    public function getConstant($key)
    {
        if (defined($key)) {
            return constant($key);
        }
        return false;
    }

    /**
     * update module status
     */
    public function update_status()
    {
        global $order, $db;

        if (($this->enabled == true) && ((int) $this->getConstant("MODULE_PAYMENT_DIGIWALLET_" . $this->config_code . "_ZONE") > 0)) {
            $check_flag = false;
            $check = tep_db_query("select zone_id from " . TABLE_ZONES_TO_GEO_ZONES . " where geo_zone_id = '" . $this->getConstant("MODULE_PAYMENT_DIGIWALLET_" . $this->config_code . "_ZONE") . "' and zone_country_id = '" . $order->billing['country']['id'] . "' order by zone_id");

            while (! $check->EOF) {
                if ($check->fields['zone_id'] < 1) {
                    $check_flag = true;
                    break;
                } elseif ($check->fields['zone_id'] == $order->billing['zone_id']) {
                    $check_flag = true;
                    break;
                }
                $check->MoveNext();
            }
            if ($check_flag == false) {
                $this->enabled = false;
            }
        }
    }

    /**
     * get bank directory
     */
    public function getDirectory()
    {
        $issuerList = array();

        $objDigiCore = new DigiWalletCore($this->config_code, $this->rtlo);

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
     * {@inheritdoc}
     *
     * @see digiwallet::selection()
     */
    public function selection()
    {
        return array(
            'id' => $this->code,
            'module' => $this->payment_icon
        );
    }

    /**
     * Don't check javascript validation
     *
     * @return boolean
     */
    public function javascript_validation()
    {
        return false;
    }

    /**
     */
    public function pre_confirmation_check()
    {
        global $cartID, $cart;

        if (empty($cart->cartID)) {
            $cartID = $cart->cartID = $cart->generate_cart_id();
        }

        if (! tep_session_is_registered('cartID')) {
            tep_session_register('cartID');
        }
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
        $objDigiCore = new DigiWalletCore($payment_issuer, $this->rtlo, 'nl', $iTest);
        $objDigiCore->setAmount($payment_amount);
        $objDigiCore->setDescription($payment_description);

        if(!empty($_POST['bankID'])) {  //for ideal
            $objDigiCore->setBankId($_POST['bankID']);
        }
        if(!empty($_POST['countryID'])) {   //for sorfor
            $objDigiCore->setBankId($_POST['countryID']);
        }

        $objDigiCore->setReturnUrl(DigiWalletCore::formatOscommerceUrl(tep_href_link('ext/modules/payment/digiwallet/callback.php?type=' . $this->config_code, '', 'SSL') . "&finished=1"));
        $objDigiCore->setReportUrl(DigiWalletCore::formatOscommerceUrl(tep_href_link('ext/modules/payment/digiwallet/callback.php?type=' . $this->config_code, '', 'SSL')));
        $objDigiCore->setCancelUrl(DigiWalletCore::formatOscommerceUrl(tep_href_link('ext/modules/payment/digiwallet/callback.php?type=' . $this->config_code, '', 'SSL') . "&cancel=1"));

        // Consumer's email address
        if(isset($order->customer['email_address']) && !empty($order->customer['email_address'])) {
            $objDigiCore->bindParam("email", $order->customer['email_address']);
        }

        $result = @$objDigiCore->startPayment();

        if ($result === false) {
            $messageStack->add_session('checkout_payment', $this->getConstant("MODULE_PAYMENT_DIGIWALLET_" . $this->config_code . "_ERROR_TEXT_ERROR_OCCURRED_PROCESSING") . "<br/>" . $objDigiCore->getErrorMessage());
            tep_redirect(tep_href_link(FILENAME_CHECKOUT_PAYMENT, 'error_message=' . urlencode($this->getConstant("MODULE_PAYMENT_DIGIWALLET_" . $this->config_code . "_ERROR_TEXT_ERROR_OCCURRED_PROCESSING") . " " . $objDigiCore->getErrorMessage()), 'SSL', true, false));
        }

        $this->transactionID = $objDigiCore->getTransactionId();

        $this->bankUrl = $objDigiCore->getBankUrl();

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
		    		NOW( ),
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
     *
     * @return false
     */
    public function confirmation()
    {
        global $cartID, $cart_digiwallet_id, $customer_id, $languages_id, $order, $order_total_modules;
        if (tep_session_is_registered('cartID')) {
            $insert_order = false;

            if (tep_session_is_registered('cart_digiwallet_id')) {
                $order_id = substr($cart_digiwallet_id, strpos($cart_digiwallet_id, '-') + 1);

                $curr_check = tep_db_query("select currency, payment_method from " . TABLE_ORDERS . " where orders_id = '" . (int) $order_id . "'");
                $curr = tep_db_fetch_array($curr_check);

                if (($curr['currency'] != $order->info['currency']) || ($curr['payment_method'] != $order->info['payment_method']) || ($cartID != substr($cart_digiwallet_id, 0, strlen($cartID)))) {
                    $check_query = tep_db_query('select orders_id from ' . TABLE_ORDERS_STATUS_HISTORY . ' where orders_id = "' . (int) $order_id . '" limit 1');

                    if (tep_db_num_rows($check_query) < 1) {
                        tep_db_query('delete from ' . TABLE_ORDERS . ' where orders_id = "' . (int) $order_id . '"');
                        tep_db_query('delete from ' . TABLE_ORDERS_TOTAL . ' where orders_id = "' . (int) $order_id . '"');
                        tep_db_query('delete from ' . TABLE_ORDERS_STATUS_HISTORY . ' where orders_id = "' . (int) $order_id . '"');
                        tep_db_query('delete from ' . TABLE_ORDERS_PRODUCTS . ' where orders_id = "' . (int) $order_id . '"');
                        tep_db_query('delete from ' . TABLE_ORDERS_PRODUCTS_ATTRIBUTES . ' where orders_id = "' . (int) $order_id . '"');
                        tep_db_query('delete from ' . TABLE_ORDERS_PRODUCTS_DOWNLOAD . ' where orders_id = "' . (int) $order_id . '"');
                    }
                    $insert_order = true;
                }
            } else {
                $insert_order = true;
            }

            if ($insert_order == true) {
                $order_totals = array();
                if (is_array($order_total_modules->modules)) {
                    reset($order_total_modules->modules);
                    while (list (, $value) = each($order_total_modules->modules)) {
                        $class = substr($value, 0, strrpos($value, '.'));
                        if ($GLOBALS[$class]->enabled) {
                            for ($i = 0, $n = sizeof($GLOBALS[$class]->output); $i < $n; $i ++) {
                                if (tep_not_null($GLOBALS[$class]->output[$i]['title']) && tep_not_null($GLOBALS[$class]->output[$i]['text'])) {
                                    $order_totals[] = array(
                                        'code' => $GLOBALS[$class]->code,
                                        'title' => $GLOBALS[$class]->output[$i]['title'],
                                        'text' => $GLOBALS[$class]->output[$i]['text'],
                                        'value' => $GLOBALS[$class]->output[$i]['value'],
                                        'sort_order' => $GLOBALS[$class]->sort_order
                                    );
                                }
                            }
                        }
                    }
                }

                $sql_data_array = array(
                    'customers_id' => $customer_id,
                    'customers_name' => $order->customer['firstname'] . ' ' . $order->customer['lastname'],
                    'customers_company' => $order->customer['company'],
                    'customers_street_address' => $order->customer['street_address'],
                    'customers_suburb' => $order->customer['suburb'],
                    'customers_city' => $order->customer['city'],
                    'customers_postcode' => $order->customer['postcode'],
                    'customers_state' => $order->customer['state'],
                    'customers_country' => $order->customer['country']['title'],
                    'customers_telephone' => $order->customer['telephone'],
                    'customers_email_address' => $order->customer['email_address'],
                    'customers_address_format_id' => $order->customer['format_id'],
                    'delivery_name' => $order->delivery['firstname'] . ' ' . $order->delivery['lastname'],
                    'delivery_company' => $order->delivery['company'],
                    'delivery_street_address' => $order->delivery['street_address'],
                    'delivery_suburb' => $order->delivery['suburb'],
                    'delivery_city' => $order->delivery['city'],
                    'delivery_postcode' => $order->delivery['postcode'],
                    'delivery_state' => $order->delivery['state'],
                    'delivery_country' => $order->delivery['country']['title'],
                    'delivery_address_format_id' => $order->delivery['format_id'],
                    'billing_name' => $order->billing['firstname'] . ' ' . $order->billing['lastname'],
                    'billing_company' => $order->billing['company'],
                    'billing_street_address' => $order->billing['street_address'],
                    'billing_suburb' => $order->billing['suburb'],
                    'billing_city' => $order->billing['city'],
                    'billing_postcode' => $order->billing['postcode'],
                    'billing_state' => $order->billing['state'],
                    'billing_country' => $order->billing['country']['title'],
                    'billing_address_format_id' => $order->billing['format_id'],
                    'payment_method' => $order->info['payment_method'],
                    'cc_type' => $order->info['cc_type'],
                    'cc_owner' => $order->info['cc_owner'],
                    'cc_number' => $order->info['cc_number'],
                    'cc_expires' => $order->info['cc_expires'],
                    'date_purchased' => 'now()',
                    'orders_status' => $order->info['order_status'],
                    'currency' => $order->info['currency'],
                    'currency_value' => $order->info['currency_value']
                );

                tep_db_perform(TABLE_ORDERS, $sql_data_array);

                $insert_id = tep_db_insert_id();

                for ($i = 0, $n = sizeof($order_totals); $i < $n; $i ++) {
                    $sql_data_array = array(
                        'orders_id' => $insert_id,
                        'title' => $order_totals[$i]['title'],
                        'text' => $order_totals[$i]['text'],
                        'value' => $order_totals[$i]['value'],
                        'class' => $order_totals[$i]['code'],
                        'sort_order' => $order_totals[$i]['sort_order']
                    );
                    tep_db_perform(TABLE_ORDERS_TOTAL, $sql_data_array);
                }

                for ($i = 0, $n = sizeof($order->products); $i < $n; $i ++) {
                    $sql_data_array = array(
                        'orders_id' => $insert_id,
                        'products_id' => tep_get_prid($order->products[$i]['id']),
                        'products_model' => $order->products[$i]['model'],
                        'products_name' => $order->products[$i]['name'],
                        'products_price' => $order->products[$i]['price'],
                        'final_price' => $order->products[$i]['final_price'],
                        'products_tax' => $order->products[$i]['tax'],
                        'products_quantity' => $order->products[$i]['qty']
                    );

                    tep_db_perform(TABLE_ORDERS_PRODUCTS, $sql_data_array);

                    $order_products_id = tep_db_insert_id();
                    $attributes_exist = '0';
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

                            $sql_data_array = array(
                                'orders_id' => $insert_id,
                                'orders_products_id' => $order_products_id,
                                'products_options' => $attributes_values['products_options_name'],
                                'products_options_values' => $attributes_values['products_options_values_name'],
                                'options_values_price' => $attributes_values['options_values_price'],
                                'price_prefix' => $attributes_values['price_prefix']
                            );

                            tep_db_perform(TABLE_ORDERS_PRODUCTS_ATTRIBUTES, $sql_data_array);

                            if ((DOWNLOAD_ENABLED == 'true') && isset($attributes_values['products_attributes_filename']) && tep_not_null($attributes_values['products_attributes_filename'])) {
                                $sql_data_array = array(
                                    'orders_id' => $insert_id,
                                    'orders_products_id' => $order_products_id,
                                    'orders_products_filename' => $attributes_values['products_attributes_filename'],
                                    'download_maxdays' => $attributes_values['products_attributes_maxdays'],
                                    'download_count' => $attributes_values['products_attributes_maxcount']
                                );

                                tep_db_perform(TABLE_ORDERS_PRODUCTS_DOWNLOAD, $sql_data_array);
                            }
                        }
                    }
                }

                $cart_digiwallet_id = $cartID . '-' . $insert_id;
                tep_session_register('cart_digiwallet_id');
            }
        }
        return false;
    }

    /**
     * make hidden value for payment system
     */
    public function process_button()
    {
        $process_button = tep_draw_hidden_field('payment_code', $this->config_code) . $this->getConstant("MODULE_PAYMENT_DIGIWALLET_" . $this->config_code . "_EXPRESS_TEXT");

        if (defined('BUTTON_CHECKOUT_DIGIWALLET_ALT')) {
            $process_button .= tep_image_submit('digiwallet.gif', BUTTON_CHECKOUT_DIGIWALLET_ALT);
        }
        return $process_button;
    }

    /**
     * before process check status or prepare transaction
     */
    public function before_process()
    {
        if ($this->getConstant("MODULE_PAYMENT_DIGIWALLET_" . $this->config_code . "_REPAIR_ORDER") === true) {
            global $order;
            // when repairing iDeal the transaction status is succes, set order status accordingly
            $order->info['order_status'] = $this->getConstant("MODULE_PAYMENT_DIGIWALLET_" . $this->config_code . "_ORDER_STATUS_ID");
            return false;
        }
        if (isset($_GET['action']) && $_GET['action'] == "process") {
            $this->checkStatus();
        } else {
            $this->prepareTransaction();
        }
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

        if ($this->transactionID == "") {
            $messageStack->add_session('checkout_payment', $this->getConstant("MODULE_PAYMENT_DIGIWALLET_" . $this->config_code . "_ERROR_TEXT_ERROR_OCCURRED_PROCESSING"));
            tep_redirect(tep_href_link(FILENAME_CHECKOUT_PAYMENT, 'error_message=' . urlencode($this->getConstant("MODULE_PAYMENT_DIGIWALLET_" . $this->config_code . "_ERROR_TEXT_ERROR_OCCURRED_PROCESSING")), 'SSL', true, false));
        }

        $iTest = false;//($this->getConstant("MODULE_PAYMENT_DIGIWALLET_" . $this->config_code . "_TESTACCOUNT") == "True") ? 1 : 0;

        $objDigiCore = new DigiWalletCore($method, $this->rtlo, 'nl', $iTest);
        $status = @$objDigiCore->checkPayment($this->transactionID);

        if ($objDigiCore->getPaidStatus()) {
            $realstatus = "success";
        } else {
            $realstatus = "open";
        }

        $customerInfo = $objDigiCore->getConsumerInfo();
        $consumerAccount = (((isset($customerInfo->consumerInfo["bankaccount"]) && ! empty($customerInfo->consumerInfo["bankaccount"])) ? $customerInfo->consumerInfo["bankaccount"] : ""));
        $consumerName = (((isset($customerInfo->consumerInfo["name"]) && ! empty($customerInfo->consumerInfo["name"])) ? $customerInfo->consumerInfo["name"] : ""));
        $consumerCity = (((isset($customerInfo->consumerInfo["city"]) && ! empty($customerInfo->consumerInfo["city"])) ? $customerInfo->consumerInfo["city"] : ""));

        tep_db_query("UPDATE " . TABLE_DIGIWALLET_TRANSACTIONS . " SET `transaction_status` = '" . $realstatus . "',`datetimestamp` = NOW( ) ,`consumer_name` = '" . $consumerName . "',`consumer_account_number` = '" . $consumerAccount . "',`consumer_city` = '" . $consumerCity . "' WHERE `transaction_id` = '" . $this->transactionID . "' LIMIT 1");

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

    /**
     * after order create set value in database
     *
     * @param
     *            $zf_order_id
     */
    public function after_order_create($zf_order_id)
    {
        tep_db_query("UPDATE " . TABLE_DIGIWALLET_TRANSACTIONS . " SET `order_id` = '" . $zf_order_id . "', `ideal_session_data` = '' WHERE `transaction_id` = '" . $this->transactionID . "' LIMIT 1 ;");
        if (isset($_SESSION['digiwallet_repair_transaction_id'])) {
            unset($_SESSION['digiwallet_repair_transaction_id']);
        }
    }

    /**
     * after process function
     *
     * @return false
     */
    public function after_process()
    {
        echo 'after process komt hier';
        return false;
    }

    /**
     * checks installation of module
     */
    public function check()
    {
        if (! isset($this->_check)) {
            $check_query = tep_db_query("select configuration_value from " . TABLE_CONFIGURATION . " where configuration_key = '" . ("MODULE_PAYMENT_DIGIWALLET_" . $this->config_code . "_STATUS") . "'");
            $this->_check = tep_db_num_rows($check_query);
        }
        return $this->_check;
    }

    /**
     * install values in database
     */
    public function install()
    {
        tep_db_query("insert IGNORE into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('Enable Digiwallet payment module', '" . ("MODULE_PAYMENT_DIGIWALLET_" . $this->config_code . "_STATUS") . "', 'True', 'Do you want to accept Digiwallet payments?', '6', '1', 'tep_cfg_select_option(array(\'True\', \'False\'), ', now())");
        tep_db_query("insert IGNORE into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('Sortorder', '" . ("MODULE_PAYMENT_DIGIWALLET_" . $this->config_code . "_SORT_ORDER") . "', '".$this->sort_order."', 'Sort order of payment methods in list. Lowest is displayed first.', '6', '2', now())");
        tep_db_query("insert IGNORE into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, use_function, set_function, date_added) values ('Payment zone', '" . ("MODULE_PAYMENT_DIGIWALLET_" . $this->config_code . "_ZONE") . "', '0', 'If a zone is selected, enable this payment method for that zone only.', '6', '3', 'tep_get_zone_class_title', 'tep_cfg_pull_down_zone_classes(', now())");

        tep_db_query("insert IGNORE into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('Transaction description', '" . ("MODULE_PAYMENT_DIGIWALLET_" . $this->config_code . "_TRANSACTION_DESCRIPTION") . "', 'Automatic', 'Select automatic for product name as description, or manual to use the text you supply below.', '6', '8', 'tep_cfg_select_option(array(\'Automatic\',\'Manual\'), ', now())");
        tep_db_query("insert IGNORE into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('Transaction description text', '" . ("MODULE_PAYMENT_DIGIWALLET_" . $this->config_code . "_MERCHANT_TRANSACTION_DESCRIPTION_TEXT") . "', '" . TITLE . "', 'Description of transactions from this webshop. <strong>Should not be empty!</strong>.', '6', '8', now())");

        tep_db_query("insert IGNORE into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('Digiwallet Outlet Identifier', '" . ("MODULE_PAYMENT_DIGIWALLET_" . $this->config_code . "_DIGIWALLET_RTLO") . "', ".self::DEFAULT_RTLO.", 'The Digiwallet layout code', '6', '4', now())"); // Default Digiwallet
        tep_db_query("insert IGNORE into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('Digiwallet API Token', '" . ("MODULE_PAYMENT_DIGIWALLET_" . $this->config_code . "_DIGIWALLET_API_TOKEN") . "', '".self::DEFAULT_DIGIWALLET_TOKEN."', 'The Digiwallet API Token', '6', '4', now())"); // Default Digiwallet

        // Remove testmode setting
        //tep_db_query("insert IGNORE into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('Testaccount?', '" . ("MODULE_PAYMENT_DIGIWALLET_" . $this->config_code . "_TESTACCOUNT") . "', 'False', 'Enable testaccount (only for validation)?', '6', '1', 'tep_cfg_select_option(array(\'True\', \'False\'), ', now())");

        //tep_db_query("insert IGNORE into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('IP address', '" . ("MODULE_PAYMENT_DIGIWALLET_" . $this->config_code . "_REPAIR_IP") . "', '" . $_SERVER['REMOTE_ADDR'] . "', 'The IP address of the user (administrator) that is allowed to complete open ideal orders (if empty everyone will be allowed, which is not recommended!).', '6', '8', now())");
        tep_db_query("insert IGNORE into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('Enable pre order emails','" . ("MODULE_PAYMENT_DIGIWALLET_" . $this->config_code . "_EMAIL_ORDER_INIT") . "', 'False', 'Do you want emails to be sent to the store owner whenever an Digiwallet order is being initiated? The default is <strong>False</strong>.', '6', '17', 'tep_cfg_select_option(array(\'True\', \'False\'), ', now())");

        tep_db_query("CREATE TABLE IF NOT EXISTS " . TABLE_DIGIWALLET_DIRECTORY . " (`issuer_id` VARCHAR( 4 ) NOT NULL ,`issuer_name` VARCHAR( 30 ) NOT NULL ,`issuer_issuerlist` VARCHAR( 5 ) NOT NULL ,`timestamp` DATETIME NOT NULL ,PRIMARY KEY ( `issuer_id` ) );");

        if (DIGIWALLET_OLD_MYSQL_VERSION_COMP == 'true') {
            tep_db_query("CREATE TABLE IF NOT EXISTS " . TABLE_DIGIWALLET_TRANSACTIONS . " (`transaction_id` VARCHAR( 30 ) NOT NULL ,`rtlo` VARCHAR( 7 ) NOT NULL ,`purchase_id` VARCHAR( 30 ) NOT NULL , `issuer_id` VARCHAR( 25 ) NOT NULL , `session_id` VARCHAR( 128 ) NOT NULL ,`ideal_session_data`  MEDIUMBLOB NOT NULL ,`order_id` INT( 11 ),`transaction_status` VARCHAR( 10 ) ,`datetimestamp` DATETIME, `consumer_name` VARCHAR( 50 ) ,`consumer_account_number` VARCHAR( 20 ) ,`consumer_city` VARCHAR( 50 ), `customer_id` INT( 11 ), `amount` DECIMAL( 15, 4 ), `currency` CHAR( 3 ), `batch_id` VARCHAR( 30 ), PRIMARY KEY ( `transaction_id` ));");
        } else {
            tep_db_query("CREATE TABLE IF NOT EXISTS " . TABLE_DIGIWALLET_TRANSACTIONS . " (`transaction_id` VARCHAR( 30 ) NOT NULL ,`rtlo` VARCHAR( 7 ) NOT NULL ,`purchase_id` VARCHAR( 30 ) NOT NULL , `issuer_id` VARCHAR( 25 ) NOT NULL , `session_id` VARCHAR( 128 ) NOT NULL ,`ideal_session_data`  MEDIUMBLOB NOT NULL ,`order_id` INT( 11 ),`transaction_status` VARCHAR( 10 ) ,`datetimestamp` DATETIME, `last_modified` TIMESTAMP NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP, `consumer_name` VARCHAR( 50 ) ,`consumer_account_number` VARCHAR( 20 ) ,`consumer_city` VARCHAR( 50 ), `customer_id` INT( 11 ), `amount` DECIMAL( 15, 4 ), `currency` CHAR( 3 ), `batch_id` VARCHAR( 30 ), PRIMARY KEY ( `transaction_id` ));");
        }

        // Add more column for Afterpay and Bankwire payment
        $result = tep_db_query("SHOW COLUMNS FROM " . TABLE_DIGIWALLET_TRANSACTIONS . " LIKE 'more';");
        if (tep_db_num_rows($result) != 1) {
            // Add more columns
            tep_db_query("ALTER TABLE " . TABLE_DIGIWALLET_TRANSACTIONS . " ADD `more` TEXT default null;");
        }

        $sql = "select max(orders_status_id) as status_id from " . TABLE_ORDERS_STATUS;
        $status_query = tep_db_query($sql);

        $status = tep_db_fetch_array($status_query);
        $status_id = $status['status_id'] + 1;
        $cancel = $status['status_id'] + 2;
        $error = $status['status_id'] + 3;
        $review = $status['status_id'] + 4;

        $languages = tep_get_languages();
        $orderStatusIds = array(
            $status_id => 'Payment Paid [digiwallet]',
            $cancel => 'Payment canceled [digiwallet]',
            $error => 'Payment error [digiwallet]',
            $review => 'Payment Review [digiwallet]',
        );
        foreach ($languages as $language) {
            foreach ($orderStatusIds as $orderStatusId => $orderStatusName) {
                $query = sprintf(
                    'SELECT 1 FROM %s WHERE `language_id` = %d AND `orders_status_name` = "%s"',
                    TABLE_ORDERS_STATUS,
                    $language['id'],
                    $orderStatusName
                );
                if (tep_db_num_rows(tep_db_query($query)) == 0) {
                    $query = sprintf(
                        'INSERT INTO %s SET `orders_status_id` = %d, `language_id` = %d, `orders_status_name` = "%s"',
                        TABLE_ORDERS_STATUS,
                        $orderStatusId,
                        $language['id'],
                        $orderStatusName
                    );
                    tep_db_query($query);
                }
            }
        }
        $sql = "select max(orders_status_id) as status_id from " . TABLE_ORDERS_STATUS . " WHERE orders_status_name = 'Payment Paid [digiwallet]'";
        $status = tep_db_fetch_array(tep_db_query($sql));
        $status_id = $status['status_id'];

        $sql = "select max(orders_status_id) as status_id from " . TABLE_ORDERS_STATUS . " WHERE orders_status_name = 'Payment canceled [digiwallet]'";
        $status = tep_db_fetch_array(tep_db_query($sql));
        $cancel = $status['status_id'];

        $sql = "select max(orders_status_id) as status_id from " . TABLE_ORDERS_STATUS . " WHERE orders_status_name = 'Payment error [digiwallet]'";
        $status = tep_db_fetch_array(tep_db_query($sql));
        $error = $status['status_id'];

        $sql = "select max(orders_status_id) as status_id from " . TABLE_ORDERS_STATUS . " WHERE orders_status_name = 'Payment Review [digiwallet]'";
        $status = tep_db_fetch_array(tep_db_query($sql));
        $review = $status['status_id'];

        tep_db_query("insert IGNORE into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, use_function, date_added)
		values ('Set Paid Order Status', '" . ("MODULE_PAYMENT_DIGIWALLET_" . $this->config_code . "_PREPARE_ORDER_STATUS_ID") . "', '" . $status_id . "', 'Set the status of prepared orders to success', '6', '0', 'tep_cfg_pull_down_order_statuses(', 'tep_get_order_status_name', now())");
        tep_db_query("insert IGNORE into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, use_function, date_added)
		values ('Set Paid Order Status', '" . ("MODULE_PAYMENT_DIGIWALLET_" . $this->config_code . "_PAYMENT_CANCELLED") . "', '" . $cancel . "', 'The payment is cancelled by the enduser', '6', '0', 'tep_cfg_pull_down_order_statuses(', 'tep_get_order_status_name', now())");
        tep_db_query("insert IGNORE into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, use_function, date_added)
		values ('Set Paid Order Status', '" . ("MODULE_PAYMENT_DIGIWALLET_" . $this->config_code . "_PAYMENT_ERROR") . "', '" . $error . "', 'The payment is error', '6', '0', 'tep_cfg_pull_down_order_statuses(', 'tep_get_order_status_name', now())");
        tep_db_query("insert IGNORE into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, use_function, date_added)
		values ('Set Payment Review Status', '" . ("MODULE_PAYMENT_DIGIWALLET_" . $this->config_code . "_PAYMENT_REVIEW") . "', '" . $review . "', 'The payment is being reviewed when paid by Bankwire', '6', '0', 'tep_cfg_pull_down_order_statuses(', 'tep_get_order_status_name', now())");
    }

    /**
     * Remove the module event
     */
    public function remove()
    {
        tep_db_query("delete from " . TABLE_CONFIGURATION . " where configuration_key in ('" . implode("', '", $this->keys()) . "')");
    }

    /**
     * Configuration keys
     *
     * @return string[]
     */
    public function keys()
    {
        return array(
            ("MODULE_PAYMENT_DIGIWALLET_" . $this->config_code . "_DIGIWALLET_RTLO"),
            ("MODULE_PAYMENT_DIGIWALLET_" . $this->config_code . "_DIGIWALLET_API_TOKEN"),
            // ("MODULE_PAYMENT_DIGIWALLET_" . $this->config_code . "_TESTACCOUNT"),
            ("MODULE_PAYMENT_DIGIWALLET_" . $this->config_code . "_PAYMENT_ERROR"),
            ("MODULE_PAYMENT_DIGIWALLET_" . $this->config_code . "_PREPARE_ORDER_STATUS_ID"),
            ("MODULE_PAYMENT_DIGIWALLET_" . $this->config_code . "_PAYMENT_CANCELLED"),
            ("MODULE_PAYMENT_DIGIWALLET_" . $this->config_code . "_PAYMENT_REVIEW"),
            ("MODULE_PAYMENT_DIGIWALLET_" . $this->config_code . "_STATUS"),
            ("MODULE_PAYMENT_DIGIWALLET_" . $this->config_code . "_SORT_ORDER"),
            ("MODULE_PAYMENT_DIGIWALLET_" . $this->config_code . "_ZONE"),
            ("MODULE_PAYMENT_DIGIWALLET_" . $this->config_code . "_TRANSACTION_DESCRIPTION"),
            ("MODULE_PAYMENT_DIGIWALLET_" . $this->config_code . "_MERCHANT_TRANSACTION_DESCRIPTION_TEXT"),
            ("MODULE_PAYMENT_DIGIWALLET_" . $this->config_code . "_EMAIL_ORDER_INIT")
        );
    }
}
