<?php
@session_start();
require_once 'FormsClass.php';
$input = new FormsClass();

$formname = 'Mortgage Pre-Qualification Form';
$prompt_message = '<span class="required-info">* Required Information</span>';
require_once 'config.php';
if ($_POST){
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL,"https://www.google.com/recaptcha/api/siteverify");
	curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_POSTFIELDS, "secret={$recaptcha_privite}&response={$_POST['g-recaptcha-response']}");
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	$server_output = curl_exec($ch);
	$result_recaptcha = json_decode($server_output);
	curl_close ($ch);

	if( empty($_POST['Name']) ||
		empty($_POST['City']) ||
		empty($_POST['Email_Address'])
		) {

	$asterisk = '<span style="color:#FF0000; font-weight:bold;">*&nbsp;</span>';
	$prompt_message = '<div id="error-msg"><div class="message"><span>Failed to send email. Please try again.</span><br/><p class="error-close">x</p></div></div>';
	}
	else if(!preg_match("/^[_\.0-9a-zA-Z-]+@([0-9a-zA-Z][0-9a-zA-Z-]+\.)+[a-zA-Z]{2,6}$/i",stripslashes(trim($_POST['Email_Address']))))
		{ $prompt_message = '<div id="recaptcha-error"><div class="message"><span>Please enter a valid email address</span><br/><p class="rclose">x</p></div></div>';}
	else if(!$result_recaptcha->success){
		$prompt_message = '<div id="recaptcha-error"><div class="message"><span>Invalid <br>Recaptcha</span><p class="rclose">x</p></div></div>';
	}
	else{

		$number =  $_POST['Social_Security_Number'];
		$mask =  str_pad(substr($number, -3), strlen($number), '*', STR_PAD_LEFT);
		$number2 =  $_POST['Social_Security_Number_'];
		$mask2 =  str_pad(substr($number2, -3), strlen($number2), '*', STR_PAD_LEFT);

		if (MAIL_TYPE == 1) {
			$formdisclaimer =  '<div style="position: relative; top: 10px; background: #eef5f8; padding: 15px 20px; border-radius: 5px; width: 660px; margin: 0 auto; text-align: center; font-family: Poppins,sans-serif; border: 1px solid #f9f9f9;  color: #6a6a6a !important;">  
					<span style="border-radius: 50%; height: 19px; display: inline-block; color: #f49d2c; font-size: 15px;   text-align: center;"></span> Please do not reply to this email. This is only a notification from your website online forms. 
					<br>To contact the person who filled out your online form, kindly use the email which is inside the form below.</div>';
		} else $formdisclaimer = '';
		

		$body =  '

				<div class="form_table" style="width:700px; height:auto; font-size:12px; color:#6a6a6a; letter-spacing:1px; margin: 0 auto; font-family: Poppins,sans-serif;">' . $formdisclaimer . '
				<div class="container" style="background: #fff; margin-top: 30px; font-family: Poppins,sans-serif; color:#6a6a6a; box-shadow: 10px 10px 31px -7px rgba(38,38,38,0.11); -webkit-box-shadow: 10px 10px 31px -7px rgba(38,38,38,0.11); -moz-box-shadow: 10px 10px 31px -7px rgba(38,38,38,0.11);  border-radius: 5px 5px 5px 5px; border: 1px solid #eee;">
					<div class="header" style="background: #a3c7d6; padding: 30px; border-radius: 5px 5px 0px 0px; ">
						<div align="left" style="font-size:22px; font-family: Poppins,sans-serif; color:#fff; font-weight: 900;">' . $formname . '</div>
						<div align="left" style=" color: #11465E;  font-size:19px; font-family: Poppins,sans-serif;  font-style: italic; margin-top: 6px; font-weight: 900;">' . COMP_NAME . '</div>
					</div>
				<div style="padding: 13px 30px 27px 30px;">
				<table border="0" cellpadding="0" cellspacing="0" width="100%" align="center" style="font-family: Poppins,sans-serif;font-size:14px; padding-bottom: 20px;">

					';
					foreach ($_POST as $key => $value) {
						if ($key == 'secode') continue;
						elseif ($key == 'submit') continue;
						elseif ($key == 'g-recaptcha-response') continue;

						if (!empty($value)) {
							$key2 = str_replace('_', ' ', $key);
							if ($value == ':') {
								$body .= ' <tr margin-bottom="10px"> <td colspan="5" height="28" class="OFDPHeading" width="100%" style=" background:#F0F0F0; margin-bottom:5px;"><b style="padding-left: 4px;">' . $key2 . '</b></td></tr>';
							}else if($key == "Checking_or_Savings"){
								$body .= '<tr><td class="Values1"colspan="2" height="28" align="left" width="45%" padding="100" style="padding-left: 4px;text-justify: inter-word;">
								<span style="position:relative !important;"><b>' . $key2 . '</b></span >:</td> <td class="Values2"colspan="2" height="28" align="left" width="45%" padding="10" style="line-height: 125%; position:static;"><span style="margin-top: 7px; position:relative;margin-left: 7px; border-collapse: collapse; display: inline-block;margin-bottom: 5px;margin-right: 7px;">$'. $_POST['Checking_or_Savings'] .'</span></td></tr>';
							
							}else if($key == "Stocks_or_Bonds"){
								$body .= '<tr><td class="Values1"colspan="2" height="28" align="left" width="45%" padding="100" style="padding-left: 4px;text-justify: inter-word;">
								<span style="position:relative !important;"><b>' . $key2 . '</b></span >:</td> <td class="Values2"colspan="2" height="28" align="left" width="45%" padding="10" style="line-height: 125%; position:static;"><span style="margin-top: 7px; position:relative;margin-left: 7px; border-collapse: collapse; display: inline-block;margin-bottom: 5px;margin-right: 7px;">$'. $_POST['Stocks_or_Bonds'] .'</span></td></tr>';

							}else if($key == "Equity_in_Real_Estate"){
								$body .= '<tr><td class="Values1"colspan="2" height="28" align="left" width="45%" padding="100" style="padding-left: 4px;text-justify: inter-word;">
								<span style="position:relative !important;"><b>' . $key2 . '</b></span >:</td> <td class="Values2"colspan="2" height="28" align="left" width="45%" padding="10" style="line-height: 125%; position:static;"><span style="margin-top: 7px; position:relative;margin-left: 7px; border-collapse: collapse; display: inline-block;margin-bottom: 5px;margin-right: 7px;">$'. $_POST['Equity_in_Real_Estate'] .'</span></td></tr>';

							}else if($key == "Checking_or_Savings_"){
								$body .= '<tr><td class="Values1"colspan="2" height="28" align="left" width="45%" padding="100" style="padding-left: 4px;text-justify: inter-word;">
								<span style="position:relative !important;"><b>' . $key2 . '</b></span >:</td> <td class="Values2"colspan="2" height="28" align="left" width="45%" padding="10" style="line-height: 125%; position:static;"><span style="margin-top: 7px; position:relative;margin-left: 7px; border-collapse: collapse; display: inline-block;margin-bottom: 5px;margin-right: 7px;">$'. $_POST['Checking_or_Savings_'] .'</span></td></tr>';

							}else if($key == "Stocks_or_Bonds_"){
								$body .= '<tr><td class="Values1"colspan="2" height="28" align="left" width="45%" padding="100" style="padding-left: 4px;text-justify: inter-word;">
								<span style="position:relative !important;"><b>' . $key2 . '</b></span >:</td> <td class="Values2"colspan="2" height="28" align="left" width="45%" padding="10" style="line-height: 125%; position:static;"><span style="margin-top: 7px; position:relative;margin-left: 7px; border-collapse: collapse; display: inline-block;margin-bottom: 5px;margin-right: 7px;">$'. $_POST['Stocks_or_Bonds_'] .'</span></td></tr>';

							}else if ($key == 'Social_Security_Number') {
								 $body .= '';

							}else if ($key == 'Social_Security_Number_') {
								 $body .= '';

							} else if($key == "Equity_in_Real_Estate_"){
								$body .= '<tr><td class="Values1"colspan="2" height="28" align="left" width="45%" padding="100" style="padding-left: 4px;text-justify: inter-word;">
								<span style="position:relative !important;"><b>' . $key2 . '</b></span >:</td> <td class="Values2"colspan="2" height="28" align="left" width="45%" padding="10" style="line-height: 125%; position:static;"><span style="margin-top: 7px; position:relative;margin-left: 7px; border-collapse: collapse; display: inline-block;margin-bottom: 5px;margin-right: 7px;">$'. $_POST['Equity_in_Real_Estate_'] .'</span></td></tr>';

							}else if($key == "Amount_Requested"){
								$body .= '<tr><td class="Values1"colspan="2" height="28" align="left" width="45%" padding="100" style="padding-left: 4px;text-justify: inter-word;">
								<span style="position:relative !important;"><b>' . $key2 . '</b></span >:</td> <td class="Values2"colspan="2" height="28" align="left" width="45%" padding="10" style="line-height: 125%; position:static;"><span style="margin-top: 7px; position:relative;margin-left: 7px; border-collapse: collapse; display: inline-block;margin-bottom: 5px;margin-right: 7px;">$'. $_POST['Amount_Requested'] .'</span></td></tr>';

							}else if($key == "Docu01"){
								$body .= '<tr style="display: block;height: 0px;"> <td colspan="5" height="0px" class="OFDPHeading" width="100%" style=" display: block; background:#F0F0F0;margin-top: 5px;margin-bottom:5px; height:0px;"><b style="padding-left: 4px; width: 90%;position: inherit;display: none;"></b></td></tr>';
						
							}else if($key == "Docu02"){
								$body .= '<tr style="display: block;height: 0px;"> <td colspan="5" height="0px" class="OFDPHeading" width="100%" style=" display: block; background:#F0F0F0;margin-top: 5px;margin-bottom:5px;height:0px;"><b style="padding-left: 4px; width: 90%;position: inherit;display: none;"></b></td></tr>';
						
							} else {
								$body .= '<tr><td class="Values1"colspan="2" height="28" align="left" width="40%" padding="100" style="line-height: normal; padding-left: 4px;text-justify: inter-word; word-wrap: anywhere; padding-right: 28px;">
								<span style="position:relative !important;"><b>' . $key2 . '</b></span >:</td> <td class="Values2"colspan="2" height="28" align="left" width="50%" padding="10" style="line-height: normal; word-wrap: anywhere; "><span style="margin-top: 7px; position:relative;margin-left: 7px; border-collapse: collapse; display: inline-block;margin-bottom: 5px;margin-right: 7px;">' . htmlspecialchars(trim($value), ENT_QUOTES) . '</span> </td></tr>';
						}
						}
					}
					$body .= '
					</table>
					</div>
					</div>';
			

		$body2 =  '

				<div class="form_table" style="width:700px; height:auto; font-size:12px; color:#6a6a6a; letter-spacing:1px; margin: 0 auto; font-family: Poppins,sans-serif;">' . $formdisclaimer . '
				<div class="container" style="background: #fff; margin-top: 30px; font-family: Poppins,sans-serif; color:#6a6a6a; box-shadow: 10px 10px 31px -7px rgba(38,38,38,0.11); -webkit-box-shadow: 10px 10px 31px -7px rgba(38,38,38,0.11); -moz-box-shadow: 10px 10px 31px -7px rgba(38,38,38,0.11);  border-radius: 5px 5px 5px 5px; border: 1px solid #eee;">
					<div class="header" style="background: #a3c7d6; padding: 30px; border-radius: 5px 5px 0px 0px; ">
						<div align="left" style="font-size:22px; font-family: Poppins,sans-serif; color:#fff; font-weight: 900;">' . $formname . '</div>
						<div align="left" style=" color: #11465E;  font-size:19px; font-family: Poppins,sans-serif;  font-style: italic; margin-top: 6px; font-weight: 900;">' . COMP_NAME . '</div>
					</div>
				<div style="padding: 13px 30px 27px 30px;">
				<table border="0" cellpadding="0" cellspacing="0" width="100%" align="center" style="font-family: Poppins,sans-serif;font-size:14px; padding-bottom: 20px;">

					';
		foreach($_POST as $key => $value){
			if($key == 'submit') continue;
			elseif($key == 'g-recaptcha-response') continue;
			elseif($key == 'checkboxVal') continue;
 


		if (!empty($value)) {
			$key2 = str_replace('_', ' ', $key);
			if ($value == ':') {
				$body2 .= ' <tr> <td colspan="5" height="28" class="OFDPHeading" width="100%" style=" background:#F0F0F0; margin-bottom:5px;"><b style="padding-left: 4px;">' . $key2 . '</b></td></tr>';
			}else if($key == "Checking_or_Savings"){
				$body2 .= '<tr><td class="Values1"colspan="2" height="28" align="left" width="45%" padding="100" style="padding-left: 4px;text-justify: inter-word;">
				<span style="position:relative !important;"><b>' . $key2 . '</b></span >:</td> <td class="Values2"colspan="2" height="28" align="left" width="45%" padding="10" style="line-height: 125%; position:static;"><span style="margin-top: 7px; position:relative;margin-left: 7px; border-collapse: collapse; display: inline-block;margin-bottom: 5px;margin-right: 7px;">$'. $_POST['Checking_or_Savings'] .'</span></td></tr>';
			
			}else if($key == "Stocks_or_Bonds"){
				$body2 .= '<tr><td class="Values1"colspan="2" height="28" align="left" width="45%" padding="100" style="padding-left: 4px;text-justify: inter-word;">
				<span style="position:relative !important;"><b>' . $key2 . '</b></span >:</td> <td class="Values2"colspan="2" height="28" align="left" width="45%" padding="10" style="line-height: 125%; position:static;"><span style="margin-top: 7px; position:relative;margin-left: 7px; border-collapse: collapse; display: inline-block;margin-bottom: 5px;margin-right: 7px;">$'. $_POST['Stocks_or_Bonds'] .'</span></td></tr>';

			}else if($key == "Equity_in_Real_Estate"){
				$body2 .= '<tr><td class="Values1"colspan="2" height="28" align="left" width="45%" padding="100" style="padding-left: 4px;text-justify: inter-word;">
				<span style="position:relative !important;"><b>' . $key2 . '</b></span >:</td> <td class="Values2"colspan="2" height="28" align="left" width="45%" padding="10" style="line-height: 125%; position:static;"><span style="margin-top: 7px; position:relative;margin-left: 7px; border-collapse: collapse; display: inline-block;margin-bottom: 5px;margin-right: 7px;">$'. $_POST['Equity_in_Real_Estate'] .'</span></td></tr>';

			}else if($key == "Checking_or_Savings_"){
				$body2 .= '<tr><td class="Values1"colspan="2" height="28" align="left" width="45%" padding="100" style="padding-left: 4px;text-justify: inter-word;">
				<span style="position:relative !important;"><b>' . $key2 . '</b></span >:</td> <td class="Values2"colspan="2" height="28" align="left" width="45%" padding="10" style="line-height: 125%; position:static;"><span style="margin-top: 7px; position:relative;margin-left: 7px; border-collapse: collapse; display: inline-block;margin-bottom: 5px;margin-right: 7px;">$'. $_POST['Checking_or_Savings_'] .'</span></td></tr>';

			}else if($key == "Stocks_or_Bonds_"){
				$body2 .= '<tr><td class="Values1"colspan="2" height="28" align="left" width="45%" padding="100" style="padding-left: 4px;text-justify: inter-word;">
				<span style="position:relative !important;"><b>' . $key2 . '</b></span >:</td> <td class="Values2"colspan="2" height="28" align="left" width="45%" padding="10" style="line-height: 125%; position:static;"><span style="margin-top: 7px; position:relative;margin-left: 7px; border-collapse: collapse; display: inline-block;margin-bottom: 5px;margin-right: 7px;">$'. $_POST['Stocks_or_Bonds_'] .'</span></td></tr>';

		}else if($key == "Social_Security_Number"){
			$body2 .= '<tr><td class="Values1"colspan="2" height="28" align="left" width="45%" padding="100" style="padding-left: 4px;text-justify: inter-word;">
			<span style="position:relative !important;"><b>' . $key2 . '</b></span >:</td> <td class="Values2"colspan="2" height="28" align="left" width="45%" padding="10" style="line-height: 125%; position:static;"><span style="margin-top: 7px; position:relative;margin-left: 7px; border-collapse: collapse; display: inline-block;margin-bottom: 5px;margin-right: 7px;">'. $mask .'</span></td></tr>';

		} else if ($key == 'Social_Security_Number_') {
			$body2 .= '<tr><td class="Values1"colspan="2" height="28" align="left" width="45%" padding="100" style="padding-left: 4px;text-justify: inter-word;">
			<span style="position:relative !important;"><b>' . $key2 . '</b></span >:</td> <td class="Values2"colspan="2" height="28" align="left" width="45%" padding="10" style="line-height: 125%; position:static;"><span style="margin-top: 7px; position:relative;margin-left: 7px; border-collapse: collapse; display: inline-block;margin-bottom: 5px;margin-right: 7px;">'. $mask2 .'</span></td></tr>';

			 } else if($key == "Equity_in_Real_Estate_"){
				$body2 .= '<tr><td class="Values1"colspan="2" height="28" align="left" width="45%" padding="100" style="padding-left: 4px;text-justify: inter-word;">
				<span style="position:relative !important;"><b>' . $key2 . '</b></span >:</td> <td class="Values2"colspan="2" height="28" align="left" width="45%" padding="10" style="line-height: 125%; position:static;"><span style="margin-top: 7px; position:relative;margin-left: 7px; border-collapse: collapse; display: inline-block;margin-bottom: 5px;margin-right: 7px;">$'. $_POST['Equity_in_Real_Estate_'] .'</span></td></tr>';

			}else if($key == "Amount_Requested"){
				$body2 .= '<tr><td class="Values1"colspan="2" height="28" align="left" width="45%" padding="100" style="padding-left: 4px;text-justify: inter-word;">
				<span style="position:relative !important;"><b>' . $key2 . '</b></span >:</td> <td class="Values2"colspan="2" height="28" align="left" width="45%" padding="10" style="line-height: 125%; position:static;"><span style="margin-top: 7px; position:relative;margin-left: 7px; border-collapse: collapse; display: inline-block;margin-bottom: 5px;margin-right: 7px;">$'. $_POST['Amount_Requested'] .'</span></td></tr>';

			}else if($key == "Docu01"){
				$body2 .= '<tr style="display: block;height: 0px;"> <td colspan="5" height="0px" class="OFDPHeading" width="100%" style=" display: block; background:#F0F0F0;margin-top: 5px;margin-bottom:5px; height:0px;"><b style="padding-left: 4px; width: 90%;position: inherit;display: none;"></b></td></tr>';
		
			}else if($key == "Docu02"){
				$body2 .= '<tr style="display: block;height: 0px;"> <td colspan="5" height="0px" class="OFDPHeading" width="100%" style=" display: block; background:#F0F0F0;margin-top: 5px;margin-bottom:5px;height:0px;"><b style="padding-left: 4px; width: 90%;position: inherit;display: none;"></b></td></tr>';
		
			} else {
				$body2 .= '<tr><td class="Values1"colspan="2" height="28" align="left" width="40%" padding="100" style="line-height: normal; padding-left: 4px;text-justify: inter-word; word-wrap: anywhere; padding-right: 28px;">
				<span style="position:relative !important;"><b>' . $key2 . '</b></span >:</td> <td class="Values2"colspan="2" height="28" align="left" width="50%" padding="10" style="line-height: normal; word-wrap: anywhere; "><span style="margin-top: 7px; position:relative;margin-left: 7px; border-collapse: collapse; display: inline-block;margin-bottom: 5px;margin-right: 7px;">' . htmlspecialchars(trim($value), ENT_QUOTES) . '</span> </td></tr>';
		}
		}
	}
	$body2 .= '
	</table>
	</div>
	</div>';

		// for email notification
		require_once 'config.php';
		include 'send_email_curl.php';

		// save data form on database
		include 'savedb.php';


		// save data form on database
		$subject = $formname ;
		$attachments = array();

		if (MAIL_TYPE == 1) {

		$name = $_POST['Name'];
		$result = insertDB($name,$subject,$body2,$attachments);

		$parameter = array(
			'body' => $body,
			'from' => $from_email,
			'from_name' => $from_name,
			'to' => $to_email,
			'subject' => 'New Message Notification',
			'attachment' => $attachments
		);

		$prompt_message = send_email($parameter);
		unset($_POST);

	} else if (MAIL_TYPE == 2) {

		$name = $_POST['Name'];
		$result = insertDB($name,$subject,$body2,$attachments);

		$parameter = array(
			'body' => $body2,
			'from' => $from_email,
			'from_name' => $from_name,
			'to' => $to_email,
			'subject' => 'New Message Notification',
			'attachment' => $attachments
		);

		$success_message =  '<div id="success"><div class="message"><span>THANK YOU</span><br/> <span>for submitting an application.</span><br/><span>We\'ll get in touch with you shortly.</span><p class="close">x</p></div></div>';

		$failed_message = '<div id="error-msg"><div class="message"><span>Failed to send email. Please try again.</span><br/><p class="error-close">x</p></div></div>';

		$prompt_message = send_email($parameter, $success_message, $failed_message);
		unset($_POST);

	}
	}

}
/*************declaration starts here************/
$state = array('Please select state.','Alabama','Alaska','Arizona','Arkansas','California','Colorado','Connecticut','Delaware','District Of Columbia','Florida','Georgia','Hawaii','Idaho','Illinois','Indiana','Iowa','Kansas','Kentucky','Louisiana','Maine','Maryland','Massachusetts','Michigan','Minnesota','Mississippi','Missouri','Montana','Nebraska','Nevada','New Hampshire','New Jersey','New Mexico','New York','North Carolina','North Dakota','Ohio','Oklahoma','Oregon','Pennsylvania','Puerto Rico','Rhode Island','South Carolina','South Dakota','Tennessee','Texas','Utah','Vermont','Virgin Islands','Virginia','Washington','West Virginia','Wisconsin','Wyoming');
?>
<!DOCTYPE html>
<html class="no-js" lang="en-US">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
		<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />
		<title><?php echo $formname; ?></title>
		 <link rel="stylesheet" href="css/intlTelInput.css">
		<link rel="stylesheet" href="style.min.css?ver23asas">
		<link rel="stylesheet" href="css/font-awesome.min.css">
		<link rel="stylesheet" href="css/media.min.css?ver24as">
		<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
		<link rel="stylesheet" type="text/css" href="css/dd.min.css" />
		<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.0.13/css/all.css">

		<link rel="stylesheet" href="css/datepicker.min.css">
		<link rel="stylesheet" href="css/jquery.datepick.min.css" type="text/css" media="screen" />

		<link rel="stylesheet" href="css/proweaverPhone.css?ver=<?php echo time(); ?>">
		<link rel="stylesheet" href="css/flag.min.css" type="text/css"/>

		<script src='https://www.google.com/recaptcha/api.js'></script>
	</head>
