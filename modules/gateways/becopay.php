<?php
/**
 * WHMCS Becopay Payment Gateway Module
 */

if (!defined("WHMCS")) {
    die("This file cannot be accessed directly");
}


/**
 * Default merchant currency
 */

Const DEFAULT_MERCHANT_CURRENCY = 'IRR';
/**
 * Define module related meta data.
 *
 * Values returned here are used to determine module related capabilities and
 * settings.
 *
 * @see https://developers.whmcs.com/payment-gateways/meta-data-params/
 *
 * @return array
 */
function Becopay_MetaData()
{
    return array(
        'DisplayName' => 'Becopay Payment Gateway Module',
        'APIVersion' => '1.1', // Use API Version 1.1
        'DisableLocalCredtCardInput' => true,
        'TokenisedStorage' => false,
    );
}


/**
 * Define Becopay gateway configuration options.
 *
 * The fields you define here determine the configuration options that are
 * presented to administrator users when activating and configuring your
 * payment gateway module for use.
 *
 * @return array
 */
function becopay_config()
{
    return array(
        // the friendly display name for a payment gateway should be
        // defined here for backwards compatibility
        "FriendlyName" => array(
            "Type" => "System",
            "Value" => "Becopay"
        ),
        'mobile' => array(
            'FriendlyName' => 'Mobile number',
            'Type' => 'text',
            'Description' => 'Enter the phone number you registered in the Becopay here',
        ),
        'apiBaseUrl' => array(
            'FriendlyName' => 'Api base url',
            'Type' => 'text',
            'Description' => 'Enter Becopay api base url here.',
        ),
        'apiKey' => array(
            'FriendlyName' => 'Api Key',
            'Type' => 'text',
            'Description' => 'Enter your Becopay Api Key her',
        ),
        'merchantCurrency' => array(
            'FriendlyName' => 'Merchant Currency',
            'Type' => 'text',
            'Description' => 'Enter your currency want to receive money. eg. IRR, US, EUR',
            'Default' => DEFAULT_MERCHANT_CURRENCY,
            'Placeholder'=>DEFAULT_MERCHANT_CURRENCY
        ),
    );
}

/**
 * Payment link.
 *
 * Required by third party payment gateway modules only.
 *
 * Defines the HTML output displayed on an invoice. Typically consists of an
 * HTML form that will take the user to the payment gateway endpoint.
 *
 * @param array $params Payment Gateway Module Parameters
 *
 * @see https://developers.whmcs.com/payment-gateways/third-party-gateway/
 *
 * @return string
 */
function becopay_link($params)
{
    if (false === isset($params) || true === empty($params)) {
        die('[ERROR] In modules/gateways/becopay.php::becopay_link() function: Missing or invalid $params data.');
    }

    // Invoice Variables
    $invoiceId = $params['invoiceid'];

    // System Variables
    $systemurl = $params['systemurl'];

    $description = array(
        'User Email:'.$params['clientdetails']['email'],
        'InvoiceId:'.$params["invoiceid"],
        'Amount:'.$params["amount"].' '.$params["currency"],
        'Description:'.$params["description"],
    );

    $post = array(
        'invoiceId' => $invoiceId,
        'returnUrl' => $params['returnurl'],
        'description'=>implode(', ',$description)
    );

    $form = '<form action="' . $systemurl . 'modules/gateways/becopay/createInvoice.php" method="POST">';

    foreach ($post as $key => $value) {
        $form .= '<input type="hidden" name="' . $key . '" value = "' . $value . '" />';
    }

    $form .= '<input type="submit" value="' . $params['langpaynow'] . '" />';
    $form .= '</form>';

    return $form;
}
