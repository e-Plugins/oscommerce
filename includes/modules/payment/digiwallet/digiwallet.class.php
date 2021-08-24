<?php

/**
 * @file     Provides support for Digiwallet iDEAL, Mister Cash and Sofort
* @author     Yellow Melon B.V.
* @url         http://www.idealplugins.nl
* @release     11-09-2014
* @ver         2.4
*
* Changes:
*
* v2.1     Cancel url added
* v2.2     Verify Peer disabled, too many problems with this
* v2.3     Added paybyinvoice (achteraf betalen) and paysafecard (former Wallie)
* v2.4        Removed IP_range and deprecated checkReportValidity . Because it is bad practice.
*/

/**
 * @class Digiwallet Core class
*/
class DigiWalletCore
{
    // Constants
    const APP_ID = 'dw_oscommerce2.x_1.0.2';

    const MIN_AMOUNT = 84;

    const ERR_NO_AMOUNT = "Geen bedrag meegegeven | No amount given";

    const ERR_NO_DESCRIPTION = "Geen omschrijving meegegeven | No description given";

    const ERR_AMOUNT_TOO_LOW = "Bedrag is te laag | Amount is too low";

    const ERR_AMOUNT_TOO_HIGH = "Bedrag is te hoog | Amount is too high";

    const ERR_NO_RTLO = "Geen DigiWallet Outletcode bekend; controleer de module instellingen | No Digiwallet Outletcode filled in, check the module settings";

    const ERR_NO_TXID = "Er is een onjuist transactie ID opgegeven | An incorrect transaction ID was given";

    const ERR_NO_RETURN_URL = "Geen of ongeldige return URL | No or invalid return URL";

    const ERR_NO_REPORT_URL = "Geen of ongeldige report URL | No or invalid report URL";

    const ERR_IDEAL_NO_BANK = "Geen bank geselecteerd voor iDEAL | No bank selected for iDEAL";

    const ERR_SOFORT_NO_COUNTRY = "Geen land geselecteerd voor Sofort | No country selected for Sofort";

    const ERR_PAYBYINVOICE = "Fout bij achteraf betalen|Error with paybyinvoice";

    // Constant array's
    protected $paymentOptions = array(
        "IDE",
        "MRC",
        "DEB",
        "WAL",
        "CC",
        "PYP",
        "BW",
        "AFP"
    );

    /*
     * If payMethod is set to 'AUTO' it will decided on the value of bankId
     * Then, when requested the bankId list will be filled with
     *
     * a) 'IDE' + the bank ID's for iDEAL
     * b) 'MRC' for Mister Cash
     * c) 'DEB' + countrycode for Sofort Banking, e.g. DEB49 for Germany
     */
    protected $minimumAmounts = array(
        "IDE" => 84,
        "MRC" => 49,
        "DEB" => 10,
        "WAL" => 10,
        "CC" => 100,
        "PYP" => 84,
        "BW"  => 84,
        "AFP" => 84
    );

    protected $maximumAmounts = array(
        "IDE"  => 100000,
        "MRC"  => 100000,
        "DEB"  => 500000,
        "WAL"  => 15000,
        "CC"   => 100000,
        "PYP"  => 100000,
        "AFP"  => 100000,
        "BW"   => 100000
    );

    protected $checkAPIs = array(
        "IDE" => "https://transaction.digiwallet.nl/ideal/check",
        "MRC" => "https://transaction.digiwallet.nl/mrcash/check",
        "DEB" => "https://transaction.digiwallet.nl/directebanking/check",
        "WAL" => "https://transaction.digiwallet.nl/paysafecard/check",
        "CC" => "https://transaction.digiwallet.nl/creditcard/check",
        "PYP" => "https://transaction.digiwallet.nl/paypal/check",
        "AFP" => "https://transaction.digiwallet.nl/afterpay/check",
        "BW" => "https://transaction.digiwallet.nl/bankwire/check"
    );

