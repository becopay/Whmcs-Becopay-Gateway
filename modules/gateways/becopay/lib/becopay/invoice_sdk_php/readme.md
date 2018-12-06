## Becopay Payment Gateway

This is php library for becopay payment gateway

"php": ">=5.3.0"
### Installing
Pull this package via Composer.
```json
    {
        "require": {
            "becopay/invoice_sdk_php": "1.*"
        }
    }
```

or run in terminal: ```composer require becopay/invoice_sdk_php```


### Usage

**Create Class**<br>
First use the library with this namespace `use Becopay\PaymentGateway;`.<br>
for getting the constructor parameters, you first need to register on becopay website([register now](https://becopay.com/en/io/#api))<br>
If invalid data type class is entered throw an exception.
```php
use Becopay\PaymentGateway;

try {
    $payment = new PaymentGateway(
        'api service url',
        'api key',
        'mobile'
    );
} catch (Exception $e) {
	//Add yours exception handling
    echo $e->getMessage();
}
```
**Create payment redirect url**<br>
For creating the payment gateway url, use `create()` method.<br>
Type of parameter value must be string.<br>
If invalid data type class is entered throw an exception.
If response is successful it will return object and if not, it will return false<br>
`$payment->error` return the error message
```php
try {
    /*
     * This function is used to create an invoice.
     * Return result data type is object
	 *
	 *	Note: default value of `merchantCur`and `currency` currency is 'IRR'  
     */ 
    $invoice = $payment->create('order id','price','description','currency','merchantCur');
    if($invoice)
    {
        /*
        * Save invoice id in your database 
        * For checking the invoice status you need invoice id
        * Then redirect user to gateway url for doing the payment process
        */
       
		//echo the result
  		echo json_encode($invoice);

        //Get invoice id and insert to database
        /*
        	$invoiceId = $invoice->id;
       		echo 'invoice id:'.$invoiceId.'<br>';
        */

        //Get gateway url
        /*
        	$redirectUrl = $invoice->gatewayUrl;
        	echo 'gateway url'.$invoiceId.'<br>';
        */
    }else{
    	//Add your error handling
    	echo $payment->error;
    }
} catch (Exception $e) {
	//Add your exception handling
    echo $e->getMessage();
}
```
Response
```json
{
    "id": "ID29_61a5",
    "shopName": "New Shop",
    "status": "waiting",
    "remaining": 40,
    "payerAmount": 15000,
    "payerCur": "IRR",
    "merchantAmount": 15000,
    "merchantCur": "IRR",
    "date": "2018-10-13 06:45:36",
    "timestamp": 1539413136148,
    "timeout": 40,
    "description": "test payment",
    "gatewayUrl": "https://gateway.url.com/invoice/ID29_61a5",
    "callback": "http://www.your-website.com/invoice?orderid=12324320",
    "orderId": "12324320"
}
```

**Check invoice status with invoiceId**<br>
For checking the invoice status you can use `check()` method.<br>
If invalid data type is entered, throw an exception<br>
If response is successful it will return object and if not, it will return false<br>
`$payment->error` return the error message<br>
__Function parameter__
```php
check($invoiceId) // Set the Becopay InvoiceId
```
```php
      /*
       * Use this function to check the invoice status.
       * This function gets invoice id (which has been created by `create()` function) as parameter
       * and returns status of that
       */
try {
      /*
       * This function will be used on your callback page or when you want to check the invoice status.
       * 
       * First you must get orderId from path or query string using $_GET
       * then find related becopay Invoice id from your database
       * and use it to check the invoice status
       */
    
      //Check invoice with InvoiceId
      $fetchedInvoice = $payment->check($invoiceId);
      
      if($fetchedInvoice)
      {
      	/*
         * Insert your code here for updating order status
         */
    	if($fetchedInvoice->status == "success")
        {
        	// success msg
        }else{
        	//error msg
        }
        
        
		//echo the result
      	echo json_encode($fetchedInvoice);
        
        
      }else{
        //Add your error handling
        echo $payment->error;
      } 
} catch (Exception $e) {
	//Add your exception handling
    echo $e->getMessage();
}
```
Response
```json
{
    "id": "ID29_61a5",
    "shopName": "New Shop",
    "status": "success",
    "remaining": 30,
    "payerAmount": 15000,
    "payerCur": "IRR",
    "date": "2018-10-13 06:45:36",
    "timestamp": 1539413136148,
    "timeout": 40,
    "description": "test payment",
    "gatewayUrl": "https://gateway.url.com/invoice/ID29_61a5",
    "callback": "http://www.your-website.com/invoice?orderid=12324320",
    "orderId": "12324320"
}
```

**Check invoice status with orderId**<br>
For checking the invoice status with your orderId, you can use `checkByOrderId()` method.<br>
If invalid data type is entered, throw an exception<br>
If response is successful it will return object and if not, it will return false<br>
`$payment->error` return the error message<br>
__Function parameter__
```php
checkByOrderId($orderId) // Set the your orderId
```
```php
      /*
       * Use this function to check the invoice status.
       * This function gets invoice id (which has been created by `create()` function) as parameter
       * and returns status of that
       */
try {
      /*
       * This function will be used on your callback page or when you want to check the invoice status.
       * 
       * You must get orderId from path or query string using $_GET
       * and use it to check the invoice status
       */
    
      //Check invoice with OrderId
      $fetchedInvoice = $payment->checkByOrderId($orderId);
      
      if($fetchedInvoice)
      {
      	/*
         * Insert your code here for updating order status
         */
    	if($fetchedInvoice->status == "success")
        {
        	// success msg
        }else{
        	//error msg
        }
        
        
		//echo the result
      	echo json_encode($fetchedInvoice);
        
        
      }else{
        //Add your error handling
        echo $payment->error;
      } 
} catch (Exception $e) {
	//Add your exception handling
    echo $e->getMessage();
}
```
Response
```json
{
    "id": "ID29_61a5",
    "shopName": "New Shop",
    "status": "success",
    "remaining": 30,
    "payerAmount": 15000,
    "payerCur": "IRR",
    "merchantAmount": 15000,
    "merchantCur": "IRR",
    "date": "2018-10-13 06:45:36",
    "timestamp": 1539413136148,
    "timeout": 40,
    "description": "test payment",
    "gatewayUrl": "https://gateway.url.com/invoice/ID29_61a5",
    "callback": "http://www.your-website.com/invoice?orderid=12324320",
    "orderId": "12324320"
}
```

### License

This package is open-source software licensed under the [Apache License 2.0](https://github.com/becopay/Invoice_SDK_PHP/blob/master/LICENSE)

### Contact
For any questions, bugs, suggestions or feature requests, please use the Github issue system or submit a pull request.
When submitting an issue, always provide a detailed explanation of your problem with any response or feedback you get. Log those messages that might be relevant or a code example that demonstrates the problem. If none of this is available, we will most likely not be able to help you with your problem. Please review the contribution guidelines before submitting your issue or pull request.

For any other question, feel free to use the credentials listed below:

- Email: io@becopay.com
