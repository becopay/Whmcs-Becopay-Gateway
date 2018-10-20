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

class PaymentGatewayCheckTest extends TestCase
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

            //Test $orderId parameter with more than 50 character
            array(
                'apiBaseUrl' => 'http://localhost', //The parameter is being tested
                'apiKey' => $this->config->API_KEY,
                'mobile' => '09100000',
                'orderId' => '21245154843156463135468435165434654456468434684664681',
                'price' => 54166,
                'description' => 'test order',
                'isAssertion' => false,
                'test' => 'Test $orderId parameter with more than 50 character'
            ),
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
        echo "\n/// Test Check Method";
        foreach (self::dataSet() as $key => $data) {
            try {
                $payment = new PaymentGateway(
                    $data['apiBaseUrl'],
                    $data['apiKey'],
                    $data['mobile']
                );

                $result = $payment->create($data['orderId'], $data['price'], $data['description']);

                echo "\n" . $key . ' : ' . $data['test'];
                $this->assertTrue(!empty($result) == $data['isAssertion']);

            } catch (\Exception $e) {
                if ($data['isAssertion'])
                    $this->assertTrue(false, 'dataSet number ' . $key . ' is not passed,' . $e->getMessage());
                else {
                    echo "\n" . $key . ' : ' . $data['test'];
                    $this->assertTrue(true);
                }
            }

        }
    }
}