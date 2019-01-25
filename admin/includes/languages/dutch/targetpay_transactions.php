<?php
/**
 Digiwallet module class for Zencart
 (C) Copyright Yellow Melon B.V. 2013
 */
define('TARGETPAY_TABLE_HEADING_TRANSACTION_DETAILS', 'Transactiebijzonderheden');
define('TARGETPAY_TABLE_HEADING_TRANSACTION_ID', 'Transactie ID');
define('TARGETPAY_TABLE_HEADING_PURCHASE_ID', 'Aankoop ID');
define('TARGETPAY_TABLE_HEADING_ORDER_ID', 'Bestel ID');
define('TARGETPAY_TABLE_HEADING_TRANSACTION_STATUS', 'Transactiestatus');
define('TARGETPAY_TABLE_HEADING_ORDER_STATUS', 'Bestelstatus');
define('TARGETPAY_TABLE_HEADING_TRANSACTION_DATE_TIME', 'Transactie tijdstip');
define('TARGETPAY_TABLE_HEADING_LAST_MODIFIED', 'Laatst bewerkt');
define('TARGETPAY_TABLE_HEADING_SUGGESTED_ACTION', 'Aanbevolen handeling');

define('TARGETPAY_TEXT_SEARCH_TRANSACTION_ID', 'Transactie ID: ');
define('TARGETPAY_TEXT_SEARCH_ORDER_ID', 'Bestel ID: ');
define('TARGETPAY_TEXT_FILTER', 'Filter');
define('TARGETPAY_TEXT_FILTER_ALL', 'Alle');
define('TARGETPAY_TEXT_FILTER_INCOMPLETE', 'Onafgeronde');
define('TARGETPAY_TEXT_FILTER_COMPLETE', 'Afgeronde');
define('TARGETPAY_TEXT_FILTER_CLEANUP', 'Opruimen');
define('TARGETPAY_TEXT_CLEANUP', '(verwijder transacties welke niet de status Success of Open hebben)');
define('TARGETPAY_TEXT_RESTOCK_QUANTITY', 'Producten restocken tijdens het opruimen?');

define('TARGETPAY_TEXT_DETAILS', 'Details');
define('TARGETPAY_NAME_ZERO_STATUS_ORDER', 'Nul-status bestelling');

define('TARGETPAY_TEXT_NO_ACTION', '[Geen handeling]');

define('TARGETPAY_TABLE_HEADING_AGE', 'Verstreken tijd');

// aanbevolen handelingen:
define('TARGETPAY_TEXT_CHECK_STATUS', 'Status peilen');
define('TARGETPAY_TEXT_CREATE_ORDER', 'Voltooi bestelling');
define('TARGETPAY_WAIT_OR_CREATE_ORDER', 'Wacht (of voltooi bestelling).');
define('TARGETPAY_TEXT_CHANGE_STATUS', 'Bestelstatus wijzigen');
define('TARGETPAY_TEXT_REFUND', 'Transactie annuleren');

define('TARGETPAY_TEXT_ARE_YOU_SURE_WAIT', 'LET OP: De klant kan de bestelling zelf nog voltooien! Het is aanbevolen te wachten. Weet u 100% zeker dat u deze bestelling nu wilt voltooien?');
define('TARGETPAY_TEXT_ARE_YOU_SURE', 'Weet u zeker dat u deze bestelling wilt voltooien?');

define('TEXT_CSV_FILENAME', 'CSV bestand');

define('TARGETPAY_MESSAGE_SUCCESS_REFUND', 'Transactiestatus is op "refunded" gezet.');
define('TARGETPAY_MESSAGE_WARNING_REFUND', 'Het was niet mogelijk om de transactiestatus op "refunded" te zetten.');
define('TARGETPAY_MESSAGE_SUCCESS_STATUS', 'Transactiestatus opgehaald.');
define('TARGETPAY_MESSAGE_WARNING_STATUS', 'Transactiestatus kon niet opgehaald worden.');

define('TARGETPAY_HEADING_CUSTOMER_ID', 'Klant ID');
define('TARGETPAY_HEADING_TRANSACTION_AMOUNT', 'Transactiebedrag');
define('TARGETPAY_HEADING_CUSTOMER_ACCOUNT', 'Bankrekening klant');
define('TARGETPAY_HEADING_CUSTOMER_NAME', 'Naam klant');
define('TARGETPAY_HEADING_CUSTOMER_CITY', 'Stad klant');

define('TARGETPAY_HEADING_EXPLANATION', 'Uitleg');
define('TARGETPAY_TEXT_EXPLANATION', 'Bovenstaande tabel geeft een overzicht van de Digiwallet iDeal transacties 
                in het systeem. Aanvankelijk worden alleen de onafgeronde transacties 
                getoond; dit ziet u aan het bolletje voor ' . TARGETPAY_TEXT_FILTER_INCOMPLETE . ' naast het knopje ' . TARGETPAY_TEXT_FILTER . '. 
                U kunt ook kiezen om alleen ' . TARGETPAY_TEXT_FILTER_COMPLETE . ' of ' . TARGETPAY_TEXT_FILTER_ALL . ' transacties te tonen. 
                ' . TARGETPAY_TEXT_FILTER_COMPLETE . ' transacties kunt u bekijken maar hebben geen bewerking nodig. ' . TARGETPAY_TEXT_FILTER_INCOMPLETE . ' 
                transacties zijn transacties die nog verder bewerkt moeten worden.
                
                ');
