<?php
/**
 * Digiwallet Payment Module for osCommerce
 *
 * @copyright Copyright 2013-2014 Yellow Melon
 * @copyright Portions Copyright 2013 Paul Mathot
 * @copyright Portions Copyright 2003 osCommerce
 * @license   see LICENSE.TXT
 */
header("Location: index.php?main_page=checkout_process&trxid=" . $_GET['trxid'] . "&method=" . $_GET["method"] . "&action=process");
