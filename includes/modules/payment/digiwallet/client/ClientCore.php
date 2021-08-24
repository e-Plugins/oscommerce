<?php
namespace Digiwallet;

/**
 * @file Provides support for Digiwallet iDEAL, Mister Cash and Sofort Banking
 *
 * @author e-plugins.nl.
 *         @url http://www.digiwallet.nl
 *         @release 29-09-2014
 *         @ver 2.5.4
 *
 *         Changes:
 *
 *         v2.1     Cancel url added
 *         v2.2     Verify Peer disabled, too many problems with this
 *         v2.3     Added paybyinvoice (achteraf betalen) and paysafecard (former Wallie)
 *         v2.4     Removed IP_range and deprecated checkReportValidity . Because it is bad practice.
 *         v2.5     Added creditcards by ATOS
 *         v2.5.3   fix multistore and bankwire outlet identifier
 *         v2.5.4   fix Cannot load order
 *         v2.5.5   refactor to update authors name
 */

use Digiwallet\client\src\Client;
use Digiwallet\client\src\Request\CheckTransaction;
use Digiwallet\client\src\Request\CreateTransaction;

require 'vendor/autoload.php';
/**
 * @class ClientCore class
 */
class ClientCore
{
    const APP_ID = 'dw_oscommerce2.x_1.0.2';

    const MIN_AMOUNT = 0.84;

    const ERR_NO_AMOUNT = "Geen bedrag meegegeven | No amount given";

    const ERR_NO_DESCRIPTION = "Geen omschrijving meegegeven | No description given";

    const ERR_UNKNOWN_ERROR = "Onbekende fout | Unknown error";

    const ERR_NO_PAYMENT_METHOD = "Geen betalingsmethode meegegeven | No payment method given";

    const ERR_NO_OUTLET_ID = "Geen DigiWallet Outletcode bekend; controleer de module instellingen | No Digiwallet Outletcode filled in, check the module settings";

    const ERR_NO_API_TOKEN = "Geen DigiWallet API Token bekend; controleer de module instellingen | No Digiwallet API Token filled in, check the module settings";

    const ERR_NO_TXID = "Er is een onjuist transactie ID opgegeven | An incorrect transaction ID was given";

    const ERR_NO_RETURN_URL = "Geen of ongeldige return URL | No or invalid return URL";

    const ERR_NO_REPORT_URL = "Geen of ongeldige report URL | No or invalid report URL";

    /**
     * COMMON SUCCESS STATUS
     */
    const STATUS_CODE_SUCCESS = 0;

    /***
     * Digiwallet EndPoint
     */
    const DIGIWALLET_API_URL = "https://api.digiwallet.nl/";

    /**
     *
     * @var array
     */
    private $paymentMethods = [
        'EPS'   => 'EPS',
        'GIP'   => 'GiroPay'
    ];

    /**
     *
     * @var string
     */
    private $outletID = null;

    /**
     * Payment Method
     *
     * @var string
     */
    private $payMethod = "EPS";

    /**
     * @var string
     */
    private $errorMessage;

    /***
     * Current request language
     * @var string
     */
    private $language = "nl";

    /**
     * ClientCore constructor.
     * @param $outlet_id
     * @param $payment_method
     * @param $language
     */
    public function __construct($outlet_id, $payment_method, $language)
    {
        $this->payMethod = strtoupper($payment_method);
        $this->outletID  = $outlet_id;
        $this->language  = $language;
        if(!isset($this->paymentMethods[$this->payMethod])) {
            $this->errorMessage = self::ERR_NO_PAYMENT_METHOD;
            return false;
        }
    }

    /**
     * Start API
     * @param $apiKey
     * @param $data
     * @return bool|\Digiwallet\client\src\Response\CreateTransaction
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function createTransaction($apiKey, $data)
    {
        if(empty($apiKey)) {
            $this->errorMessage = self::ERR_NO_API_TOKEN;
            return false;
        }
        if(!isset($this->paymentMethods[$this->payMethod])) {
            $this->errorMessage = self::ERR_NO_PAYMENT_METHOD;
            return false;
        }
        if(empty($this->outletID)) {
            $this->errorMessage = self::ERR_NO_OUTLET_ID;
            return false;
        }

        if(is_array($data)) {
            $data['outletId'] = $this->outletID;
            $data['currencyCode'] = "EUR";
            $data['consumerIp'] = $this->getCustomerIP();
            $data['paymentMethods'] = [$this->payMethod];
            $data['app_id'] = self::APP_ID;
            $data['environment'] = 0;
            $data['amountChangeable'] = 0;
            $data['acquirerPreprodMode'] = 0;
            $data['suggestedLanguage'] = $this->language == "nl" ? "NLD" : "ENG";
        }

        /** @var \Digiwallet\client\src\Response\CreateTransaction $response */
        $response = $this->clientStartTransaction($apiKey, $data);

