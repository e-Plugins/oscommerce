<?php
/**
 * Digiwallet Payment Module for osCommerce
 *
 * @copyright Copyright 2013-2014 Yellow Melon
 * @copyright Portions Copyright 2013 Paul Mathot
 * @copyright Portions Copyright 2003 osCommerce
 * @license   see LICENSE.TXT
 */
$ywincludefile = realpath(dirname(__FILE__) . '/digiwallet/digiwallet.client.php');
require_once $ywincludefile;

class digiwallet_eps extends digiwalletClient
{
    /**
     *
     * @method digiwallet inits the module
     */
    public function digiwallet_eps()
    {
        $this->config_code = "EPS";
        parent::digiwalletpayment();
    }
}
