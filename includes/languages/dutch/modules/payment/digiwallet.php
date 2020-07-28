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
define('MODULE_PAYMENT_DIGIWALLET_TEXT_DESCRIPTION', 'iDeal via Digiwallet is het online betalingssysteem in Nederland: snel, veilig en eenvoudig.<br/><a href="https://www.digiwallet.nl" target="_blank">Vraag hier uw gratis Digiwallet account aan</a>');
define('MODULE_PAYMENT_DIGIWALLET_TESTMODE_WARNING_MESSAGE', '<br/><br/><b>Note:</b> You can enable test-mode for your outlet from your DigiWallet Organization Dashboard to test your payments through the DigiWallet Test Panel.<br/><br/>');


define('MODULE_PAYMENT_DIGIWALLET_TEXT_ISSUER_SELECTION', 'Kies uw bank...');
define('MODULE_PAYMENT_DIGIWALLET_TEXT_ISSUER_SELECTION_SEPERATOR', '---Overige banken---');
define('MODULE_PAYMENT_DIGIWALLET_TEXT_ORDERED_PRODUCTS', 'Bestelling: ');
define('MODULE_PAYMENT_DIGIWALLET_TEXT_INFO', '&nbsp;<span>Betaal veilig en snel via het betaalvenster van uw eigen bank.</span>');

define('MODULE_PAYMENT_DIGIWALLET_EXPRESS_TEXT', '<h3 id="iDealExpressText">Klik a.u.b. na betaling bij uw bank op "Volgende" zodat u terugkeert op onze site en uw order direct verwerkt kan worden!</h3>');

define('MODULE_PAYMENT_DIGIWALLET_ERROR_TEXT_ERROR_OCCURRED_PROCESSING', 'Er is een fout opgetreden bij het verwerken van uw iDEAL transactie. Kies opnieuw een betaalwijze.');
define('MODULE_PAYMENT_DIGIWALLET_ERROR_TEXT_ERROR_OCCURRED_STATUS_REQUEST', 'Er is een fout opgetreden bij het ophalen van de status van uw iDEAL transactie. Controleer of de transactie is uitgevoerd via internetbankieren van uw bank en neem vervolgens contact op met de webwinkel.');
define('MODULE_PAYMENT_DIGIWALLET_ERROR_TEXT_NO_ISSUER_SELECTED', 'Er is geen bank geselecteerd, kies een bank of een andere betaalwijze.');
define('MODULE_PAYMENT_DIGIWALLET_ERROR_TEXT_TRANSACTION_CANCELLED', 'De transactie werd geannuleerd, kies opnieuw een betaalwijze.');
define('MODULE_PAYMENT_DIGIWALLET_ERROR_TEXT_TRANSACTION_EXPIRED', 'De transactie is verlopen, kies opnieuw een betaalwijze.');

define('MODULE_PAYMENT_DIGIWALLET_ERROR_TEXT_TRANSACTION_INCOMPLETE', 'Door een storing is de status van uw betaling onbekend. Indien u nog niet heeft betaald dan verzoeken wij u nu opnieuw een betaalwijze te kiezen. Heeft u reeds heeft betaald dan zal uw bestelling spoedig door ons worden voltooid.');
define('MODULE_PAYMENT_DIGIWALLET_ERROR_TEXT_TRASACTION_FAILED', '[Transactie mislukt]&nbsp;' . MODULE_PAYMENT_DIGIWALLET_ERROR_TEXT_TRANSACTION_INCOMPLETE);
define('MODULE_PAYMENT_DIGIWALLET_ERROR_TEXT_TRANSACTION_OPEN', '[Transactie open]&nbsp;' . MODULE_PAYMENT_DIGIWALLET_ERROR_TEXT_TRANSACTION_INCOMPLETE);
define('MODULE_PAYMENT_DIGIWALLET_ERROR_TEXT_UNKNOWN_STATUS', '[Transactiestatus onbekend]&nbsp;' . MODULE_PAYMENT_DIGIWALLET_ERROR_TEXT_TRANSACTION_INCOMPLETE);
define('MODULE_PAYMENT_DIGIWALLET_ERROR_AMOUNT_TO_LOW', 'Het bedrag is te laag voor deze betaalmethode');
define('MODULE_PAYMENT_DIGIWALLET_ERROR_TEXT_NO_TRANSACTION_ID', 'Er werd geen transactienummer gevonden.');

// IDE ========================================================================
define('MODULE_PAYMENT_DIGIWALLET_IDE_TEXT_TITLE', 'Digiwallet - iDEAL');
define('MODULE_PAYMENT_DIGIWALLET_IDE_TEXT_PUBLIC_TITLE', 'iDEAL');
define('MODULE_PAYMENT_DIGIWALLET_IDE_TEXT_DESCRIPTION', 'iDeal via Digiwallet is het online betalingssysteem in Nederland: snel, veilig en eenvoudig.<br/><a href="https://www.digiwallet.nl" target="_blank">Vraag hier uw gratis Digiwallet account aan</a>');