        if(!empty($response)) {
            // Accept Json response only
            if($this->checkStatusCode($response->status())) {
                return $response;
            } else {
                // Get error message
                $this->errorMessage = $response->message();
            }
        } else {
            if(empty($this->errorMessage)) {
                $this->errorMessage = self::ERR_UNKNOWN_ERROR;
            }
        }
        return false;
    }

    /**
     * Check transaction result
     * @param $apiKey
     * @param $transactionID
     * @return bool
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function checkTransaction($apiKey, $transactionID)
    {
        if(!isset($this->paymentMethods[$this->payMethod])) {
            $this->errorMessage = self::ERR_NO_PAYMENT_METHOD;
            return false;
        }
        if(empty($this->outletID)) {
            $this->errorMessage = self::ERR_NO_OUTLET_ID;
            return false;
        }

        /** @var \Digiwallet\client\src\Response\CheckTransaction $response */
        $response = $this->clientCheckTransaction($apiKey, $transactionID);

        if(!empty($response)) {
            // Accept Json response only
            if($this->checkStatusCode($response->getStatus())) {
                if(in_array($response->getTransactionStatus(), ['Completed'])) {
                    return true;
                }
            }
            $this->errorMessage = $response->getMessage();
        } else {
            if(empty($this->errorMessage)) {
                $this->errorMessage = self::ERR_UNKNOWN_ERROR;
            }
        }
        return false;
    }

    /**
     * Check response status
     * @param $status
     * @return bool
     */
    protected function checkStatusCode($status)
    {
        if(in_array($status, [self::STATUS_CODE_SUCCESS])) {
            return true;
        }
        return false;
    }

    /**
     * Call Digiwallet api client
     * @param $apiKey
     * @param $params
     * @return mixed
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    protected function clientStartTransaction($apiKey, $params)
    {
        // Using Client Transaction Library
        $digiwalletApi = new \Digiwallet\Packages\Transaction\Client\Client(self::DIGIWALLET_API_URL);
        $request = new \Digiwallet\Packages\Transaction\Client\Request\CreateTransaction($digiwalletApi, $params);
        $request->withBearer($apiKey);
        try{
            return $request->send();
        } catch (\Exception $exception) {
            $this->errorMessage = $exception->getMessage();
        }
        return false;
    }

    /**
     * Check Transaction result
     * @param $apiKey
     * @param $transactionID
     * @return \Digiwallet\client\src\Response\CheckTransaction
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    protected function clientCheckTransaction($apiKey, $transactionID)
    {
        $digiwalletApi = new \Digiwallet\Packages\Transaction\Client\Client(self::DIGIWALLET_API_URL);
        $request = new \Digiwallet\Packages\Transaction\Client\Request\CheckTransaction($digiwalletApi);
        $request->withBearer($apiKey);
        $request->withOutlet($this->outletID);
        $request->withTransactionId($transactionID);
        try{
            return $request->send();
        } catch (\Exception $exception) {
            $this->errorMessage = $exception->getMessage();
        }
        return false;
    }

    /**
     * Curl http request
     * @param $url
     * @param string $method
     * @param array $postParams
     * @param array $headerParams
     * @return mixed
     */
    protected function httpRequest($url, $method = "GET", $postParams = array(), $headerParams = array())
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
        if ($method == "POST") {
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $postParams);
        }
        if (!empty($headerParams)) {
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headerParams);
        }
        $result = curl_exec($ch);
        curl_close($ch);
        return $result;
    }

    /***
     * Get current error message
     * @return mixed|string
     */
    public function getErrorMessage() {
        $returnVal = '';
        if (! empty($this->errorMessage)) {
            if ($this->language == "nl" && strpos($this->errorMessage, " | ") !== false) {
                list ($returnVal) = explode(" | ", $this->errorMessage, 2);
            } elseif ($this->language == "en" && strpos($this->errorMessage, " | ") !== false) {
                list ($discard, $returnVal) = explode(" | ", $this->errorMessage, 2);
            } else {
                $returnVal = $this->errorMessage;
            }
        }
        return $returnVal;
    }

    /***
     * Get user's ip address
     * @return mixed
     */
    public function getCustomerIP()
    {
        if(!empty($_SERVER['HTTP_CLIENT_IP'])){
            //ip from share internet
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        } else {
            $ip = $_SERVER['REMOTE_ADDR'];
        }
        return $ip;
    }

}
