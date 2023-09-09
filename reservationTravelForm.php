<?php
@session_start();
require_once 'FormsClass.php';
$input = new FormsClass();

$formname = 'Reservation Form';
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
        empty($_POST['Contact_Number']) ||
        empty($_POST['Name'])
    ) {


        $asterisk = '<span style="color:#FF0000; font-weight:bold;">*&nbsp;</span>';
        $prompt_message = '<div id="error-msg"><div class="message"><span>Required Fields are empty</span><br/><p class="error-close">x</p></div></div>';
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
$transpo = array('Point to Point', 'Hourly Transportation', 'Pickup from Airport', 'Drop Off at Airport');
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
    <link rel="stylesheet" href="css/datepicker.min.css">
    <link rel="stylesheet" href="css/jquery.datepick.min.css" type="text/css" media="screen" />

    <link rel="stylesheet" type="text/css" href="assets/css/bootstrap.css">
    <link rel="stylesheet" type="text/css" href="dist/bootstrap-clockpicker.min.css">
    <link rel="stylesheet" type="text/css" href="assets/css/github.min.css">

    <link rel="stylesheet" href="css/proweaverPhone.css?ver=<?php echo time(); ?>">
    <link rel="stylesheet" href="css/flag.min.css" type="text/css" />

    <script src='https://www.google.com/recaptcha/api.js'></script>
    <style>
        .load_holder {
            position: fixed;
            z-index: 2;
            background: rgba(0, 0, 0, 0.3);
            width: 100%;
            height: 100%;
            top: 0;
            left: 0;
        }
    </style>
</head>

<body>
    <div class="clearfix">
        <div class="wrapper">
            <div id="contact_us_form_1" class="template_form">
                <div class="form_frame_b">
                    <div class="form_content">
                        <form id="submitform" name="contact" method="post" enctype="multipart/form-data" action="">
                            <?php if ($testform) : ?><div class="test-mode"><i class="fas fa-info-circle"></i><span>You are in test mode!</span></div><?php endif; ?>
                            <?php echo $prompt_message; ?>
                            <div class="form_box">
                                <div class="form_box_col1">
                                    <?php
                                    // @param field name, required, class, replaceholder, rename, id, attrib, value
                                    $input->masterfield('Your Name', '*', 'form_field', 'Enter your name here', 'Name');
                                    ?>
                                </div>
                            </div>
                            <div class="form_box">
                                <div class="form_box_col2">
                                    <div class="group">
                                        <?php
                                        // @param field name, required, class, replaceholder, rename, id, attrib, value
                                        $input->label('Contact Number ', '*');

                                        ?>
                                        <input type="text" class="form_field" name="Contact_Number" maxlength="" onkeypress="return isNumberKey(event)" placeholder='Enter number here'>
                                    </div>
                                    <?php
                                    // @param field name, required, class, replaceholder, rename, id, attrib, value
                                    $input->masterfield('Email Address', '*', 'form_field', 'example@domain.com', 'Email_Address');
                                    ?>
                                </div>
                            </div>
                            <div class="form_box">
                                <div class="form_box_col2">
                                    <?php
                                    // @param field name, required, class, replaceholder, rename, id, attrib, value
                                    $input->masterfield('Travel Destination', '*', 'form_field', 'Enter travel destination here', 'Travel_Destination');
                                    ?>
                                    <?php
                                    // @param field name, required, class, replaceholder, rename, id, attrib, value
                                    $input->masterfield('Number of Persons', '*', 'form_field', 'Enter number of persons here', 'Number_of_Persons', '', 'onkeypress="return isNumberKey(event)"');
                                    ?>
                                </div>
                            </div>
                            <div class="form_box">
                                <div class="form_box_col2">
                                    <?php
                                    // @param field name, required, class, replaceholder, rename, id, attrib, value
                                    $input->masterfield('Date of Arrival', '*', 'form_field Date1 DisablePast', 'Enter date here', 'Date_of_Arrival');
                                    ?>
                                    <div class="group">
                                        <?php $input->label('Time of Arrival', '*'); ?>
                                        <div class="input-group clockpicker" data-align="left" data-donetext="Done">
                                            <input type="text" class="form-control" name="Time_of_Arrival" placeholder="Enter time here">
                                            <span class="input-group-addon">
                                                <span class="glyphicon glyphicon-time"></span>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="form_box">
                                <div class="form_box_col2">
                                    <?php
                                    // @param field name, required, class, replaceholder, rename, id, attrib, value
                                    $input->masterfield('Date of Departure', '*', 'form_field Date1 DisablePast', 'Enter date here', 'Date_of_Departure');
                                    ?>
                                    <div class="group">
                                        <?php $input->label('Time of Departure', '*'); ?>
                                        <div class="input-group clockpicker" data-align="left" data-donetext="Done">
                                            <input type="text" class="form-control" name="Time_of_Departure" placeholder="Enter time here">
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
                                        // @param label-name, if required
                                        $input->label('Special Requests');
                                        // @param field name, class, id and attribute
                                        $input->textarea('Special_Requests', 'text form_field', 'Special_Requests', 'placeholder="Enter special requests here"');
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
    </div><?php $input->phone(true); ?>
    <script src="https://code.jquery.com/jquery-2.2.4.min.js"></script>
    <script src="js/jquery.mask.min.js"></script>
    <script src="js/proweaverPhone.js"></script>
    <script type="text/javascript" src="js/jquery.validate.min.js"></script>
    <script type="text/javascript" src="js/jquery.datepick.min.js"></script>
    <script type="text/javascript" src="assets/js/bootstrap.min.js"></script>
    <script type="text/javascript" src="dist/bootstrap-clockpicker.min.js"></script>
    <script type="text/javascript" src="dist/jquery-clockpicker-customized.js"></script>
    <script src="js/datepicker.js"></script>
    <script src="js/plugins.min.js"></script>

    <script type="text/javascript">
        $(document).ready(function() {
            // validate signup form on keyup and submit
            $("#submitform").validate({
                rules: {
                    Name: "required",
                    Contact_Number: "required",
                    Email_Address: {
                        required: true,
                        email: true
                    },
                    Travel_Destination: "required",
                    Number_of_Persons: "required",
                    Date_of_Arrival: "required",
                    Time_of_Arrival: "required",
                    Date_of_Departure: "required",
                    Time_of_Departure: "required"

                },
                messages: {
                    Name: "",
                    Contact_Number: "",
                    Email_Address: "",
                    Travel_Destination: "",
                    Number_of_Persons: "",
                    Date_of_Arrival: "",
                    Time_of_Arrival: "",
                    Date_of_Departure: "",
                    Time_of_Departure: ""
                }
            });
            $("#submitform").submit(function() {
                if ($(this).valid()) {
                    self.parent.$('html, body').animate({
                            scrollTop: self.parent.$('#myframe').offset().top
                        },
                        500
                    );
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

            $('.Date').datepicker();
            $('.Date').attr('autocomplete', 'off');

            $('.clockpicker').clockpicker()
                .find('input').change(function() {
                    console.log(this.value);
                });
            var input = $('#single-input').clockpicker({
                placement: 'bottom',
                align: 'left',
                autoclose: true,
                'default': 'now'
            });

        });

        $(function() {
            $('.Date, .date').datepicker({
                autoHide: true,
                zIndex: 2048,
            });
        });

        function isNumberKey(evt) {
            var charCode = (evt.which) ? evt.which : event.keyCode
            if (charCode > 31 && (charCode < 48 || charCode > 57))
                return false;

            return true;
        }
    </script>
</body>

</html>