    /**
     *
     * @var array
     */
    protected $startAPIs = [
        "IDE" => "https://transaction.digiwallet.nl/ideal/start",
        "MRC" => "https://transaction.digiwallet.nl/mrcash/start",
        "DEB" => "https://transaction.digiwallet.nl/directebanking/start",
        "WAL" => "https://transaction.digiwallet.nl/paysafecard/start",
        "CC" => "https://transaction.digiwallet.nl/creditcard/start",
        "PYP" => "https://transaction.digiwallet.nl/paypal/start",
        "AFP" => "https://transaction.digiwallet.nl/afterpay/start",
        "BW" => "https://transaction.digiwallet.nl/bankwire/start"
    ];

    // Variables
    protected $rtlo = null;

    protected $testMode = false;

    protected $language = "nl";

    protected $payMethod = "IDE";
    // Payment Method
    protected $currency = "EUR";

    protected $bankId = null;

    protected $amount = 0;

    protected $description = null;

    protected $returnUrl = null;
    // When using the AUTO-setting; %payMethod% will be replaced by the actual payment method just before starting the payment
    protected $cancelUrl = null;
    // When using the AUTO-setting; %payMethod% will be replaced by the actual payment method just before starting the payment
    protected $reportUrl = null;
    // When using the AUTO-setting; %payMethod% will be replaced by the actual payment method just before starting the payment
    protected $bankUrl = null;

    protected $transactionId = null;

    protected $paidStatus = false;

    protected $consumerInfo = array();

    protected $errorMessage = null;

    protected $parameters = array();
    // Additional parameters

    /**
     * More information
     *
     * @var unknown
     */
    private $moreInformation = null;

    /**
     * Salt parameter for BW
     *
     * @var unknown
     */
    private $salt = "e381277";

    /**
     * Constructor
     *
     * @param int $rtlo
     *            Layoutcode
     */
    public function __construct($payMethod, $rtlo = false, $language = "nl", $testMode = false)
    {
        $payMethod = strtoupper($payMethod);
        if (in_array($payMethod, $this->paymentOptions)) {
            $this->payMethod = $payMethod;
        } else {
            return false;
        }
        $this->rtlo = (int) $rtlo;
        $this->testMode = ($testMode) ? '1' : '0';
        $this->language = strtolower(substr($language, 0, 2));
    }

    /**
     * Get list with banks based on PayMethod setting (AUTO, IDE, ...
     * etc.)
     */
    public function getBankList()
    {
        $url = "https://transaction.digiwallet.nl/api/idealplugins?banklist=" . urlencode($this->payMethod) . '&ver=4';

        $xml = $this->httpRequest($url);
        if (! $xml) {
            if ($this->payMethod == "IDE") {
                $banks_array["IDE0001"] = "Bankenlijst kon niet opgehaald worden bij Digiwallet, controleer of curl werkt!";
            } else {
                $banks_array["IDE0001"] = "Landenlijst kon niet opgehaald worden bij Digiwallet, controleer of curl werkt!";
            }
            $banks_array["IDE0002"] = "  ";
        } else {
            $banks_object = new SimpleXMLElement($xml);
            foreach ($banks_object->bank as $bank) {
                $banks_array["{$bank->bank_id}"] = "{$bank->bank_name}";
            }
        }

        return $banks_array;
    }

