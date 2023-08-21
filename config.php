<?php
ini_set('display_errors', 'off');
error_reporting(E_ALL);
define('COMP_EMAIL', 'onlineform@mail.com'); // clients email

define('SMTP_ENCRYPTION', 'off'); // TLS, SSL or off
define('SMTP_PORT', 587); // SMPT port number 587 or default
define('COMP_NAME', 'Company Name'); // company name
define('MAIL_TYPE', 2); // 1 - html, 2 - txt
define('MAIL_DOMAIN', 'companyname.com'); // company domain
define('DEV', true); //if false = launched account , true = pages account

// Update it using a working google Site key
$recaptcha_sitekey = '6LeIxAcTAAAAAJcZVRqyHh71UMIEGNQ_MXjiZKhI';
// Update it using a working google Privite key
$recaptcha_privite = '6LeIxAcTAAAAAGG-vFI1TnRWxMZNFuojJ4WifJWe';

// do not edit
$subject = COMP_NAME . " [" . $formname . "]";
$template = 'template';
$to_name = NULL;
$from_email = 'noreply@companydomain.com';
$from_name = 'Message From Your Site';
$attachments = array();

// testing here
$testform = false;
if($testform){
	$to_email 	= array('testemail@mail.com');
	$cc = '';
	$bcc = '';
}else{
	$to_email 	= array('clientemail@mail.com');
	$cc = '';
	$bcc = '';
}