define('MODULE_PAYMENT_DIGIWALLET_IDE_TEXT_ISSUER_SELECTION', 'Kies uw bank...');
define('MODULE_PAYMENT_DIGIWALLET_IDE_TEXT_ISSUER_SELECTION_SEPERATOR', '---Overige banken---');
define('MODULE_PAYMENT_DIGIWALLET_IDE_TEXT_ORDERED_PRODUCTS', 'Bestelling: ');
define('MODULE_PAYMENT_DIGIWALLET_IDE_TEXT_INFO', '&nbsp;<span>Betaal veilig en snel via het betaalvenster van uw eigen bank.</span>');

define('MODULE_PAYMENT_DIGIWALLET_IDE_EXPRESS_TEXT', '<h3 id="iDealExpressText">Klik a.u.b. na betaling bij uw bank op "Volgende" zodat u terugkeert op onze site en uw order direct verwerkt kan worden!</h3>');

define('MODULE_PAYMENT_DIGIWALLET_IDE_ERROR_TEXT_ERROR_OCCURRED_PROCESSING', 'Er is een fout opgetreden bij het verwerken van uw iDEAL transactie. Kies opnieuw een betaalwijze.');
define('MODULE_PAYMENT_DIGIWALLET_IDE_ERROR_TEXT_ERROR_OCCURRED_STATUS_REQUEST', 'Er is een fout opgetreden bij het ophalen van de status van uw iDEAL transactie. Controleer of de transactie is uitgevoerd via internetbankieren van uw bank en neem vervolgens contact op met de webwinkel.');
define('MODULE_PAYMENT_DIGIWALLET_IDE_ERROR_TEXT_NO_ISSUER_SELECTED', 'Er is geen bank geselecteerd, kies een bank of een andere betaalwijze.');
define('MODULE_PAYMENT_DIGIWALLET_IDE_ERROR_TEXT_TRANSACTION_CANCELLED', 'De transactie werd geannuleerd, kies opnieuw een betaalwijze.');
define('MODULE_PAYMENT_DIGIWALLET_IDE_ERROR_TEXT_TRANSACTION_EXPIRED', 'De transactie is verlopen, kies opnieuw een betaalwijze.');

define('MODULE_PAYMENT_DIGIWALLET_IDE_ERROR_TEXT_TRANSACTION_INCOMPLETE', 'Door een storing is de status van uw betaling onbekend. Indien u nog niet heeft betaald dan verzoeken wij u nu opnieuw een betaalwijze te kiezen. Heeft u reeds heeft betaald dan zal uw bestelling spoedig door ons worden voltooid.');
define('MODULE_PAYMENT_DIGIWALLET_IDE_ERROR_TEXT_TRASACTION_FAILED', '[Transactie mislukt]&nbsp;' . MODULE_PAYMENT_DIGIWALLET_IDE_ERROR_TEXT_TRANSACTION_INCOMPLETE);
define('MODULE_PAYMENT_DIGIWALLET_IDE_ERROR_TEXT_TRANSACTION_OPEN', '[Transactie open]&nbsp;' . MODULE_PAYMENT_DIGIWALLET_IDE_ERROR_TEXT_TRANSACTION_INCOMPLETE);
define('MODULE_PAYMENT_DIGIWALLET_IDE_ERROR_TEXT_UNKNOWN_STATUS', '[Transactiestatus onbekend]&nbsp;' . MODULE_PAYMENT_DIGIWALLET_IDE_ERROR_TEXT_TRANSACTION_INCOMPLETE);
define('MODULE_PAYMENT_DIGIWALLET_IDE_ERROR_AMOUNT_TO_LOW', 'Het bedrag is te laag voor deze betaalmethode');
define('MODULE_PAYMENT_DIGIWALLET_IDE_ERROR_TEXT_NO_TRANSACTION_ID', 'Er werd geen transactienummer gevonden.');

// CC =======================================================================
define('MODULE_PAYMENT_DIGIWALLET_CC_TEXT_TITLE', 'Digiwallet - Visa/Mastercard');
define('MODULE_PAYMENT_DIGIWALLET_CC_TEXT_PUBLIC_TITLE', 'Visa/Mastercard');
define('MODULE_PAYMENT_DIGIWALLET_CC_TEXT_DESCRIPTION', 'Visa/Mastercard via Digiwallet is het online betalingssysteem in Nederland: snel, veilig en eenvoudig.<br/><a href="https://www.digiwallet.nl" target="_blank">Vraag hier uw gratis Digiwallet account aan</a>');

define('MODULE_PAYMENT_DIGIWALLET_CC_TEXT_ISSUER_SELECTION', 'Kies uw bank...');
define('MODULE_PAYMENT_DIGIWALLET_CC_TEXT_ISSUER_SELECTION_SEPERATOR', '---Overige banken---');
define('MODULE_PAYMENT_DIGIWALLET_CC_TEXT_ORDERED_PRODUCTS', 'Bestelling: ');
define('MODULE_PAYMENT_DIGIWALLET_CC_TEXT_INFO', '&nbsp;<span>Betaal veilig en snel via het betaalvenster van uw eigen bank.</span>');

define('MODULE_PAYMENT_DIGIWALLET_CC_EXPRESS_TEXT', '<h3 id="iDealExpressText">Klik a.u.b. na betaling bij uw bank op "Volgende" zodat u terugkeert op onze site en uw order direct verwerkt kan worden!</h3>');

