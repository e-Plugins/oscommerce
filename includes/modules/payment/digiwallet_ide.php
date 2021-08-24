<?php
/**
 * Digiwallet Payment Module for osCommerce
 *
 * @copyright Copyright 2013-2014 Yellow Melon
 * @copyright Portions Copyright 2013 Paul Mathot
 * @copyright Portions Copyright 2003 osCommerce
 * @license   see LICENSE.TXT
 */
$ywincludefile = realpath(dirname(__FILE__) . '/digiwallet/digiwalletpayment.class.php');
require_once $ywincludefile;

class digiwallet_ide extends digiwalletpayment
{

    /**
     *
     * @method digiwallet inits the module
     */
    public function digiwallet_ide()
    {
        $this->sort_order = 1;
        $this->config_code = "IDE";
        parent::digiwalletpayment();
    }

    /**
     * make bank selection field
     */
    public function selection()
    {
        $directory = $this->getDirectory();
        if (! is_null($directory)) {
            $issuers = array();
            $issuerType = "Short";

            $issuers[] = array(
                'id' => "-1",
                'text' => $this->getConstant("MODULE_PAYMENT_DIGIWALLET_" . $this->config_code . "_TEXT_ISSUER_SELECTION")
            );

            foreach ($directory as $issuer) {
                if ($issuer->issuerList != $issuerType) {
                    $issuerType = $issuer->issuerList;
                }
                $issuers[] = array(
                    'id' => $issuer->issuerID,
                    'text' => $issuer->issuerName
                );
            }

            $selection = array(
                'id' => $this->code,
                'module' => $this->payment_icon, // $this->title . " ".$this->getConstant("MODULE_PAYMENT_DIGIWALLET_".$this->config_code."_TEXT_INFO")
                'fields' => array(
                    array(
                        'title' => $this->getConstant("MODULE_PAYMENT_DIGIWALLET_".$this->config_code."_TEXT_ISSUER_SELECTION"),
                        'field' => tep_draw_pull_down_menu('bankID', $issuers, '', 'onChange="$(\'input[type=radio][name=payment][value=' . $this->code . ']\').prop(\'checked\', true);"')
                    )
                ),
                'issuers' => $issuers
            );
            return $selection;
        }
    }

    /**
     * make hidden value for payment system
     */
    public function process_button()
    {
        global $messageStack;
        if ($_POST["payment"] == 'digiwallet_ide' && (! isset($_POST['bankID']) || ($_POST['bankID'] < 0))) {
            $messageStack->add_session('checkout_payment', $this->getConstant("MODULE_PAYMENT_DIGIWALLET_" . $this->config_code . "_ERROR_TEXT_NO_ISSUER_SELECTED"));

            $url = tep_href_link(FILENAME_CHECKOUT_PAYMENT, 'error_message=' . urlencode($this->getConstant("MODULE_PAYMENT_DIGIWALLET_" . $this->config_code . "_ERROR_TEXT_NO_ISSUER_SELECTED")), 'SSL', true, false);
            echo '<script> location.replace("'.$url.'"); </script>';
            exit();
        }

        $process_button = tep_draw_hidden_field('bankID', $_POST['bankID']) . $this->getConstant("MODULE_PAYMENT_DIGIWALLET_" . $this->config_code . "_EXPRESS_TEXT");

        if (defined('BUTTON_CHECKOUT_DIGIWALLET_ALT')) {
            $process_button .= tep_image_submit('digiwallet.gif', BUTTON_CHECKOUT_DIGIWALLET_ALT);
        }
        return $process_button;
    }
}
