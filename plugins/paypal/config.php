<?php
// configuration file
$__path__ = '../../';
require_once '../../app/app.php';
require_once "vendor/autoload.php";
$app = new App();
//check connection to database
$app->use_database();
$app->release_database();
//paypal base url 
define("paypal_base",scheme.$_SERVER['SERVER_NAME'].base_path."paypal/" );
// define("paypal_base","https://www.anchortrends.com/plugins/paypal/" );


use Omnipay\Omnipay;
//get this from paypal developers
define('CLIENT_ID', paypal_client_id);
define('CLIENT_SECRET', paypal_client_secret);
define('PAYPAL_RETURN_URL', paypal_base ."success.php".(  isset($_POST['client_id']) && !empty($_POST['client_id'])  ?"?client_id=".$_POST['client_id']:""));
define('PAYPAL_CANCEL_URL', (paypal_base."cancel.php"));
define('PAYPAL_CURRENCY', 'USD'); // set your currency here
$gateway = Omnipay::create('PayPal_Rest');
$gateway->setClientId(CLIENT_ID);
$gateway->setSecret(CLIENT_SECRET);
$gateway->setTestMode(paypal_is_test_mode); //set it to 'false' when go live

     
?>

