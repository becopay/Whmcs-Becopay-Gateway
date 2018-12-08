<?php
/**
 * User: Becopay Team
 * Version 1.1.0
 * Date: 10/10/18
 * Time: 10:36 AM
 */

namespace Test;

use PHPUnit\Framework\TestCase;
use Becopay\PaymentGateway;
use Tests\LoadConfig;

class PaymentGatewayCreateTest extends TestCase
{

    private $config = array();

    public function __construct($name = null, array $data = array(), $dataName = '')
    {
        parent::__construct($name, $data, $dataName);
        $this->config = new LoadConfig();
    }

    public function dataSet()
    {
        return $dataSet = array(
            // Test invalid api key
            array(
                'apiBaseUrl' => $this->config->API_BASE_URL,
                'apiKey' => 'apikey', //The parameter is being tested
                'mobile' => $this->config->MOBILE,
                'orderId' => '4988849849',
                'price' => 54166,
                'description' => 'test order',
                'isAssertion' => false,
                'test' => 'Test invalid api key'
            ),
            //Test invalid mobile
            array(
                'apiBaseUrl' => $this->config->API_BASE_URL,
                'apiKey' => $this->config->API_KEY,
                'mobile' => '09100000', //The parameter is being tested
                'orderId' => '4988849849',
                'price' => 54166,
                'description' => 'test order',
                'isAssertion' => false,
                'test' => 'Test invalid mobile'
            ),
            //Test invalid api base url
            array(
                'apiBaseUrl' => 'http://localhost', //The parameter is being tested
                'apiKey' => $this->config->API_KEY,
                'mobile' => '09100000',
                'orderId' => '4988849849',
                'price' => 54166,
                'description' => 'test order',
                'isAssertion' => false,
                'test' => 'Test invalid api url'
            ),
            //Test $orderId parameter with more than 50 character
            array(
                'apiBaseUrl' => $this->config->API_BASE_URL,
                'apiKey' => $this->config->API_KEY,
                'mobile' => $this->config->MOBILE,
                'orderId' => '21245154843156463135468435165434654456468434684664681', //The parameter is being tested
                'price' => 54166,
                'description' => 'test order',
                'isAssertion' => false,
                'test' => 'Test $orderId parameter with more than 50 character'
            ),
            //Test $price parameter with more than 20 character
            array(
                'apiBaseUrl' => $this->config->API_BASE_URL,
                'apiKey' => $this->config->API_KEY,
                'mobile' => $this->config->MOBILE,
                'orderId' => '21245154',
                'price' => 12545658754125485321554,//The parameter is being tested
                'description' => 'test order',
                'isAssertion' => false,
                'test' => 'Test $price parameter with more than 20 character'
            ),
            //Test $price parameter with invalid format
            array(
                'apiBaseUrl' => $this->config->API_BASE_URL,
                'apiKey' => $this->config->API_KEY,
                'mobile' => $this->config->MOBILE,
                'orderId' => '21245154',
                'price' => 'a65646',//The parameter is being tested
                'description' => 'test order',
                'isAssertion' => false,
                'test' => 'Test $price parameter with invalid format'
            ),
            //Test $price parameter with float value
            array(
                'apiBaseUrl' => $this->config->API_BASE_URL,
                'apiKey' => $this->config->API_KEY,
                'mobile' => $this->config->MOBILE,
                'orderId' => rand(),
                'price' => 1251.5,//The parameter is being tested
                'description' => 'test order',
                'isAssertion' => false,
                'test' => 'Test $price parameter with float value'
            ),
            //Test $description parameter with more than 255 character
            array(
                'apiBaseUrl' => $this->config->API_BASE_URL,
                'apiKey' => $this->config->API_KEY,
                'mobile' => $this->config->MOBILE,
                'orderId' => '21245154',
                'price' => 1254565875,
                'description' => 'test order,test ordertest ordertest ordertest ordertest ordertest ordertest ordertest ordertest ordertest ordertest ordertest ordertest ordertest ordertest ordertest order', //The parameter is being tested
                'isAssertion' => false,
                'test' => 'Test $description parameter with more than 255 character'
            ),
            //Test create invoice with float
            array(
                'apiBaseUrl' => $this->config->API_BASE_URL,
                'apiKey' => $this->config->API_KEY,
                'mobile' => $this->config->MOBILE,
                'orderId' => rand(),
                'price' => 4166.4,
                'merchantCur' => 'IRR',
                'payerCur' => 'USD',
                'description' => 'test order',
                'isAssertion' => true,
                'test' => 'Test create invoice with double price',
                'response' => array(
                    'id' => 'string',
                    'shopName' => 'string',
                    'status' => 'string',
                    'remaining' => 'integer',
                    'payerAmount' => '',
                    'payerCur' => 'string',
                    'merchantAmount' => '',
                    'merchantCur' => 'string',
                    'date' => 'string',
                    'timestamp' => 'integer',
                    'timeout' => 'integer',
                    'description' => 'string',
                    'gatewayUrl' => 'string',
                    'callback' => 'string',
                    'orderId' => 'string',
                ),
                'validate' => array(
                    'payerAmount' => 'price',
                    'payerCur' => 'payerCur',
                    'merchantCur' => 'merchantCur',
                    'orderId' => 'orderId',
                    'description' => 'description'
                )
            ),
            //Test create invoice
            array(
                'apiBaseUrl' => $this->config->API_BASE_URL,
                'apiKey' => $this->config->API_KEY,
                'mobile' => $this->config->MOBILE,
                'orderId' => rand(),
                'price' => 4166,
                'merchantCur' => 'IRR',
                'payerCur' => 'USD',
                'description' => 'test order',
                'isAssertion' => true,
                'test' => 'Test create invoice',
                'response' => array(
                    'id' => 'string',
                    'shopName' => 'string',
                    'status' => 'string',
                    'remaining' => 'integer',
                    'payerAmount' => '',
                    'payerCur' => 'string',
                    'merchantAmount' => '',
                    'merchantCur' => 'string',
                    'date' => 'string',
                    'timestamp' => 'integer',
                    'timeout' => 'integer',
                    'description' => 'string',
                    'gatewayUrl' => 'string',
                    'callback' => 'string',
                    'orderId' => 'string',
                ),
                'validate' => array(
                    'payerAmount' => 'price',
                    'payerCur' => 'payerCur',
                    'merchantCur' => 'merchantCur',
                    'orderId' => 'orderId',
                    'description' => 'description'
                )
            )
        );
    }