define('MODULE_PAYMENT_DIGIWALLET_CC_ERROR_TEXT_ERROR_OCCURRED_PROCESSING', 'Er is een fout opgetreden bij het verwerken van uw Visa/Mastercard transactie. Kies opnieuw een betaalwijze.');
define('MODULE_PAYMENT_DIGIWALLET_CC_ERROR_TEXT_ERROR_OCCURRED_STATUS_REQUEST', 'Er is een fout opgetreden bij het ophalen van de status van uw Visa/Mastercard transactie. Controleer of de transactie is uitgevoerd via internetbankieren van uw bank en neem vervolgens contact op met de webwinkel.');
define('MODULE_PAYMENT_DIGIWALLET_CC_ERROR_TEXT_NO_ISSUER_SELECTED', 'Er is geen bank geselecteerd, kies een bank of een andere betaalwijze.');
define('MODULE_PAYMENT_DIGIWALLET_CC_ERROR_TEXT_TRANSACTION_CANCELLED', 'De transactie werd geannuleerd, kies opnieuw een betaalwijze.');
define('MODULE_PAYMENT_DIGIWALLET_CC_ERROR_TEXT_TRANSACTION_EXPIRED', 'De transactie is verlopen, kies opnieuw een betaalwijze.');

define('MODULE_PAYMENT_DIGIWALLET_CC_ERROR_TEXT_TRANSACTION_INCOMPLETE', 'Door een storing is de status van uw betaling onbekend. Indien u nog niet heeft betaald dan verzoeken wij u nu opnieuw een betaalwijze te kiezen. Heeft u reeds heeft betaald dan zal uw bestelling spoedig door ons worden voltooid.');
define('MODULE_PAYMENT_DIGIWALLET_CC_ERROR_TEXT_TRASACTION_FAILED', '[Transactie mislukt]&nbsp;' . MODULE_PAYMENT_DIGIWALLET_CC_ERROR_TEXT_TRANSACTION_INCOMPLETE);
define('MODULE_PAYMENT_DIGIWALLET_CC_ERROR_TEXT_TRANSACTION_OPEN', '[Transactie open]&nbsp;' . MODULE_PAYMENT_DIGIWALLET_CC_ERROR_TEXT_TRANSACTION_INCOMPLETE);
define('MODULE_PAYMENT_DIGIWALLET_CC_ERROR_TEXT_UNKNOWN_STATUS', '[Transactiestatus onbekend]&nbsp;' . MODULE_PAYMENT_DIGIWALLET_CC_ERROR_TEXT_TRANSACTION_INCOMPLETE);
define('MODULE_PAYMENT_DIGIWALLET_CC_ERROR_AMOUNT_TO_LOW', 'Het bedrag is te laag voor deze betaalmethode');
define('MODULE_PAYMENT_DIGIWALLET_CC_ERROR_TEXT_NO_TRANSACTION_ID', 'Er werd geen transactienummer gevonden.');

// WAL ====================================================================
define('MODULE_PAYMENT_DIGIWALLET_WAL_TEXT_TITLE', 'Digiwallet - PaysafeCard');
define('MODULE_PAYMENT_DIGIWALLET_WAL_TEXT_PUBLIC_TITLE', 'PaysafeCard');
define('MODULE_PAYMENT_DIGIWALLET_WAL_TEXT_DESCRIPTION', 'PaysafeCard via Digiwallet is het online betalingssysteem in Nederland: snel, veilig en eenvoudig.<br/><a href="https://www.digiwallet.nl" target="_blank">Vraag hier uw gratis Digiwallet account aan</a>');

define('MODULE_PAYMENT_DIGIWALLET_WAL_TEXT_ISSUER_SELECTION', 'Kies uw bank...');
define('MODULE_PAYMENT_DIGIWALLET_WAL_TEXT_ISSUER_SELECTION_SEPERATOR', '---Overige banken---');
define('MODULE_PAYMENT_DIGIWALLET_WAL_TEXT_ORDERED_PRODUCTS', 'Bestelling: ');
define('MODULE_PAYMENT_DIGIWALLET_WAL_TEXT_INFO', '&nbsp;<span>Betaal veilig en snel via het betaalvenster van uw eigen bank.</span>');

define('MODULE_PAYMENT_DIGIWALLET_WAL_EXPRESS_TEXT', '<h3 id="iDealExpressText">Klik a.u.b. na betaling bij uw bank op "Volgende" zodat u terugkeert op onze site en uw order direct verwerkt kan worden!</h3>');

define('MODULE_PAYMENT_DIGIWALLET_WAL_ERROR_TEXT_ERROR_OCCURRED_PROCESSING', 'Er is een fout opgetreden bij het verwerken van uw PaysafeCard transactie. Kies opnieuw een betaalwijze.');
define('MODULE_PAYMENT_DIGIWALLET_WAL_ERROR_TEXT_ERROR_OCCURRED_STATUS_REQUEST', 'Er is een fout opgetreden bij het ophalen van de status van uw PaysafeCard transactie. Controleer of de transactie is uitgevoerd via internetbankieren van uw bank en neem vervolgens contact op met de webwinkel.');
define('MODULE_PAYMENT_DIGIWALLET_WAL_ERROR_TEXT_NO_ISSUER_SELECTED', 'Er is geen bank geselecteerd, kies een bank of een andere betaalwijze.');
define('MODULE_PAYMENT_DIGIWALLET_WAL_ERROR_TEXT_TRANSACTION_CANCELLED', 'De transactie werd geannuleerd, kies opnieuw een betaalwijze.');
define('MODULE_PAYMENT_DIGIWALLET_WAL_ERROR_TEXT_TRANSACTION_EXPIRED', 'De transactie is verlopen, kies opnieuw een betaalwijze.');

