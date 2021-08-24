<?php
/**
 * Digiwallet Payment Module for osCommerce
 *
 * @copyright Copyright 2013-2014 Yellow Melon
 * @copyright Portions Copyright 2013 Paul Mathot
 * @copyright Portions Copyright 2003 osCommerce
 * @license see LICENSE.TXT
 */

// DEFAULT
define('MODULE_PAYMENT_DIGIWALLET_TEXT_TITLE', 'Digiwallet - iDEAL');
define('MODULE_PAYMENT_DIGIWALLET_TEXT_PUBLIC_TITLE', 'iDEAL');
define('MODULE_PAYMENT_DIGIWALLET_TEXT_DESCRIPTION', 'iDEAL via Digiwallet is the payment method via the Dutch banks: fast, safe, and simple.<br/><a href="https://www.digiwallet.nl" target="_blank">Get a Digiwallet account for free</a>');
define('MODULE_PAYMENT_DIGIWALLET_TESTMODE_WARNING_MESSAGE', '<br/><br/><b>Note:</b> If you have a question or need any help, please visit https://www.digiwallet.com<br/><br/>');

define('MODULE_PAYMENT_DIGIWALLET_TEXT_ISSUER_SELECTION', 'Choose your bank...');
define('MODULE_PAYMENT_DIGIWALLET_TEXT_ISSUER_SELECTION_SEPERATOR', '---Other banks---');
define('MODULE_PAYMENT_DIGIWALLET_TEXT_ORDERED_PRODUCTS', 'Order: ');
define('MODULE_PAYMENT_DIGIWALLET_TEXT_INFO', 'Safe online payment via the Dutch banks.');

define('MODULE_PAYMENT_IDEAL_EXPRESS_TEXT', '<h3 id="iDealExpressText">Klik a.u.b. na betaling bij uw bank op "Volgende" zodat u terugkeert op onze site en uw order direct verwerkt kan worden!</h3>');

define('MODULE_PAYMENT_DIGIWALLET_ERROR_TEXT_ERROR_OCCURRED_PROCESSING', 'An error occurred while processing your iDEAL transaction. Please select a payment method.');
define('MODULE_PAYMENT_DIGIWALLET_ERROR_TEXT_ERROR_OCCURRED_STATUS_REQUEST', 'An error occurred while confirming the status of your iDEAL transaction. Please check whether the transaction has been completed via your online banking system and then contact the web store.');
define('MODULE_PAYMENT_DIGIWALLET_ERROR_TEXT_NO_ISSUER_SELECTED', 'No bank was selected; please select a bank or another payment method.');
define('MODULE_PAYMENT_DIGIWALLET_ERROR_TEXT_TRANSACTION_CANCELLED', 'The transaction was cancelled; please select a payment method.');
define('MODULE_PAYMENT_DIGIWALLET_ERROR_TEXT_TRANSACTION_EXPIRED', 'The transaction has expired; please select a payment method.');
define('MODULE_PAYMENT_DIGIWALLET_ERROR_TEXT_TRASACTION_FAILED', 'The transaction failed; please select a payment method.');
define('MODULE_PAYMENT_DIGIWALLET_ERROR_TEXT_UNKNOWN_STATUS', 'The transaction failed; please select a payment method.');
define('MODULE_PAYMENT_DIGIWALLET_ERROR_AMOUNT_TO_LOW', 'The amount is too low for this paymetn type');
define('MODULE_PAYMENT_DIGIWALLET_ERROR_TEXT_NO_TRANSACTION_ID', 'No transaction ID was found.');

// IDE ==========================================================================================
define('MODULE_PAYMENT_DIGIWALLET_IDE_TEXT_TITLE', 'Digiwallet - iDEAL');
define('MODULE_PAYMENT_DIGIWALLET_IDE_TEXT_PUBLIC_TITLE', 'iDEAL');
define('MODULE_PAYMENT_DIGIWALLET_IDE_TEXT_DESCRIPTION', 'iDEAL via Digiwallet is the payment method via the Dutch banks: fast, safe, and simple.<br/><a href="https://www.digiwallet.nl" target="_blank">Get a Digiwallet account for free</a>');

define('MODULE_PAYMENT_DIGIWALLET_IDE_TEXT_ISSUER_SELECTION', 'Choose your bank...');
define('MODULE_PAYMENT_DIGIWALLET_IDE_TEXT_ISSUER_SELECTION_SEPERATOR', '---Other banks---');
define('MODULE_PAYMENT_DIGIWALLET_IDE_TEXT_ORDERED_PRODUCTS', 'Order: ');
define('MODULE_PAYMENT_DIGIWALLET_IDE_TEXT_INFO', 'Safe online payment via the Dutch banks.');

