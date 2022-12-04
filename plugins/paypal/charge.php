<?php
//paypal configuration file
require_once 'config.php';
if (isset($_POST['honey_pot']) && !empty($_POST['honey_pot']))
{
    die("error");
} 


if (isset($_POST['amount'])) 
{
    if(!is_numeric($_POST['amount'])){
        die("Enter correct amount");
    }
    $amount = htmlspecialchars($_POST['amount']);
    //send to paypal  
    try 
    {
        $response = $gateway->purchase(array(
            'amount' => $amount,
            'currency' => PAYPAL_CURRENCY,
            'returnUrl' => PAYPAL_RETURN_URL,
            'cancelUrl' => PAYPAL_CANCEL_URL,
        ))->send();

        if ($response->isRedirect()) 
        {
            $response->redirect(); // this will automatically forward the customer
        } 
        else 
        {
            // not successful
            echo $response->getMessage();
        }
    } 
    catch(Exception $e) 
    {
        echo $e->getMessage();
    }   
       
   
}

?>

