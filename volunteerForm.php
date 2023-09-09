<?php
@session_start();
require_once 'FormsClass.php';
$input = new FormsClass();

$formname = 'Volunteer Form';
$prompt_message = '<span class="required-info">* Required Information</span>';
require_once 'config.php';
if ($_POST) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, "https://www.google.com/recaptcha/api/siteverify");
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, "secret={$recaptcha_privite}&response={$_POST['g-recaptcha-response']}");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $server_output = curl_exec($ch);
    $result_recaptcha = json_decode($server_output);
    curl_close($ch);

    if (
        empty($_POST['Heard_about_the_program_through']) ||
        empty($_POST['Special_Skills_And_Other_Languages_Spoken']) ||
        empty($_POST['Previous_Volunteer_Experience']) ||
        empty($_POST['Receive_information_through']) ||
        empty($_POST['Name']) ||
        empty($_POST['Address']) ||
        empty($_POST['Contact_Number']) ||
        empty($_POST['Interested_Helping_in_Area_of']) ||
        empty($_POST['Email_Address'])
    ) {


        $asterisk = '<span style="color:#FF0000; font-weight:bold;">*&nbsp;</span>';
        $prompt_message = '<div id="error-msg"><div class="message"><span>Failed to send email. Please try again.</span><br/><p class="error-close">x</p></div></div>';
    } else if (!preg_match("/^[_\.0-9a-zA-Z-]+@([0-9a-zA-Z][0-9a-zA-Z-]+\.)+[a-zA-Z]{2,6}$/i", stripslashes(trim($_POST['Email_Address'])))) {
        $prompt_message = '<div id="recaptcha-error"><div class="message"><span>Please enter a valid email address</span><br/><p class="rclose">x</p></div></div>';
    } else if (!$result_recaptcha->success) {
        $prompt_message = '<div id="recaptcha-error"><div class="message"><span>Invalid <br>Recaptcha</span><p class="rclose">x</p></div></div>';
    } else {

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
                    $body .= '<tr><td class="Values1"colspan="2" height="28" align="left" width="45%" padding="100" style="padding-left: 4px;text-justify: inter-word;">
								<span style="position:relative !important;"><b>' . $key2 . '</b></span >:</td> <td class="Values2"colspan="2" height="28" align="left" width="45%" padding="10" style="line-height: 125%; position:static;"><span style="margin-top: 7px; position:relative;margin-left: 7px; border-collapse: collapse; display: inline-block;margin-bottom: 5px;margin-right: 7px;">' . htmlspecialchars(trim($value), ENT_QUOTES) . '</span> </td></tr>';
                }
            }
        }
        $body .= '
			</table>

			</div>
			</div>';

        //echo $body; exit;
        // for email notification
        require_once 'config.php';
        include 'send_email_curl.php';

        // save data form on database
        include 'savedb.php';


        // save data form on database
        $subject = $formname;
        $attachments = array();


        $name = $_POST['Name'];
        $result = insertDB($name, $subject, $body, $attachments);

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
$areasNeed = array('Please select', 'Fundraising', 'PR/Marketing', 'Office Assistance', 'Volunteer Recruitment', 'Special Events');
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
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.0.13/css/all.css">
    <script src='https://www.google.com/recaptcha/api.js'></script>
</head>

