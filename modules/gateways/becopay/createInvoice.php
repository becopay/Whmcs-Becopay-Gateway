<?php

/**
 * Check request method
 */
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    error_log('[ERROR] In modules/gateways/becopay/createInvoice.php: invalid request method. ' . $_SERVER['REQUEST_METHOD']);
    exit(404);
}

include_once "lib/autoload.php";

use WHMCS\Database\Capsule;
use Becopay\PaymentGateway;

include '../../../includes/functions.php';
include '../../../includes/gatewayfunctions.php';
include '../../../includes/invoicefunctions.php';

if (file_exists('../../../dbconnect.php')) {
    include '../../../dbconnect.php';
} else if (file_exists('../../../init.php')) {
    include '../../../init.php';
} else {
    error_log('[ERROR] In modules/gateways/becopay/createInvoice.php: include error: Cannot find dbconnect.php or init.php');
    die('[ERROR] In modules/gateways/becopay/createInvoice.php: include error: Cannot find dbconnect.php or init.php');
}

//get parameter via POST method
getParameter($_POST);

/**
 * check get parameter and check is valid create invoice
 *
 * @param $param
 */
function getParameter($param)
{
    if (isset($param['invoiceId']) && isset($param['returnUrl']))
        createInvoice((int)$param['invoiceId'], $param['description'], $param['returnUrl']);
    else {
        error_log('[ERROR] In modules/gateways/becopay/createInvoice.php: undefined invoiceId or returnUrl #');
        die();
    }
}


/**
 * Create payment invoice and redirect to gateway for payment action
 *
 * @param $invoiceId
 * @param $description
 * @param $returnUrl
 * @return bool
 */
function createInvoice($invoiceId, $description, $returnUrl)
{



    //get invoice info
    $invoice = getInvoiceInfo($invoiceId);

    //check invoice payment status is not paid
    if ($invoice['status'] != 'Unpaid') {
        header('Location: ' . $returnUrl);
        exit;
    }

    /**
     * Get Becopay Gateway Variables.
     */
    $GATEWAY = getConfig();

    $paymentInvoiceId = uniqid($invoiceId . '-');
    $total = floatval($invoice['total']);
    $currency = $invoice['code'];
    $merchantCurrency = $GATEWAY['merchantCurrency']?:DEFAULT_MERCHANT_CURRENCY;

    try {
        $payment = new PaymentGateway($GATEWAY['apiBaseUrl'], $GATEWAY['apiKey'], $GATEWAY['mobile']);

        $paymentInvoice = $payment->create((string)$paymentInvoiceId, $total, (string)$description, $currency, $merchantCurrency);

        if ($paymentInvoice) {
            //validate the invoice response
            if (
                $currency != $paymentInvoice->payerCur ||
                $total != $paymentInvoice->payerAmount ||
                $merchantCurrency != $paymentInvoice->merchantCur
            )
            {
                error_log('[ERROR] In modules/gateways/becopay/createInvoice.php: line 91, Invoice price is not same with gateway result');
                return display_error($returnUrl);
            }

                header('Location: ' . $paymentInvoice->gatewayUrl);
            exit;
        } else {
            error_log('[ERROR] In modules/gateways/becopay/createInvoice.php: line 98, ' . $payment->error . ' #' . $invoiceId);
            return display_error($returnUrl);
        }
    } catch (\Exception $e) {
        error_log('[ERROR] In modules/gateways/becopay/createInvoice.php: line 102, ' . $e->getMessage() . ' #' . $invoiceId);
            return display_error($returnUrl);
    }
}

/**
 * Get invoice information form database
 *
 * @param $invoiceId
 * @return array
 */
function getInvoiceInfo($invoiceId)
{
    $invoice = Capsule::connection()->select("SELECT tblinvoices.total, tblinvoices.status, tblcurrencies.code FROM tblinvoices, tblclients, tblcurrencies where tblinvoices.userid = tblclients.id and tblclients.currency = tblcurrencies.id and tblinvoices.id=$invoiceId");
    $data = (array)$invoice[0];
    if (!$data) {
        error_log('[ERROR] In modules/gateways/becopay/createInvoice.php: No invoice found for invoice id #' . $invoiceId);
        die('[ERROR] In modules/gateways/becopay/createInvoice.php: Invalid invoice id #' . $invoiceId);
    }
    return $data;
}

/**
 * Get Becopay Gateway Variables.
 *
 * Retrieves configuration setting values for a given module name.
 */
function getConfig()
{
    $gatewayModule = 'becopay';
    /**
     * Get Becopay Gateway Variables.
     *
     * Retrieves configuration setting values for a given module name.
     *
     * @param string $gatewayName
     */
    $gatewayParams = getGatewayVariables($gatewayModule);
    // Die if module is not active.
    if (!$gatewayParams['type']) {
        die("Module Not Activated");
    }

    return $gatewayParams;
}


/**
 * Display error
 * @param $backLink
 * @return bool
 */
function display_error($backLink)
{
    echo '<div style="text-align:center">' .
        '<p>Error on redirect to gateway</p>' .
        '<a href="' . $backLink . '">back to invoice page</a>'
        . '</div>';
    return true;
}