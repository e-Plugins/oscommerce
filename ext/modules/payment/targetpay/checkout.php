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

// if the customer is not logged on, redirect them to the login page
if (! tep_session_is_registered('customer_id')) {
    $navigation->set_snapshot(array(
        'mode' => 'SSL',
        'page' => FILENAME_CHECKOUT_PAYMENT
    ));
    tep_redirect(tep_href_link(FILENAME_LOGIN, '', 'SSL'));
}

// if there is nothing in the customers cart, redirect them to the shopping cart page
//if ($cart->count_contents() < 1) {
//    tep_redirect(tep_href_link(FILENAME_SHOPPING_CART));
//}

// avoid hack attempts during the checkout procedure by checking the internal cartID
if (isset($cart->cartID) && tep_session_is_registered('cartID')) {
    if ($cart->cartID != $cartID) {
        tep_redirect(tep_href_link(FILENAME_CHECKOUT_SHIPPING, '', 'SSL'));
    }
}

// if no shipping method has been selected, redirect the customer to the shipping method selection page
if (! tep_session_is_registered('shipping')) {
    tep_redirect(tep_href_link(FILENAME_CHECKOUT_SHIPPING, '', 'SSL'));
}

if (! tep_session_is_registered('payment')) {
    tep_redirect(tep_href_link(FILENAME_CHECKOUT_PAYMENT, '', 'SSL'));
}

// load the selected payment module
require DIR_WS_CLASSES . 'payment.php';
$payment_modules = new payment($payment);

require DIR_WS_CLASSES . 'order.php';
$order = new order();

$payment_modules->update_status();

//if (
//    (is_array($payment_modules->modules) && (sizeof($payment_modules->modules) > 1) && !is_object($payment)) ||
//    (is_object($payment) && ($payment->enabled == false))
//) {
//    tep_redirect(tep_href_link(FILENAME_CHECKOUT_PAYMENT, 'error_message=' . urlencode(ERROR_NO_PAYMENT_MODULE_SELECTED), 'SSL'));
//}

if (is_array($payment_modules->modules)) {
    $payment_modules->pre_confirmation_check();
}
// load the selected shipping module
require DIR_WS_CLASSES . 'shipping.php';
$shipping_modules = new shipping($shipping);

require DIR_WS_CLASSES . 'order_total.php';
$order_total_modules = new order_total();
$order_total_modules->process();

// Stock Check
$any_out_of_stock = false;
if (STOCK_CHECK == 'true') {
    for ($i = 0, $n = sizeof($order->products); $i < $n; $i ++) {
        if (tep_check_stock($order->products[$i]['id'], $order->products[$i]['qty'])) {
            $any_out_of_stock = true;
        }
    }
    // Out of Stock
    if ((STOCK_ALLOW_CHECKOUT != 'true') && ($any_out_of_stock == true)) {
        tep_redirect(tep_href_link(FILENAME_SHOPPING_CART));
    }
}

require DIR_WS_LANGUAGES . $language . '/' . FILENAME_CHECKOUT_CONFIRMATION;

$breadcrumb->add(NAVBAR_TITLE_1, tep_href_link(FILENAME_CHECKOUT_SHIPPING, '', 'SSL'));
$breadcrumb->add(NAVBAR_TITLE_2);

// require DIR_WS_INCLUDES . 'template_top.php';

$trxid = $_REQUEST["trxid"];
$pay_type = $_REQUEST["type"];
if(!isset($pay_type) || empty($pay_type)){
    echo "No payment method found!";
    die;
}
//if($pay_type == "PYP"){
//    $trxid = $_REQUEST['paypalid'];
//    if(empty($trxid)) {
//        $trxid = $_REQUEST['acquirerID'];
//    }
//}
//else if($pay_type == "AFP"){
//    // For Afterpay only
//    $trxid = $_REQUEST['invoiceID'];
//}
if(!isset($trxid) || empty($trxid)){
    echo "No transaction found!";
    die;
}

$pay_type = (empty($pay_type)) ? "_" : "_" . $pay_type . "_";
$sql = "select * from " . TABLE_TARGETPAY_TRANSACTIONS . " where `transaction_id` = '" . $trxid . "'";
$transaction_info = tep_db_query($sql);
$transaction_info = tep_db_fetch_array($transaction_info);

$sql = "select `orders_status_id` from " . TABLE_ORDERS_STATUS_HISTORY . " where
			`orders_status_history_id` = (SELECT MAX(`orders_status_history_id`) FROM " . TABLE_ORDERS_STATUS_HISTORY . " WHERE `orders_id` = '" . $transaction_info["order_id"] . "')";
$stateRow = tep_db_query($sql);
$stateRow = tep_db_fetch_array($stateRow);

// Payment callback was first, so we can say: the payment was successfull
if ($stateRow["orders_status_id"] == constant('MODULE_PAYMENT_TARGETPAY' . $pay_type . 'PREPARE_ORDER_STATUS_ID')) {
    $message = 'Je betaling is gelukt!<br/><a href="index.php">Klik hier om verder te winkelen.</a>';
    $bgcolor = '#D3FFD2';
    $bordercolor = '#8FFF8C';
    $cart->reset(true);
    $cart->contents = array();
    $cart->total = 0;
    $cart->weight = 0;
    $cart->content_type = false;

    // unregister session variables used during checkout
    tep_session_unregister('sendto');
    tep_session_unregister('billto');
    tep_session_unregister('shipping');
    tep_session_unregister('payment');
    tep_session_unregister('comments');
    $cart->reset(true);
    tep_redirect(tep_href_link(FILENAME_CHECKOUT_SUCCESS, '', 'SSL'));
    die();
} elseif ($stateRow["orders_status_id"] == constant('MODULE_PAYMENT_TARGETPAY' . $pay_type . 'PAYMENT_CANCELLED')) {
    // $message = 'Je hebt je betaling geannuleerd, <br/><a href="checkout_payment.php">Klik hier om een andere betaalmethode te kiezen.</a>';
    // Remove html due to wrong displaying message on frontend
    $message = 'Je hebt je betaling geannuleerd. Klik hier om een andere betaalmethode te kiezen.';
    $bgcolor = '#FFE5C8';
    $bordercolor = '#FFC78C';
} elseif ($stateRow["orders_status_id"] == constant('MODULE_PAYMENT_TARGETPAY' . $pay_type . 'PAYMENT_ERROR')) {
    $message = 'Er was een probleem tijdens het controleren van je betaling, contacteer de webshop.';
    $bgcolor = '#FFBDB3';
    $bordercolor = '#FF9B8C';
} else {
    $message = 'Uw transactie is onderbroken.';
    $bgcolor = '#FFBDB3';
    $bordercolor = '#FF9B8C';
}

tep_redirect(tep_href_link(FILENAME_CHECKOUT_PAYMENT, 'error_message=' . urlencode($message), 'SSL', true, false));
die();

$ct = '<p><table frame="box" style="background-color: ' . $bgcolor . ' ;border: 1px solid ' . $bordercolor . '">
			<tr>
				<td>' . $message . '</td>
			</tr>
		</table></p>';
echo $ct;

require DIR_WS_INCLUDES . 'template_bottom.php';
require DIR_WS_INCLUDES . 'application_bottom.php';