<body>
    <div class="clearfix">
        <div class="wrapper">
            <div id="contact_us_form_1" class="template_form">
                <div class="form_frame_b">
                    <div class="form_content">
                        <?php if ($testform) : ?><div class="test-mode"><i class="fas fa-info-circle"></i><span>You are in test mode!</span></div><?php endif; ?>

                        <form id="submitform" name="contact" method="post" enctype="multipart/form-data" action="">
                            <?php echo $prompt_message; ?>

                            <div class="form_box">
                                <div class="form_box_col1">
                                    <div class="group">
                                        <?php
                                        $input->label('How did you first hear about our program?', '*');
                                        // @param field name, class, id and attribute
                                        $input->fields('Heard_about_the_program_through', 'form_field', 'Heard_about_the_program_through', 'placeholder="Enter details here"');
                                        ?>
                                    </div>

                                </div>
                            </div>

                            <div class="form_box">
                                <div class="form_box_col1">
                                    <div class="group">
                                        <?php
                                        // @param label-name, if required
                                        $input->label('Please list any of your special skills and other languages spoken', '*');
                                        // @param field name, class, id and attribute
                                        $input->textarea('Special_Skills_And_Other_Languages_Spoken', 'text form_field', 'Special_Skills_And_Other_Languages_Spoken', 'placeholder="Enter details here"');
                                        ?>
                                    </div>
                                </div>
                            </div>

                            <div class="form_box">
                                <div class="form_box_col1">
                                    <div class="group">
                                        <?php
                                        // @param label-name, if required
                                        $input->label('If you have previous volunteer experience, please describe it here', '*');
                                        // @param field name, class, id and attribute
                                        $input->textarea('Previous_Volunteer_Experience', 'text form_field', 'Previous_Volunteer_Experience', 'placeholder="Enter description here"');
                                        ?>
                                    </div>
                                </div>
                            </div>

                            <div class="form_box">
                                <div class="form_box_col1">
                                    <div class="group">
                                        <?php
                                        $input->label('How would you like to receive information from us?', '*');
                                        // @param field name, class, id and attribute
                                        $input->fields('Receive_information_through', 'form_field', 'Receive_information_through', 'placeholder="Enter details here"');
                                        ?>
                                    </div>
                                </div>
                            </div>

                            <p class="strong_head">Contact Information</p>
                            <input type="hidden" name="Contact_Information" value=":" />
                            <div class="form_box">
                                <div class="form_box_col2">
                                    <div class="group">
                                        <?php
                                        $input->label('Name', '*');
                                        // @param field name, class, id and attribute
                                        $input->fields('Name', 'form_field', 'Name', 'placeholder="Enter name here"');
                                        ?>
                                    </div>
                                    <div class="group">
                                        <?php
                                        $input->label('Address', '*');
                                        // @param field name, class, id and attribute
                                        $input->fields('Address', 'form_field', 'Address', 'placeholder="Enter address here"');
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
                                        $input->fields('Contact_Number', 'form_field', 'Contact_Number', 'placeholder="Enter contact number here"');
                                        ?>
                                    </div>
                                    <div class="group">
                                        <?php
                                        $input->label('Email Address', '*');
                                        // @param field name, class, id and attribute
                                        $input->fields('Email_Address', 'form_field', 'Email_Address', 'placeholder="example@domain.com"');
                                        ?>
                                    </div>
                                </div>
                            </div>

                            <div class="form_box">
                                <div class="form_box_col">
                                    <div class="group">
                                        <?php
                                        $input->label('If you are interested in helping in any of the following areas of need, please select one', '*');
                                        // @param field name, class, id and attribute
                                        $input->select('Interested_Helping_in_Area_of', 'form_field', $areasNeed);

                                        ?>
                                    </div>
                                </div>
                            </div>

                            <div class="form_box5 secode_box">
                                <div class="group">
                                    <div class="inner_form_box1 recapBtn">
                                        <div class="g-recaptcha" data-sitekey="<?php echo $recaptcha_sitekey; ?>"></div>
                                        <div class="btn-submit"><input type="submit" class="form_button" value="SUBMIT" /></div>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="js/jquery-1.9.0.min.js"></script>
    <script type="text/javascript" src="js/jquery.validate.min.js"></script>
    <script src="js/plugins.min.js"></script>


    <script type="text/javascript">
        $(document).ready(function() {
            // validate signup form on keyup and submit
            $("#submitform").validate({
                rules: {
                    Heard_about_the_program_through: "required",
                    Special_Skills_And_Other_Languages_Spoken: "required",
                    Previous_Volunteer_Experience: "required",
                    Receive_information_through: "required",
                    Name: "required",
                    Address: "required",
                    Contact_Number: "required",
                    Interested_Helping_in_Area_of: "required",
                    Email_Address: {
                        required: true,
                        email: true
                    },
                },
                messages: {
                    Heard_about_the_program_through: "",
                    Special_Skills_And_Other_Languages_Spoken: "",
                    Previous_Volunteer_Experience: "",
                    Receive_information_through: "",
                    Name: "",
                    Address: "",
                    Contact_Number: "",
                    Interested_Helping_in_Area_of: "",
                    Email_Address: ""
                }
            });


            $("#submitform").submit(function() {
                if ($(this).valid()) {
                    $('.load_holder').css('display', 'block');
                    self.parent.$('html, body').animate({
                            scrollTop: self.parent.$('#myframe').offset().top
                        },
                        500
                    );
                }
                if (grecaptcha.getResponse() == "") {
                    var $recaptcha = document.querySelector('#g-recaptcha-response');
                    $recaptcha.setAttribute("required", "required");
                    $('.g-recaptcha').addClass('errors').attr('id', 'recaptcha');
                }
            });

            $("input").keypress(function(event) {
                if (grecaptcha.getResponse() == "") {
                    var $recaptcha = document.querySelector('#g-recaptcha-response');
                    $recaptcha.setAttribute("required", "required");
                }
            });

        });
    </script>
</body>

</html>