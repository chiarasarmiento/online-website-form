<?php
@session_start();
require_once 'FormsClass.php';
$input = new FormsClass();

$formname = 'Employee Satisfactory Form';
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

	if(!$result_recaptcha->success){
		$prompt_message = '<div id="recaptcha-error"><div class="message"><span>Invalid <br>Recaptcha</span><p class="rclose">x</p></div></div>';
	}elseif(empty($_POST['Can_see_myself_working_here_in_five_years']) and empty($_POST['Have_a_clear_understanding_of_my_company\'s_strategic_goals']) and
	empty($_POST['Believe_in_my_company\'s_mission']) and
	empty($_POST['Proud_to_be_part_of_this_company']) and
	empty($_POST['Always_recommend_my_company_to_others']) and
	
	empty($_POST['Always_know_what_is_expected_of_me_when_it_comes_to_my_goals_and_objectives']) and
	empty($_POST['Have_all_the_resources_I_need_to_do_my_job_successfully']) and
	empty($_POST['Involved_in_decision-making_that_affects_my_work']) and
	empty($_POST['Have_opportunities_to_express_myself,_recommend_new_ideas_and_solutions']) and
	empty($_POST['When_I_do_something_successfully,_it_feels_like_a_personal_accomplishment']) and
	empty($_POST['My_manager_recognizes_my_full_potential_and_capitalizes_on_my_strengths']) and
	empty($_POST['The_management_always_demonstrates_a_commitment_to_quality']) and
	empty($_POST['The_management_always_encourages_others_to_a_commitment_to_quality'])){
			$prompt_message = '<div id="recaptcha-error"><div class="message"><span>Please answer at least one question.</span><br/><p class="rclose">x</p></div></div>';
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
								$body .= '<tr><td class="Values1"colspan="2" height="50px" max-height="100px" align="left" width="55%" padding="100" style="line-height: normal; padding-left: 4px;text-justify: inter-word; word-wrap: anywhere; padding-right: 28px;">
								<span style="position:relative !important; "><b>' . $key2 . '</b></span >:</td> <td class="Values2"colspan="2" height="50px" align="left" width="40%" padding="10" style="line-height: normal; word-wrap: anywhere; "><span style="margin-top: 7px; position:relative;margin-left: 7px; border-collapse: collapse; display: inline-block;margin-bottom: 5px;margin-right: 7px;">' . htmlspecialchars(trim($value), ENT_QUOTES) . '</span> </td></tr>';
						}
						}
					}
					$body .= '
					</table>
					</div>
					</div>';

		// for email notification
		include 'send_email_curl.php';

		// save data form on database
		include 'savedb.php';

		// save data form on database
		$subject = $formname ;
		$attachments = array();

	 	//name of sender
		$name = "New Message Notification";
		$result = insertDB($name,$subject,$body,$attachments);

		$parameter = array(
			'body' => $body,
			'from' => $from_email,
			'from_name' => $from_name,
			'to' => $to_email,
			'subject' => 'New Message Notification',	
			'attachment' => $attachments	
		);
		
		$success_msg = '<div id="success"><div class="message"><span>THANK YOU</span><br/><span>We value your feedback.</span><p class="close">x</p></div></div>';
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
		<link rel="stylesheet" href="css/font-awesome.min.css">
		<link rel="stylesheet" href="css/media.min.css?ver24as">
		<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.0.13/css/all.css">
		<script src='https://www.google.com/recaptcha/api.js'></script>
		
		<style>
		.sentencecase .text_uppercase {text-transform: none;}
		.radio tr td  {
		  margin-bottom: 5px;
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
								<div class="form_box_col1">
									<div class="group sentencecase">
										<?php
											// @param label-name, if required
											$input->label('I can see myself working here in five years.');
											// @param field name, class, id and attribute
											$input->radio('Can_see_myself_working_here_in_five_years',array('Strongly Disagree','Disagree','Neutral','Agree','Strongly Agree'),'','','3');
										?>
									</div>
								</div>
							</div>

							<div class="form_box">
								<div class="form_box_col1">
									<div class="group sentencecase">
										<?php
											// @param label-name, if required
											$input->label('I have a clear understanding of my company\'s strategic goals.');
											// @param field name, class, id and attribute
											$input->radio('Have_a_clear_understanding_of_my_company\'s_strategic_goals',array('Strongly Disagree','Disagree','Neutral','Agree','Strongly Agree'),'','','3');
										?>
									</div>
								</div>
							</div>


							<div class="form_box">
								<div class="form_box_col1">
									<div class="group sentencecase">
										<?php
											// @param label-name, if required
											$input->label('I believe in my company\'s mission.');
											// @param field name, class, id and attribute
											$input->radio('Believe_in_my_company\'s_mission',array('Strongly Disagree','Disagree','Neutral','Agree','Strongly Agree'),'','','3');
										?>
									</div>
								</div>
							</div>


							<div class="form_box">
								<div class="form_box_col1">
									<div class="group sentencecase">
										<?php
											// @param label-name, if required
											$input->label('I\'m proud to be part of this company.');
											// @param field name, class, id and attribute
											$input->radio('Proud_to_be_part_of_this_company',array('Strongly Disagree','Disagree','Neutral','Agree','Strongly Agree'),'','','3');
										?>
									</div>
								</div>
							</div>



							<div class="form_box">
								<div class="form_box_col1">
									<div class="group sentencecase">
										<?php
											// @param label-name, if required
											$input->label('I always recommend my company to others.');
											// @param field name, class, id and attribute
											$input->radio('Always_recommend_my_company_to_others',array('Strongly Disagree','Disagree','Neutral','Agree','Strongly Agree'),'','','3');
										?>
									</div>
								</div>
							</div>


							<div class="form_box">
								<div class="form_box_col1">
									<div class="group sentencecase">
										<?php
											// @param label-name, if required
											$input->label('I always know what is expected of me when it comes to my goals and objectives.');
											// @param field name, class, id and attribute
											$input->radio('Always_know_what_is_expected_of_me_when_it_comes_to_my_goals_and_objectives',array('Strongly Disagree','Disagree','Neutral','Agree','Strongly Agree'),'','','3');
										?>
									</div>
								</div>
							</div>


							<div class="form_box">
								<div class="form_box_col1">
									<div class="group sentencecase">
										<?php
											// @param label-name, if required
											$input->label('I have all the resources I need to do my job successfully.');
											// @param field name, class, id and attribute
											$input->radio('Have_all_the_resources_I_need_to_do_my_job_successfully',array('Strongly Disagree','Disagree','Neutral','Agree','Strongly Agree'),'','','3');
										?>
									</div>
								</div>
							</div>

							<div class="form_box">
								<div class="form_box_col1">
									<div class="group sentencecase">
										<?php
											// @param label-name, if required
											$input->label('I am involved in decision-making that affects my work.');
											// @param field name, class, id and attribute
											$input->radio('Involved_in_decision-making_that_affects_my_work',array('Strongly Disagree','Disagree','Neutral','Agree','Strongly Agree'),'','','3');
										?>
									</div>
								</div>
							</div>


							<div class="form_box">
								<div class="form_box_col1">
									<div class="group sentencecase">
										<?php
											// @param label-name, if required
											$input->label('I have opportunities to express myself, recommend new ideas and solutions.');
											// @param field name, class, id and attribute
											$input->radio('Have_opportunities_to_express_myself,_recommend_new_ideas_and_solutions',array('Strongly Disagree','Disagree','Neutral','Agree','Strongly Agree'),'','','3');
										?>
									</div>
								</div>
							</div>


							<div class="form_box">
								<div class="form_box_col1">
									<div class="group sentencecase">
										<?php
											// @param label-name, if required
											$input->label('When I do something successfully, it feels like a personal accomplishment.');
											// @param field name, class, id and attribute
											$input->radio('When_I_do_something_successfully,_it_feels_like_a_personal_accomplishment',array('Strongly Disagree','Disagree','Neutral','Agree','Strongly Agree'),'','','3');
										?>
									</div>
								</div>
							</div>
							

							<div class="form_box">
								<div class="form_box_col1">
									<div class="group sentencecase">
										<?php
											// @param label-name, if required
											$input->label('My manager recognizes my full potential and capitalizes on my strengths.');
											// @param field name, class, id and attribute
											$input->radio('Manager_recognizes_my_full_potential_and_capitalizes_on_my_strengths',array('Strongly Disagree','Disagree','Neutral','Agree','Strongly Agree'),'','','3');
										?>
									</div>
								</div>
							</div>
							


							
							<div class="form_box">
								<div class="form_box_col1">
									<div class="group sentencecase">
										<?php
											// @param label-name, if required
											$input->label('The management always demonstrates a commitment to quality.');
											// @param field name, class, id and attribute
											$input->radio('Management_always_demonstrates_a_commitment_to_quality',array('Strongly Disagree','Disagree','Neutral','Agree','Strongly Agree'),'','','3');
										?>
									</div>
								</div>
							</div>
							
							
							
							<div class="form_box">
								<div class="form_box_col1">
									<div class="group sentencecase">
										<?php
											// @param label-name, if required
											$input->label('The management always encourages others to a commitment to quality.');
											// @param field name, class, id and attribute
											$input->radio('Management_always_encourages_others_to_a_commitment_to_quality',array('Strongly Disagree','Disagree','Neutral','Agree','Strongly Agree'),'','','3');
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
	<script src = "js/jquery-1.9.0.min.js"></script>
	<script type="text/javascript" src="js/jquery.validate.min.js"></script>
	<script src = "js/plugins.min.js"></script>


	<script type="text/javascript">
$(document).ready(function() {
	// validate signup form on keyup and submit
	$("#submitform").validate({
		rules: {
/* 			First_Name: "required",
			Last_Name: "required",
			Address: "required",
			Phone: "required",
			Question_Comment: "required",
			Email_Address: {
				required: true,
				email: true
			},
			secode: "required"	 */
		},
		messages: {
/* 			First_Name: "",
			Last_Name: "",
			Address: "",
			Phone: "",
			Question_Comment: "",
			Email_Address: "",
			secode: "" */
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


});
</script>
</body>
</html>