    /**
     * Start transaction with Digiwallet
     *
     * Set at least: amount, description, returnUrl, reportUrl (optional: cancelUrl)
     * In case of iDEAL: bankId
     * In case of Sofort: countryId
     *
     * After starting, it will return a link to the bank if successfull :
     * - Link can also be fetched with getBankUrl()
     * - Get the transaction id via getTransactionId()
     * - Read the errors with getErrorMessage()
     * - Get the actual started payment method, in case of auto-setting, using getPayMethod()
     */
    public function startPayment()
    {
        if (! $this->rtlo) {
            $this->errorMessage = self::ERR_NO_RTLO;
            return false;
        }

        if (! $this->amount) {
            $this->errorMessage = self::ERR_NO_AMOUNT;
            return false;
        }

        if ($this->amount < $this->minimumAmounts[$this->payMethod]) {
            $this->errorMessage = self::ERR_AMOUNT_TOO_LOW;
            return false;
        }

        if ($this->amount > $this->maximumAmounts[$this->payMethod]) {
            $this->errorMessage = self::ERR_AMOUNT_TOO_HIGH;
            return false;
        }

        if (! $this->description) {
            $this->errorMessage = self::ERR_NO_DESCRIPTION;
            return false;
        }

        if (! $this->returnUrl) {
            $this->errorMessage = self::ERR_NO_RETURN_URL;
            return false;
        }

        if (! $this->reportUrl) {
            $this->errorMessage = self::ERR_NO_REPORT_URL;
            return false;
        }

        if (($this->payMethod == "IDE") && (! $this->bankId)) {
            $this->errorMessage = self::ERR_IDEAL_NO_BANK;
            return false;
        }

        if (($this->payMethod == "DEB") && (! $this->countryId)) {
            $this->errorMessage = self::ERR_SOFORT_NO_BANK;
            return false;
        }

        $this->returnUrl = str_replace("%payMethod%", $this->payMethod, $this->returnUrl);
        $this->cancelUrl = str_replace("%payMethod%", $this->payMethod, $this->cancelUrl);
        $this->reportUrl = str_replace("%payMethod%", $this->payMethod, $this->reportUrl);

        // Startpayment Url builder
        $url = $this->startAPIs[$this->payMethod] . "?rtlo=" . urlencode($this->rtlo);
        $url .= "&bank=" . urlencode($this->bankId);
        $url .= "&amount=" . urlencode($this->amount);
        $url .= "&description=" . urlencode($this->description);
        // Remove testmode parameter
        //$url .= "&test=" . $this->testMode;
        $url .= "&userip=" . urlencode($_SERVER["REMOTE_ADDR"]);
        $url .= "&domain=" . urlencode($_SERVER["HTTP_HOST"]);
        $url .= "&returnurl=" . urlencode($this->returnUrl);
        $url .= "&reporturl=" . urlencode($this->reportUrl);
        $url .= "&app_id=" . urlencode(self::APP_ID);
        $url .= ((! empty($this->salt)) ? "&salt=" . urlencode($this->salt) : "");
        $url .= ((! empty($this->cancelUrl)) ? "&cancelurl=" . urlencode($this->cancelUrl) : "");
        // Case by case
        $url .= (($this->payMethod == "WAL") ? "&ver=2" : "");
        $url .= (($this->payMethod == "BW") ? "&ver=2" : "");
        $url .= (($this->payMethod == "CC") ? "&ver=3" : "");
        $url .= (($this->payMethod == "PYP") ? "&ver=1" : "");
        $url .= (($this->payMethod == "AFP") ? "&ver=1" : "");
        $url .= (($this->payMethod == "IDE") ? "&ver=4&language=nl" : "");
        $url .= (($this->payMethod == "MRC") ? "&ver=2&lang=" . urlencode($this->getLanguage(array(
            "NL",
            "FR",
            "EN"
        ), "NL")) : "");
        $url .= (($this->payMethod == "DEB") ? "&ver=2&type=1&country=" . urlencode($this->countryId) . "&lang=" . urlencode($this->getLanguage(array(
            "NL",
            "EN",
            "DE"
        ), "DE")) : "");
        // Another parameter
        if (is_array($this->parameters)) {
            foreach ($this->parameters as $k => $v) {
                $url .= "&" . $k . "=" . urlencode($v);
            }
        }

        $result = $this->httpRequest($url);
        
        $result_code = substr($result, 0, 6);        
        if (($result_code == "000000") || ($result_code == "000001" && $this->payMethod == "CC")) {
            $result = substr($result, 7);
            if ($this->payMethod == 'AFP' || $this->payMethod == 'BW') {
                list ($this->transactionId) = explode("|", $result);
                $this->moreInformation = $result;
                return true; // Process later
            } else {
                list ($this->transactionId, $this->bankUrl) = explode("|", $result);
            }
            return $this->bankUrl;
        } else {
            $this->errorMessage = "Digiwallet antwoordde: " . $result . " | Digiwallet responded with: " . $result;
            return false;
        }
    }

