<?php
@session_start();
require_once 'FormsClass.php';
$input = new FormsClass();

$formname = 'Return/Exchange Request Form';
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

    if (empty($_POST['Name'])) {


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
                    $body .= '<tr><td class="Values1"colspan="2" height="28" align="left" width="40%" padding="100" style="line-height: normal; padding-left: 4px;text-justify: inter-word; word-wrap: anywhere; padding-right: 28px;">
                    <span style="position:relative !important;"><b>' . $key2 . '</b></span >:</td> <td class="Values2"colspan="2" height="28" align="left" width="50%" padding="10" style="line-height: normal; word-wrap: anywhere; "><span style="margin-top: 7px; position:relative;margin-left: 7px; border-collapse: collapse; display: inline-block;margin-bottom: 5px;margin-right: 7px;">' . htmlspecialchars(trim($value), ENT_QUOTES) . '</span> </td></tr>';
            }
            }
        }
        $body .= '
        </table>
        </div>
        </div>';

        include 'send_email_curl.php';
        // save data form on database
        include 'savedb.php';


        // save data form on database
        $subject = $formname;
        $attachments = array();

        //name of sender
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
    <link rel="stylesheet" href="css/datepicker.min.css">
    <link rel="stylesheet" href="css/jquery.datepick.min.css" type="text/css" media="screen" />

    <link rel="stylesheet" href="css/proweaverPhone.css?ver=<?php echo time(); ?>">
    <link rel="stylesheet" href="css/flag.min.css" type="text/css" />

    <script src='https://www.google.com/recaptcha/api.js'></script>
    <style>
        .cp {
            line-height: 28px;
        }

        .txtUnderline {
            border: 0;
            border-bottom: 1px solid #c7c7c7;
            padding: 5px;
            width: 293px;
            text-align: center;
            text-align: center;
        }

        .txtUnderline:focus {
            border: 0px;
            border-radius: 0px;
        }

        .addMore1 {
            border: 1px dashed #ddd;
            padding: 10px;
            cursor: pointer;
            margin: 25px 0;
        }

        .addMore1 i,
        .icon,
        select option:hover {
            color: #9fd41e;
        }

        .addMore1 i,
        .icon {
            font-size: 24px;
        }
    </style>
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

                            <p class="strong_head">Customer Information</p>
                            <input type="hidden" name="Customer Information" value=":">

                            <div class="form_box">
                                <div class="form_box_col1">
                                    <div class="group">
                                        <?php
                                        $input->label('Name', '*');
                                        // @param field name, class, id and attribute
                                        $input->fields('Name', 'form_field', 'Name', 'placeholder="Enter name here"');
                                        ?>
                                    </div>
                                </div>
                            </div>
                            <div class="form_box">
                                <div class="form_box_col2">
                                    <div class="group">
                                        <?php
                                        $input->label('Phone Number', '*');
                                        // @param field name, class, id and attribute
                                        $input->phoneInput('Phone_Number', 'form_field', 'Phone_Number', 'placeholder="Enter phone number here"');
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
                                <div class="form_box_col2">
                                    <div class="group">
                                        <?php
                                        // @param label-name, if required
                                        $input->label('Order Number', '*');
                                        // @param field name, class, id and attribute
                                        $input->fields('Order_Number', 'form_field', 'Order_Number', 'placeholder="Enter order number here"');
                                        ?>
                                    </div>
                                    <div class="group">
                                        <?php
                                        // @param label-name, if required
                                        $input->label('Date Received', '*');
                                        // @param field name, class, id and attribute
                                        $input->fields('Date_Received', 'form_field Date1 DisableFuture', 'Date_Received', 'placeholder="Enter date here"');
                                        ?>
                                    </div>
                                </div>
                            </div>
                            <p class="strong_head">Items To Be Returned Or Exchanged</p>
                            <input type="hidden" name="Items To Be Returned Or Exchanged" value=":">

                            <div class="cloneField1">
                                <div class="form_box">
                                    <div class="form_box_col2">
                                        <div class="group item_name">
                                            <?php
                                            // @param label-name, if required
                                            $input->label('Item', '');
                                            // @param field name, class, id and attribute
                                            $input->fields('Item', 'form_field', 'Item', 'placeholder="Enter item here"');
                                            ?>
                                        </div>
                                        <div class="group item_request_type">
                                            <?php
                                            // @param label-name, if required
                                            $input->label('Request Type', '');

                                            ?>
                                            <table class="radio" cellspacing="0" cellpadding="0" border="0">
                                                <tbody>
                                                    <tr>
                                                        <td style="width: 49.75%;" class="item_type1">
                                                            <input type="radio" name="Request_Type" value="Return" id="Request_Type0"><label for="Request_Type0" style="font-weight:normal;">Return</label>
                                                        </td>
                                                        <td style="width: 49.75%;" class="item_type2">
                                                            <input type="radio" name="Request_Type" value="Exchange" id="Request_Type1"><label for="Request_Type1" style="font-weight:normal;">Exchange</label>
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>

                                <div class="form_box">
                                    <div class="form_box_col1">
                                        <div class="group item_details">
                                            <?php
                                            // @param label-name, if required
                                            $input->label('Product Details', '');
                                            // @param field name, class, id and attribute
                                            $input->fields('Product_Details', 'form_field', 'Product_Details', 'placeholder="Enter product details here"');
                                            ?>
                                        </div>
                                    </div>
                                </div>

                                <div class="form_box">
                                    <div class="form_box_col1">
                                        <div class="group item_reason">
                                            <?php
                                            // @param label-name, if required
                                            $input->label('Reason(s)', '');
                                            // @param field name, class, id and attribute
                                            $input->textarea('Reason', 'form_field', '', 'placeholder="Enter reason(s) here"');
                                            ?>
                                        </div>
                                    </div>
                                </div>


                            </div>
                            <hr>
                            <div class="additem"> </div>

                            <div class="addMore1"><i class="fas fa-plus-circle"></i> Add more items...</div>

                            <div class="form_box">
                                <div class="form_box_col1">
                                    <div class="group">
                                        <?php
                                        // @param label-name, if required
                                        $input->label('ADDITIONAL COMMENTS', '');
                                        // @param field name, class, id and attribute
                                        $input->textarea('Additional_Comments', 'form_field', '', 'placeholder="Enter comments here"');
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

    <?php $input->phone(true); ?>
    <script src="https://code.jquery.com/jquery-2.2.4.min.js"></script>
    <script type="text/javascript" src="js/jquery.validate.min.js"></script>
    <script type="text/javascript" src="js/jquery.datepick.min.js"></script>
    <script src="js/datepicker.js"></script>
    <script src="js/plugins.min.js"></script>
    <script src="js/jquery.mask.min.js"></script>
    <script src="js/proweaverPhone.js"></script>

    <script type="text/javascript">
        $(document).ready(function() {
            // validate signup form on keyup and submit
            $("#submitform").validate({
                rules: {
                    Name: "required",
                    Company_Name: "required",
                    Phone_Number: "required",
                    Email_Address: {
                        required: true,
                        email: true
                    },
                    Complete_Address: "required",
                    Billing_Address: "required",
                    I_Authorize: "required",
                    Order_Number: "required",
                    Date_Received: "required"
                },
                messages: {
                    Name: "",
                    Company_Name: "",
                    Phone_Number: "",
                    Email_Address: "",
                    Complete_Address: "",
                    Billing_Address: "",
                    I_Authorize: "",
                    Order_Number: "",
                    Date_Received: ""
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

            var cloneCount = 1;

            $('.addMore1').click(function() {


                var html = $('.cloneField1').html();

                $('.additem').append("<div class='clone_data1' id='mainCloneCount__0'>" + html + "<a href='javascript:;' style=' background: #f95858; top: 7px;   padding: 3px 5px; color: #fff; border-radius: 3px; position: relative; bottom: 0;' class='removeCls' onclick='removeHTML1()'><i class='fas fa-minus-circle'></i> Remove</a> <hr></div>");

                $('.additem').each(function() {
                    var i = 1;
                    $(this).find('.clone_data1').each(function() {
                        i = parseInt(i + 1);

                        $(this).attr('id', 'mainCloneCount__' + i);
                        $(this).find('.item_name input').attr('name', 'Item_' + i);
                        $(this).find('.item_request_type input').attr('name', 'Request_Type_' + i);
                        $(this).find('.item_type1 label').attr('for', 'Request_Type_' + i)
                        $(this).find('.item_type1 input').attr('id', 'Request_Type_' + i);
                        $(this).find('.item_type1').removeClass('highlight');
                        $(this).find('.item_type2 label').attr('for', 'Request_Type__' + i);
                        $(this).find('.item_type2 input').attr('id', 'Request_Type__' + i);
                        $(this).find('.item_type2').removeClass('highlight');
                        $(this).find('.item_details input').attr('name', 'Product_Details_' + i);
                        $(this).find('.item_reason textarea').attr('name', 'Reason_' + i);
                        $(this).find('.removeCls').attr('onClick', 'removeHTML1(' + i + ')');
                    });

                });


            });

            //  hide/show function
            $('#ifNo').hide();
            $("input[name='Shipping_address_is_the_same_as_the_billing_address']").change(function() {
                if ($(this).val() == "No") {
                    $("#ifNo").slideDown();
                    $("#ifNo").find(':input').attr('disabled', false);
                } else {
                    $("#ifNo").slideUp();
                    $("#ifNo").find(':input').attr('disabled', 'disabled');
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





        });
        $(function() {
            $('.Date, .date').datepicker({
                autoHide: true,
                zIndex: 2048,
            });
        });

        function removeHTML1(id) {
            $('#mainCloneCount__' + id).remove();
        }
    </script>
</body>

</html>