define('MODULE_PAYMENT_DIGIWALLET_WAL_ERROR_TEXT_TRANSACTION_INCOMPLETE', 'Door een storing is de status van uw betaling onbekend. Indien u nog niet heeft betaald dan verzoeken wij u nu opnieuw een betaalwijze te kiezen. Heeft u reeds heeft betaald dan zal uw bestelling spoedig door ons worden voltooid.');
define('MODULE_PAYMENT_DIGIWALLET_WAL_ERROR_TEXT_TRASACTION_FAILED', '[Transactie mislukt]&nbsp;' . MODULE_PAYMENT_DIGIWALLET_WAL_ERROR_TEXT_TRANSACTION_INCOMPLETE);
define('MODULE_PAYMENT_DIGIWALLET_WAL_ERROR_TEXT_TRANSACTION_OPEN', '[Transactie open]&nbsp;' . MODULE_PAYMENT_DIGIWALLET_WAL_ERROR_TEXT_TRANSACTION_INCOMPLETE);
define('MODULE_PAYMENT_DIGIWALLET_WAL_ERROR_TEXT_UNKNOWN_STATUS', '[Transactiestatus onbekend]&nbsp;' . MODULE_PAYMENT_DIGIWALLET_WAL_ERROR_TEXT_TRANSACTION_INCOMPLETE);
define('MODULE_PAYMENT_DIGIWALLET_WAL_ERROR_AMOUNT_TO_LOW', 'Het bedrag is te laag voor deze betaalmethode');
define('MODULE_PAYMENT_DIGIWALLET_WAL_ERROR_TEXT_NO_TRANSACTION_ID', 'Er werd geen transactienummer gevonden.');

// DEB ======================================================================
define('MODULE_PAYMENT_DIGIWALLET_DEB_TEXT_TITLE', 'Digiwallet - Sofort');
define('MODULE_PAYMENT_DIGIWALLET_DEB_TEXT_PUBLIC_TITLE', 'Sofort');
define('MODULE_PAYMENT_DIGIWALLET_DEB_TEXT_DESCRIPTION', 'Sofort via Digiwallet is het online betalingssysteem in Nederland: snel, veilig en eenvoudig.<br/><a href="https://www.digiwallet.nl" target="_blank">Vraag hier uw gratis Digiwallet account aan</a>');

define('MODULE_PAYMENT_DIGIWALLET_DEB_TEXT_ISSUER_SELECTION', 'Kies uw land...');
define('MODULE_PAYMENT_DIGIWALLET_DEB_TEXT_ISSUER_SELECTION_SEPERATOR', '---Overige banken---');
define('MODULE_PAYMENT_DIGIWALLET_DEB_TEXT_ORDERED_PRODUCTS', 'Bestelling: ');
define('MODULE_PAYMENT_DIGIWALLET_DEB_TEXT_INFO', '&nbsp;<span>Betaal veilig en snel via het betaalvenster van uw eigen bank.</span>');

define('MODULE_PAYMENT_DIGIWALLET_DEB_EXPRESS_TEXT', '<h3 id="iDealExpressText">Klik a.u.b. na betaling bij uw bank op "Volgende" zodat u terugkeert op onze site en uw order direct verwerkt kan worden!</h3>');

define('MODULE_PAYMENT_DIGIWALLET_DEB_ERROR_TEXT_ERROR_OCCURRED_PROCESSING', 'Er is een fout opgetreden bij het verwerken van uw Sofort transactie. Kies opnieuw een betaalwijze.');
define('MODULE_PAYMENT_DIGIWALLET_DEB_ERROR_TEXT_ERROR_OCCURRED_STATUS_REQUEST', 'Er is een fout opgetreden bij het ophalen van de status van uw Sofort transactie. Controleer of de transactie is uitgevoerd via internetbankieren van uw bank en neem vervolgens contact op met de webwinkel.');
define('MODULE_PAYMENT_DIGIWALLET_DEB_ERROR_TEXT_NO_ISSUER_SELECTED', 'Er is geen bank geselecteerd, kies een bank of een andere betaalwijze.');
define('MODULE_PAYMENT_DIGIWALLET_DEB_ERROR_TEXT_TRANSACTION_CANCELLED', 'De transactie werd geannuleerd, kies opnieuw een betaalwijze.');
define('MODULE_PAYMENT_DIGIWALLET_DEB_ERROR_TEXT_TRANSACTION_EXPIRED', 'De transactie is verlopen, kies opnieuw een betaalwijze.');