<body>
	<div class="clearfix">
		<div class = "wrapper">
			<div id = "contact_us_form_1" class = "template_form">
				<div class = "form_frame_b">
					<div class = "form_content">
						<?php if($testform):?><div class="test-mode"><i class="fas fa-info-circle"></i><span>You are in test mode!</span></div><?php endif;?>

						<form id="submitform" name="contact" method="post" enctype="multipart/form-data" action="">
							<?php echo $prompt_message; ?>
              <p class="strong_head">Borrower Information</p>
							<input type="hidden" name="Borrower Information" value=":">
							<div class="form_box">
								<div class="form_box_col2">
									<div class="group">
										<?php
											$input->label('Name', '*');
											// @param field name, class, id and attribute
											$input->fields('Name', 'form_field','Name','placeholder="Enter name here"');
										?>
									</div>
									<div class="group">
										<?php
											$input->label('Present Address', '*');
											// @param field name, class, id and attribute
											$input->fields('Present_Address', 'form_field','Present_Address','placeholder="Enter address here"');
										?>
									</div>
								</div>
							</div>

							<div class="form_box">
								<div class="form_box_col2" id="country">
									<div class="group">
										<?php $input->label('Country',''); ?>
										<select class="form_field" name="Country" readonly>
											<option value="Afghanistan">Afghanistan</option>
											<option value="Albania">Albania</option>
											<option value="Algeria">Algeria</option>
											<option value="Andorra">Andorra</option>
											<option value="Angola">Angola</option>
											<option value="Antigua and Barbuda">Antigua and Barbuda</option>
											<option value="Argentina">Argentina</option>
											<option value="Armenia">Armenia</option>
											<option value="Australia">Australia</option>
											<option value="Austria">Austria</option>
											<option value="Azerbaijan">Azerbaijan</option>
											<option value="Bahamas">Bahamas</option>
											<option value="Bahrain">Bahrain</option>
											<option value="Bangladesh">Bangladesh</option>
											<option value="Barbados">Barbados</option>
											<option value="Belarus">Belarus</option>
											<option value="Belgium">Belgium</option>
											<option value="Belize">Belize</option>
											<option value="Benin">Benin</option>
											<option value="Bhutan">Bhutan</option>
											<option value="Bolivia">Bolivia</option>
											<option value="Bosnia and Herzegovina">Bosnia and Herzegovina</option>
											<option value="Botswana">Botswana</option>
											<option value="Brazil">Brazil</option>
											<option value="Brunei">Brunei</option>
											<option value="Bulgaria">Bulgaria</option>
											<option value="Burkina Faso">Burkina Faso</option>
											<option value="Burundi">Burundi</option>
											<option value="Cabo Verde">Cabo Verde</option>
											<option value="Cambodia">Cambodia</option>
											<option value="Cameroon">Cameroon</option>
											<option value="Canada">Canada</option>
											<option value="Central African Republic (CAR)">Central African Republic (CAR)</option>
											<option value="Chad">Chad</option>
											<option value="Chile">Chile</option>
											<option value="China">China</option>
											<option value="Colombia">Colombia</option>
											<option value="Comoros">Comoros</option>
											<option value="Congo, Democratic Republic of the">Congo, Democratic Republic of the</option>
											<option value="Congo, Republic of the">Congo, Republic of the</option>
											<option value="Costa Rica">Costa Rica</option>
											<option value="Cote d'Ivoire">Cote d'Ivoire</option>
											<option value="Croatia">Croatia</option>
											<option value="Cuba">Cuba</option>
											<option value="Cyprus">Cyprus</option>
											<option value="Czechia">Czechia</option>
											<option value="Denmark">Denmark</option>
											<option value="Djibouti">Djibouti</option>
											<option value="Dominica">Dominica</option>
											<option value="Dominican Republic">Dominican Republic</option>
											<option value="Ecuador">Ecuador</option>
											<option value="Egypt">Egypt</option>
											<option value="El Salvador">El Salvador</option>
											<option value="Equatorial Guinea">Equatorial Guinea</option>
											<option value="Eritrea">Eritrea</option>
											<option value="Estonia">Estonia</option>
											<option value="Eswatini">Eswatini</option>
											<option value="Ethiopia">Ethiopia</option>
											<option value="Fiji">Fiji</option>
											<option value="Finland">Finland</option>
											<option value="France">France</option>
											<option value="Gabon">Gabon</option>
											<option value="Gambia">Gambia</option>
											<option value="Georgia">Georgia</option>
											<option value="Germany">Germany</option>
											<option value="Ghana">Ghana</option>
											<option value="Greece">Greece</option>
											<option value="Grenada">Grenada</option>
											<option value="Guatemala">Guatemala</option>
											<option value="Guinea">Guinea</option>
											<option value="Guinea-Bissau">Guinea-Bissau</option>
											<option value="Guyana">Guyana</option>
											<option value="Haiti">Haiti</option>
											<option value="Honduras">Honduras</option>
											<option value="Hungary">Hungary</option>
											<option value="Iceland">Iceland</option>
											<option value="India">India</option>
											<option value="Indonesia">Indonesia</option>
											<option value="Iran">Iran</option>
											<option value="Iraq">Iraq</option>
											<option value="Ireland">Ireland</option>
											<option value="Israel">Israel</option>
											<option value="Italy">Italy</option>
											<option value="Jamaica">Jamaica</option>
											<option value="Japan">Japan</option>
											<option value="Jordan">Jordan</option>
											<option value="Kazakhstan">Kazakhstan</option>
											<option value="Kenya">Kenya</option>
											<option value="Kiribati">Kiribati</option>
											<option value="Kosovo">Kosovo</option>
											<option value="Kuwait">Kuwait</option>
											<option value="Kyrgyzstan">Kyrgyzstan</option>
											<option value="Laos">Laos</option>
											<option value="Latvia">Latvia</option>
											<option value="Lebanon">Lebanon</option>
											<option value="Lesotho">Lesotho</option>
											<option value="Liberia">Liberia</option>
											<option value="Libya">Libya</option>
											<option value="Liechtenstein">Liechtenstein</option>
											<option value="Lithuania">Lithuania</option>
											<option value="Luxembourg">Luxembourg</option>
											<option value="Madagascar">Madagascar</option>
											<option value="Malawi">Malawi</option>
											<option value="Malaysia">Malaysia</option>
											<option value="Maldives">Maldives</option>
											<option value="Mali">Mali</option>
											<option value="Malta">Malta</option>
											<option value="Marshall Islands">Marshall Islands</option>
											<option value="Mauritania">Mauritania</option>
											<option value="Mauritius">Mauritius</option>
											<option value="Mexico">Mexico</option>
											<option value="Micronesia">Micronesia</option>
											<option value="Moldova">Moldova</option>
											<option value="Monaco">Monaco</option>
											<option value="Mongolia">Mongolia</option>
											<option value="Montenegro">Montenegro</option>
											<option value="Morocco">Morocco</option>
											<option value="Mozambique">Mozambique</option>
											<option value="Myanmar">Myanmar</option>
											<option value="Namibia">Namibia</option>
											<option value="Nauru">Nauru</option>
											<option value="Nepal">Nepal</option>
											<option value="Netherlands">Netherlands</option>
											<option value="New Zealand">New Zealand</option>
											<option value="Nicaragua">Nicaragua</option>
											<option value="Niger">Niger</option>
											<option value="Nigeria">Nigeria</option>
											<option value="North Korea">North Korea</option>
											<option value="North Macedonia">North Macedonia</option>
											<option value="Norway">Norway</option>
											<option value="Oman">Oman</option>
											<option value="Pakistan">Pakistan</option>
											<option value="Palau">Palau</option>
											<option value="Palestine">Palestine</option>
											<option value="Panama">Panama</option>
											<option value="Papua New Guinea">Papua New Guinea</option>
											<option value="Paraguay">Paraguay</option>
											<option value="Peru">Peru</option>
											<option value="Philippines">Philippines</option>
											<option value="Poland">Poland</option>
											<option value="Portugal">Portugal</option>
											<option value="Qatar">Qatar</option>
											<option value="Romania">Romania</option>
											<option value="Russia">Russia</option>
											<option value="Rwanda">Rwanda</option>
											<option value="Saint Kitts and Nevis">Saint Kitts and Nevis</option>
											<option value="Saint Lucia">Saint Lucia</option>
											<option value="Saint Vincent and the Grenadines">Saint Vincent and the Grenadines</option>
											<option value="Samoa">Samoa</option>
											<option value="San Marino">San Marino</option>
											<option value="Sao Tome and Principe">Sao Tome and Principe</option>
											<option value="Saudi Arabia">Saudi Arabia</option>
											<option value="Senegal">Senegal</option>
											<option value="Serbia">Serbia</option>
											<option value="Seychelles">Seychelles</option>
											<option value="Sierra Leone">Sierra Leone</option>
											<option value="Singapore">Singapore</option>
											<option value="Slovakia">Slovakia</option>
											<option value="Slovenia">Slovenia</option>
											<option value="Solomon Islands">Solomon Islands</option>
											<option value="Somalia">Somalia</option>
											<option value="South Africa">South Africa</option>
											<option value="South Korea">South Korea</option>
											<option value="South Sudan">South Sudan</option>
											<option value="Spain">Spain</option>
											<option value="Sri Lanka">Sri Lanka</option>
											<option value="Sudan">Sudan</option>
											<option value="Suriname">Suriname</option>
											<option value="Sweden">Sweden</option>
											<option value="Switzerland">Switzerland</option>
											<option value="Syria">Syria</option>
											<option value="Taiwan">Taiwan</option>
											<option value="Tajikistan">Tajikistan</option>
											<option value="Tanzania">Tanzania</option>
											<option value="Thailand">Thailand</option>
											<option value="Timor-Leste">Timor-Leste</option>
											<option value="Togo">Togo</option>
											<option value="Tonga">Tonga</option>
											<option value="Trinidad and Tobago">Trinidad and Tobago</option>
											<option value="Tunisia">Tunisia</option>
											<option value="Turkey">Turkey</option>
											<option value="Turkmenistan">Turkmenistan</option>
											<option value="Tuvalu">Tuvalu</option>
											<option value="Uganda">Uganda</option>
											<option value="Ukraine">Ukraine</option>
											<option value="United Arab Emirates">United Arab Emirates (UAE)</option>
											<option value="United Kingdom">United Kingdom (UK)</option>
											<option value="United States of America" selected>United States of America (USA)</option>
											<option value="Uruguay">Uruguay</option>
											<option value="Uzbekistan">Uzbekistan</option>
											<option value="Vanuatu">Vanuatu</option>
											<option value="Vatican City">Vatican City (Holy See)</option>
											<option value="Venezuela">Venezuela</option>
											<option value="Vietnam">Vietnam</option>
											<option value="Yemen">Yemen</option>
											<option value="Zambia">Zambia</option>
											<option value="Zimbabwe">Zimbabwe</option>
										</select>
									</div>
									<div class="group" id="prov">
										<?php $input->label('Province',''); ?>
										<select class="form_field" id="province" name="Province"></select>
									</div>
									<div class="group" id="city_con">
										<?php
											 $input->label('City','*');
											 $input->fields('City','form_field', 'city_usa','placeholder="Enter city here"');
										?>
									</div>
								</div>
								<div class="form_box_col2">
									<div class="group" id="muni_city">
										<?php $input->label('Municipality/City',''); ?>
										<select class="form_field" id="city" name="Municipality_or_City"></select>
									</div>
									<div class="group" id="zip_ph_con">
										<?php
											 $input->label('Zip Code','');
											 $input->fields('Zip_Code','form_field', 'zip_ph','placeholder="Enter zip code here"');
										?>
									</div>
								</div>
						  </div>
							<div class="form_box">
								<div class="form_box_col2 forZip">
									<div class="group" id="state_con">
										<?php
											$input->label('State','');
											$input->select('State','form_field', $state, 'state_usa');
										?>
									</div>
									<div class="group" id="zip_con">
										<?php
											 $input->label('Zip Code','*');
											 $input->fields('Zip_Code_','form_field', 'zip_usa','placeholder="Enter zip code here"');
										?>
									</div>
								</div>
							</div>
							<div class="form_box">
								<div class="form_box_col2">
									<div class="group">
										<?php
											$input->label('Social Security Number', '*');
											// @param field name, class, id and attribute
											$input->fields('Social_Security_Number', 'form_field','','placeholder="Enter social security number here"');
										?>
									</div>
									<div class="group">
										<?php
											$input->label('Birthdate', '*');
											// @param field name, class, id and attribute
											$input->fields('Birthdate', 'form_field Date','','placeholder="Enter date here"');
										?>
									</div>
								</div>
							</div>


							<div class="form_box">
								<div class="form_box_col2">
									<div class="group">
										<?php
											$input->label('Contact Number', '*');
											// @param field name, class, id and attribute
											$input->fields('Contact_Number', 'form_field','','placeholder="Enter contact number here"');
										?>
									</div>
									<div class="group">
										<?php
											$input->label('Email Address', '*');
											// @param field name, class, id and attribute
											$input->fields('Email_Address', 'form_field','','placeholder="example@domain.com"');
										?>
									</div>
								</div>
							</div>

								<br>
							<p class="strong_head">Co-Borrower Information</p>
							<input type="hidden" name="Co-Borrower Information" value=":">
							<div class="form_box">
								<div class="form_box_col1">
									<div class="group">
										<?php
											$input->label('Name', '');
											// @param field name, class, id and attribute
											$input->fields('Name_', 'form_field','Name','placeholder="Enter name here"');
										?>
									</div>
								</div>
							</div>

							<div class="form_box">
								<div class="form_box_col2">
									<div class="group">
										<?php
											$input->label('Present Address', '');
											// @param field name, class, id and attribute
											$input->fields('Present_Address_', 'form_field','Present_Address_','placeholder="Enter address here"');
										?>
									</div>
									<div class="group">
										<?php
											$input->label('City', '');
											// @param field name, class, id and attribute
											$input->fields('City_', 'form_field','','placeholder="Enter city here"');
										?>
									</div>
								</div>
							</div>
							<div class="form_box">
								<div class="form_box_col2">
									<div class="group">
										<?php
											$input->label('State', '');
											// @param field name, class, id and attribute
											$input->select('State_', 'form_field', $state);
										?>
									</div>
									<div class="group">
										<?php
											$input->label('Zip Code', '');
											// @param field name, class, id and attribute
											$input->fields('Zip_Code__', 'form_field','','placeholder="Enter zip code here"');
										?>
									</div>
								</div>
							</div>
							<div class="form_box">
								<div class="form_box_col2">
									<div class="group">
										<?php
											$input->label('Social Security Number', '');
											// @param field name, class, id and attribute
											$input->fields('Social_Security_Number_', 'form_field','','placeholder="Enter social security number here"');
										?>
									</div>
									<div class="group">
										<?php
											$input->label('Birthdate', '');
											// @param field name, class, id and attribute
											$input->fields('Birthdate_', 'form_field Date','','placeholder="Enter date here"');
										?>
									</div>
								</div>
							</div>


							<div class="form_box">
								<div class="form_box_col2">
									<div class="group">
										<?php
											$input->label('Contact Number', '');
											// @param field name, class, id and attribute
											$input->fields('Contact_Number_', 'form_field','','placeholder="Enter contact number here"');
										?>
									</div>
									<div class="group">
										<?php
											$input->label('Email Address', '');
											// @param field name, class, id and attribute
											$input->fields('Email_Address_', 'form_field','','placeholder="example@domain.com"');
										?>
									</div>
								</div>
							</div>

								<br>
							<p class="strong_head">Financial Information (Borrower)</p>
							<input type="hidden" name="Financial Information (Borrower)" value=":">

							<p style="display:none;"> <input type="checkbox" name="Docu01"  >Income </p>


							<p style="font-weight:bold; font-size:20px; margin-top: 15px;">Income</p>
							<input type="hidden" name="Income" value=":">

							<div class="form_box">
								<div class="form_box_col2">
									<div class="group">
										<?php
											$input->label('Base (per hr/wk/month)', '');
											// @param field name, class, id and attribute
											$input->fields('Base', 'form_field','','placeholder="Enter base here"');
										?>
									</div>
									<div class="group">
										<?php
											$input->label('Bonuses/Overtime', '');
											// @param field name, class, id and attribute
											$input->fields('Bonuses_or_Overtime', 'form_field','','placeholder="Enter bonuses or overtime here"');
										?>
									</div>
								</div>
							</div>
							<div class="form_box">
								<div class="form_box_col2">
									<div class="group">
										<?php
											$input->label('Commissions', '');
											// @param field name, class, id and attribute
											$input->fields('Commissions', 'form_field','','placeholder="Enter commissions here"');
										?>
									</div>
									<div class="group">
										<?php
											$input->label('Dividends/Interest', '');
											// @param field name, class, id and attribute
											$input->fields('Dividends_or_Interest', 'form_field','','placeholder="Enter dividends or Interest here"');
										?>
									</div>
								</div>
							</div>
							<div class="form_box">
								<div class="form_box_col2">
									<div class="group">
										<?php
											$input->label('Other', '');
											// @param field name, class, id and attribute
											$input->fields('_Other', 'form_field','','placeholder="Enter other here"');
										?>
									</div>
									<div class="group">
										<?php
											$input->label('Total', '');
											// @param field name, class, id and attribute
											$input->fields('_Total', 'form_field','','placeholder="Enter total here"');
										?>
									</div>
								</div>
							</div>

								<br>
							<p style="font-weight:bold; font-size:20px;">Assets</p>
							<input type="hidden" name="Assets" value=":">
							<div class="form_box">
								<div class="form_box_col2">
									<div class="group">
										<?php
											$input->label('Checking/Savings', '');
											// @param field name, class, id and attribute
											$input->fields('Checking_or_Savings', 'form_field','','placeholder="Enter checking or saving here"');
										?>
									</div>
									<div class="group">
										<?php
											$input->label('Stocks/Bonds', '');
											// @param field name, class, id and attribute
											$input->fields('Stocks_or_Bonds', 'form_field','','placeholder="Enter stocks or bonds here"');
										?>
									</div>
								</div>
							</div>
							<div class="form_box">
								<div class="form_box_col2">
									<div class="group">
										<?php
											$input->label('Equity in Real Estate', '');
											// @param field name, class, id and attribute
											$input->amount('Equity_in_Real_Estate', 'form_field','','placeholder="Enter equity here"');
										?>
									</div>
									<div class="group">
										<?php
											$input->label('Other', '');
											// @param field name, class, id and attribute
											$input->fields('Other_', 'form_field','','placeholder="Enter other here"');
										?>
									</div>
								</div>
							</div>
							<div class="form_box">
								<div class="form_box_col1">
									<div class="group">
										<?php
											$input->label('Total', '');
											// @param field name, class, id and attribute
											$input->fields('__Total', 'form_field','','placeholder="Enter total here"');
										?>
									</div>
								</div>
							</div>

								<br>
							<p style="font-weight:bold; font-size:20px;">Liabilities</p>
							<input type="hidden" name="Liabilities" value=":">
							<div class="form_box">
								<div class="form_box_col2">
									<div class="group">
										<?php
											$input->label('Mortgage/Rent', '');
											// @param field name, class, id and attribute
											$input->fields('Mortgage_or_Rent', 'form_field','','placeholder="Enter morgage/rent here"');
										?>
									</div>
									<div class="group">
										<?php
											$input->label('Auto Loan(s)', '');
											// @param field name, class, id and attribute
											$input->fields('Auto_Loan(s)', 'form_field','','placeholder="Enter auto loan(s) here"');
										?>
									</div>
								</div>
							</div>
							<div class="form_box">
								<div class="form_box_col2">
									<div class="group">
										<?php
											$input->label('Installment Loan(s)', '');
											// @param field name, class, id and attribute
											$input->fields('Installment_Loan(s)', 'form_field','','placeholder="Enter installment loan(s) here"');
										?>
									</div>
									<div class="group">
										<?php
											$input->label('Credit Card', '');
											// @param field name, class, id and attribute
											$input->fields('Credit_Card', 'form_field','','placeholder="Enter credit card here"');
										?>
									</div>
								</div>
							</div>
							<div class="form_box">
								<div class="form_box_col2">
									<div class="group">
										<?php
											$input->label('Credit Card', '');
											// @param field name, class, id and attribute
											$input->fields('Credit_Card_', 'form_field','','placeholder="Enter credit card here"');
										?>
									</div>
									<div class="group">
										<?php
											$input->label('Credit Card', '');
											// @param field name, class, id and attribute
											$input->fields('Credit_Card__', 'form_field','','placeholder="Enter credit card here"');
										?>
									</div>
								</div>
							</div>
							<div class="form_box">
								<div class="form_box_col2">
									<div class="group">
										<?php
											$input->label('Alimony', '');
											// @param field name, class, id and attribute
											$input->fields('Alimony', 'form_field','','placeholder="Enter alimony here"');
										?>
									</div>
									<div class="group">
										<?php
											$input->label('Child care/Support', '');
											// @param field name, class, id and attribute
											$input->fields('Child_care_or_Support', 'form_field','','placeholder="Enter child care/support here"');
										?>
									</div>
								</div>
							</div>
							<div class="form_box">
								<div class="form_box_col2">
									<div class="group">
										<?php
											$input->label('Student Loans', '');
											// @param field name, class, id and attribute
											$input->fields('Student_Loans', 'form_field','','placeholder="Enter student loans here"');
										?>
									</div>
									<div class="group">
										<?php
											$input->label('Other', '');
											// @param field name, class, id and attribute
											$input->fields('_Other___', 'form_field','','placeholder="Enter other here"');
										?>
									</div>
								</div>
							</div>
							<div class="form_box">
								<div class="form_box_col1">
									<div class="group">
										<?php
											$input->label('Total', '');
											// @param field name, class, id and attribute
											$input->fields('__Total___', 'form_field','','placeholder="Enter total here"');
										?>
									</div>
								</div>
							</div>

							<br>
							<p class="strong_head">Financial Information (Co-borrower)</p>
							<input type="hidden" name="Financial Information (Co-borrower)" value=":">

							<p  style="display:none;"> <input type="checkbox" name="Docu02" >Income </p>

							<p style="font-weight:bold; font-size:20px; margin-top: 15px;">Income</p>
							<input type="hidden" name="Income_" value=":">

							<div class="form_box">
								<div class="form_box_col2">
									<div class="group">
										<?php
											$input->label('Base (per hr/wk/month)', '');
											// @param field name, class, id and attribute
											$input->fields('Base_', 'form_field','','placeholder="Enter base here"');
										?>
									</div>
									<div class="group">
										<?php
											$input->label('Bonuses/Overtime', '');
											// @param field name, class, id and attribute
											$input->fields('Bonuses_or_Overtime_', 'form_field','','placeholder="Enter bonuses or overtime here"');
										?>
									</div>
								</div>
							</div>
							<div class="form_box">
								<div class="form_box_col2">
									<div class="group">
										<?php
											$input->label('Commissions', '');
											// @param field name, class, id and attribute
											$input->fields('Commissions_', 'form_field','','placeholder="Enter commissions here"');
										?>
									</div>
									<div class="group">
										<?php
											$input->label('Dividends/Interest', '');
											// @param field name, class, id and attribute
											$input->fields('Dividends_or_Interest_', 'form_field','','placeholder="Enter dividends or Interest here"');
										?>
									</div>
								</div>
							</div>
							<div class="form_box">
								<div class="form_box_col2">
									<div class="group">
										<?php
											$input->label('Other', '');
											// @param field name, class, id and attribute
											$input->fields('_Other_', 'form_field','','placeholder="Enter other here"');
										?>
									</div>
									<div class="group">
										<?php
											$input->label('Total', '');
											// @param field name, class, id and attribute
											$input->fields('_Total_____', 'form_field','','placeholder="Enter total here"');
										?>
									</div>
								</div>
							</div>

							<br>

							<p style="font-weight:bold; font-size:20px;">Assets</p>
							<input type="hidden" name="Assets_" value=":">
							<div class="form_box">
								<div class="form_box_col2">
									<div class="group">
										<?php
											$input->label('Checking/Savings', '');
											// @param field name, class, id and attribute
											$input->amount('Checking_or_Savings_', 'form_field','Checking_or_Savings_','placeholder="Enter checking or saving here"');
										?>
									</div>
									<div class="group">
										<?php
											$input->label('Stocks/Bonds', '');
											// @param field name, class, id and attribute
											$input->amount('Stocks_or_Bonds_', 'form_field','Stocks_or_Bonds_','placeholder="Enter stocks or bonds here"');
										?>
									</div>
								</div>
							</div>
							<div class="form_box">
								<div class="form_box_col2">
									<div class="group">
										<?php
											$input->label('Equity in Real Estate', '');
											// @param field name, class, id and attribute
											$input->amount('Equity_in_Real_Estate_', 'form_field','Equity_in_Real_Estate_','placeholder="Enter equity here"');
										?>
									</div>
									<div class="group">
										<?php
											$input->label('Other', '');
											// @param field name, class, id and attribute
											$input->fields('Other______', 'form_field','','placeholder="Enter other here"');
										?>
									</div>
								</div>
							</div>
							<div class="form_box">
								<div class="form_box_col1">
									<div class="group">
										<?php
											$input->label('Total', '');
											// @param field name, class, id and attribute
											$input->fields('____Total_', 'form_field','','placeholder="Enter total here"');
										?>
									</div>
								</div>
							</div>
								<br>
							<p style="font-weight:bold; font-size:20px;">Liabilities</p>
							<input type="hidden" name="Liabilities_" value=":">
							<div class="form_box">
								<div class="form_box_col2">
									<div class="group">
										<?php
											$input->label('Mortgage/Rent', '');
											// @param field name, class, id and attribute
											$input->fields('Mortgage_or_Rent__', 'form_field','','placeholder="Enter morgage/rent here"');
										?>
									</div>
									<div class="group">
										<?php
											$input->label('Auto Loan(s)', '');
											// @param field name, class, id and attribute
											$input->fields('Auto_Loan(s)__', 'form_field','','placeholder="Enter auto loan(s) here"');
										?>
									</div>
								</div>
							</div>
							<div class="form_box">
								<div class="form_box_col2">
									<div class="group">
										<?php
											$input->label('Installment Loan(s)', '');
											// @param field name, class, id and attribute
											$input->fields('Installment_Loan(s)__', 'form_field','','placeholder="Enter installment loan(s) here"');
										?>
									</div>
									<div class="group">
										<?php
											$input->label('Credit Card', '');
											// @param field name, class, id and attribute
											$input->fields('Credit_Card______', 'form_field','','placeholder="Enter credit card here"');
										?>
									</div>
								</div>
							</div>
							<div class="form_box">
								<div class="form_box_col2">
									<div class="group">
										<?php
											$input->label('Credit Card', '');
											// @param field name, class, id and attribute
											$input->fields('Credit_Card________', 'form_field','','placeholder="Enter credit card here"');
										?>
									</div>
									<div class="group">
										<?php
											$input->label('Credit Card', '');
											// @param field name, class, id and attribute
											$input->fields('_____Credit_Card__', 'form_field','','placeholder="Enter credit card here"');
										?>
									</div>
								</div>
							</div>
							<div class="form_box">
								<div class="form_box_col2">
									<div class="group">
										<?php
											$input->label('Alimony', '');
											// @param field name, class, id and attribute
											$input->fields('Alimony_', 'form_field','','placeholder="Enter alimony here"');
										?>
									</div>
									<div class="group">
										<?php
											$input->label('Child care/Support', '');
											// @param field name, class, id and attribute
											$input->fields('Child_care_or_Support_', 'form_field','','placeholder="Enter child care/support here"');
										?>
									</div>
								</div>
							</div>
							<div class="form_box">
								<div class="form_box_col2">
									<div class="group">
										<?php
											$input->label('Student Loans', '');
											// @param field name, class, id and attribute
											$input->fields('Student_Loans___', 'form_field','','placeholder="Enter student loans here"');
										?>
									</div>
									<div class="group">
										<?php
											$input->label('Other', '');
											// @param field name, class, id and attribute
											$input->fields('____Other___', 'form_field','','placeholder="Enter other here"');
										?>
									</div>
								</div>
							</div>
							<div class="form_box">
								<div class="form_box_col1">
									<div class="group">
										<?php
											$input->label('Total', '');
											// @param field name, class, id and attribute
											$input->fields('_____Total___', 'form_field','','placeholder="Enter total here"');
										?>
									</div>
								</div>
							</div>
								<br>
							<p style="font-weight:bold; font-size:20px;">AMOUNT AND TERMS OF MORTGAGE REQUEST</p>
							<input type="hidden" name="Amount and Terms of Mortgage Request" value=":">
							<div class="form_box">
								<div class="form_box_col2">
									<div class="group">
										<?php
											$input->label('Amount Requested', '');
											// @param field name, class, id and attribute
											$input->amount('Amount_Requested', 'form_field','','placeholder="Enter amount here"');
										?>
									</div>
									<div class="group">
										<?php
											$input->label('Desired Payment', '');
											// @param field name, class, id and attribute
											$input->fields('Desired_Payment', 'form_field','','placeholder="Enter desired payment here"');
										?>
									</div>
								</div>
							</div>
							<div class="form_box">
								<div class="form_box_col1">
									<div class="group">
										<?php
											$input->label('Mortgage Term', '');
											// @param field name, class, id and attribute
											$input->radio('Mortgage_Term', array('30 Year Fixed Rate',' 15 Year Fixed Rate',' 7 Year ARM'),'','','3');
										?>
									</div>
								</div>
							</div>



					<br>


							<div class = "form_box5 secode_box">
								<div class = "group">
									<div class="inner_form_box1 recapBtn">
										<div class="g-recaptcha" data-sitekey="<?php echo $recaptcha_sitekey; ?>"></div>
										<div class="btn-submit"><input type = "submit" class = "form_button" value = "SUBMIT" /></div>
									</div>
								</div>
							</div>
						</form>
					</div>
				</div>
			</div>
		</div>
	</div>
		<?php $input->phone(true); ?>
	<script src="https://code.jquery.com/jquery-3.3.1.min.js" ></script>
	<script type="text/javascript" src="js/city_state.min.js"></script>
	<script type="text/javascript" src="js/addressFunctionality.min.js"></script>
	<script type="text/javascript" src="js/jquery.validate.min.js"></script>
	<script type="text/javascript" src="js/jquery.datepick.min.js"></script>
	<script src="js/datepicker.js"></script>
	<script src = "js/plugins.min.js"></script>
	<script src = "js/jquery.mask.min.js"></script>
	<script src = "js/proweaverPhone.js"></script>
	<script>
		window.onload = function() {
		// ---------------
		// basic usage
		// ---------------
		var $ = new City();
		$.showProvinces("#province");
		$.showCities("#city");
		// ------------------
		// additional methods
		// -------------------
		// will return all provinces
		// console.log($.getProvinces());
		// // will return all cities
		// console.log($.getAllCities());
		// // will return all cities under specific province (e.g Batangas)
		// console.log($.getCities("Batangas"));
	}
	</script>
	<script type="text/javascript">