    /**
     * Check transaction with Digiwallet
     *
     * @param string $payMethodId
     *            Payment method's see above
     * @param string $transactionId
     *            Transaction ID to check Returns true if payment successfull (or testmode) and false if not After payment: - Read the errors with getErrorMessage() - Get user information using getConsumerInfo() Returns true if payment successfull (or testmode) and false if not After payment: - Read the errors with getErrorMessage() - Get user information using getConsumerInfo()
     *
     *            Returns true if payment successfull (or testmode) and false if not
     *
     *            After payment:
     *            - Read the errors with getErrorMessage()
     *            - Get user information using getConsumerInfo()
     */
    public function checkPayment($transactionId, $params = [])
    {
        if (! $this->rtlo) {
            $this->errorMessage = self::ERR_NO_RTLO;
            return false;
        }

        if (! $transactionId) {
            $this->errorMessage = self::ERR_NO_TXID;
            return false;
        }
        $url = $this->checkAPIs[$this->payMethod] . "?" . "rtlo=" . urlencode($this->rtlo) . "&" . "trxid=" . urlencode($transactionId) . "&" . "once=0&" . "test=" . (($this->testMode) ? "1" : "0");

        foreach ($params as $k => $v) {
            $url .= "&" . $k . "=" . urlencode($v);
        }

        $result = $this->httpRequest($url);

        $this->moreInformation = $result;

        if ($this->payMethod == 'AFP'){
            // Stop checking status and transfer result to Afterpay Model to process
            return $result;
        }

        $_result = explode("|", $result);

        $consumerBank = "";
        $consumerName = "";
        $consumerCity = "NOT PROVIDED";

        if (count($_result) == 4) {
            list ($resultCode, $consumerBank, $consumerName, $consumerCity) = $_result;
        } elseif(count($_result) == 3){
            // For BankWire
            list ($resultCode, $due_amount, $paid_amount) = $_result;
            $this->consumerInfo["bw_due_amount"] = $due_amount;
            $this->consumerInfo["bw_paid_amount"] = $paid_amount;
        }else{
            list ($resultCode) = $_result;
        }

        $this->consumerInfo["bankaccount"] = "bank";
        $this->consumerInfo["name"] = "customername";
        $this->consumerInfo["city"] = "city";

        if (($resultCode == "000000 OK")  || ($resultCode == "000001 OK" && $this->payMethod == "CC")) {
            $this->consumerInfo["bankaccount"] = $consumerBank;
            $this->consumerInfo["name"] = $consumerName;
            $this->consumerInfo["city"] = ($consumerCity != "NOT PROVIDED") ? $consumerCity : "";
            $this->paidStatus = true;
            return true;
        } else {
            $this->paidStatus = false;
            $this->errorMessage = $result;
            return false;
        }
    }

    /**
     * [DEPRECATED] checkReportValidity
     * Will removed in future versions
     * This function used to act as a redundant check on the validity of reports by checking IP addresses
     * Because this is bad practice and not necessary it is now removed
     */
    public function checkReportValidity($post, $server)
    {
        return true;
    }

