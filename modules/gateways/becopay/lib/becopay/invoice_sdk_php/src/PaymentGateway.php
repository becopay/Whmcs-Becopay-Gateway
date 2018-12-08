<?php
/**
 * User: Becopay Team
 * Version 1.1.0
 * Date: 10/10/18
 * Time: 10:36 AM
 */

namespace Becopay;


/**
 * Class PaymentGateway
 *
 * @package Becopay
 */
class PaymentGateway implements PaymentGatewayInterface
{

    /**
     * @var string payment gateway api base url
     */
    private $apiBaseUrl;

    /**
     * @var string payment gateway api key
     */
    private $apiKey;
    /**
     * @var string merchant mobile number
     */
    private $mobile;

    /**
     * @var string error message
     */
    public $error = '';

    /**
     * PaymentGateway constructor.
     *
     * @param string $apiBaseUrl
     * @param string $apiKey
     * @param string $mobile merchant mobile number
     * @throws \Exception
     */
    public function __construct($apiBaseUrl, $apiKey, $mobile)
    {
        /**
         * validate the url &&  Check value is string
         * If is invalid url or is not string value throw the exception
         */
        if (
            !self::__validateUrl($apiBaseUrl) ||
            !self::__validateString($apiKey, 1, 100) ||
            !self::__validateString($mobile, 3, 15)
        )
            throw new \Exception($this->error);


        $this->apiBaseUrl = trim($apiBaseUrl);
        $this->apiKey = trim($apiKey);
        $this->mobile = trim($mobile);
    }

    /**
     * Create the payment invoice and return the gateway url
     *
     * @param  string | integer $orderId
     * @param integer           $price
     * @param string            $description
     * @param string            $currency payer currency
     * @param string            $merchantCur merchant currency
     *
     * @return mixed  false|response object
     * @throws \Exception
     */
    public function create($orderId, $price, $description = '', $currency = 'IRR', $merchantCur = 'IRR')
    {

        // Clear the error variable
        $this->error = '';

        $orderId = (string)$orderId;

        /**
         * Check value is string
         * If validation functions return false, it will be set error message on error variable
         */
        if (
            !self::__validateString($orderId, 1, 50) ||
            !self::__validateNumeric($price, 1, 20) ||
            !self::__validateString($description, 0, 255)
        )
            return false;

        $param = array(
            "apikey" => $this->apiKey,
            "mobile" => $this->mobile,
            "description" => $description,
            "orderId" => $orderId,
            "merchantCur" => $merchantCur,
            "currency" => $currency,
            "price" => (string)$price
        );

        $result = self::__sendRequest('invoice', 'POST', $param);


        // if response code is 200 return the response value
        if ($result->code === 200) {
            if (isset($result->response->qr))
                unset($result->response->qr);

            return $result->response;

        } else if ($result->code == 0) { // if response code is 0 return false and set error message
            $this->error = $result->response;
            return false;
        } else { //Get error massage and return false
            if (isset($result->response->description) && !is_null($result->response->description))
                $this->error = $result->response->description;
            else if (isset($result->response->message) && !is_null($result->response->message))
                $this->error = $result->response->message;

            return false;
        }
    }

    /**
     * Check the payment status with invoice id
     *
     * @param string $invoiceId
     * @return mixed false|response object
     * @throws \Exception
     */
    public function check($invoiceId)
    {
        // Clear the error variable
        $this->error = '';

        /**
         * Check value is string
         * If string return false and set error message on error variable
         */
        if (
        !self::__validateString($invoiceId, 1, 50)
        )
            return false;

        $param = array(
            "id" => $invoiceId,
            "apikey" => $this->apiKey,
            "mobile" => $this->mobile
        );

        return self::__sendCheckRequest('invoice', $param);
    }

    /**
     * Check the payment status
     * for check invoice status with orderId set $isOrderId value true
     *
     * @param string $orderId
     * @return mixed false|response object
     * @throws \Exception
     */
    public function checkByOrderId($orderId)
    {
        // Clear the error variable
        $this->error = '';

        /**
         * Check value is string
         * If string return false and set error message on error variable
         */
        if (
        !self::__validateString($orderId, 1, 50)
        )
            return false;

        $param = array(
            "apikey" => $this->apiKey,
            "mob" => $this->mobile,
            "id" => $orderId
        );

        return self::__sendCheckRequest('invoice/byorderid', $param);
    }


    /**
     * Send Check invoice request
     *
     * @param $path
     * @param $param
     * @return bool
     * @throws \Exception
     */
    private function __sendCheckRequest($path, $param)
    {
        $result = self::__sendRequest($path, 'GET', $param);

        // if response code is 200 return the response value
        if ($result->code === 200) {
            if (isset($result->response->qr))
                unset($result->response->qr);

            return $result->response;

        } else if ($result->code == 0) { // if response code is 0 return false and set error message
            $this->error = $result->response;
            return false;
        } else { //Get error massage and return false
            if (isset($result->response->description) && !is_null($result->response->description))
                $this->error = $result->response->description;
            else if (isset($result->response->message) && !is_null($result->response->message))
                $this->error = $result->response->message;
            return false;
        }
    }

