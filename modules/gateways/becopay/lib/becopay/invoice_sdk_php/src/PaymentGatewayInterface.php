<?php
/**
 * User: Becopay Team
 * Version 1.1.0
 * Date: 10/10/18
 * Time: 10:50 AM
 */

namespace Becopay;


/**
 * Interface PaymentGatewayInterface
 *
 * @package becopay\gateway
 */
interface PaymentGatewayInterface
{
    /**
     * PaymentGatewayInterface constructor.
     *
     * @param string $apiBaseUrl payment gateway api base url
     * @param string $apiKey payment gateway api key
     * @param string $mobile merchant mobile number
     */
    public function __construct($apiBaseUrl, $apiKey, $mobile);

    /**
     * Create the payment invoice and return the gateway url
     *
     * @param  string | integer $orderId
     * @param integer           $price payer order price
     * @param string            $description
     * @param string            $currency payer currency
     * @param string            $merchantCur merchant currency
     * @return object
     */
    public function create($orderId, $price, $description, $currency, $merchantCur);

    /**
     * Check the payment status with invoiceId
     *
     * @param string $invoiceId
     * @return object
     */
    public function check($invoiceId);

    /**
     * Check the payment status with orderId
     *
     * @param string $invoiceId
     * @return object
     */
    public function checkByOrderId($invoiceId);

}