$(document).ready(function() {
	// validate signup form on keyup and submit
	$("#submitform").validate({
		rules: {
			Name: "required",
			Present_Address: "required",
			Street: "required",
			City: "required",
			Zip_Code_: "required",
			Social_Security_Number: "required",
			Birthdate: "required",
			Contact_Number: "required",
			Email_Address:{
				required: true,
				email: true
			},
			Email_Address_: {email:true}
		},
		messages: {
			Name: "",
			Present_Address: "",
			Street: "",
			City: "",
			Zip_Code_: "",
			Social_Security_Number: "",
			Birthdate: "",
			Contact_Number: "",
			Email_Address: "",
			Email_Address_: ""
		}
	});


	// Jquery Dependency

	$("input[data-type='currency']").on({
	    keyup: function() {
	      formatCurrency($(this));
	    },
	    blur: function() {
	      formatCurrency($(this), "blur");
	    }
	});


	function formatNumber(n) {
	  return n.replace(/\D/g, "").replace(/\B(?=(\d{3})+(?!\d))/g, ",")
	}


	function formatCurrency(input, blur) {
	  var input_val = input.val();
	  if (input_val === "") { return; }
	  var original_len = input_val.length;
	  var caret_pos = input.prop("selectionStart");
	  if (input_val.indexOf(".") >= 0) {
	    var decimal_pos = input_val.indexOf(".");
	    var left_side = input_val.substring(0, decimal_pos);
	    var right_side = input_val.substring(decimal_pos);
	    left_side = formatNumber(left_side);
	    right_side = formatNumber(right_side);

	    if (blur === "blur") {
	      right_side += "00";
	    }
	    right_side = right_side.substring(0, 2);
	    input_val = left_side + "." + right_side;

	  } else {
	    input_val = formatNumber(input_val);
	    input_val = input_val;

	    // final formatting
	    if (blur === "blur") {
	      input_val += ".00";
	    }
	  }

	  // send updated string to input
	  input.val(input_val);

	  // put caret back in the right position
	  var updated_len = input_val.length;
	  caret_pos = updated_len - original_len + caret_pos;
	  input[0].setSelectionRange(caret_pos, caret_pos);
	}




	$("#submitform").submit(function(){
		if($(this).valid()){
			$('.load_holder').css('display','block');
			self.parent.$('html, body').animate(
				{ scrollTop: self.parent.$('#myframe').offset().top },
				500
			);
		}
		if(grecaptcha.getResponse() == "") {
			var $recaptcha = document.querySelector('#g-recaptcha-response');
				$recaptcha.setAttribute("required", "required");
				$('.g-recaptcha').addClass('errors').attr('id','recaptcha');
		  }
	});

	$( "input" ).keypress(function( event ) {
		if(grecaptcha.getResponse() == "") {
			var $recaptcha = document.querySelector('#g-recaptcha-response');
			$recaptcha.setAttribute("required", "required");
			$('.g-recaptcha').addClass('errors').attr('id','recaptcha');
		  }
	});

		$('.Date').datepicker();
		$('.Date').attr('autocomplete', 'off');

});

</script>
</body>
</html>
