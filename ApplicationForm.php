<?php
@session_start();

require_once 'FormsClass.php';
$input = new FormsClass();

$formname = 'Application Form';
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

	if( empty($_POST['First_Name']) ||
			empty($_POST['Last_Name']) ||
			empty($_POST['Phone_Number']) ||
			empty($_POST['Position_Applying_For']) ||
			empty($_POST['Email_Address'])) {


	$asterisk = '<span style="color:#FF0000; font-weight:bold;">*&nbsp;</span>';
	$prompt_message = '<div id="error-msg"><div class="message"><span>Failed to send email. Please try again.</span><br/><p class="error-close">x</p></div></div>';
	}
	else if(!preg_match("/^[_\.0-9a-zA-Z-]+@([0-9a-zA-Z][0-9a-zA-Z-]+\.)+[a-zA-Z]{2,6}$/i",stripslashes(trim($_POST['Email_Address']))))
		{ $prompt_message = '<div id="recaptcha-error"><div class="message"><span>Please enter a valid email address</span><br/><p class="rclose">x</p></div></div>';}
	else if(!$result_recaptcha->success){
		$prompt_message = '<div id="recaptcha-error"><div class="message"><span>Invalid <br>Recaptcha</span><p class="rclose">x</p></div></div>';
	}
	else{

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
		<div style="padding: 13px 30px 25px 30px;">
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
			

	

			// require_once 'swiftmailer/mail.php';
			// save data form on database
		include 'savedb.php';
		include 'send_email_curl.php';

		// save data form on database
		$subject2 = $formname ;
		$attachments = array();

		// when form has attachments, uncomment code below
		if(!empty($_FILES['attachment']['name'])){
			$attachmentsdir = ABSPATH.'onlineforms/attachments/';
			$validextensions = array('pdf', 'doc', 'docx', 'txt', 'jpg', 'jpeg', 'png', 'zip', 'rar'); // include file type here
			for($i = 0 ; $i < count($_FILES['attachment']['name']) ; $i++ ){

				$checkfile =  $attachmentsdir.$_FILES['attachment']['name'][$i];
				//$tobeuploadfile = $_FILES['attachment']['tmp_name'][$i];
				$tempfile = pathinfo($_FILES['attachment']['name'][$i]);
				if(in_array(strtolower($tempfile['extension']), $validextensions)){
					if(file_exists($checkfile)){
						$storedfile = $tempfile['filename'].'-'.time().'.'.$tempfile['extension'];
					}else{
						$storedfile = $_FILES['attachment']['name'][$i];
					}

					if( move_uploaded_file($_FILES['attachment']['tmp_name'][$i], $attachmentsdir.$storedfile) ){
						$attachments[] = $storedfile;
					}
				}
			}
		}

	 	//name of sender
		$name = $_POST['First_Name'] . ' '.$_POST['Last_Name'];
		$result = insertDB($name,$subject2,$body,$attachments);
		
		$parameter = array(
			'body' => $body,
			'from' => $from_email,
			'from_name' => $from_name,
			'to' => $to_email,
			'subject' => 'New Message Notification',	
			'attachment' => $attachments,
			'comb' => true
		);
		
		$success_msg = '<div id="success"><div class="message"><span>THANK YOU</span><br/> <span>for submitting an application.</span><br/><span>We\'ll get in touch with you shortly.</span><p class="close">x</p></div></div>';
		$error_msg = '<div id="error-msg"><div class="message"><span>Failed to send email. Please try again.</span><br/><p class="error-close">x</p></div></div>';

		$prompt_message = send_email($parameter, $success_msg, $error_msg);
		unset($_POST);

	}

}
/*************declaration starts here************/