    /**
     * Test the class constructor
     * Create class with correct data
     * If create without error pass the test
     */
    public function testCreateMethod()
    {
        echo "\n//////////////////////////////////";
        echo "\n/// Test Create Method";
        foreach (self::dataSet() as $key => $data) {
            try {
                $payment = new PaymentGateway(
                    $data['apiBaseUrl'],
                    $data['apiKey'],
                    $data['mobile']
                );

                $result = $payment->create($data['orderId'], $data['price'],
                    $data['description'], $data['payerCur'], $data['merchantCur']);

                if ($data['isAssertion'] && isset($data['response']) && $result) {
                    foreach ($data['response'] as $key => $value) {
                        if (!isset($result->$key))
                            $this->assertTrue(false, 'dataSet "' . $data['test'] .
                                ', undefined "' . $key . '" on response');
                        else if (!empty($value) && gettype($result->$key) != $value)
                            $this->assertTrue(false, 'dataSet "' . $data['test'] .
                                ', "'.$key . '"  values is not ' . $value . ' on response'.
                                ', type is '.gettype($result->$key));
                    }

                    //check validate dataset
                    foreach ($data['validate'] as $res_value => $req_value)
                        if($result->$res_value != $data[$req_value])
                            $this->assertTrue(false, 'dataSet "' . $data['test'] .
                                ', "' . $res_value . '" response value is not same with request value');
                }
                echo "\n" . $key . ' : ' . $data['test'];
                $this->assertTrue(!empty($result) == $data['isAssertion']);

            } catch (\Exception $e) {
                if ($data['isAssertion'])
                    $this->assertTrue(false, 'dataSet "' . $data['test'] .
                        '" is not passed,' . $e->getMessage() . ', ' . $payment->error);
                else {
                    echo "\n" . $key . ' : ' . $data['test'];
                    $this->assertTrue(true);
                }
            }

        }
    }
}