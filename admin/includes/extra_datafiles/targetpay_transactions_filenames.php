<?php
/**
 Digiwallet module class for Zencart
 (C) Copyright Yellow Melon B.V. 2013
 */
defined('DB_PREFIX') or define('DB_PREFIX', '');

define('FILENAME_TARGETPAY_TRANSACTIONS', 'targetpay_transactions.php');

define('TABLE_TARGETPAY_TRANSACTIONS', DB_PREFIX . 'digiwallet_transactions');
define('TABLE_TARGETPAY_DIRECTORY', DB_PREFIX . 'digiwallet_directory');