define('MODULE_PAYMENT_DIGIWALLET_DEB_ERROR_TEXT_TRANSACTION_INCOMPLETE', 'Door een storing is de status van uw betaling onbekend. Indien u nog niet heeft betaald dan verzoeken wij u nu opnieuw een betaalwijze te kiezen. Heeft u reeds heeft betaald dan zal uw bestelling spoedig door ons worden voltooid.');
define('MODULE_PAYMENT_DIGIWALLET_DEB_ERROR_TEXT_TRASACTION_FAILED', '[Transactie mislukt]&nbsp;' . MODULE_PAYMENT_DIGIWALLET_DEB_ERROR_TEXT_TRANSACTION_INCOMPLETE);
define('MODULE_PAYMENT_DIGIWALLET_DEB_ERROR_TEXT_TRANSACTION_OPEN', '[Transactie open]&nbsp;' . MODULE_PAYMENT_DIGIWALLET_DEB_ERROR_TEXT_TRANSACTION_INCOMPLETE);
define('MODULE_PAYMENT_DIGIWALLET_DEB_ERROR_TEXT_UNKNOWN_STATUS', '[Transactiestatus onbekend]&nbsp;' . MODULE_PAYMENT_DIGIWALLET_DEB_ERROR_TEXT_TRANSACTION_INCOMPLETE);
define('MODULE_PAYMENT_DIGIWALLET_DEB_ERROR_AMOUNT_TO_LOW', 'Het bedrag is te laag voor deze betaalmethode');
define('MODULE_PAYMENT_DIGIWALLET_DEB_ERROR_TEXT_NO_TRANSACTION_ID', 'Er werd geen transactienummer gevonden.');

// MRC =======================================================================
define('MODULE_PAYMENT_DIGIWALLET_MRC_TEXT_TITLE', 'Digiwallet - Bancontact');
define('MODULE_PAYMENT_DIGIWALLET_MRC_TEXT_PUBLIC_TITLE', 'Bancontact');
define('MODULE_PAYMENT_DIGIWALLET_MRC_TEXT_DESCRIPTION', 'Bancontact via Digiwallet is het online betalingssysteem in Nederland: snel, veilig en eenvoudig.<br/><a href="https://www.digiwallet.nl" target="_blank">Vraag hier uw gratis Digiwallet account aan</a>');

define('MODULE_PAYMENT_DIGIWALLET_MRC_TEXT_ISSUER_SELECTION', 'Kies uw bank...');
define('MODULE_PAYMENT_DIGIWALLET_MRC_TEXT_ISSUER_SELECTION_SEPERATOR', '---Overige banken---');
define('MODULE_PAYMENT_DIGIWALLET_MRC_TEXT_ORDERED_PRODUCTS', 'Bestelling: ');
define('MODULE_PAYMENT_DIGIWALLET_MRC_TEXT_INFO', '&nbsp;<span>Betaal veilig en snel via het betaalvenster van uw eigen bank.</span>');

define('MODULE_PAYMENT_DIGIWALLET_MRC_EXPRESS_TEXT', '<h3 id="iDealExpressText">Klik a.u.b. na betaling bij uw bank op "Volgende" zodat u terugkeert op onze site en uw order direct verwerkt kan worden!</h3>');

define('MODULE_PAYMENT_DIGIWALLET_MRC_ERROR_TEXT_ERROR_OCCURRED_PROCESSING', 'Er is een fout opgetreden bij het verwerken van uw Bancontact transactie. Kies opnieuw een betaalwijze.');
define('MODULE_PAYMENT_DIGIWALLET_MRC_ERROR_TEXT_ERROR_OCCURRED_STATUS_REQUEST', 'Er is een fout opgetreden bij het ophalen van de status van uw Bancontact transactie. Controleer of de transactie is uitgevoerd via internetbankieren van uw bank en neem vervolgens contact op met de webwinkel.');
define('MODULE_PAYMENT_DIGIWALLET_MRC_ERROR_TEXT_NO_ISSUER_SELECTED', 'Er is geen bank geselecteerd, kies een bank of een andere betaalwijze.');
define('MODULE_PAYMENT_DIGIWALLET_MRC_ERROR_TEXT_TRANSACTION_CANCELLED', 'De transactie werd geannuleerd, kies opnieuw een betaalwijze.');
define('MODULE_PAYMENT_DIGIWALLET_MRC_ERROR_TEXT_TRANSACTION_EXPIRED', 'De transactie is verlopen, kies opnieuw een betaalwijze.');

define('MODULE_PAYMENT_DIGIWALLET_MRC_ERROR_TEXT_TRANSACTION_INCOMPLETE', 'Door een storing is de status van uw betaling onbekend. Indien u nog niet heeft betaald dan verzoeken wij u nu opnieuw een betaalwijze te kiezen. Heeft u reeds heeft betaald dan zal uw bestelling spoedig door ons worden voltooid.');
define('MODULE_PAYMENT_DIGIWALLET_MRC_ERROR_TEXT_TRASACTION_FAILED', '[Transactie mislukt]&nbsp;' . MODULE_PAYMENT_DIGIWALLET_MRC_ERROR_TEXT_TRANSACTION_INCOMPLETE);
define('MODULE_PAYMENT_DIGIWALLET_MRC_ERROR_TEXT_TRANSACTION_OPEN', '[Transactie open]&nbsp;' . MODULE_PAYMENT_DIGIWALLET_MRC_ERROR_TEXT_TRANSACTION_INCOMPLETE);
define('MODULE_PAYMENT_DIGIWALLET_MRC_ERROR_TEXT_UNKNOWN_STATUS', '[Transactiestatus onbekend]&nbsp;' . MODULE_PAYMENT_DIGIWALLET_MRC_ERROR_TEXT_TRANSACTION_INCOMPLETE);
define('MODULE_PAYMENT_DIGIWALLET_MRC_ERROR_AMOUNT_TO_LOW', 'Het bedrag is te laag voor deze betaalmethode');
define('MODULE_PAYMENT_DIGIWALLET_MRC_ERROR_TEXT_NO_TRANSACTION_ID', 'Er werd geen transactienummer gevonden.');


