<?php
@session_start();
require_once 'FormsClass.php';
$input = new FormsClass();

$formname = 'Online Job Order Form';
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

	if( empty($_POST['Company']) ||
		empty($_POST['Contact_Person']) ||
    	empty($_POST['Phone_Number']) ||
   		empty($_POST['Job_Location']) ||
		empty($_POST['Email_Address'])) {

	$asterisk = '<span style="color:#FF0000; font-weight:bold;">*&nbsp;</span>';
	$prompt_message = '<div id="error-msg"><div class="message"><span>Required Fields are empty</span><br/><p class="error-close">x</p></div></div>';
	}
	else if(!preg_match("/^[_\.0-9a-zA-Z-]+@([0-9a-zA-Z][0-9a-zA-Z-]+\.)+[a-zA-Z]{2,6}$/i",stripslashes(trim($_POST['Email_Address']))))
		{ $prompt_message = '<div id="recaptcha-error"><div class="message"><span>Please enter a valid email address</span><br/><p class="rclose">x</p></div></div>';}
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
						}else if($key == "Employee_Rate"){
							$body .= '<tr><td class="Values1"colspan="2" height="28" align="left" width="45%" padding="100" style="padding-left: 4px;text-justify: inter-word;">
							<span style="position:relative !important;"><b>' . $key2 . '</b></span >:</td> <td class="Values2"colspan="2" height="28" align="left" width="45%" padding="10" style="line-height: 125%; position:static;"><span style="margin-top: 7px; position:relative;margin-left: 7px; border-collapse: collapse; display: inline-block;margin-bottom: 5px;margin-right: 7px;">$'. $_POST['Employee_Rate'] .'</span></td></tr>';
						
												} else {
							$body .= '<tr><td class="Values1"colspan="2" max-height="100px" height="28" align="left" width="40%" padding="100" style="line-height: normal; padding-left: 4px;text-justify: inter-word; word-wrap: anywhere; padding-right: 28px;">
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


		$name = $_POST['Contact_Person'];
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
		<link rel="stylesheet" href="css/font-awesome.min.css">
		<link rel="stylesheet" href="css/media.min.css?ver24as">
		<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
		<link rel="stylesheet" type="text/css" href="css/dd.min.css" />
		<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.0.13/css/all.css">
		<link rel="stylesheet" type="text/css" href="assets/css/bootstrap.css">
		<link rel="stylesheet" type="text/css" href="dist/bootstrap-clockpicker.min.css">
		<link rel="stylesheet" type="text/css" href="assets/css/github.min.css">

		<link rel="stylesheet" href="css/datepicker.min.css">
		<link rel="stylesheet" href="css/jquery.datepick.min.css" type="text/css" media="screen" />

		<link rel="stylesheet" href="css/proweaverPhone.css?ver=<?php echo time(); ?>">
		<link rel="stylesheet" href="css/flag.min.css" type="text/css"/>

		<script src='https://www.google.com/recaptcha/api.js'></script>
		<style>


			.information, .information2{background: #fee7e3; color: #444444; position: relative;font-weight: bold; padding: 20px 20px 20px 50px; border-radius: 5px; margin: 20px auto 10px;}
			.information:before{position: absolute; left: 10px; top: 15px; content: url(images/info-reTranspo-icon.png)!important;}
			.information2:before{position: absolute; left: 10px; top: 15px; content: url(images/wage-icon.png)!important;}
			.radio tr td{width:33%; margin-right:0;}
			.radio tr td:last-child {width: 33%; margin-right: 0; }

			.amount{ padding: 10px 75px; }
			#icon { position: absolute; padding: 14px 33px 0px 10px; background: #c9c9c9; height: 63px; color: #fff; font-size: 31px; border-top-left-radius: 15px; border-bottom-left-radius: 15px; }
			.fa-dollar-sign::before { content: "\f155"; position: relative; left: 11px;top: 2px;}

			@media only screen and (max-width : 500px) {
				.amount {padding: 9px 10px 9px 68px;}
			}

			@media only screen and (min-width: 110px) and (max-width : 1490px) {
				.radio tr td{width: 33%; margin-right: 0;}
			}
			@media only screen and (max-width : 430px) {
				.radio tr td, .radio tr td:last-child{width:100%; display:block;}
			}

			.load_holder {     position: fixed;     z-index: 2;     background: rgba(0,0,0,0.3);     width: 100%;     height: 100%;     top: 0;     left: 0; }

			.radio tr td {margin: 5px 0;}

		</style>
	</head>
<body>
	<div class="clearfix">
		<div class = "wrapper">
			<div id = "contact_us_form_1" class = "template_form">
				<div class = "form_frame_b">
					<div class = "form_content">
					<form id="submitform" name="contact" method="post" enctype="multipart/form-data" action="">
					<?php if($testform):?><div class="test-mode"><i class="fas fa-info-circle"></i><span>You are in test mode!</span></div><?php endif;?>
					<?php echo $prompt_message; ?>

							<div class="form_box">
								<div class="form_box_col1">
									<?php
										// @param field name, required, class, replaceholder, rename, id, attrib, value
										$input->masterfield('Company', '*', 'form_field','Enter company here','Company');
									?>
								</div>
							</div>
							<div class="form_box">
								<div class="form_box_col2">
									<div class="group">
									<?php
										$input->label('Contact Person', '*');
										// @param field name, class, id and attribute
										$input->fields('Contact_Person', 'form_field','','placeholder="Enter contact person here"');
									?>
									</div>
										<div class="group">
									<?php
										$input->label('Phone Number', '*');
										// @param field name, class, id and attribute
										$input->phoneInput('Phone_Number', 'form_field','Phone_Number','placeholder="Enter phone number here"');
									?>
									</div>
								</div>
							</div>
							<div class="form_box">
								<div class="form_box_col2">
									<div class="group">
										<?php
											$input->label('Fax Number', '');
											// @param field name, class, id and attribute
										?>
										<input type="text" class="form_field" name="Fax_Number" onkeypress="return isNumberKey(event)" placeholder='Enter number here'>
									</div>
									<?php
										// @param field name, required, class, replaceholder, rename, id, attrib, value
										$input->masterfield('Email Address', '*', 'form_field','example@domain.com','Email_Address');
									?>
								</div>
							</div>
							<div class="form_box">
								<div class="form_box_col2">
									<?php
										// @param field name, required, class, replaceholder, rename, id, attrib, value
										$input->masterfield('Job Location', '*', 'form_field','Enter job location here','Job_Location');
									?>
									<?php
										// @param field name, required, class, replaceholder, rename, id, attrib, value
										$input->masterfield('Position (Job Title)', '*', 'form_field','Enter position here','Position');
									?>
								</div>
							</div>

							<div class="form_box">
								<div class="form_box_col2">
									<div class="group">
									<?php
										// @param field name, required, class, replaceholder, rename, id, attrib, value
										$input->label('Number of Workers', '*');

									?>

									<input type="text" class="form_field" name="Number_Of_Workers" maxlength="" onkeypress="return isNumberKey(event)" placeholder='Enter number here'>
										</div>
									<div class="group">
									<?php
										// @param field name, required, class, replaceholder, rename, id, attrib, value
										$input->label('Start Date', '*');
										$input->fields('Start_Date', 'form_field Date1 DisablePast','Start_Date', 'placeholder="Enter date here"');
									?>
									</div>
								</div>
							</div>

							<div class="form_box">
								<div class="form_box_col2">
									<?php
										// @param field name, required, class, replaceholder, rename, id, attrib, value
										$input->masterfield('Duration (Weeks)', '*', 'form_field','Enter duration here','Duration_in_Weeks');
									?>
									<?php
										// @param field name, required, class, replaceholder, rename, id, attrib, value
										$input->masterfield('Job Classification', '*', 'form_field','Enter job classification here','Job_Classification');
									?>
								</div>
							</div>

							<div class="strong_head">
								Shift Information

							</div>

							<div class="form_box">
								<div class="form_box_col2">
									<div class="group">
										<?php $input->label('Shift Start Time', ''); ?>
										<div class="input-group clockpicker" data-align="left" data-donetext="Done">
										<input type="text" class="form-control" name="Shift_Start_Time" placeholder="Enter time here">
											<span class="input-group-addon">
												<span class="glyphicon glyphicon-time"></span>
											</span>
										</div>
									</div>
									<div class="group">
										<?php $input->label('Shift End Time', ''); ?>
										<div class="input-group clockpicker" data-align="left" data-donetext="Done">
										<input type="text" class="form-control" name="Shift_End_Time" placeholder="Enter time here">
											<span class="input-group-addon">
												<span class="glyphicon glyphicon-time"></span>
											</span>
										</div>
									</div>

								</div>
							</div>

							<div class="form_box">
								<div class="form_box_col1">
									<div class="group">
									<?php
										$input->label('Job Arrangement', '');
										// @param field name, required, class, replaceholder, rename, id, attrib, value
										$input->radio('Job_Arrangement', array('Weekend','Flex','Job Share','Other'),'Job_Arrangement','','2');
									?>
									</div>
								</div>
							</div>

							<div class="form_box">
								<div class="form_box_col2" id="other">
									<div class="group">
									<?php
										// @param field name, required, class, replaceholder, rename, id, attrib, value
										$input->fields('Other_Job_Arrangement', 'text form_field','Other_Job_Arrangement','placeholder="Enter other here"');
									?>
									</div>
								</div>
							</div>

							<div class="strong_head">
								Wage Information

							</div>


							<div class="form_box">
								<div class="form_box_col1">
								<div class="group">
									<?php
										$input->label('Employee Rate','');
										// @param field name, required, class, replaceholder, rename, id, attrib, value
										$input->amount('Employee_Rate', 'form_field','Employee_Rate','placeholder="Enter rate here"');
									?>
									</div>
								</div>
							</div>
							<div class="form_box">
								<div class="form_box_col1">
									<?php
										// @param label-name, if required
										$input->label('Job Description and Comments');
										// @param field name, class, id and attribute
										$input->textarea('Job_Description_and_Comments', 'text form_field','Job_Description_and_Comments','placeholder="Enter job description and comments here"');
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
	</div>
	<?php $input->phone(true); ?>
	<script src="https://code.jquery.com/jquery-2.2.4.min.js"></script>
	<script type="text/javascript" src="assets/js/bootstrap.min.js"></script>
	<script type="text/javascript" src="dist/bootstrap-clockpicker.min.js"></script>
	<script type="text/javascript" src="js/jquery.validate.min.js"></script>
	<script type="text/javascript" src="js/jquery.datepick.min.js"></script>
	<script type="text/javascript" src="dist/bootstrap-clockpicker.min.js"></script>
<script type="text/javascript" src="dist/jquery-clockpicker-customized.js"></script>
	<script src="js/datepicker.js"></script>
	<script src = "js/plugins.min.js"></script>
	<script src = "js/jquery.mask.min.js"></script>
	<script src = "js/proweaverPhone.js"></script>
	<script type="text/javascript">
$(document).ready(function() {
	// validate signup form on keyup and submit
	$("#submitform").validate({
		rules: {
			Company: "required",
			Contact_Person: "required",
			Phone_Number: "required",
			Job_Location: "required",
			"Position": "required",
			Number_Of_Workers: "required",
			Start_Date: "required",
			Duration_in_Weeks: "required",
			Job_Classification: "required",
			Email_Address: {
				required: true,
				email: true
			}
		},
		messages: {
			Company: "",
			Contact_Person: "",
			Phone_Number: "",
			Job_Location: "",
			"Position": "",
			Number_Of_Workers: "",
			Start_Date: "",
			Duration_in_Weeks: "",
			Job_Classification: "",
			Email_Address: ""
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



	$("#other").hide();


		$("input[name='Job_Arrangement']").change(function(){
		if($(this).val() == "Other"){
			$("#other").fadeIn();
			$("#other").find(':input').attr('disabled', false);
		}else{
			$("#other").fadeOut();
			$("#other").find(':input').attr('disabled', 'disabled');
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


			// Clockpicker //

			$('.clockpicker').clockpicker()
				.find('input').change(function(){
					console.log(this.value);
				});
			var input = $('#single-input').clockpicker({
				placement: 'bottom',
				align: 'left',
				autoclose: true,
				'default': 'now'
			});


		});
		$("label:contains('Number')").each(function(){
			$(this).parent().next('div').find(':input').keypress(function(e) {
					  var verified = (e.which == 8 || e.which == undefined || e.which == 0) ? null : String.fromCharCode(e.which).match(/[^0-9 -]/);
					  if (verified) {e.preventDefault();}
			  });
		  });

		$(function() {
		  $('.Date, .date').datepicker({
			autoHide: true,
			zIndex: 2048,
		  });
		});

		function isNumberKey(evt)
      {
         var charCode = (evt.which) ? evt.which : event.keyCode
         if (charCode > 31 && (charCode < 48 || charCode > 57))
            return false;

         return true;
      }

	</script>
</body>
</html>
