# Becopay payment plugin for WHMCS


Becopay payment plugin for WHMCS using the becopay.com service.


## Prerequisites


You must have a Becopay merchant account to use this plugin.  It's free and easy to [sign-up for a becopay merchant account](https://becopay.com/en/merchant-register/).


## Installation

Extract these files into the WHMCS directory on your webserver (parent directory of
modules/folder).


## Configuration

1. Take a moment to ensure that you have set your store's domain and the WHMCS System URL under **whmcs/admin > Setup > General Settings**.
2. After the register as merchant on website. you will receive your Api Configuration data. [sign-up for a becopay merchant account](https://becopay.com/en/merchant-register/).
3. In the admin control panel, go to **Setup > Payment Gateways**, select **Becopay** in the list of modules and click **Activate**.
  * If you can't find the Becopay plugin in the list of payment gateways -or- in the WHMCS app store, then you may clone this repo and copy modules/gateways into your <whmcs root>/modules/gateways/.
4. Paste the your Mobile number ,API Base URL and API Key ID that you got and copied from step 2. 
8. Click **Save Changes**.

You're done!

## Callback url
Plugin Callback url is `http://your-site/modules/gateways/callback/becopay.php?orderId=`

## Usage

When a client chooses the Becopay payment method, they will be presented with an invoice showing a button they will have to click on in order to pay their order.  Upon requesting to pay their order, the system takes the client to a full-screen Becopay.com invoice page where the client is presented with payment instructions.  Once payment is received, a link is presented to the shopper that will return them to your website.

**NOTE:** Don't worry!  A payment will automatically update your WHMCS store whether or not the customer returns to your website after they've paid the invoice.

In your WHMCS control panel, you can see the information associated with each order made via Becopay by choosing **Orders > Pending Orders**.  This screen will tell you whether payment has been received by the Becopay servers.  You can also view the details for any paid invoice inside your Becopay merchant dashboard under the **Payments** page.

## Support

**Becopay Support:**

* [GitHub Issues](https://github.com/becopay/Whmcs-Becopay-Gateway/issues)
  * Open an issue if you are having issues with this plugin
* [Support](https://becopay.com/en/support/#contact-us)
  * Becopay support

**WHMCS Support:**

* [Homepage](https://www.whmcs.com/)
* [Documentation](http://docs.whmcs.com/Main_Page)
* [SupportForums](http://forum.whmcs.com/)

## Contribute

Would you like to help with this project?  Great!  You don't have to be a developer, either.  If you've found a bug or have an idea for an improvement, please open an [issue](https://github.com/becopay/Whmcs-Becopay-Gateway/issues) and tell us about it.

If you *are* a developer wanting contribute an enhancement, bugfix or other patch to this project, please fork this repository and submit a pull request detailing your changes. We review all PRs!

This open source project is released under the [Apache 2.0 license](https://opensource.org/licenses/Apache-2.0) which means if you would like to use this project's code in your own project you are free to do so.  Speaking of, if you have used our code in a cool new project we would like to hear about it!  [Please send us an email](mailto:io@becopay.com).

## License

Please refer to the [LICENSE](https://github.com/becopay/Whmcs-Becopay-Gateway/blob/master/LICENSE.txt) file that came with this project.