define('MODULE_PAYMENT_IDEAL_EXPRESS_TEXT', '<h3 id="iDealExpressText">Klik a.u.b. na betaling bij uw bank op "Volgende" zodat u terugkeert op onze site en uw order direct verwerkt kan worden!</h3>');

define('MODULE_PAYMENT_DIGIWALLET_IDE_ERROR_TEXT_ERROR_OCCURRED_PROCESSING', 'An error occurred while processing your iDEAL transaction. Please select a payment method.');
define('MODULE_PAYMENT_DIGIWALLET_IDE_ERROR_TEXT_ERROR_OCCURRED_STATUS_REQUEST', 'An error occurred while confirming the status of your iDEAL transaction. Please check whether the transaction has been completed via your online banking system and then contact the web store.');
define('MODULE_PAYMENT_DIGIWALLET_IDE_ERROR_TEXT_NO_ISSUER_SELECTED', 'No bank was selected; please select a bank or another payment method.');
define('MODULE_PAYMENT_DIGIWALLET_IDE_ERROR_TEXT_TRANSACTION_CANCELLED', 'The transaction was cancelled; please select a payment method.');
define('MODULE_PAYMENT_DIGIWALLET_IDE_ERROR_TEXT_TRANSACTION_EXPIRED', 'The transaction has expired; please select a payment method.');
define('MODULE_PAYMENT_DIGIWALLET_IDE_ERROR_TEXT_TRASACTION_FAILED', 'The transaction failed; please select a payment method.');
define('MODULE_PAYMENT_DIGIWALLET_IDE_ERROR_TEXT_UNKNOWN_STATUS', 'The transaction failed; please select a payment method.');
define('MODULE_PAYMENT_DIGIWALLET_IDE_ERROR_AMOUNT_TO_LOW', 'The amount is too low for this paymetn type');
define('MODULE_PAYMENT_DIGIWALLET_IDE_ERROR_TEXT_NO_TRANSACTION_ID', 'No transaction ID was found.');

// CC =========================================================================================
define('MODULE_PAYMENT_DIGIWALLET_CC_TEXT_TITLE', 'Digiwallet - Visa/Mastercard');
define('MODULE_PAYMENT_DIGIWALLET_CC_TEXT_PUBLIC_TITLE', 'Visa/Mastercard');
define('MODULE_PAYMENT_DIGIWALLET_CC_TEXT_DESCRIPTION', 'Visa/Mastercard via Digiwallet is the payment method via the Dutch banks: fast, safe, and simple.<br/><a href="https://www.digiwallet.nl" target="_blank">Get a Digiwallet account for free</a>');

define('MODULE_PAYMENT_DIGIWALLET_CC_TEXT_ISSUER_SELECTION', 'Choose your bank...');
define('MODULE_PAYMENT_DIGIWALLET_CC_TEXT_ISSUER_SELECTION_SEPERATOR', '---Other banks---');
define('MODULE_PAYMENT_DIGIWALLET_CC_TEXT_ORDERED_PRODUCTS', 'Order: ');
define('MODULE_PAYMENT_DIGIWALLET_CC_TEXT_INFO', 'Safe online payment via the Dutch banks.');

define('MODULE_PAYMENT_IDEAL_EXPRESS_TEXT', '<h3 id="iDealExpressText">Klik a.u.b. na betaling bij uw bank op "Volgende" zodat u terugkeert op onze site en uw order direct verwerkt kan worden!</h3>');

define('MODULE_PAYMENT_DIGIWALLET_CC_ERROR_TEXT_ERROR_OCCURRED_PROCESSING', 'An error occurred while processing your transaction. Please select a payment method.');
define('MODULE_PAYMENT_DIGIWALLET_CC_ERROR_TEXT_ERROR_OCCURRED_STATUS_REQUEST', 'An error occurred while confirming the status of your Visa/Mastercard transaction. Please check whether the transaction has been completed via your online banking system and then contact the web store.');
define('MODULE_PAYMENT_DIGIWALLET_CC_ERROR_TEXT_NO_ISSUER_SELECTED', 'No bank was selected; please select a bank or another payment method.');
define('MODULE_PAYMENT_DIGIWALLET_CC_ERROR_TEXT_TRANSACTION_CANCELLED', 'The transaction was cancelled; please select a payment method.');
define('MODULE_PAYMENT_DIGIWALLET_CC_ERROR_TEXT_TRANSACTION_EXPIRED', 'The transaction has expired; please select a payment method.');
define('MODULE_PAYMENT_DIGIWALLET_CC_ERROR_TEXT_TRASACTION_FAILED', 'The transaction failed; please select a payment method.');
define('MODULE_PAYMENT_DIGIWALLET_CC_ERROR_TEXT_UNKNOWN_STATUS', 'The transaction failed; please select a payment method.');
define('MODULE_PAYMENT_DIGIWALLET_CC_ERROR_AMOUNT_TO_LOW', 'The amount is too low for this paymetn type');
define('MODULE_PAYMENT_DIGIWALLET_CC_ERROR_TEXT_NO_TRANSACTION_ID', 'No transaction ID was found.');

