<?php
/**
 Digiwallet module class for Zencart
 (C) Copyright Yellow Melon B.V. 2013
 */
define('DIGIWALLET_TABLE_HEADING_TRANSACTION_DETAILS', 'Transactiebijzonderheden');
define('DIGIWALLET_TABLE_HEADING_TRANSACTION_ID', 'Transactie ID');
define('DIGIWALLET_TABLE_HEADING_PURCHASE_ID', 'Aankoop ID');
define('DIGIWALLET_TABLE_HEADING_ORDER_ID', 'Bestel ID');
define('DIGIWALLET_TABLE_HEADING_TRANSACTION_STATUS', 'Transactiestatus');
define('DIGIWALLET_TABLE_HEADING_ORDER_STATUS', 'Bestelstatus');
define('DIGIWALLET_TABLE_HEADING_TRANSACTION_DATE_TIME', 'Transactie tijdstip');
define('DIGIWALLET_TABLE_HEADING_LAST_MODIFIED', 'Laatst bewerkt');
define('DIGIWALLET_TABLE_HEADING_SUGGESTED_ACTION', 'Aanbevolen handeling');

define('DIGIWALLET_TEXT_SEARCH_TRANSACTION_ID', 'Transactie ID: ');
define('DIGIWALLET_TEXT_SEARCH_ORDER_ID', 'Bestel ID: ');
define('DIGIWALLET_TEXT_FILTER', 'Filter');
define('DIGIWALLET_TEXT_FILTER_ALL', 'Alle');
define('DIGIWALLET_TEXT_FILTER_INCOMPLETE', 'Onafgeronde');
define('DIGIWALLET_TEXT_FILTER_COMPLETE', 'Afgeronde');
define('DIGIWALLET_TEXT_FILTER_CLEANUP', 'Opruimen');
define('DIGIWALLET_TEXT_RESTOCK_QUANTITY', 'Producten restocken tijdens het opruimen?');

define('DIGIWALLET_TEXT_DETAILS', 'Details');
define('DIGIWALLET_NAME_ZERO_STATUS_ORDER', 'Nul-status bestelling');
define('DIGIWALLET_TEXT_CHECK_STATUS', 'Status peilen');
define('DIGIWALLET_TEXT_REFUND', 'Transactie annuleren');
define('DIGIWALLET_TEXT_CHANGE_STATUS', 'Bestelstatus wijzigen');
define('DIGIWALLET_TEXT_NO_ACTION', '[Geen handeling]');

define('DIGIWALLET_TEXT_CREATE_ORDER', 'Maak bestelling aan');

define('DIGIWALLET_MESSAGE_SUCCESS_REFUND', 'Transactiestatus is op "refunded" gezet.');
define('DIGIWALLET_MESSAGE_WARNING_REFUND', 'Het was niet mogelijk om de transactiestatus op "refunded" te zetten.');
define('DIGIWALLET_MESSAGE_SUCCESS_STATUS', 'Transactiestatus opgehaald.');
define('DIGIWALLET_MESSAGE_WARNING_STATUS', 'Transactiestatus kon niet opgehaald worden.');

define('DIGIWALLET_HEADING_CUSTOMER_ID', 'Klant ID');
define('DIGIWALLET_HEADING_TRANSACTION_AMOUNT', 'Transactiebedrag');
define('DIGIWALLET_HEADING_CUSTOMER_ACCOUNT', 'Bankrekening klant');
define('DIGIWALLET_HEADING_CUSTOMER_NAME', 'Naam klant');
define('DIGIWALLET_HEADING_CUSTOMER_CITY', 'Stad klant');

define('DIGIWALLET_HEADING_EXPLANATION', 'Uitleg');
define('DIGIWALLET_TEXT_EXPLANATION', 'Bovenstaande tabel geeft een overzicht van de Digiwallet iDeal transacties 
                in het systeem. Aanvankelijk zijn alleen de onafgeronde transacties 
                vertoond; dit ziet u aan het bolletje voor ' . DIGIWALLET_TEXT_FILTER_INCOMPLETE . ' naast het knopje ' . DIGIWALLET_TEXT_FILTER . '. 
                U kunt daar ook kiezen om ' . DIGIWALLET_TEXT_FILTER_COMPLETE . ' of ' . DIGIWALLET_TEXT_FILTER_ALL . ' transacties te laten vertonen. 
                ' . DIGIWALLET_TEXT_FILTER_COMPLETE . ' transacties zijn transacties die afgerond zijn. ' . DIGIWALLET_TEXT_FILTER_INCOMPLETE . ' 
                transacties zijn transacties die nog verder bewerkt moeten worden. 
                ');
