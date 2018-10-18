<?php

include_once "../becopay/lib/autoload.php";

use WHMCS\Database\Capsule;
use Becopay\PaymentGateway;

// Required File Includes
include '../../../includes/functions.php';
include '../../../includes/gatewayfunctions.php';
include '../../../includes/invoicefunctions.php';

if (file_exists('../../../dbconnect.php')) {
    include '../../../dbconnect.php';
} else if (file_exists('../../../init.php')) {
    include '../../../init.php';
} else {
    error_log('[ERROR] In modules/gateways/becopay/createinvoice.php: include error: Cannot find dbconnect.php or init.php');
    die('[ERROR] In modules/gateways/becopay/createinvoice.php: include error: Cannot find dbconnect.php or init.php');
}


///////////////////////////////////////////
/// Get Request

//get parameter via GET method
getParameter($_GET);


//if have error redirect to client area
Header('Location: ' . $CONFIG['SystemURL'] . '/clientarea.php?action=invoices');
exit;

////////////////////////////////////////
/**
 * check get parameter and check is valid create invoice
 *
 * @param $param
 */
function getParameter($param)
{
    if (isset($param['orderId']))
        invoiceProcess($param['orderId']);
    else {
        error_log('[ERROR] In modules/gateways/callback/becopay.php: undefined orderId #');
        die();
    }
}

/**
 * Done the invoice payment processing
 * check payment status is success and update the invoice status
 *
 * @param $paymentInvoiceId
 */
function invoiceProcess($paymentInvoiceId)
{
    global $CONFIG;
    /**
     * Get Becopay Gateway Variables.
     */
    $GATEWAY = getConfig();

    /**
     * Validate Callback Invoice ID.
     *
     * Checks invoice ID is a valid invoice number. Note it will count an
     * invoice in any status as valid.
     *
     */
    $invoiceId = checkCbInvoiceID(reset(explode('-', $paymentInvoiceId)), $GATEWAY['paymentmethod']);

    //get invoice info
    $invoice = getInvoiceInfo($invoiceId);

    //check invoice payment status is not paid
    if ($invoice['status'] != 'Unpaid')
        return;

    try {
        $payment = new PaymentGateway($GATEWAY['apiBaseUrl'], $GATEWAY['apiKey'], $GATEWAY['mobile']);

        /**
         * Check invoice payment status on becopay gateway
         */
        $checkInvoice = $payment->check($paymentInvoiceId, true);

        //if have response
        if ($checkInvoice) {

            /**
             * Check invoice status
             * if not success set log and return
             */
            if ($checkInvoice->status != 'success') {
                error_log('transaction status is ' . $checkInvoice->status);
                logTransaction($GATEWAY['name'], $_GET, 'transaction status is ' . $checkInvoice->status);
                return;
            }

            /**
             * Check invoice price
             * if not same error set log and return
             */
            if (intval($invoice['total']) != $checkInvoice->price) {
                error_log('transaction price is not same,invoice price:' . intval($invoice['total']) .
                    ' ,callback response:' . json_encode($checkInvoice));
                logTransaction($GATEWAY['name'], $checkInvoice, 'transaction status is ' . $checkInvoice->status);
                return;
            }

            /**
             * Check Becopay Transaction ID.
             *
             * Performs a check for any existing transactions with the same given
             * transaction number.
             *
             */
            checkCbTransID($checkInvoice->id);


            /**
             * Log Transaction.
             *
             * Add an entry to the Gateway Log for debugging purposes.
             */
            logTransaction($GATEWAY['name'], $_GET, 'The payment has been received. This will be updated when the transaction has been completed.');

            /**
             * Set user default gateway to becopay
             */
            updateUserDefaultGateway($invoiceId, $GATEWAY['paymentmethod']);

            addInvoicePayment(
                $invoiceId,
                $checkInvoice->id,
                $checkInvoice->price,
                0,
                $GATEWAY['paymentmethod']
            );

            logTransaction($GATEWAY['name'], $_GET, 'The transaction is now complete.');

            //redirect to view invoice page
            Header('Location: ' . $CONFIG['SystemURL'] . '/viewinvoice.php?id=' . $invoiceId);
            exit;

        } else {
            error_log($payment->error);
            logTransaction($GATEWAY['name'], $_GET, $payment->error);
            Header('Location: ' . $CONFIG['SystemURL'] . '/clientarea.php?action=invoices');
            return;
        }
    } catch (\Exception $e) {
        error_log('transaction throw exception. ' . $e->getMessage());
        logTransaction($GATEWAY['name'], $_GET, 'transaction throw exception. ' . $e->getMessage());
        Header('Location: ' . $CONFIG['SystemURL'] . '/clientarea.php?action=invoices');
        return;
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
 * Set default gateway to user
 *
 * @param $invoiceId
 * @param $gatewayModule
 */
function updateUserDefaultGateway($invoiceId, $gatewayModule)
{
    $invoice = Capsule::table('tblinvoices')->where('id', $invoiceId)->first();

    $userId = $invoice->userid;
    Capsule::table('tblclients')->where('id', $userId)->update(array('defaultgateway' => $gatewayModule));
}