// WAL =========================================================================================
define('MODULE_PAYMENT_DIGIWALLET_WAL_TEXT_TITLE', 'Digiwallet - PaysafeCard');
define('MODULE_PAYMENT_DIGIWALLET_WAL_TEXT_PUBLIC_TITLE', 'PaysafeCard');
define('MODULE_PAYMENT_DIGIWALLET_WAL_TEXT_DESCRIPTION', 'PaysafeCard via Digiwallet is the payment method via the Dutch banks: fast, safe, and simple.<br/><a href="https://www.digiwallet.nl" target="_blank">Get a Digiwallet account for free</a>');

define('MODULE_PAYMENT_DIGIWALLET_WAL_TEXT_ISSUER_SELECTION', 'Choose your bank...');
define('MODULE_PAYMENT_DIGIWALLET_WAL_TEXT_ISSUER_SELECTION_SEPERATOR', '---Other banks---');
define('MODULE_PAYMENT_DIGIWALLET_WAL_TEXT_ORDERED_PRODUCTS', 'Order: ');
define('MODULE_PAYMENT_DIGIWALLET_WAL_TEXT_INFO', 'Safe online payment via the Dutch banks.');

define('MODULE_PAYMENT_IDEAL_EXPRESS_TEXT', '<h3 id="iDealExpressText">Klik a.u.b. na betaling bij uw bank op "Volgende" zodat u terugkeert op onze site en uw order direct verwerkt kan worden!</h3>');

define('MODULE_PAYMENT_DIGIWALLET_WAL_ERROR_TEXT_ERROR_OCCURRED_PROCESSING', 'An error occurred while processing your PaysafeCard transaction. Please select a payment method.');
define('MODULE_PAYMENT_DIGIWALLET_WAL_ERROR_TEXT_ERROR_OCCURRED_STATUS_REQUEST', 'An error occurred while confirming the status of your PaysafeCard transaction. Please check whether the transaction has been completed via your online banking system and then contact the web store.');
define('MODULE_PAYMENT_DIGIWALLET_WAL_ERROR_TEXT_NO_ISSUER_SELECTED', 'No bank was selected; please select a bank or another payment method.');
define('MODULE_PAYMENT_DIGIWALLET_WAL_ERROR_TEXT_TRANSACTION_CANCELLED', 'The transaction was cancelled; please select a payment method.');
define('MODULE_PAYMENT_DIGIWALLET_WAL_ERROR_TEXT_TRANSACTION_EXPIRED', 'The transaction has expired; please select a payment method.');
define('MODULE_PAYMENT_DIGIWALLET_WAL_ERROR_TEXT_TRASACTION_FAILED', 'The transaction failed; please select a payment method.');
define('MODULE_PAYMENT_DIGIWALLET_WAL_ERROR_TEXT_UNKNOWN_STATUS', 'The transaction failed; please select a payment method.');
define('MODULE_PAYMENT_DIGIWALLET_WAL_ERROR_AMOUNT_TO_LOW', 'The amount is too low for this paymetn type');
define('MODULE_PAYMENT_DIGIWALLET_WAL_ERROR_TEXT_NO_TRANSACTION_ID', 'No transaction ID was found.');

// DEB ==========================================================================================
define('MODULE_PAYMENT_DIGIWALLET_DEB_TEXT_TITLE', 'Digiwallet - Sofort');
define('MODULE_PAYMENT_DIGIWALLET_DEB_TEXT_PUBLIC_TITLE', 'Sofort');
define('MODULE_PAYMENT_DIGIWALLET_DEB_TEXT_DESCRIPTION', 'Sofort via Digiwallet is the payment method via the Dutch banks: fast, safe, and simple.<br/><a href="https://www.digiwallet.nl" target="_blank">Get a Digiwallet account for free</a>');

