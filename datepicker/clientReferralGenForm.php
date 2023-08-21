<?php
@session_start();
require_once 'FormsClass.php';
$input = new FormsClass();

$formname = 'Refer a Client Form';
$prompt_message = '<span class="required-info">* Required Information</span>';
require_once 'config.php';
if ($_POST){
	if(empty($_POST['Date_of_Referral']) ||
		empty($_POST['Referrers_Name']) ||
		empty($_POST['Referrers_Email']) ||
		empty($_POST['Referrers_Phone']) ||
		empty($_POST['Name'])) {


	$asterisk = '<span style="color:#FF0000; font-weight:bold;">*&nbsp;</span>';
	$prompt_message = '<div id="error-msg"><div class="message"><span>Required Fields are empty</span><br/><p class="error-close">x</p></div></div>';
	}
	else if(!preg_match("/^[_\.0-9a-zA-Z-]+@([0-9a-zA-Z][0-9a-zA-Z-]+\.)+[a-zA-Z]{2,6}$/i",stripslashes(trim($_POST['Email']))) && !preg_match("/^[_\.0-9a-zA-Z-]+@([0-9a-zA-Z][0-9a-zA-Z-]+\.)+[a-zA-Z]{2,6}$/i",stripslashes(trim($_POST['Referrers_Email']))))
		{ $prompt_message = '<div id="recaptcha-error"><div class="message"><span>Please enter a valid email address</span><br/><p class="rclose">x</p></div></div>';}
	else if(empty($_POST['g-recaptcha-response'])){
		$prompt_message = '<div id="recaptcha-error"><div class="message"><span>Invalid recaptcha</span><br/><p class="rclose">x</p></div></div>';
	}else{

		$body = '<div class="form_table" style="width:700px; height:auto; font-size:12px; color:#333333; letter-spacing:1px; line-height:20px; margin: 0 auto;">
			<div style="border:8px double #c3c3d0; padding:12px;">
			<div align="center" style="color:#990000; font-style:italic; font-size:20px; font-family:Arial; margin:bottom: 15px;">('.$formname.')</div>

			<table width="90%" cellspacing="2" cellpadding="5" align="center" style="font-family:Verdana; font-size:13px">
				';

			foreach($_POST as $key => $value){
				if($key == 'secode') continue;
				elseif($key == 'submit') continue;
				elseif($key == 'g-recaptcha-response') continue;

				if(!empty($value)){
					$key2 = str_replace('_', ' ', $key);
					if($value == ':') {
						$body .= '<tr><td colspan="2" style="background:#F0F0F0; line-height:30px"><b>'.$key2.'</b></td></tr>';
					}else {
						$body .= '<tr><td><b>'.$key2.'</b>:</td> <td>'.htmlspecialchars(trim($value), ENT_QUOTES).'</td></tr>';
					}
				}
			}
			$body .= '
			</table>

			</div>
			</div>';

		// for email notification
		require_once 'config.php';
		require_once 'swiftmailer/mail.php';

		// save data form on database
		include 'savedb.php';

		// save data form on database
		$subject = $formname ;
		$attachments = array();

		$name = $_POST['Referrers_Name'];
		$result = insertDB($name,$subject,$body,$attachments);

		$templateVars = array('{link}' => get_home_url().'/onlineforms/'.$_SESSION['token'], '{company}' => COMP_NAME);

		Mail::Send($template, 'New Message Notification', $templateVars, $to_email, $to_name, $from_email, $from_name, $cc, $bcc);


		if($result){
			$prompt_message = '<div id="success"><div class="message"><span>THANK YOU</span><br/> <span>for sending us a message!</span><br/><span>We will be in touch with you soon.</span><p class="close">x</p></div></div>';
				unset($_POST);
		}else {
			$prompt_message = '<div id="error-msg"><div class="message"><span>Failed to send email. Please try again.</span><br/><p class="error-close">x</p></div></div>';
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

		<!--[if IE]><meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1"><![endif]-->
		<link rel="stylesheet" href="style.min.css?ver23asas">
		<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.0.13/css/all.css">
		<link rel="stylesheet" href="css/media.min.css?ver24as">
		<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
		<link rel="stylesheet" type="text/css" href="css/dd.min.css" />

		<link rel="stylesheet" href="css/datepicker.min.css">
		<link rel="stylesheet" href="css/jquery.datepick.min.css" type="text/css" media="screen" />
		
			<link rel="stylesheet" href="css/proweaverPhone.css" type="text/css"/>
		<link rel="stylesheet" href="css/flag.min.css" type="text/css"/>

		<script src='https://www.google.com/recaptcha/api.js'></script>
		<style>
			.information, .information2{background: #effdff; color: #444444; font-weight: bold;}
			.information:before{content: url(images/clientgen-icon.png)!important;}

			@media only screen and (max-width : 780px) {
				.form_box.left{width: 100%;}
				.information, .information2{margin: 10px auto !important;}
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
							<div class="form_box">
								<div class="form_box_col2">
									<div class="group">
										<?php
											$input->label('Date of Referral');
											$input->datetoday('Date_of_Referral', '');
										?>
									</div>
									<?php
											// @param field name, required, class, replaceholder, rename, id, attrib, value
										$input->masterfield('Referrer\'s Name', '*', 'form_field','Enter referrer\'s name here','Referrers_Name');
									?>
								</div>
							</div>
						<div class="form_box">
								<div class="form_box_col2">
									<div class="group">
										<?php
											$input->label('Referrer\'s Email', '*');
											// @param field name, class, id and attribute
											$input->fields('Referrers_Email', 'form_field','Referrers_Email','placeholder="example@example.com"');
										?>
									</div>
									<div class="group">
										<?php
											$input->label('Referrer\'s Phone', '*');
											// @param field name, class, id and attribute
											$input->phoneInput('Referrers_Phone', 'form_field','Referrers_Phone','placeholder="Enter number here"');
										?>
									</div>
								</div>
							</div>
							<div class="form_box">
								<div class="form_box_col1">
									<?php
									// @param field name, required, class, replaceholder, rename, id, attrib, value
										$input->label('Contact Preference');
									?>
									<?php
									// @param field name, required, class, replaceholder, rename, id, attrib, value
										$input->radio('Contact_Preference',array('Contact by email','Contact by phone'),'Contact_Preference','','2');
									?>
								</div>
							</div>
							<div style="clear:both;"></div>

							<div class="strong_head">
								Client Details
								<input type="hidden" name="Client Details" value=":"/>
							</div>
							<div class="form_box">
								<div class="form_box_col2">
									<?php
									// @param field name, required, class, replaceholder, rename, id, attrib, value
										$input->masterfield('Name', '*', 'form_field','Enter name here','Name');
									?>
									<?php
										// @param field name, required, class, replaceholder, rename, id, attrib, value
										$input->masterfield('Address 1', '', 'form_field','Enter address 1 here','Address_1');
									?>
								</div>
							</div>
							<div class="form_box">
								<div class="form_box_col2">
									<?php
									// @param field name, required, class, replaceholder, rename, id, attrib, value
										$input->masterfield('Address Line 2', '', 'form_field','Enter address line 2 here','Address_2');
									?>
									<?php
										// @param field name, required, class, replaceholder, rename, id, attrib, value
										$input->masterfield('City', '', 'form_field','Enter city here','City');
									?>
								</div>
							</div>
							<div class="form_box">
								<div class="form_box_col2">
									<?php
									// @param field name, required, class, replaceholder, rename, id, attrib, value
										$input->masterselect('State', '', 'form_field', $state);
									?>
									<?php
										// @param field name, required, class, replaceholder, rename, id, attrib, value
										$input->masterfield('Zip', '', 'form_field','Enter zip here','Zip');
									?>
								</div>
							</div>
								<div class="form_box">
								<div class="form_box_col2">
								<div class="group">
										<?php
											$input->label('Email', '*');
											// @param field name, class, id and attribute
											$input->fields('Email', 'form_field','Email','placeholder="example@example.com"');
										?>
									</div>
									<div class="group">
										<?php
											// @param label-name, if required
											$input->label('Phone Number', '');
											// @param field name, class, id and attribute
											$input->phoneInput('Phone_Number', 'form_field','Phone_Number','placeholder="Enter phone number here"');
										?>
									</div>
								</div>
							</div>
							<div class="form_box">
								<div class="form_box_col1">
									<?php
										// @param field name, required, class, replaceholder, rename, id, attrib, value
										$input->masterfield('Service Request', '*', 'form_field','Enter service request here','Service_Request');
									?>
								</div>
							</div>
							<div class="form_box">
								<div class="form_box_col1">
									<?php
									// @param field name, required, class, replaceholder, rename, id, attrib, value
										$input->mastertextarea('Your statement/reason for referral', '*', 'form_field','Enter statement/reason for referral here','Statement_or_reason_for_referral');
									?>
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
	</div><?php $input->phone(true); ?>
	<script src="https://code.jquery.com/jquery-2.2.4.min.js"></script>
	<script type="text/javascript" src="js/jquery.validate.min.js"></script>
	<script type="text/javascript" src="js/jquery.datepick.min.js"></script>
	<script src="js/datepicker.js"></script>
	<script src = "js/plugins.min.js"></script>
	<script src = "js/jquery.mask.min.js"></script>
	<script src = "js/proweaverPhone.js"></script>


	<script type="text/javascript">
$(document).ready(function() {
	// validate signup form on keyup and submit
	$("#submitform").validate({
		rules: {
			Referrers_Name: "required",
			Referrers_Phone: "required",
			Name: "required",
			Service_Request: "required",
			Statement_or_reason_for_referral: "required",
			Referrers_Email: {
				required: true,
				email: true
			},
			Email: {
				required: true,
				email: true
			}
		},
		messages: {
			Date_of_Referral: "",
			Referrers_Name: "",
			Referrers_Phone: "",
			Service_Request: "",
			Statement_or_reason_for_referral: "",
			Name: "",
			Referrers_Email: "",
			Email: "",
		}
	});
	$("#submitform").submit(function(){
		if($(this).valid()){
			self.parent.$('html, body').animate(
				{ scrollTop: self.parent.$('#myframe').offset().top },
				500
			);
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
