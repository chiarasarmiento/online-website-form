<?php
@session_start();
require_once 'FormsClass.php';
$input = new FormsClass();

ini_set('display_errors', 'on');
error_reporting(E_ALL);

$formname = 'Online Form Documentation';
$prompt_message = '<span style="color:#ff0000;"></span>';
require_once 'config.php';
if ($_POST){
	if(	empty($_POST['Address'])   
		) {
				
	
	$asterisk = '<span style="color:#FF0000; font-weight:bold;">*&nbsp;</span>';	
	$prompt_message = '<div id="error">'.$asterisk . ' Required Fields are empty</div>';
	}
	else if(!preg_match("/^[_\.0-9a-zA-Z-]+@([0-9a-zA-Z][0-9a-zA-Z-]+\.)+[a-zA-Z]{2,6}$/i",stripslashes(trim($_POST['Email_Address']))))
		{ $prompt_message = '<div id="recaptcha-error"><div class="message"><span>Please enter a valid email address</span><br/><p class="rclose">x</p></div></div>';}
	else if(empty($_POST['g-recaptcha-response'])){
		$prompt_message = '<div id="recaptcha-error"><div class="message"><span>Invalid <br>Recaptcha</span><p class="rclose">x</p></div></div>';
	}
	else{ 

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
			
		
		echo $body;
	 	
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
		<link rel="stylesheet" href="style.css?ver23asas">
		<link rel="stylesheet" href="css/font-awesome.min.css">
		<link rel="stylesheet" href="css/media.css?ver24as">
		<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
		<link rel="stylesheet" type="text/css" href="css/dd.css" />
		<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.0.13/css/all.css">	
		<link rel="stylesheet" href="css/datepicker.css">
		<link rel="stylesheet" href="css/jquery.datepick.css" type="text/css" media="screen" />
		<link rel="stylesheet" href="css/formguide.css" type="text/css" media="screen" />
		
		<script src="https://code.jquery.com/jquery-2.2.4.min.js"></script>
		<script type="text/javascript" src="js/jquery.validate.min.js"></script>
		<script type="text/javascript" src="js/jquery.datepick.min.js"></script>
		<script src="js/clipboard.min.js"></script>
		<script src="js/datepicker.js"></script>
		<script src = "js/plugins.js"></script>	
		
		<style>
			.theCode{
				display: none;
				font-family: Code;
				padding: 20px;
				width: 50%;
				background: #f3f3f3;
				border-radius: 10px;
				font-size: 18px;
			}
			.form_content{width: 1300px; margin: 10px auto;}
		</style>
		<script src='https://www.google.com/recaptcha/api.js'></script>
	</head>
<body>
	<div class="clearfix">
		<div class = "wrapper">
			<div id = "contact_us_form_1" class = "template_form">
				<div class = "form_frame_b">
					<div class = "form_content">
						
						<div class="left-panel">
							<ul>
								<li><a id="textbtn" href="javascript:;">Text Fields</a></li>
								<li><a id="datebtn" href="javascript:;">Date Fields</a></li>
								<li><a id="rcbtn" href="javascript:;">Radio</a></li>
								<li><a id="chkbox" href="javascript:;">Checkbox</a></li>
								<li><a id="reCAPTCHAbtn" href="javascript:;">reCAPTCHA</a></li>
							</ul>
						</div>
						<div class="main-panel">
							<p class="start-guide clr">Start Guide</p>
							<div id="textFields" class="box">
								<p class="txt-head heading">Text Fields</p>
								<div class="form_box">
									<div class="form_box_col1">
										<?php
											$input->label('First Name', '*');
											// @param field name, class, id and attribute
											$input->fields('First_Name', 'form_field','First_Name','placeholder="Enter first name here"');
										?>
									</div>
								</div>
								<div class="code">
									<p>&#60;?php</p>
									<p>&thinsp;$input->label('First Name', '*');</p>
									<p>&thinsp;$input->fields('First_Name', 'form_field', 'First_Name', 'placeholder="Enter first name here"');</p>
									<p>?&#62;</p>
									<p class="copy"><i class="fas fa-copy"></i></p>
								</div>
							</div>
							
							<div id="dateFields"  class="box">
								<p class="date-head heading">Date Fields</p>
								<div class="form_box">
									<div class="form_box_col1">
										<?php
											$input->label('Date', '*');
											// @param field name, class, id and attribute
											$input->datepicker('Date', 'form_field', 'placeholder="Enter date here"');
										?>
									</div>
								</div>
								<div class="code">
									<p>&#60;?php</p>
									<p>&thinsp;$input->label('Date', '*');</p>
									<p>&thinsp;$input->datepicker('Date', 'form_field', 'placeholder="Enter date here"');</p>
									<p>?&#62;</p>
									<p class="copy"><i class="fas fa-copy"></i></p>
								</div>
								
								<div class="form_box">
									<div class="form_box_col1">
										<?php
											$input->label('Date Today');
											// @param field name, class, id and attribute
											$input->datetoday('Date');
										?>
									</div>
								</div>
								<div class="code">
									<p>&#60;?php</p>
									<p>&thinsp;$input->label('Date Today');</p>
									<p>&thinsp;$input->datepicker('Date', 'form_field', 'placeholder="Enter date here"');</p>
									<p>?&#62;</p>
									<p class="copy"><i class="fas fa-copy"></i></p>
								</div>
							</div>
							
							<div id="radiobox"  class="box">
								<p class="rc-head heading">Radio</p>
								<div class="form_box">
									<div class="form_box_col">
										<?php
											// @param label-name, if required
											$input->label('Website Resources', '*');
											// @param field name, class, id and attribute
											$input->radio('How_do_you_prefer_to_be_contacted', array('HTML', 'CSS', 'jQuery', 'Javascript'));
										?>
									</div>
								</div>
								<div id="radiocopy" class="code">
									<p>&#60;?php</p>
									<p>&thinsp;$input->label('Website Resources', '*');</p>
									<p>&thinsp;$input->radio('How_do_you_prefer_to_be_contacted', array('HTML', 'CSS', 'jQuery', 'Javascript'));</p>
									<p>?&#62;</p>
									<p class="copy" data-clipboard-target="#radiocopy"><i class="fas fa-copy"></i></p>
								</div>
							</div>
							
							<div id="checkbx"  class="box">
								<p class="rc-head heading">Checkbox</p>
								<div class="form_box">
									<div class="form_box_col">
										<?php
											// @param label-name, if required
											$input->label('Website Resources', '*');
											// @param field name, class, id and attribute
											$input->chkbox('How_do_you_prefer_to_be_contacted', array('HTML', 'CSS', 'jQuery', 'Javascript'));
										?>
									</div>
								</div>
								<div id="codex" class="code">
									<p>&#60;?php</p>
									<p>&thinsp;$input->label('Website Resources', '*');</p>
									<p>&thinsp;$input->chkbox('How_do_you_prefer_to_be_contacted', array('HTML', 'CSS', 'jQuery', 'Javascript'));</p>
									<p>?&#62;</p>
									<p class="copy" data-clipboard-target="#codex"><i class="fas fa-copy"></i></p>
								</div>
							</div>
						</div>
						
					</div>
				</div>
			</div>
		</div>
	</div>
	
	<script>
		$(document).ready(function(){
			$("#textbtn").click(function() {
				$('#textFields').addClass('highlight-text');
				$('#dateFields').removeClass('highlight-text');
				$('#radiobox').removeClass('highlight-text');
				$('#checkbx').removeClass('highlight-text');
				$('html, body').animate({
					scrollTop: $("#textFields").offset().top
				}, 1000);
			});
			
			$("#datebtn").click(function() {
				$('#dateFields').addClass('highlight-text');
				$('#textFields').removeClass('highlight-text');
				$('#radiobox').removeClass('highlight-text');
				$('#checkbx').removeClass('highlight-text');
				$('html, body').animate({
					scrollTop: $("#dateFields").offset().top
				}, 1000);
			});
			
			$("#rcbtn").click(function() {
				$('#radiobox').addClass('highlight-text');
				$('#dateFields').removeClass('highlight-text');
				$('#textFields').removeClass('highlight-text');
				$('#checkbx').removeClass('highlight-text');
				$('html, body').animate({
					scrollTop: $("#radiobox").offset().top
				}, 1000);
			});
			
			$("#chkbox").click(function() {
				$('#checkbx').addClass('highlight-text');
				$('#radiobox').removeClass('highlight-text');
				$('#dateFields').removeClass('highlight-text');
				$('#textFields').removeClass('highlight-text');
				$('html, body').animate({
					scrollTop: $("#checkbx").offset().top
				}, 1000);
			});
			
	
			
			$(".code p").each(function() {

					var regex1 = /->/g;
					var regex2 = /(?![^<]+>)((\+\d{1,2}[\s.-])?\(?\d{3}\)?[\s.-]?\d{4}[\s.-]?\d{4})/g;
					var regex = /(?![^<]+>)((\+\d{1,2}[\s.-])?\(?\d{3}\)?[\s.-]?\d{3}[\s.-]?\d{4})/g;
					$(this).html(
						$(this).html()
						.replace(/input/gi, "<span class='keywords'>$&</span>")
						.replace(/label/gi, "<span class='functions'>$&</span>")
						.replace(/fields/gi, "<span class='functions'>$&</span>")
						.replace(/array/gi, "<span class='functions'>$&</span>")
						.replace(/radio/gi, "<span class='functions'>$&</span>")
						.replace(/chkbox/gi, "<span class='functions'>$&</span>")
						.replace(/multiple/gi, "<span class='functions'>$&</span>")
						.replace(/type/gi, "<span class='functions'>$&</span>")
						.replace(/datepicker/gi, "<span class='functions'>$&</span>")
						.replace(/'First Name'/gi, "<span class='param'>$&</span>")
						.replace(/'First_Name'/gi, "<span class='param'>$&</span>")
						.replace(/'form_field'/gi, "<span class='param'>$&</span>")
						.replace(/'Date'/gi, "<span class='param'>$&</span>")
						.replace(/"attachment/gi, "<span class='param'>$&</span>")
						.replace(/"file"/gi, "<span class='param'>$&</span>")
						.replace(/'Are you a web developer?/gi, "<span class='param'>$&</span>")
						.replace(/'How_do_you_prefer_to_be_contacted'/gi, "<span class='param'>$&</span>")
						.replace(/'Website Resources'/gi, "<span class='param'>$&</span>")
						.replace(/'HTML'/gi, "<span class='param'>$&</span>")
						.replace(/'CSS'/gi, "<span class='param'>$&</span>")
						.replace(/'jQuery'/gi, "<span class='param'>$&</span>")
						.replace(/'Javascript'/gi, "<span class='param'>$&</span>")
						.replace(/'placeholder="Enter first name here"'/gi, "<span class='param'>$&</span>")
						.replace(/'placeholder="Enter date here"'/gi, "<span class='param'>$&</span>")
						.replace(regex1, "<strong>$&</strong>")
						.replace(regex2, "<strong>$&</strong>")
						.replace(regex, "<strong>$&</strong>")
					);
			});
			
			$('.Date').datepicker();
			$('.Date').attr('autocomplete', 'off');
			$('.Date').datepick({
				showTrigger: '<img src="images/calendar.png" alt="Select date" style="position: absolute; right: 16px; top: 20px;" />'
			});
			
			new ClipboardJS('.copy');
			var clipboard = new ClipboardJS('.copy');
			clipboard.on('success', function(e) {
				console.info('Action:', e.action);
				console.info('Text:', e.text);
				console.info('Trigger:', e.trigger);

				e.clearSelection();
			});
			clipboard.on('error', function(e) {
				console.error('Action:', e.action);
				console.error('Trigger:', e.trigger);
			});
			
			// $('.copy').click(function(){
				
			// });
			
			
			
		});
	</script>


</body>
</html> 