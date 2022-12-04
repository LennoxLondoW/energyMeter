<?php
if (!defined('__path__')) {
    define('__path__', isset($__path__) ? $__path__ : "../");
}
require_once __path__ . 'app/app.php';
class Validation extends App
{
    // lets validate username 
    public function is_correct_name($input)
    {
        $input = strip_tags($input);
        return preg_match("/^[a-zA-Z-' ]*$/", $input);
    }

    public function is_alphanumeral($input)
    {
        $input = strip_tags($input);
        // echo preg_match('/[^a-z0-9 ]/i', $input)
        return preg_match('/[^a-z0-9 ]/i', $input)? false: true;
    }

    // lets validate email 
    public function is_correct_email($input)
    {
        $input = strip_tags($input);
        return filter_var($input, FILTER_VALIDATE_EMAIL);
    }

    // lets validate password 
    public function is_correct_password($input)
    {
        $input = strip_tags($input);
        return  preg_match('@[A-Z]@', $input) && //uppercase
            preg_match('@[a-z]@', $input) && //lowercase
            preg_match('@[0-9]@', $input) && //number
            preg_match('@[^\w]@', $input); //specual character
    }

     // lets validate phone number 
     public function is_correct_phoneNumber($input)
     {
         $input = strip_tags($input);
         return filter_var($input, FILTER_SANITIZE_NUMBER_INT) && strlen($input)>=6 && strlen($input)<15 && $input >= 0;
         
     }

}