// AFP =======================================================================
define('MODULE_PAYMENT_DIGIWALLET_AFP_TEXT_TITLE', 'Digiwallet - Afterpay');
define('MODULE_PAYMENT_DIGIWALLET_AFP_TEXT_PUBLIC_TITLE', 'Afterpay');
define('MODULE_PAYMENT_DIGIWALLET_AFP_TEXT_DESCRIPTION', 'Afterpay via Digiwallet is het online betalingssysteem in Nederland: snel, veilig en eenvoudig.<br/><a href="https://www.digiwallet.nl" target="_blank">Vraag hier uw gratis Digiwallet account aan</a>');

define('MODULE_PAYMENT_DIGIWALLET_AFP_TEXT_ISSUER_SELECTION', 'Kies uw bank...');
define('MODULE_PAYMENT_DIGIWALLET_AFP_TEXT_ISSUER_SELECTION_SEPERATOR', '---Overige banken---');
define('MODULE_PAYMENT_DIGIWALLET_AFP_TEXT_ORDERED_PRODUCTS', 'Bestelling: ');
define('MODULE_PAYMENT_DIGIWALLET_AFP_TEXT_INFO', '&nbsp;<span>Betaal veilig en snel via het betaalvenster van uw eigen bank.</span>');

define('MODULE_PAYMENT_DIGIWALLET_AFP_EXPRESS_TEXT', '<h3 id="iDealExpressText">Klik a.u.b. na betaling bij uw bank op "Volgende" zodat u terugkeert op onze site en uw order direct verwerkt kan worden!</h3>');

define('MODULE_PAYMENT_DIGIWALLET_AFP_ERROR_TEXT_ERROR_OCCURRED_PROCESSING', 'Er is een fout opgetreden bij het verwerken van uw Afterpay transactie.');
define('MODULE_PAYMENT_DIGIWALLET_AFP_ERROR_TEXT_ERROR_OCCURRED_STATUS_REQUEST', 'Er is een fout opgetreden bij het ophalen van de status van uw Afterpay transactie. Controleer of de transactie is uitgevoerd via internetbankieren van uw bank en neem vervolgens contact op met de webwinkel.');
define('MODULE_PAYMENT_DIGIWALLET_AFP_ERROR_TEXT_NO_ISSUER_SELECTED', 'Er is geen bank geselecteerd, kies een bank of een andere betaalwijze.');
define('MODULE_PAYMENT_DIGIWALLET_AFP_ERROR_TEXT_TRANSACTION_CANCELLED', 'De transactie werd geannuleerd, kies opnieuw een betaalwijze.');
define('MODULE_PAYMENT_DIGIWALLET_AFP_ERROR_TEXT_TRANSACTION_EXPIRED', 'De transactie is verlopen, kies opnieuw een betaalwijze.');

define('MODULE_PAYMENT_DIGIWALLET_AFP_ERROR_TEXT_TRANSACTION_INCOMPLETE', 'Door een storing is de status van uw betaling onbekend. Indien u nog niet heeft betaald dan verzoeken wij u nu opnieuw een betaalwijze te kiezen. Heeft u reeds heeft betaald dan zal uw bestelling spoedig door ons worden voltooid.');
define('MODULE_PAYMENT_DIGIWALLET_AFP_ERROR_TEXT_TRASACTION_FAILED', '[Transactie mislukt]&nbsp;' . MODULE_PAYMENT_DIGIWALLET_AFP_ERROR_TEXT_TRANSACTION_INCOMPLETE);
define('MODULE_PAYMENT_DIGIWALLET_AFP_ERROR_TEXT_TRANSACTION_OPEN', '[Transactie open]&nbsp;' . MODULE_PAYMENT_DIGIWALLET_AFP_ERROR_TEXT_TRANSACTION_INCOMPLETE);
define('MODULE_PAYMENT_DIGIWALLET_AFP_ERROR_TEXT_UNKNOWN_STATUS', '[Transactiestatus onbekend]&nbsp;' . MODULE_PAYMENT_DIGIWALLET_AFP_ERROR_TEXT_TRANSACTION_INCOMPLETE);
define('MODULE_PAYMENT_DIGIWALLET_AFP_ERROR_AMOUNT_TO_LOW', 'Het bedrag is te laag voor deze betaalmethode');
define('MODULE_PAYMENT_DIGIWALLET_AFP_ERROR_TEXT_NO_TRANSACTION_ID', 'Er werd geen transactienummer gevonden.');