define('MODULE_PAYMENT_DIGIWALLET_DEB_TEXT_ISSUER_SELECTION', 'Choose your country...');
define('MODULE_PAYMENT_DIGIWALLET_DEB_TEXT_ISSUER_SELECTION_SEPERATOR', '---Other banks---');
define('MODULE_PAYMENT_DIGIWALLET_DEB_TEXT_ORDERED_PRODUCTS', 'Order: ');
define('MODULE_PAYMENT_DIGIWALLET_DEB_TEXT_INFO', 'Safe online payment via the Dutch banks.');

define('MODULE_PAYMENT_IDEAL_EXPRESS_TEXT', '<h3 id="iDealExpressText">Klik a.u.b. na betaling bij uw bank op "Volgende" zodat u terugkeert op onze site en uw order direct verwerkt kan worden!</h3>');

define('MODULE_PAYMENT_DIGIWALLET_DEB_ERROR_TEXT_ERROR_OCCURRED_PROCESSING', 'An error occurred while processing your Sofort transaction. Please select a payment method.');
define('MODULE_PAYMENT_DIGIWALLET_DEB_ERROR_TEXT_ERROR_OCCURRED_STATUS_REQUEST', 'An error occurred while confirming the status of your iDEAL transaction. Please check whether the transaction has been completed via your online banking system and then contact the web store.');
define('MODULE_PAYMENT_DIGIWALLET_DEB_ERROR_TEXT_NO_ISSUER_SELECTED', 'No bank was selected; please select a bank or another payment method.');
define('MODULE_PAYMENT_DIGIWALLET_DEB_ERROR_TEXT_TRANSACTION_CANCELLED', 'The transaction was cancelled; please select a payment method.');
define('MODULE_PAYMENT_DIGIWALLET_DEB_ERROR_TEXT_TRANSACTION_EXPIRED', 'The transaction has expired; please select a payment method.');
define('MODULE_PAYMENT_DIGIWALLET_DEB_ERROR_TEXT_TRASACTION_FAILED', 'The transaction failed; please select a payment method.');
define('MODULE_PAYMENT_DIGIWALLET_DEB_ERROR_TEXT_UNKNOWN_STATUS', 'The transaction failed; please select a payment method.');
define('MODULE_PAYMENT_DIGIWALLET_DEB_ERROR_AMOUNT_TO_LOW', 'The amount is too low for this paymetn type');
define('MODULE_PAYMENT_DIGIWALLET_DEB_ERROR_TEXT_NO_TRANSACTION_ID', 'No transaction ID was found.');

// MRC ==========================================================================================
define('MODULE_PAYMENT_DIGIWALLET_MRC_TEXT_TITLE', 'Digiwallet - Bancontact');
define('MODULE_PAYMENT_DIGIWALLET_MRC_TEXT_PUBLIC_TITLE', 'Bancontact');
define('MODULE_PAYMENT_DIGIWALLET_MRC_TEXT_DESCRIPTION', 'Bancontact via Digiwallet is the payment method via the Dutch banks: fast, safe, and simple.<br/><a href="https://www.digiwallet.nl" target="_blank">Get a Digiwallet account for free</a>');

define('MODULE_PAYMENT_DIGIWALLET_MRC_TEXT_ISSUER_SELECTION', 'Choose your bank...');
define('MODULE_PAYMENT_DIGIWALLET_MRC_TEXT_ISSUER_SELECTION_SEPERATOR', '---Other banks---');
define('MODULE_PAYMENT_DIGIWALLET_MRC_TEXT_ORDERED_PRODUCTS', 'Order: ');
define('MODULE_PAYMENT_DIGIWALLET_MRC_TEXT_INFO', 'Safe online payment via the Dutch banks.');

define('MODULE_PAYMENT_IDEAL_EXPRESS_TEXT', '<h3 id="iDealExpressText">Klik a.u.b. na betaling bij uw bank op "Volgende" zodat u terugkeert op onze site en uw order direct verwerkt kan worden!</h3>');

