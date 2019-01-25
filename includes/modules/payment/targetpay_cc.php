<?php
/**
 * Digiwallet Payment Module for osCommerce
 *
 * @copyright Copyright 2013-2014 Yellow Melon
 * @copyright Portions Copyright 2013 Paul Mathot
 * @copyright Portions Copyright 2003 osCommerce
 * @license   see LICENSE.TXT
 */
$ywincludefile = realpath(dirname(__FILE__) . '/targetpay/targetpayment.class.php');
require_once $ywincludefile;

class targetpay_cc extends targetpayment
{

    /**
     *
     * @method targetpay inits the module
     */
    public function targetpay_cc()
    {
        $this->config_code = "CC";
        parent::targetpayment();
    }
}
