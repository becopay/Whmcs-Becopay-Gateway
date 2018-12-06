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

class PaymentGatewayCreateAndCheck extends TestCase
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
            //Test create invoice and check invoice with invoiceId
            array(
                'apiBaseUrl' => $this->config->API_BASE_URL,
                'apiKey' => $this->config->API_KEY,
                'mobile' => $this->config->MOBILE,
                'orderId' => (string)uniqid('test_'),
                'price' => 15000,
                'merchantCur' => 'IRR',
                'payerCur' => 'USD',
                'withOrderId' => false,
                'description' => 'test order',
                'test' => 'Test create invoice and check  with invoiceId',
                'response' => array(
                    'id' => 'string',
                    'shopName' => 'string',
                    'status' => 'string',
                    'remaining' => 'integer',
                    'payerAmount' => '',
                    'payerCur' => 'string',
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
                    'orderId' => 'orderId',
                    'description' => 'description'
                )
            ),
            //Test create invoice and check invoice with orderId
            array(
                'apiBaseUrl' => $this->config->API_BASE_URL,
                'apiKey' => $this->config->API_KEY,
                'mobile' => $this->config->MOBILE,
                'orderId' => (string)uniqid('test_'),
                'price' => 15000,
                'merchantCur' => 'IRR',
                'payerCur' => 'IRR',
                'withOrderId' => true,
                'description' => 'test order',
                'test' => 'Test create invoice and check invoice with orderId',
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
        echo "\n/// Test Create invoice and Check Method";
        foreach (self::dataSet() as $key => $data) {
            try {
                $payment = new PaymentGateway(
                    $data['apiBaseUrl'],
                    $data['apiKey'],
                    $data['mobile']
                );
                echo "\n" . $key . ' : ' . $data['test'];

                $result = $payment->create($data['orderId'], $data['price'],
                    $data['description'], $data['payerCur'], $data['merchantCur']);
                if ($result) {
                    if ($data['withOrderId'])
                        $invoice = $payment->checkByOrderId($result->orderId);
                    else
                        $invoice = $payment->check($result->id);

                    if ($invoice) {
                        //check response dataset
                        foreach ($data['response'] as $res_key => $res_value) {
                            if (!isset($invoice->$res_key))
                                $this->assertTrue(false, 'dataSet "' . $data['test'] .
                                    ', undefined "' . $res_key . '" on response');
                            else if (!empty($res_value) && gettype($invoice->$res_key) != $res_value)
                                $this->assertTrue(false, 'dataSet "' . $data['test'] .
                                    ', "' . $res_key . '"  values is not ' . $res_value . ' on response' .
                                    ', type is ' . gettype($invoice->$res_key));
                        }

                        //check validate dataset
                        foreach ($data['validate'] as $res_value => $req_value)
                            if($invoice->$res_value != $data[$req_value])
                                $this->assertTrue(false, 'dataSet "' . $data['test'] .
                                    ', "' . $res_value . '" response value is not same with request value');
                    } else
                        $this->assertTrue(false);
                } else {
                    $this->assertTrue(false);
                }
            } catch (\Exception $e) {
                $this->assertTrue(false, 'dataSet "' . $data['test'] .
                    '" is not passed,' . $e->getMessage() . ', ' . $payment->error);
            }
        }

    }
}