    /**
     * PRIVATE FUNCTIONS
     */
    protected function httpRequest($url, $method = "GET")
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        if ($method == "POST") {
            curl_setopt($ch, CURLOPT_POST, 1);
        }
        $result = curl_exec($ch);
        curl_close($ch);
        return $result;
    }

    /**
     * GETTERS & SETTERS
     */
    public function setAmount($amount)
    {
        $this->amount = round($amount);
        return true;
    }

    /**
     * Bind additional parameter to start request.
     * Safe for chaining.
     */
    public function bindParam($name, $value)
    {
        $this->parameters[$name] = $value;
        return $this;
    }

    public function getAmount()
    {
        return $this->amount;
    }

    public function setBankId($bankId)
    {
        if ($this->payMethod == "DEB") {
            $this->countryId = $bankId;
            $this->bankId = false;
            return true;
        } else {
            $this->bankId = $bankId;
            return true;
        }
    }

    public function getBankId()
    {
        return $this->bankId;
    }

    public function getBankUrl()
    {
        return $this->bankUrl;
    }

    public function getConsumerInfo()
    {
        return $this->consumerInfo;
    }

    public function setCountryId($countryId)
    {
        $this->countryId = strtolower(substr($countryId, 0, 2));
        return true;
    }

    public function getCountryId()
    {
        return $this->countryId;
    }

    public function setCurrency($currency)
    {
        $this->currency = strtoupper(substr($currency, 0, 3));
        return true;
    }

    public function getCurrency()
    {
        return $this->currency;
    }

    public function setDescription($description)
    {
        $this->description = substr($description, 0, 32);
        return true;
    }

    /**
     * Set salt value for BW transaction
     *
     * @param unknown $salt
     */
    public function setSalt($salt)
    {
        $this->salt = $salt;
    }

    /**
     * Set salt value for BW transaction
     *
     * @param unknown $salt
     */
    public function getSalt()
    {
        return $this->salt;
    }

    /**
     * *
     * Get BW more information
     *
     */
    public function getMoreInformation()
    {
        if(empty($this->moreInformation)) return "";
        return $this->moreInformation;
    }

    public function getDescription()
    {
        return $this->description;
    }

    public function getErrorMessage()
    {
        if ($this->language == "nl") {
            list ($returnVal) = explode(" | ", $this->errorMessage, 2);
        } elseif ($this->language == "en") {
            list ($discard, $returnVal) = explode(" | ", $this->errorMessage, 2);
        } else {
            $returnVal = $this->errorMessage;
        }
        return $returnVal;
    }

    public function getLanguage($allowList = false, $defaultLanguage = false)
    {
        if (! $allowList) {
            return $this->language;
        } else {
            if (in_array(strtoupper($this->language), $allowList)) {
                return strtoupper($this->language);
            } else {
                return $this->defaultLanguage;
            }
        }
    }

    public function getPaidStatus()
    {
        return $this->paidStatus;
    }

    public function getPayMethod()
    {
        return $this->payMethod;
    }

    public function setReportUrl($reportUrl)
    {
        if (preg_match('|(\w+)://([^/:]+)(:\d+)?(.*)|', $reportUrl)) {
            $this->reportUrl = $reportUrl;
            return true;
        } else {
            return false;
        }
    }

    public function getReportUrl()
    {
        return $this->reportUrl;
    }

    public function setReturnUrl($returnUrl)
    {
        if (preg_match('|(\w+)://([^/:]+)(:\d+)?(.*)|', $returnUrl)) {
            $this->returnUrl = $returnUrl;
            return true;
        } else {
            return false;
        }
    }

    public function getReturnUrl()
    {
        return $this->returnUrl;
    }

    public function setCancelUrl($cancelUrl)
    {
        if (preg_match('|(\w+)://([^/:]+)(:\d+)?(.*)|', $cancelUrl)) {
            $this->cancelUrl = $cancelUrl;
            return true;
        } else {
            return false;
        }
    }

    public function getCancelUrl()
    {
        return $this->cancelUrl;
    }

    public function setTransactionId($transactionId)
    {
        $this->transactionId = substr($transactionId, 0, 32);
        return true;
    }

    public function getTransactionId()
    {
        return $this->transactionId;
    }

    /**
     * Reformat URL if add wrong params
     * @param $url
     * @return string
     */
    public static function formatOscommerceUrl($url)
    {
        $separator = "?";
        if(substr_count($url, $separator) > 1) {
            $url_array = explode($separator, $url);
            $url = "";
            foreach ($url_array as $url_item) {
                $url = $url . $url_item . $separator;
                $separator = "&";
            }
            $url = substr($url, 0, strlen($url) - 1);
        }
        return $url;
    }
}