define('MODULE_PAYMENT_DIGIWALLET_MRC_ERROR_TEXT_ERROR_OCCURRED_PROCESSING', 'An error occurred while processing your Bancontact transaction. Please select a payment method.');
define('MODULE_PAYMENT_DIGIWALLET_MRC_ERROR_TEXT_ERROR_OCCURRED_STATUS_REQUEST', 'An error occurred while confirming the status of your Bancontact transaction. Please check whether the transaction has been completed via your online banking system and then contact the web store.');
define('MODULE_PAYMENT_DIGIWALLET_MRC_ERROR_TEXT_NO_ISSUER_SELECTED', 'No bank was selected; please select a bank or another payment method.');
define('MODULE_PAYMENT_DIGIWALLET_MRC_ERROR_TEXT_TRANSACTION_CANCELLED', 'The transaction was cancelled; please select a payment method.');
define('MODULE_PAYMENT_DIGIWALLET_MRC_ERROR_TEXT_TRANSACTION_EXPIRED', 'The transaction has expired; please select a payment method.');
define('MODULE_PAYMENT_DIGIWALLET_MRC_ERROR_TEXT_TRASACTION_FAILED', 'The transaction failed; please select a payment method.');
define('MODULE_PAYMENT_DIGIWALLET_MRC_ERROR_TEXT_UNKNOWN_STATUS', 'The transaction failed; please select a payment method.');
define('MODULE_PAYMENT_DIGIWALLET_MRC_ERROR_AMOUNT_TO_LOW', 'The amount is too low for this paymetn type');
define('MODULE_PAYMENT_DIGIWALLET_MRC_ERROR_TEXT_NO_TRANSACTION_ID', 'No transaction ID was found.');


// AFP ==========================================================================================
define('MODULE_PAYMENT_DIGIWALLET_AFP_TEXT_TITLE', 'Digiwallet - Afterpay');
define('MODULE_PAYMENT_DIGIWALLET_AFP_TEXT_PUBLIC_TITLE', 'Afterpay');
define('MODULE_PAYMENT_DIGIWALLET_AFP_TEXT_DESCRIPTION', 'Afterpay via Digiwallet is the payment method via the Dutch banks: fast, safe, and simple.<br/><a href="https://www.digiwallet.nl" target="_blank">Get a Digiwallet account for free</a>');

define('MODULE_PAYMENT_DIGIWALLET_AFP_TEXT_ISSUER_SELECTION', 'Choose your bank...');
define('MODULE_PAYMENT_DIGIWALLET_AFP_TEXT_ISSUER_SELECTION_SEPERATOR', '---Other banks---');
define('MODULE_PAYMENT_DIGIWALLET_AFP_TEXT_ORDERED_PRODUCTS', 'Order: ');
define('MODULE_PAYMENT_DIGIWALLET_AFP_TEXT_INFO', 'Safe online payment via the Dutch banks.');

define('MODULE_PAYMENT_IDEAL_EXPRESS_TEXT', '<h3 id="iDealExpressText">Klik a.u.b. na betaling bij uw bank op "Volgende" zodat u terugkeert op onze site en uw order direct verwerkt kan worden!</h3>');

define('MODULE_PAYMENT_DIGIWALLET_AFP_ERROR_TEXT_ERROR_OCCURRED_PROCESSING', 'An error occurred while processing your Afterpay transaction.');
define('MODULE_PAYMENT_DIGIWALLET_AFP_ERROR_TEXT_ERROR_OCCURRED_STATUS_REQUEST', 'An error occurred while confirming the status of your Afterpay transaction. Please check whether the transaction has been completed via your online banking system and then contact the web store.');
define('MODULE_PAYMENT_DIGIWALLET_AFP_ERROR_TEXT_NO_ISSUER_SELECTED', 'No bank was selected; please select a bank or another payment method.');
define('MODULE_PAYMENT_DIGIWALLET_AFP_ERROR_TEXT_TRANSACTION_CANCELLED', 'The transaction was cancelled; please select a payment method.');
define('MODULE_PAYMENT_DIGIWALLET_AFP_ERROR_TEXT_TRANSACTION_EXPIRED', 'The transaction has expired; please select a payment method.');
define('MODULE_PAYMENT_DIGIWALLET_AFP_ERROR_TEXT_TRASACTION_FAILED', 'The transaction failed; please select a payment method.');
define('MODULE_PAYMENT_DIGIWALLET_AFP_ERROR_TEXT_UNKNOWN_STATUS', 'The transaction failed; please select a payment method.');
define('MODULE_PAYMENT_DIGIWALLET_AFP_ERROR_AMOUNT_TO_LOW', 'The amount is too low for this paymetn type');
define('MODULE_PAYMENT_DIGIWALLET_AFP_ERROR_TEXT_NO_TRANSACTION_ID', 'No transaction ID was found.');