// BW =======================================================================
define('MODULE_PAYMENT_DIGIWALLET_BW_TEXT_TITLE', 'Digiwallet - Bankwire');
define('MODULE_PAYMENT_DIGIWALLET_BW_TEXT_PUBLIC_TITLE', 'Bankwire');
define('MODULE_PAYMENT_DIGIWALLET_BW_TEXT_DESCRIPTION', 'Overschrijvingen via Digiwallet is het online betalingssysteem in Nederland: snel, veilig en eenvoudig.<br/><a href="https://www.digiwallet.nl" target="_blank">Vraag hier uw gratis Digiwallet account aan</a>');

define('MODULE_PAYMENT_DIGIWALLET_BW_TEXT_ISSUER_SELECTION', 'Kies uw bank...');
define('MODULE_PAYMENT_DIGIWALLET_BW_TEXT_ISSUER_SELECTION_SEPERATOR', '---Overige banken---');
define('MODULE_PAYMENT_DIGIWALLET_BW_TEXT_ORDERED_PRODUCTS', 'Bestelling: ');
define('MODULE_PAYMENT_DIGIWALLET_BW_TEXT_INFO', '&nbsp;<span>Betaal veilig en snel via het betaalvenster van uw eigen bank.</span>');

define('MODULE_PAYMENT_DIGIWALLET_BW_EXPRESS_TEXT', '<h3 id="iDealExpressText">Klik a.u.b. na betaling bij uw bank op "Volgende" zodat u terugkeert op onze site en uw order direct verwerkt kan worden!</h3>');

define('MODULE_PAYMENT_DIGIWALLET_BW_ERROR_TEXT_ERROR_OCCURRED_PROCESSING', 'Er is een fout opgetreden bij het verwerken van uw Overschrijvingen transactie. Kies opnieuw een betaalwijze.');
define('MODULE_PAYMENT_DIGIWALLET_BW_ERROR_TEXT_ERROR_OCCURRED_STATUS_REQUEST', 'Er is een fout opgetreden bij het ophalen van de status van uw Overschrijvingen transactie. Controleer of de transactie is uitgevoerd via internetbankieren van uw bank en neem vervolgens contact op met de webwinkel.');
define('MODULE_PAYMENT_DIGIWALLET_BW_ERROR_TEXT_NO_ISSUER_SELECTED', 'Er is geen bank geselecteerd, kies een bank of een andere betaalwijze.');
define('MODULE_PAYMENT_DIGIWALLET_BW_ERROR_TEXT_TRANSACTION_CANCELLED', 'De transactie werd geannuleerd, kies opnieuw een betaalwijze.');
define('MODULE_PAYMENT_DIGIWALLET_BW_ERROR_TEXT_TRANSACTION_EXPIRED', 'De transactie is verlopen, kies opnieuw een betaalwijze.');

define('MODULE_PAYMENT_DIGIWALLET_BW_ERROR_TEXT_TRANSACTION_INCOMPLETE', 'Door een storing is de status van uw betaling onbekend. Indien u nog niet heeft betaald dan verzoeken wij u nu opnieuw een betaalwijze te kiezen. Heeft u reeds heeft betaald dan zal uw bestelling spoedig door ons worden voltooid.');
define('MODULE_PAYMENT_DIGIWALLET_BW_ERROR_TEXT_TRASACTION_FAILED', '[Transactie mislukt]&nbsp;' . MODULE_PAYMENT_DIGIWALLET_BW_ERROR_TEXT_TRANSACTION_INCOMPLETE);
define('MODULE_PAYMENT_DIGIWALLET_BW_ERROR_TEXT_TRANSACTION_OPEN', '[Transactie open]&nbsp;' . MODULE_PAYMENT_DIGIWALLET_BW_ERROR_TEXT_TRANSACTION_INCOMPLETE);
define('MODULE_PAYMENT_DIGIWALLET_BW_ERROR_TEXT_UNKNOWN_STATUS', '[Transactiestatus onbekend]&nbsp;' . MODULE_PAYMENT_DIGIWALLET_BW_ERROR_TEXT_TRANSACTION_INCOMPLETE);
define('MODULE_PAYMENT_DIGIWALLET_BW_ERROR_AMOUNT_TO_LOW', 'Het bedrag is te laag voor deze betaalmethode');
define('MODULE_PAYMENT_DIGIWALLET_BW_ERROR_TEXT_NO_TRANSACTION_ID', 'Er werd geen transactienummer gevonden.');


// PYP =======================================================================
define('MODULE_PAYMENT_DIGIWALLET_PYP_TEXT_TITLE', 'Digiwallet - PayPal');
define('MODULE_PAYMENT_DIGIWALLET_PYP_TEXT_PUBLIC_TITLE', 'PayPal');
define('MODULE_PAYMENT_DIGIWALLET_PYP_TEXT_DESCRIPTION', 'PayPal via Digiwallet is het online betalingssysteem in Nederland: snel, veilig en eenvoudig.<br/><a href="https://www.digiwallet.nl" target="_blank">Vraag hier uw gratis Digiwallet account aan</a>');

