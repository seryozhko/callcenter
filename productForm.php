<?php
$settings = require_once 'settings.php';

$allowed_emails = explode(',', $settings["global"]["allowed_users"]);
if(isset($_POST['email']) AND in_array($_POST['email'],$allowed_emails))
{
    // echo file_get_contents($settings["global"]["product_form"]);
    echo "ok";
}