// BW ==========================================================================================
define('MODULE_PAYMENT_DIGIWALLET_BW_TEXT_TITLE', 'Digiwallet - Bankwire - Overschrijving');
define('MODULE_PAYMENT_DIGIWALLET_BW_TEXT_PUBLIC_TITLE', 'Bankwire - Overschrijving');
define('MODULE_PAYMENT_DIGIWALLET_BW_TEXT_DESCRIPTION', 'Overschrijvingen via Digiwallet is the payment method via the Dutch banks: fast, safe, and simple.<br/><a href="https://www.digiwallet.nl" target="_blank">Get a Digiwallet account for free</a>');

define('MODULE_PAYMENT_DIGIWALLET_BW_TEXT_ISSUER_SELECTION', 'Choose your bank...');
define('MODULE_PAYMENT_DIGIWALLET_BW_TEXT_ISSUER_SELECTION_SEPERATOR', '---Other banks---');
define('MODULE_PAYMENT_DIGIWALLET_BW_TEXT_ORDERED_PRODUCTS', 'Order: ');
define('MODULE_PAYMENT_DIGIWALLET_BW_TEXT_INFO', 'Safe online payment via the Dutch banks.');

define('MODULE_PAYMENT_IDEAL_EXPRESS_TEXT', '<h3 id="iDealExpressText">Klik a.u.b. na betaling bij uw bank op "Volgende" zodat u terugkeert op onze site en uw order direct verwerkt kan worden!</h3>');

define('MODULE_PAYMENT_DIGIWALLET_BW_ERROR_TEXT_ERROR_OCCURRED_PROCESSING', 'An error occurred while processing your Overschrijvingen transaction. Please select a payment method.');
define('MODULE_PAYMENT_DIGIWALLET_BW_ERROR_TEXT_ERROR_OCCURRED_STATUS_REQUEST', 'An error occurred while confirming the status of your Overschrijvingen transaction. Please check whether the transaction has been completed via your online banking system and then contact the web store.');
define('MODULE_PAYMENT_DIGIWALLET_BW_ERROR_TEXT_NO_ISSUER_SELECTED', 'No bank was selected; please select a bank or another payment method.');
define('MODULE_PAYMENT_DIGIWALLET_BW_ERROR_TEXT_TRANSACTION_CANCELLED', 'The transaction was cancelled; please select a payment method.');
define('MODULE_PAYMENT_DIGIWALLET_BW_ERROR_TEXT_TRANSACTION_EXPIRED', 'The transaction has expired; please select a payment method.');
define('MODULE_PAYMENT_DIGIWALLET_BW_ERROR_TEXT_TRASACTION_FAILED', 'The transaction failed; please select a payment method.');
define('MODULE_PAYMENT_DIGIWALLET_BW_ERROR_TEXT_UNKNOWN_STATUS', 'The transaction failed; please select a payment method.');
define('MODULE_PAYMENT_DIGIWALLET_BW_ERROR_AMOUNT_TO_LOW', 'The amount is too low for this paymetn type');
define('MODULE_PAYMENT_DIGIWALLET_BW_ERROR_TEXT_NO_TRANSACTION_ID', 'No transaction ID was found.');



// EPS ==========================================================================================
define('MODULE_PAYMENT_DIGIWALLET_EPS_TEXT_TITLE', 'Digiwallet - EPS');
define('MODULE_PAYMENT_DIGIWALLET_EPS_TEXT_PUBLIC_TITLE', 'EPS');
define('MODULE_PAYMENT_DIGIWALLET_EPS_TEXT_DESCRIPTION', 'EPS via Digiwallet is the payment method via the Dutch banks: fast, safe, and simple.<br/><a href="https://www.digiwallet.nl" target="_blank">Get a Digiwallet account for free</a>');

define('MODULE_PAYMENT_DIGIWALLET_EPS_TEXT_ISSUER_SELECTION', 'Choose your bank...');
define('MODULE_PAYMENT_DIGIWALLET_EPS_TEXT_ISSUER_SELECTION_SEPERATOR', '---Other banks---');
define('MODULE_PAYMENT_DIGIWALLET_EPS_TEXT_ORDERED_PRODUCTS', 'Order: ');
define('MODULE_PAYMENT_DIGIWALLET_EPS_TEXT_INFO', 'Safe online payment via the Dutch banks.');

define('MODULE_PAYMENT_IDEAL_EXPRESS_TEXT', '<h3 id="iDealExpressText">Klik a.u.b. na betaling bij uw bank op "Volgende" zodat u terugkeert op onze site en uw order direct verwerkt kan worden!</h3>');