?>
<!DOCTYPE html>
<html class="no-js" lang="en-US">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
		<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />
		<title><?php echo $formname; ?></title>
		<link rel="stylesheet" href="style.min.css?ver23asas">
			<link rel="stylesheet" href="css/media.min.css?ver24as">
			<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
			<link rel="stylesheet" type="text/css" href="css/dd.min.css" />
			<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.0.13/css/all.css">
			<link rel="stylesheet" href="css/font-awesome.min.css">
			<link rel="stylesheet" href="css/datepicker.min.css">
			<link rel="stylesheet" href="css/jquery.datepick.min.css" type="text/css" media="screen" />

			<link rel="stylesheet" href="css/proweaverPhone.css?ver=<?php echo time(); ?>">
			<link rel="stylesheet" href="css/flag.min.css" type="text/css"/>

		<script src='https://www.google.com/recaptcha/api.js'></script>
		<style>
				[type="radio"]:checked + label::after, [type="radio"]:not(:checked) + label::after {left: 3px;}
				[type="radio"]:checked + label::before, [type="radio"]:not(:checked) + label::before {left: 0;}
				.form_head {border-radius: 10px; }
				.form_head p.title_head:nth-child(1) { background: #ff3f3f;  margin: 0;  padding: 10px;  color: #fff;  font-weight: bold;  border-top-right-radius: 8px;  border-top-left-radius: 8px;}
				.form_head .form_box .form_box_col1 p { margin-bottom: 4px; }
				.form_head .form_box { margin: 0; padding: 25px 28px; border: 2px solid #ddd; border-top: none;  border-bottom-right-radius: 8px;  border-bottom-left-radius: 8px;}
				.field_holder.animated_box { font-weight: normal; }
 
			 
				@media only screen and (max-width: 780px) {
					.lbl .form_label {display:none; }
					.group.lbl {   margin-top: 10px; } 
					
				}
			</style>
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
							 

								<div class = "form_box">
									<div class = "form_box_col2">
										<div class="group">
											<?php
												$input->label('Full Name', '*');
												$input->fields('First Name', 'form_field','First_Name','placeholder="First Name"');
											?>
										</div>
										<div class="group lbl">
											<?php
												$input->label('&nbsp;', '');
												$input->fields('Last Name', 'form_field','Last_Name','placeholder="Last Name"');
											?>
										</div>
									</div>
								</div>

								<div class="form_box_col2">
									<div class="group">
										<?php
											$input->label('Email Address', '*');
											// @param field name, class, id and attribute
											$input->fields('Email_Address', 'form_field','Email_Address','placeholder="example@domain.com"');
										?>
									</div>
									<div class="group">
										<?php
												// @param label-name, if required
												$input->label('Phone Number', '*');
												// @param field name, class, id and attribute
												$input->phoneInput('Phone_Number', 'form_field','Phone_Number','placeholder="Enter phone number here"');
											?>
									</div>
								</div>

								<div class="form_box">
									<div class="form_box_col1">
										<div class="group">
											<?php
												$input->label('Position Applying For', '*');
												// @param field name, class, id and attribute
												$input->fields('Position_Applying_For', 'form_field','Position_Applying_For','placeholder="Enter position applying for here"');
											?>
										</div>
									</div>
								</div>
								<div class="form_box">
									<div class="form_box_col1">
										<div class="group">
											<?php
												// @param label-name, if required
												$input->label('Attach Resume <span style="font-style: italic; font-size: 13px; text-transform: lowercase; color:#b1b1b1;">(accepted file formats: .doc, .docx, .pdf | Max: 10MB)</span>', '*');
											?>
											<input type="file" name="attachment[]" id="file" class="input-file" >
											<label for="file" class="btn btn-tertiary js-labelFile">
												<span class="js-fileName">Choose a file</span>
												<span class="icon"><i class="fas fa-plus-circle"></i></span>
											</label>
										</div>
									</div>
								</div>
								
								<div id="error-message" class="valid_Extension_Message"><i class="fas fa-info-circle"></i><span>Upload Error!</span> <span class="suberror"> </span>  </div>
								 

								<div class="form_box">
									<div class="form_box_col1">
										<div class="group">
											<?php
												// @param label-name, if required
												$input->label('Message');
												// @param field name, class, id and attribute
												$input->textarea('Message', 'text form_field','Message','placeholder="Enter your message here"');
											?>
										</div>
									</div>
								</div>


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
	<script src="https://code.jquery.com/jquery-2.2.4.min.js"></script>
	<script type="text/javascript" src="js/jquery.validate.min.js"></script>
	<script type="text/javascript" src="js/jquery.datepick.min.js"></script>
	<script src="js/datepicker.js"></script>
	<script src = "js/plugins.js"></script>
	<script src = "js/jquery.mask.min.js"></script>
	<script src = "js/proweaverPhone.js"></script>

	<script type="text/javascript">
$(document).ready(function() {
	// validate signup form on keyup and submit
	$("#submitform").validate({
		rules: {
				First_Name: "required",
				Last_Name: "required",
				Phone_Number: "required",
				Position_Applying_For: "required",
				Email_Address: {
					required: true,
					email: true
				},
				"attachment[]": "required"
			},
			messages: {
				First_Name: "",
				Last_Name: "",
				Phone_Number: "",
				Position_Applying_For: "",
				Email_Address: "",
				"attachment[]": ""
			}
	});


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
$(function() {
	  $('.Date, .date').datepicker({
		autoHide: true,
		zIndex: 2048,
	  });
	});
	
	
</script>
</body>
</html>
