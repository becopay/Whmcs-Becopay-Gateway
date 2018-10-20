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

/**
 * Class PaymentGatewayTest
 *
 * @package Test
 */
class PaymentGatewayConstructorTest extends TestCase
{

    private $dataSet = array(
        //test constructor with correct data
        array(
            'apiBaseUrl' => 'http://api.com',
            'apiKey' => 'apikey',
            'mobile' => '09100000000',
            'isAssertion' => true,
            'test'=>'Test constructor with correct data',
        ),
        /////////////////////////////////////////////////
        /// Test $apiBaseUrl value

        // Test $apiBaseUrl parameter with null value
        array(
            'apiBaseUrl' => null, //The parameter is being tested
            'apiKey' => 'apikey',
            'mobile' => '09100000000',
            'isAssertion' => false,
            'test'=>'Test $apiBaseUrl parameter with null value',
        ),
        // Test $apiBaseUrl parameter length more than 512 character
        array(
            'apiBaseUrl' => 'https://www.api.com/url?sa=t&rct=j&q=&esrc=s&source=web&cd=1&ved=2ahUKEwi56MDUi_zdAhUGiSwKHAwQFjAAegQICRAB&url=http%3A%2F%2Fstring-functions.com%2Flength.aspx&usg=AOvVaw1utca5OnUZuOIkPiOsOGnu&sa=t&rct=j&q=&esrc=s&source=web&cd=1&ved=2ahUKEwi56MDUi_zdAhUGiSwKHc47DAwQFjAAegQICRAB&url=http%3A%2F%2Fstring-functions.com%2Flength.aspx&usg=AOvVaw1utca5OnUZuOIkPiOsOGnu&sa=t&rct=j&q=&esrc=s&source=web&cd=1&ved=2ahUKEwi56MDUi_zdAhUGiSwKHc47DAwQFjAAegQICRAB&url=http%3A%2F%2Fstring-functionscom%2Flength.aspx&usg=AOvVaw1utca5OnUZuOIkPiOsOGnu', //The parameter is being tested,
            'apiKey' => 'apikey',
            'mobile' => '09100000000',
            'isAssertion' => false,
            'test'=>'Test $apiBaseUrl parameter length more than 512 character',
        ),
        // Test $apiBaseUrl parameter with incorrect protocol
        array(
            'apiBaseUrl' => 'httpq://api.com', //The parameter is being tested
            'apiKey' => 'apikey',
            'mobile' => '09100000000',
            'isAssertion' => false,
            'test'=>'Test $apiBaseUrl parameter with incorrect protocol'
        ),
        // Test $apiBaseUrl parameter  without protocol
        array(
            'apiBaseUrl' => 'api.com', //The parameter is being tested
            'apiKey' => 'apikey',
            'mobile' => '09100000000',
            'isAssertion' => false,
            'test'=>'Test $apiBaseUrl parameter without protocol'
        ),
        // Test $apiBaseUrl parameter with port
        array(
            'apiBaseUrl' => 'https://api.com:8080', //The parameter is being tested
            'apiKey' => 'apikey',
            'mobile' => '09100000000',
            'isAssertion' => true,
            'test'=>'Test $apiBaseUrl parameter with port'
        ),
        // Test $apiBaseUrl parameter with Ip
        array(
            'apiBaseUrl' => 'https://80.80.80.80', //The parameter is being tested
            'apiKey' => 'apikey',
            'mobile' => '09100000000',
            'isAssertion' => true,
            'test'=>'Test $apiBaseUrl parameter with Ip'
        ),
        // Test $apiBaseUrl parameter with Ip and port
        array(
            'apiBaseUrl' => 'https://80.80.80.80:8054', //The parameter is being tested
            'apiKey' => 'apikey',
            'mobile' => '09100000000',
            'isAssertion' => true,
            'test'=>'Test $apiBaseUrl parameter with Ip and port'
        ),
        ///////////////////////////////////////////////
        /// Test $apiKey
        ///
        // Test $apiKey parameter with null value
        array(
            'apiBaseUrl' => 'https://api.url',
            'apiKey' => null, //The parameter is being tested
            'mobile' => '09100000000',
            'isAssertion' => false,
            'test'=>'Test $apiKey parameter with null value'
        ),
        // Test $apiKey parameter with other type of value
        array(
            'apiBaseUrl' => 'https://api.url',
            'apiKey' => 13165465, //The parameter is being tested
            'mobile' => '09100000000',
            'isAssertion' => false,
            'test'=>'Test $apiKey parameter with other type of value'
        ),
        // Test $apiKey parameter with more than 100 character
        array(
            'apiBaseUrl' => 'https://api.url',
            'apiKey' => 'B8B8$%%^93905A34$98B3FB82@!kF8BF37ED&&&4AB8939;0@5B822U%#BB93^8B3#0CWE45FEE5EQ^$*DFG%$!6GHJ6ES646544545640', //The parameter is being tested
            'mobile' => '09100000000',
            'isAssertion' => false,
            'test'=>'Test $apiKey parameter with more than 100 character'
        ),
        ///////////////////////////////////////////////
        /// Test $mobile
        ///
        // Test $mobile parameter with null value
        array(
            'apiBaseUrl' => 'https://api.url',
            'apiKey' => 'api key',
            'mobile' => null, //The parameter is being tested
            'isAssertion' => false,
            'test'=>'Test $mobile parameter with null value'
        ),
        // Test $mobile parameter with other type of value
        array(
            'apiBaseUrl' => 'https://api.url',
            'apiKey' => 'api key',
            'mobile' => 13165465, //The parameter is being tested
            'isAssertion' => false,
            'test'=>'Test $mobile parameter with other type of value'
        ),
        // Test $mobile parameter with more than 15 character
        array(
            'apiBaseUrl' => 'https://api.url',
            'apiKey' => 'api key',
            'mobile' => '0910000005689574', //The parameter is being tested
            'isAssertion' => false,
            'test'=>'Test $mobile parameter with more than 15 character'
        ),
    );

    /**
     * Test the class constructor with dataSet
     */
    public function testConstructor()
    {
        echo "\n//////////////////////////////////";
        echo "\n/// Test Constructor Method";
        foreach ($this->dataSet as $key => $data) {
            echo "\n".$key.' : '.$data['test'];
            try {
                new PaymentGateway(
                    $data['apiBaseUrl'],
                    $data['apiKey'],
                    $data['mobile']
                );
            } catch (\Exception $e) {
                $this->assertEquals($data['isAssertion'], false, 'Error:dataSet number ' . $key . ' is not passed'.
            "\nTest:".$data['test']);
            }
        }
        $this->assertTrue(true);
    }

}