define('MODULE_PAYMENT_DIGIWALLET_EPS_ERROR_TEXT_ERROR_OCCURRED_PROCESSING', 'An error occurred while processing your EPS transaction. Please select a payment method.');
define('MODULE_PAYMENT_DIGIWALLET_EPS_ERROR_TEXT_ERROR_OCCURRED_STATUS_REQUEST', 'An error occurred while confirming the status of your EPS transaction. Please check whether the transaction has been completed via your online banking system and then contact the web store.');
define('MODULE_PAYMENT_DIGIWALLET_EPS_ERROR_TEXT_NO_ISSUER_SELECTED', 'No bank was selected; please select a bank or another payment method.');
define('MODULE_PAYMENT_DIGIWALLET_EPS_ERROR_TEXT_TRANSACTION_CANCELLED', 'The transaction was cancelled; please select a payment method.');
define('MODULE_PAYMENT_DIGIWALLET_EPS_ERROR_TEXT_TRANSACTION_EXPIRED', 'The transaction has expired; please select a payment method.');
define('MODULE_PAYMENT_DIGIWALLET_EPS_ERROR_TEXT_TRASACTION_FAILED', 'The transaction failed; please select a payment method.');
define('MODULE_PAYMENT_DIGIWALLET_EPS_ERROR_TEXT_UNKNOWN_STATUS', 'The transaction failed; please select a payment method.');
define('MODULE_PAYMENT_DIGIWALLET_EPS_ERROR_AMOUNT_TO_LOW', 'The amount is too low for this paymetn type');
define('MODULE_PAYMENT_DIGIWALLET_EPS_ERROR_TEXT_NO_TRANSACTION_ID', 'No transaction ID was found.');

// GIP ==========================================================================================
define('MODULE_PAYMENT_DIGIWALLET_GIP_TEXT_TITLE', 'Digiwallet - GiroPay');
define('MODULE_PAYMENT_DIGIWALLET_GIP_TEXT_PUBLIC_TITLE', 'GiroPay');
define('MODULE_PAYMENT_DIGIWALLET_GIP_TEXT_DESCRIPTION', 'GiroPay via Digiwallet is the payment method via the Dutch banks: fast, safe, and simple.<br/><a href="https://www.digiwallet.nl" target="_blank">Get a Digiwallet account for free</a>');

define('MODULE_PAYMENT_DIGIWALLET_GIP_TEXT_ISSUER_SELECTION', 'Choose your bank...');
define('MODULE_PAYMENT_DIGIWALLET_GIP_TEXT_ISSUER_SELECTION_SEPERATOR', '---Other banks---');
define('MODULE_PAYMENT_DIGIWALLET_GIP_TEXT_ORDERED_PRODUCTS', 'Order: ');
define('MODULE_PAYMENT_DIGIWALLET_GIP_TEXT_INFO', 'Safe online payment via the Dutch banks.');

define('MODULE_PAYMENT_IDEAL_EXPRESS_TEXT', '<h3 id="iDealExpressText">Klik a.u.b. na betaling bij uw bank op "Volgende" zodat u terugkeert op onze site en uw order direct verwerkt kan worden!</h3>');

define('MODULE_PAYMENT_DIGIWALLET_GIP_ERROR_TEXT_ERROR_OCCURRED_PROCESSING', 'An error occurred while processing your GiroPay transaction. Please select a payment method.');
define('MODULE_PAYMENT_DIGIWALLET_GIP_ERROR_TEXT_ERROR_OCCURRED_STATUS_REQUEST', 'An error occurred while confirming the status of your GiroPay transaction. Please check whether the transaction has been completed via your online banking system and then contact the web store.');
define('MODULE_PAYMENT_DIGIWALLET_GIP_ERROR_TEXT_NO_ISSUER_SELECTED', 'No bank was selected; please select a bank or another payment method.');
define('MODULE_PAYMENT_DIGIWALLET_GIP_ERROR_TEXT_TRANSACTION_CANCELLED', 'The transaction was cancelled; please select a payment method.');
define('MODULE_PAYMENT_DIGIWALLET_GIP_ERROR_TEXT_TRANSACTION_EXPIRED', 'The transaction has expired; please select a payment method.');
define('MODULE_PAYMENT_DIGIWALLET_GIP_ERROR_TEXT_TRASACTION_FAILED', 'The transaction failed; please select a payment method.');
define('MODULE_PAYMENT_DIGIWALLET_GIP_ERROR_TEXT_UNKNOWN_STATUS', 'The transaction failed; please select a payment method.');
define('MODULE_PAYMENT_DIGIWALLET_GIP_ERROR_AMOUNT_TO_LOW', 'The amount is too low for this paymetn type');
define('MODULE_PAYMENT_DIGIWALLET_GIP_ERROR_TEXT_NO_TRANSACTION_ID', 'No transaction ID was found.');


