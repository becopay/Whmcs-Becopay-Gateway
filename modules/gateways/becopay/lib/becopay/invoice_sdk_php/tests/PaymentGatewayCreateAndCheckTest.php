<?php
/**
 * User: Becopay Team
 * Version 1.0.0
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
                'price' => 54166,
                'withOrderId' => false,
                'description' => 'test order',
                'test' => 'Test create invoice and check  with invoiceId'
            ),
            //Test create invoice and check invoice with orderId
            array(
                'apiBaseUrl' => $this->config->API_BASE_URL,
                'apiKey' => $this->config->API_KEY,
                'mobile' => $this->config->MOBILE,
                'orderId' => (string)uniqid('test_'),
                'price' => 54166,
                'withOrderId' => true,
                'description' => 'test order',
                'test' => 'Test create invoice and check invoice with orderId'
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

                $result = $payment->create($data['orderId'], $data['price'], $data['description']);
                if ($result) {
                    if ($data['withOrderId'])
                        $invoice = $payment->checkByOrderId($result->orderId);
                    else
                        $invoice = $payment->check($result->id);

                    if ($invoice)
                        $this->assertTrue(true);
                    else
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