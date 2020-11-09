<?php

  require('includes/application_top.php');

  require 'includes/modules/payment/digiwallet/compatibility.php';

  if(!defined('TABLE_ORDERS')) {
    require_once 'includes/modules/payment/digiwallet/database_tables.php';
  }

  require(DIR_WS_INCLUDES . 'template_top.php');
  require_once 'includes/modules/payment/digiwallet/digiwallet.class.php';  
  require_once 'includes/extra_datafiles/digiwallet.php';
  $availableLanguages = array("dutch","english");
  $langDir = (isset($_SESSION["language"]) && in_array($_SESSION["language"], $availableLanguages)) ? $_SESSION["language"] : "dutch";
  $ywincludefile = realpath(DIR_WS_LANGUAGES . $langDir . '/modules/payment/digiwallet_ide.php');
  require_once $ywincludefile;

  $trxid = $_REQUEST["trxid"];
  if(empty($trxid)){
      // For Afterpay only
      $trxid = $_REQUEST['invoiceID'];
  }
  if(!$trxid){
      tep_redirect(tep_href_link(FILENAME_DEFAULT, '', 'SSL', true, false));
      exit(0);
  }
  
  // Check transaction in digiwallet sale table
  $sql = "select * from " . TABLE_DIGIWALLET_TRANSACTIONS . " where `transaction_id` = '" . tep_db_input($trxid) . "'";
  $sale_obj = tep_db_query($sql);
  if (tep_db_num_rows($sale_obj) > 0){
      $sale = tep_db_fetch_array($sale_obj);
  } else {
      tep_redirect(tep_href_link(FILENAME_DEFAULT, '', 'SSL', true, false));
      exit(0);
  }
  // Check customer's order information
  $customer_info = null;
  $query = tep_db_query("select * from " . TABLE_ORDERS . " where `orders_id` = '" . $sale['order_id'] . "'");
  if (tep_db_num_rows($query) > 0){
      $customer_info = tep_db_fetch_array($query);
  } else {
      tep_redirect(tep_href_link(FILENAME_DEFAULT, '', 'SSL', true, false));
      exit(0);
  }
?>

<?php 
   if($sale['transaction_status'] == "success"){
       ?>
       		<h1><?php echo MODULE_PAYMENT_DIGIWALLET_BANKWIRE_THANKYOU_FINISHED;?></h1>
       <?php 
   } else {
      list($trxid, $accountNumber, $iban, $bic, $beneficiary, $bank) = explode("|", $sale['more']);
      // Encode email address
      $emails = str_split($customer_info['customers_email_address']);
      $counter = 0;
      $cus_email = "";
      foreach ($emails as $char) {
          if($counter == 0) {
              $cus_email .= $char;
              $counter++;
          } else if($char == "@") {
              $cus_email .= $char;
              $counter++;
          } else if($char == "." && $counter > 1) {
              $cus_email .= $char;
              $counter++;
          } else if($counter > 2) {
              $cus_email .= $char;
          } else {
              $cus_email .= "*";
          }
      }
      echo sprintf(MODULE_PAYMENT_DIGIWALLET_BANKWIRE_THANKYOU_PAGE,
         $currencies->display_price(((float) $sale['amount'])/100, 0),
         $iban,
         $beneficiary,
         $trxid,
         $cus_email,
         $bic,
         $bank
      );
   }
   
  require(DIR_WS_INCLUDES . 'template_bottom.php');
  require(DIR_WS_INCLUDES . 'application_bottom.php');
?>