// PYP ==========================================================================================
define('MODULE_PAYMENT_DIGIWALLET_PYP_TEXT_TITLE', 'Digiwallet - PayPal');
define('MODULE_PAYMENT_DIGIWALLET_PYP_TEXT_PUBLIC_TITLE', 'PayPal');
define('MODULE_PAYMENT_DIGIWALLET_PYP_TEXT_DESCRIPTION', 'PayPal via Digiwallet is the payment method via the Dutch banks: fast, safe, and simple.<br/><a href="https://www.digiwallet.nl" target="_blank">Get a Digiwallet account for free</a>');

define('MODULE_PAYMENT_DIGIWALLET_PYP_TEXT_ISSUER_SELECTION', 'Choose your bank...');
define('MODULE_PAYMENT_DIGIWALLET_PYP_TEXT_ISSUER_SELECTION_SEPERATOR', '---Other banks---');
define('MODULE_PAYMENT_DIGIWALLET_PYP_TEXT_ORDERED_PRODUCTS', 'Order: ');
define('MODULE_PAYMENT_DIGIWALLET_PYP_TEXT_INFO', 'Safe online payment via the Dutch banks.');

define('MODULE_PAYMENT_IDEAL_EXPRESS_TEXT', '<h3 id="iDealExpressText">Klik a.u.b. na betaling bij uw bank op "Volgende" zodat u terugkeert op onze site en uw order direct verwerkt kan worden!</h3>');

define('MODULE_PAYMENT_DIGIWALLET_PYP_ERROR_TEXT_ERROR_OCCURRED_PROCESSING', 'An error occurred while processing your PayPal transaction. Please select a payment method.');
define('MODULE_PAYMENT_DIGIWALLET_PYP_ERROR_TEXT_ERROR_OCCURRED_STATUS_REQUEST', 'An error occurred while confirming the status of your PayPal transaction. Please check whether the transaction has been completed via your online banking system and then contact the web store.');
define('MODULE_PAYMENT_DIGIWALLET_PYP_ERROR_TEXT_NO_ISSUER_SELECTED', 'No bank was selected; please select a bank or another payment method.');
define('MODULE_PAYMENT_DIGIWALLET_PYP_ERROR_TEXT_TRANSACTION_CANCELLED', 'The transaction was cancelled; please select a payment method.');
define('MODULE_PAYMENT_DIGIWALLET_PYP_ERROR_TEXT_TRANSACTION_EXPIRED', 'The transaction has expired; please select a payment method.');
define('MODULE_PAYMENT_DIGIWALLET_PYP_ERROR_TEXT_TRASACTION_FAILED', 'The transaction failed; please select a payment method.');
define('MODULE_PAYMENT_DIGIWALLET_PYP_ERROR_TEXT_UNKNOWN_STATUS', 'The transaction failed; please select a payment method.');
define('MODULE_PAYMENT_DIGIWALLET_PYP_ERROR_AMOUNT_TO_LOW', 'The amount is too low for this paymetn type');
define('MODULE_PAYMENT_DIGIWALLET_PYP_ERROR_TEXT_NO_TRANSACTION_ID', 'No transaction ID was found.');


// Bankwire sucess
define('MODULE_PAYMENT_DIGIWALLET_BANKWIRE_THANKYOU_FINISHED', 'Your order has been processed!');
define('MODULE_PAYMENT_DIGIWALLET_BANKWIRE_THANKYOU_PAGE',
    <<<HTML
<h2>Thank you for ordering in our webshop!</h2>
<div class="bankwire-info">
    <p>
        You will receive your order as soon as we receive payment from the bank. <br>
        Would you be so friendly to transfer the total amount of %s  to the bankaccount <b>
		%s </b> in name of %s* ?
    </p>
    <p>
        State the payment feature <b>%s</b>, this way the payment can be automatically processed.<br>
        As soon as this happens you shall receive a confirmation mail on %s
    </p>
    <p>
        If it is necessary for payments abroad, then the BIC code from the bank %s and the name of the bank is %s.
    <p>
        <i>* Payment for our webstore is processed by TargetMedia. TargetMedia is certified as a Collecting Payment Service Provider by Currence. This means we set the highest security standards when is comes to security of payment for you as a customer and us as a webshop.</i>
    </p>
</div>
HTML
);