define('MODULE_PAYMENT_DIGIWALLET_PYP_TEXT_ISSUER_SELECTION', 'Kies uw bank...');
define('MODULE_PAYMENT_DIGIWALLET_PYP_TEXT_ISSUER_SELECTION_SEPERATOR', '---Overige banken---');
define('MODULE_PAYMENT_DIGIWALLET_PYP_TEXT_ORDERED_PRODUCTS', 'Bestelling: ');
define('MODULE_PAYMENT_DIGIWALLET_PYP_TEXT_INFO', '&nbsp;<span>Betaal veilig en snel via het betaalvenster van uw eigen bank.</span>');

define('MODULE_PAYMENT_DIGIWALLET_PYP_EXPRESS_TEXT', '<h3 id="iDealExpressText">Klik a.u.b. na betaling bij uw bank op "Volgende" zodat u terugkeert op onze site en uw order direct verwerkt kan worden!</h3>');

define('MODULE_PAYMENT_DIGIWALLET_PYP_ERROR_TEXT_ERROR_OCCURRED_PROCESSING', 'Er is een fout opgetreden bij het verwerken van uw PayPal transactie. Kies opnieuw een betaalwijze.');
define('MODULE_PAYMENT_DIGIWALLET_PYP_ERROR_TEXT_ERROR_OCCURRED_STATUS_REQUEST', 'Er is een fout opgetreden bij het ophalen van de status van uw PayPal transactie. Controleer of de transactie is uitgevoerd via internetbankieren van uw bank en neem vervolgens contact op met de webwinkel.');
define('MODULE_PAYMENT_DIGIWALLET_PYP_ERROR_TEXT_NO_ISSUER_SELECTED', 'Er is geen bank geselecteerd, kies een bank of een andere betaalwijze.');
define('MODULE_PAYMENT_DIGIWALLET_PYP_ERROR_TEXT_TRANSACTION_CANCELLED', 'De transactie werd geannuleerd, kies opnieuw een betaalwijze.');
define('MODULE_PAYMENT_DIGIWALLET_PYP_ERROR_TEXT_TRANSACTION_EXPIRED', 'De transactie is verlopen, kies opnieuw een betaalwijze.');

define('MODULE_PAYMENT_DIGIWALLET_PYP_ERROR_TEXT_TRANSACTION_INCOMPLETE', 'Door een storing is de status van uw betaling onbekend. Indien u nog niet heeft betaald dan verzoeken wij u nu opnieuw een betaalwijze te kiezen. Heeft u reeds heeft betaald dan zal uw bestelling spoedig door ons worden voltooid.');
define('MODULE_PAYMENT_DIGIWALLET_PYP_ERROR_TEXT_TRASACTION_FAILED', '[Transactie mislukt]&nbsp;' . MODULE_PAYMENT_DIGIWALLET_PYP_ERROR_TEXT_TRANSACTION_INCOMPLETE);
define('MODULE_PAYMENT_DIGIWALLET_PYP_ERROR_TEXT_TRANSACTION_OPEN', '[Transactie open]&nbsp;' . MODULE_PAYMENT_DIGIWALLET_PYP_ERROR_TEXT_TRANSACTION_INCOMPLETE);
define('MODULE_PAYMENT_DIGIWALLET_PYP_ERROR_TEXT_UNKNOWN_STATUS', '[Transactiestatus onbekend]&nbsp;' . MODULE_PAYMENT_DIGIWALLET_PYP_ERROR_TEXT_TRANSACTION_INCOMPLETE);
define('MODULE_PAYMENT_DIGIWALLET_PYP_ERROR_AMOUNT_TO_LOW', 'Het bedrag is te laag voor deze betaalmethode');
define('MODULE_PAYMENT_DIGIWALLET_PYP_ERROR_TEXT_NO_TRANSACTION_ID', 'Er werd geen transactienummer gevonden.');


// Bankwire sucess
define('MODULE_PAYMENT_DIGIWALLET_BANKWIRE_THANKYOU_FINISHED', 'Uw bestelling is verwerkt!');
define('MODULE_PAYMENT_DIGIWALLET_BANKWIRE_THANKYOU_PAGE',
    <<<HTML
<h2>Bedankt voor uw bestelling in onze webwinkel!</h2>
<div class="bankwire-info">
    <p>
        U ontvangt uw bestelling zodra we de betaling per bank ontvangen hebben. <br>
        Zou u zo vriendelijk willen zijn het totaalbedrag van %s over te maken op bankrekening <b>
		%s </b> t.n.v. %s* ?
    </p>
    <p>
        Vermeld daarbij als betaalkenmerk <b>%s</b>, zodat de betaling automatisch verwerkt kan worden.
        Zodra dit gebeurd is ontvangt u een mail op %s ter bevestiging.
    </p>
    <p>
        Mocht het nodig zijn voor betalingen vanuit het buitenland, dan is de BIC code van de bank %s en de naam van de bank is '%s'.
        Zorg ervoor dat u kiest voor kosten in het buitenland voor eigen rekening (optie: OUR), anders zal het bedrag wat binnenkomt te laag zijn.
    <p>
        <i>* De betalingen voor onze webwinkel worden verwerkt door TargetMedia. TargetMedia is gecertificeerd als Collecting Payment Service Provider door Currence.
        Dat houdt in dat zij aan strenge eisen dient te voldoen als het gaat om de veiligheid van de betalingen voor jou als klant en ons als webwinkel.</i>
    </p>
</div>
HTML
);