    /**
     * Send the request to payment server
     *
     * @param string $urlPath
     * @param string $method request method type , POST|GET
     * @param array  $param request parameters
     * @return object
     * @throws \Exception
     */
    private function __sendRequest($urlPath, $method, $param)
    {
        // Check Curl function if is enabled to send request
        if (function_exists('curl_version'))
            return self::__sendCurl($urlPath, $method, $param);

        // Check file_get_contents function if is enabled to send request
        else if (ini_get('allow_url_fopen'))
            return self::__sendFileGetContents($urlPath, $method, $param);

        //throw exception if none of them is enable
        else
            throw new \Exception('file_get_content and curl are disabled. you must enabled one of them');
    }

    /**
     * Use Curl function for Send request
     *
     * @param string $urlPath
     * @param string $method request method type , POST|GET
     * @param array  $param request parameters
     * @return object
     */
    private function __sendCurl($urlPath, $method, $param)
    {
        $url = trim($this->apiBaseUrl, '/') . '/' . trim($urlPath, '/');

        if ($method == 'GET') {
            $query = http_build_query($param);
            $url = $url . '?' . $query;
        }

        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_CUSTOMREQUEST => $method,
            CURLOPT_POSTFIELDS => $method == 'POST' ? json_encode($param) : '',
            CURLOPT_HTTPHEADER => array(
                "Cache-Control: no-cache",
                "Content-Type: application/json",
            ),
        ));

        $response = curl_exec($curl);
        $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        $error = curl_error($curl);
        curl_close($curl);

        return (object)array(
            'code' => $httpCode,
            'response' => $error ? $error : json_decode($response),
        );

    }

    /**
     * Use file_get_contents function for Send request
     *
     * @param string $urlPath
     * @param string $method request method type , POST|GET
     * @param array  $param request parameters
     * @return object
     */
    private function __sendFileGetContents($urlPath, $method, $param)
    {
        $url = trim($this->apiBaseUrl, '/') . '/' . trim($urlPath, '/');

        if ($method == 'GET') {
            $query = http_build_query($param);
            $url = $url . '?' . $query;
        }

        $opts = array('http' =>
            array(
                'method' => $method,
                'timeout' => '30',
                'max_redirects' => '10',
                'ignore_errors' => '1',
                'header' => 'Content-type: application/json',
                'content' => $method == 'POST' ? json_encode($param) : ''
            )
        );

        $context = stream_context_create($opts);
        $result = file_get_contents($url, false, $context);

        //get http status code
        $response_header = explode(' ', $http_response_header[0]);
        $httpCode = (int)$response_header[1];


        return (object)array(
            'code' => $httpCode,
            'response' => json_decode($result),
        );
    }

    /**
     * Validate url
     * If url is invalid throw the exception
     *
     * @param $url
     * @return bool
     */
    private function __validateUrl($url)
    {
        if (empty($url) || !is_string($url) || strlen($url) > 512 ||
            !preg_match('/^http(s)?:\/\/[a-z0-9-]+(.[a-z0-9-]+)+(:[0-9]+)?(\/.*)?$/i', $url) ||
            !filter_var($url, FILTER_VALIDATE_URL)
        ) {
            $this->error = 'invalid url format';
            return false;
        }
        return true;
    }

    /**
     * validate the string
     *
     * @param $string
     * @param $minLength
     * @param $maxLength
     * @return bool
     */
    private function __validateString($string, $minLength = 1, $maxLength = 0)
    {
        if (!is_string($string)) {
            $this->error = 'parameter is not string.';
            return false;
        } else if ($maxLength > 0 && strlen($string) > $maxLength) {
            $this->error = 'parameter is too long. string:' . $string;
            return false;
        } else if (strlen($string) < $minLength) {
            $this->error = 'parameter is too short. string:' . $string;
            return false;
        }
        return true;
    }

    /**
     * validate variable is a number or a numeric string
     *
     * @param $int
     * @param $minLength
     * @param $maxLength
     * @return bool
     */
    private function __validateNumeric($int, $minLength = 1, $maxLength = 0)
    {
        if (!is_numeric($int)) {
            $this->error = 'parameter is not number';
            return false;
        } else if ($maxLength > 0 && strlen($int) > $maxLength) {
            $this->error = 'parameter is too long. number:' . $int;
            return false;
        } else if (strlen($int) < $minLength) {
            $this->error = 'parameter is too short. number:' . $int;
            return false;
        }
        return true;
    }

}