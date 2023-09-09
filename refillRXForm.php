<?php
@session_start();
require_once 'FormsClass.php';
$input = new FormsClass();

$formname = 'Refill Prescription Form';
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

	if(empty($_POST['Last_Name']) ||
		empty($_POST['First_Name']) ||
		empty($_POST['Phone_Number'])) {


	$asterisk = '<span style="color:#FF0000; font-weight:bold;">*&nbsp;</span>';
	$prompt_message = '<div id="error-msg"><div class="message"><span>Required Fields are empty</span><br/><p class="error-close">x</p></div></div>';
	}
	else if(!$result_recaptcha->success){
		$prompt_message = '<div id="recaptcha-error"><div class="message"><span>Invalid <br>Recaptcha</span><p class="rclose">x</p></div></div>';
	}else{

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

				foreach($_POST as $key => $value){
					if($key == 'submit') continue;
					elseif($key == 'g-recaptcha-response') continue;

					if(!empty($value)){
						$key2 = str_replace('_', ' ', $key);
						if($value == ':') {
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
		

		 // for email notification
		require_once 'config.php';
		include 'send_email_curl.php';

		// save data form on database
		include 'savedb.php';


		// save data form on database
		$subject = $formname ;
		$attachments = array();


		$name = $_POST['First_Name'].' '.$_POST['Last_Name'];
		$result = insertDB($name,$subject,$body,$attachments);

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
	}

}
/*************declaration starts here************/
$choices = array('- Please Select -','No, thanks','Yes, via phone');
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

		<link rel="stylesheet" href="css/proweaverPhone.css?ver=<?php echo time(); ?>">
		<link rel="stylesheet" href="css/flag.min.css" type="text/css"/>

		<script src='https://www.google.com/recaptcha/api.js'></script>
			<style>
			.amount, .fldicon{
			  padding: 10px 65px;
			}
			.req {color: #f00; font-size: 20px;}
	
			.fa-dollar-sign::before {content: "\f155";position: relative;left: 13px;top: 5px;}
 #icon_num {position: absolute;padding: 10px 15px 10px 10px;background: #ececec;height: 63px;color: #8e8c8c;font-size: 15px;line-height:40px; width:60px;text-align: center;font-weight:bold;border-radius: 15px 0 0 15px !important;}
			.form_head {border-radius: 10px; }
			.form_head p.title_head:nth-child(1) { background: #c1c1c1;  margin: 0;  padding: 10px;  color: #fff;  font-weight: bold;  border-top-right-radius: 8px;  border-top-left-radius: 8px;}
			.form_head .form_box .form_box_col1 p { margin-bottom: 4px; }
			.mrg0 { margin: 0; }
			.form_head .form_box { margin: 0; padding: 25px 28px; border: 2px solid #ddd; border-top: none;  border-bottom-right-radius: 8px;  border-bottom-left-radius: 8px;}
			.grouping .form_box_col1 { margin: 0 0 20px 0; }
			@media only screen and (max-width : 780px) {
				.form_box.left{width: 100%;}
				.amount, .fldicon{margin-bottom: 10px; padding: 10px 0 10px 65px;}
				.mrg0{margin-top:20px;}
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
						<p class="strong_head" >Who is this prescription for?</p><input type="hidden" name="This_Prescription_is_for" value=":" />
					</div>

							<div class="form_box">
								<div class="form_box_col2">
									<?php
									// @param field name, required, class, replaceholder, rename, id, attrib, value
										$input->masterfield('Last Name', '*', 'form_field','Enter last name here','Last_Name');
									?>
									<?php
										// @param field name, required, class, replaceholder, rename, id, attrib, value
										$input->masterfield('First Name', '*', 'form_field','Enter first name here','First_Name');
									?>
								</div>
							</div>
							<div class="form_box left">
								<div class="form_box_col1">
									<div class="group">
									<?php
									$input->label('Phone Number', '*');
									// @param field name, required, class, replaceholder, rename, id, attrib, value
										$input->phoneInput('Phone_Number', 'form_field','Phone_Number','placeholder="Enter phone number here"');
									?>
									</div>
								</div>
							</div>
							<div class="clearfix"></div>

					<div class="form_box" style="margin: 20px 0 0 0;">
						<p class="strong_head">RX REFILL NUMBERS </p><input type="hidden" name="RX_Refill_Numbers" value=":" />
					</div>

							<div class="form_box mrg0">
								<div class="form_box_col2">
									<div class="group">
										<?php
										// @param field name, required, class, replaceholder, rename, id, attrib, value
											$input->masterfieldicon('01 <span class="req">*</span>','', '*', 'form_field','Enter RX refill number here','Rx_1');
										?>
									</div>
									<div class="group">
										<?php
										// @param field name, required, class, replaceholder, rename, id, attrib, value
											$input->masterfieldicon('02','', '*', 'form_field','Enter RX refill number here','Rx_2');
										?>
									</div>
								</div>
							</div>
							<div class="form_box">
								<div class="form_box_col2">
									<div class="group">
										<?php
										// @param field name, required, class, replaceholder, rename, id, attrib, value
											$input->masterfieldicon('03','', '*', 'form_field','Enter RX refill number here','Rx_3');
										?>
									</div>
									<div class="group">
											<?php
											// @param field name, required, class, replaceholder, rename, id, attrib, value
												$input->masterfieldicon('04','', '*', 'form_field','Enter RX refill number here','Rx_4');
											?>
									</div>
								</div>
							</div>
					<div class="form_box">
						<p class="strong_head">ADD MORE PRESCRIPTIONS <span style="color:#000; font-size:15px; font-weight:normal;">(OVER THE COUNTER ITEM)</span></p><input type="hidden" name="More Prescriptions" value=":" />
					</div>

							<br>
									<div class="form_head grouping">
										<p class="title_head"></p>
										<div class="form_box">
											<div class="form_box_col2">
												<div class="group">
												<?php
													$input->label('1', '*');
													// @param field name, required, class, replaceholder, rename, id, attrib, value
													$input->fields('1)_Prescription_Name', 'form_field', '1)_Prescription_Name', 'placeholder="Enter prescription here"');
												?>
												</div>
												<div class="group">
												<?php
													$input->label('&nbsp;');
													// @param field name, required, class, replaceholder, rename, id, attrib, value
													$input->fields('_Quantity', 'form_field', '_Quantity', 'placeholder="Enter quantity here"');
												?>
												</div>
											</div>
											<div class="form_box_col2">
												<div class="group">
												<?php
													$input->label('2');
													// @param field name, required, class, replaceholder, rename, id, attrib, value
													$input->fields('2)_Prescription_Name', 'form_field', '2)_Prescription_Name', 'placeholder="Enter prescription here"');
												?>
												</div>
												<div class="group">
												<?php
													$input->label('&nbsp;');
													// @param field name, required, class, replaceholder, rename, id, attrib, value
													$input->fields('__Quantity', 'form_field', '__Quantity', 'placeholder="Enter quantity here"');
												?>
												</div>
											</div>
											<div class="form_box_col2">
												<div class="group">
												<?php
													$input->label('3');
													// @param field name, required, class, replaceholder, rename, id, attrib, value
													$input->fields('3)_Prescription_Name', 'form_field', '3)_Prescription_Name', 'placeholder="Enter prescription here"');
												?>
												</div>
												<div class="group">
												<?php
													$input->label('&nbsp;');
													// @param field name, required, class, replaceholder, rename, id, attrib, value
													$input->fields('___Quantity', 'form_field', '___Quantity', 'placeholder="Enter quantity here"');
												?>
												</div>
											</div>
											<div class="form_box_col2">
												<div class="group">
												<?php
													$input->label('4');
													// @param field name, required, class, replaceholder, rename, id, attrib, value
													$input->fields('4)_Prescription_Name', 'form_field', '4)_Prescription_Name', 'placeholder="Enter prescription here"');
												?>
												</div>
												<div class="group">
												<?php
													$input->label('&nbsp;');
													// @param field name, required, class, replaceholder, rename, id, attrib, value
													$input->fields('____Quantity', 'form_field', '____Quantity', 'placeholder="Enter quantity here"');
												?>
												</div>
											</div>
											<div class="cloneField">
											<div class="form_box_col2">
												<div class="group">
												<?php
													$input->label('5');
													// @param field name, required, class, replaceholder, rename, id, attrib, value
													$input->fields('5)_Prescription_Name', 'form_field', '5)_Prescription_Name', 'placeholder="Enter prescription here"');
												?>
												</div>
												<div class="group">
												<?php
													$input->label('&nbsp;');
													// @param field name, required, class, replaceholder, rename, id, attrib, value
													$input->fields('_____Quantity', 'form_field', 'Quantity_____', 'placeholder="Enter quantity here"');
												?>
												</div>
											</div>
											</div>

										</div>

									</div>



							<div class="form_box">
								<div class="form_box_col1">
									<div class="group">
									<?php
										$input->label('Pick up or Delivery','*');
										// @param field name, required, class, replaceholder, rename, id, attrib, value
										$input->radio('Pick_up_or_Delivery',array('Pickup','Delivery'),'','','2');
									?>
									</div>
								</div>
							</div>
							<div class="form_box">
								<div class="form_box_col1">
									<div class="group">
									<?php
										$input->label('Would you like us to notify you when your prescription(s) are ready?');
										// @param field name, required, class, replaceholder, rename, id, attrib, value
										$input->select('Notify when prescription(s) are ready','form_field',$choices);
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
			Last_Name: "required",
			First_Name: "required",
			Rx_1: "required",
			'1)_Prescription_Name': "required",
			Quantity_: "required",
			Pick_up_or_Delivery: "required",
			Phone_Number: "required"
		},
		messages: {
			Last_Name: "",
			First_Name: "",
			Rx_1: "",
			'1)_Prescription_Name': "",
			Quantity_: "",
			Pick_up_or_Delivery: "",
			Phone_Number: ""
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

			var cloneCount = 1;

			$('.addMorepres').click(function(){

				var html = $('.cloneField').html();

				$('.addprescription').prepend("<div class='clone_data' id='mainCloneCount_0'>"+html+"<a href='javascript:;' style=' background: #f95858;     padding: 3px 5px; color: #fff; border-radius: 3px; position: relative; bottom: 0;' class='removeCls' onclick='removeHTML()'><i class='fas fa-minus-circle'></i> Remove</a></div>");

				$('.addprescription').each(function(){
					var i = 0;
					$(this).find('.clone_data').each(function(){
						i = parseInt(i + 1);
						$(this).attr('id','mainCloneCount_'+i);
						$(this).find('.Prescription_Name_5 input').attr('name', 'Prescription_Name_5'+i);
						$(this).find('.Prescription_Qty_5 input').attr('name', 'Prescription_Qty_5'+i);
						$(this).find('.removeCls').attr('onClick', 'removeHTML('+i+')');
					});
				});


			});
			 $("#Qty_1, #Qty_2, #Qty_3, #Qty_4, #Qty_5").keypress(function(e) {
					var verified = (e.which == 8 || e.which == undefined || e.which == 0) ? null : String.fromCharCode(e.which).match(/[^0-9]/);
					if (verified) {e.preventDefault();}
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

		function removeHTML(id){
	$('#mainCloneCount_'+id).remove();
}

	</script>
</body>
</html>
