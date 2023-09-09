<?php
@session_start();
require_once 'FormsClass.php';
$input = new FormsClass();

$formname = 'Easy RX Buying Form';
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
        empty($_POST['Name']) ||
        empty($_POST['Contact_Number']) ||
        empty($_POST['Email_Address'])
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
$state = array('Please select state.', 'Alabama', 'Alaska', 'Arizona', 'Arkansas', 'California', 'Colorado', 'Connecticut', 'Delaware', 'District Of Columbia', 'Florida', 'Georgia', 'Hawaii', 'Idaho', 'Illinois', 'Indiana', 'Iowa', 'Kansas', 'Kentucky', 'Louisiana', 'Maine', 'Maryland', 'Massachusetts', 'Michigan', 'Minnesota', 'Mississippi', 'Missouri', 'Montana', 'Nebraska', 'Nevada', 'New Hampshire', 'New Jersey', 'New Mexico', 'New York', 'North Carolina', 'North Dakota', 'Ohio', 'Oklahoma', 'Oregon', 'Pennsylvania', 'Puerto Rico', 'Rhode Island', 'South Carolina', 'South Dakota', 'Tennessee', 'Texas', 'Utah', 'Vermont', 'Virgin Islands', 'Virginia', 'Washington', 'West Virginia', 'Wisconsin', 'Wyoming');
$country = array('Please select country.', 'Afghanistan', 'Albania', 'Algeria', 'Andorra', 'Angola', 'Anguilla', 'Antigua & Barbuda', 'Argentina', 'Armenia', 'Australia', 'Austria', 'Azerbaijan', 'Bahamas', 'Bahrain', 'Bangladesh', 'Barbados', 'Belarus', 'Belgium', 'Belize', 'Benin', 'Bermuda', 'Bhutan', 'Bolivia', 'Bosnia & Herzegovina', 'Botswana', 'Brazil', 'Brunei Darussalam', 'Bulgaria', 'Burkina Faso', 'Myanmar/Burma', 'Burundi', 'Cambodia', 'Cameroon', 'Canada', 'Cape Verde', 'Cayman Islands', 'Central African Republic', 'Chad', 'Chile', 'China', 'Colombia', 'Comoros', 'Congo', 'Costa Rica', 'Croatia', 'Cuba', 'Cyprus', 'Czech Republic', 'Democratic Republic of the Congo', 'Denmark', 'Djibouti', 'Dominica', 'Dominican Republic', 'Ecuador', 'Egypt', 'El Salvador', 'Equatorial Guinea', 'Eritrea', 'Estonia', 'Ethiopia', 'Fiji', 'Finland', 'France', 'French Guiana', 'Gabon', 'Gambia', 'Georgia', 'Germany', 'Ghana', 'Great Britain', 'Greece', 'Grenada', 'Guadeloupe', 'Guatemala', 'Guinea', 'Guinea-Bissau', 'Guyana', 'Haiti', 'Honduras', 'Hungary', 'Iceland', 'India', 'Indonesia', 'Iran', 'Iraq', 'Israel and the Occupied Territories', 'Italy', 'Ivory Coast (Cote d\'Ivoire)', 'Jamaica', 'Japan', 'Jordan', 'Kazakhstan', 'Kenya', 'Kosovo', 'Kuwait', 'Kyrgyz Republic (Kyrgyzstan)', 'Laos', 'Latvia', 'Lebanon', 'Lesotho', 'Liberia', 'Libya', 'Liechtenstein', 'Lithuania', 'Luxembourg', 'Republic of Macedonia', 'Madagascar', 'Malawi', 'Malaysia', 'Maldives', 'Mali', 'Malta', 'Martinique', 'Mauritania', 'Mauritius', 'Mayotte', 'Mexico', 'Moldova, Republic of', 'Monaco', 'Mongolia', 'Montenegro', 'Montserrat', 'Morocco', 'Mozambique', 'Namibia', 'Nepal', 'Netherlands', 'New Zealand', 'Nicaragua', 'Niger', 'Nigeria', 'Korea, Democratic Republic of (North Korea)', 'Norway', 'Oman', 'Pacific Islands', 'Pakistan', 'Panama', 'Papua New Guinea', 'Paraguay', 'Peru', 'Philippines', 'Poland', 'Portugal', 'Puerto Rico', 'Qatar', 'Reunion', 'Romania', 'Russian Federation', 'Rwanda', 'Saint Kitts and Nevis', 'Saint Lucia', 'Saint Vincent\'s & Grenadines', 'Samoa', 'Sao Tome and Principe', 'Saudi Arabia', 'Senegal', 'Serbia', 'Seychelles', 'Sierra Leone', 'Singapore', 'Slovak Republic (Slovakia)', 'Slovenia', 'Solomon Islands', 'Somalia', 'South Africa', 'Korea, Republic of (South Korea)', 'South Sudan', 'Spain', 'Sri Lanka', 'Sudan', 'Suriname', 'Swaziland', 'Sweden', 'Switzerland', 'Syria', 'Tajikistan', 'Tanzania', 'Thailand', 'Timor Leste', 'Togo', 'Trinidad & Tobago', 'Tunisia', 'Turkey', 'Turkmenistan', 'Turks & Caicos Islands', 'Uganda', 'Ukraine', 'United Arab Emirates', 'United States of America (USA)', 'Uruguay', 'Uzbekistan', 'Venezuela', 'Vietnam', 'Virgin Islands (UK)', 'Virgin Islands (US)', 'Yemen', 'Zambia', 'Zimbabwe');
$choices = array('- Please Select -', 'No, thanks', 'Yes, via phone');

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

    <link rel="stylesheet" href="css/datepicker.min.css">
    <link rel="stylesheet" href="css/jquery.datepick.min.css" type="text/css" media="screen" />
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.0.13/css/all.css">
    <link rel="stylesheet" href="css/proweaverPhone.css?ver=<?php echo time(); ?>">
    <link rel="stylesheet" href="css/flag.min.css" type="text/css" />


    <script src='https://www.google.com/recaptcha/api.js'></script>
    <style>
        .information,
        .information2 {
            background: #fee7e3;
            color: #444444;
            font-weight: bold;
        }

        .information:before {
            content: url(images/info-reTranspo-icon.png) !important;
        }

        .information2:before {
            position: absolute;
            left: 25px;
            top: 13px;
            content: url(images/wage-icon.png) !important;
        }

        .radio tr td {
            width: 33%;
            margin-right: 0;
        }

        .radio tr td:last-child {
            width: 33%;
            margin-right: 0;
        }

        <blade media|%20only%20screen%20and%20(min-width%3A%20110px)%20and%20(max-width%20%3A%201490px)%20%7B%0D>.radio tr td {
            width: 33%;
            margin-right: 0;
        }
        }

        <blade media|%20only%20screen%20and%20(max-width%20%3A%20430px)%20%7B%0D>.radio tr td,
        .radio tr td:last-child {
            width: 100%;
            display: block;
        }
        }

        @blade media|%20only%20screen%20and%20(max-width%20%3A%20340px)%20%7B%0D>.country {
            font-size: 12px;
        }
        }

        .hdh_con {
            text-align: center;
            padding: 15px 15px;
            background: #d63d26;
            font-weight: bold;
            color: #fff;
            font-size: 20px;
            border-radius: 4px;
        }
    </style>
</head>

<body>
    <div class="clearfix">
        <div class="wrapper">
            <div id="contact_us_form_1" class="template_form">
                <div class="form_frame_b">
                    <div class="form_content">
                        <?php if ($testform) : ?><div class="test-mode"><i class="fas fa-info-circle"></i><span>You are in
                                    test mode!</span></div><?php endif; ?>
                        <form id="submitform" name="contact" method="post" enctype="multipart/form-data" action="">
                            <?php echo $prompt_message; ?>

                            <p class="strong_head">Order Details</p>

                            <div class="form_box">
                                <div class="form_box_col3">
                                    <div class="group">
                                        <?php
                                        $input->label('Name', '*');
                                        $input->fields('Name', 'form_field', '', 'placeholder="Enter your name here"');                     ?>
                                    </div>
                                    <div class="group">
                                        <?php
                                        $input->label('Contact Number', '*');
                                        $input->fields('Contact_Number', 'form_field', '', 'placeholder="Enter contact number here"');
                                        ?>
                                    </div>
                                    <div class="group">
                                        <?php
                                        $input->label('Email Address', '*');
                                        $input->fields('Email_Address', 'form_field', '', 'placeholder="example@domain.com"');
                                        ?>
                                    </div>
                                </div>
                            </div>
                            <input type="hidden" name="Order Details" value=":">

                            <div class="form_head grouping">
                                <p class="title_head"></p>
                                <div class="form_box">
                                    <div class="form_box_col2">
                                        <div class="group">
                                            <?php
                                            $input->label('1', '*');
                                            // @param field name, required, class, replaceholder, rename, id, attrib, value
                                            $input->fields('1)_Prescription_Name', 'form_field', '1) Prescription_Name_', 'placeholder="Enter prescription here"');
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
                                            $input->fields('2)_Prescription_Name', 'form_field', '2) Prescription_Name_', 'placeholder="Enter prescription here"');
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
                                            $input->fields('3)_Prescription_Name', 'form_field', '3) Prescription_Name_', 'placeholder="Enter prescription here"');
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
                                            $input->fields('4)_Prescription_Name', 'form_field', '4) Prescription_Name_', 'placeholder="Enter prescription here"');
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
                                                $input->fields('5)_Prescription_Name', 'form_field', '5) Prescription_Name_', 'placeholder="Enter prescription here"');
                                                ?>
                                            </div>
                                            <div class="group">
                                                <?php
                                                $input->label('&nbsp;');
                                                // @param field name, required, class, replaceholder, rename, id, attrib, value
                                                $input->fields('_____Quantity', 'form_field', '_____Quantity', 'placeholder="Enter quantity here"');
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
                                        $input->label('Pick up or Delivery', '*');
                                        // @param field name, required, class, replaceholder, rename, id, attrib, value
                                        $input->radio('Pick_up_or_Delivery', array('Pickup', 'Delivery'), '', '', '2');
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
                                        $input->select('Notify when prescription(s) are ready', 'form_field', $choices);
                                        ?>
                                    </div>
                                </div>
                            </div>

                            <div class="form_box" id="Delivery_Address">
                                <p class="strong_head">DELIVERY ADDRESS</p>
                                <input type="hidden" name="_Delivery_Address" value=":">
                                <div class="form_box">
                                    <div class="form_box_col1">
                                        <div class="group ifdelivery">
                                            <?php
                                            $input->label('Address', '*');
                                            $input->fields('Address', 'form_field ifdelivery', '', 'placeholder="Enter address here"');
                                            ?>
                                        </div>
                                    </div>
                                </div>
                                <div class="form_box">
                                    <div class="form_box_col2" id="country">
                                        <div class="group">
                                            <?php $input->label('Country', ''); ?>
                                            <select class="form_field country" name="Country" readonly>
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
                                                <option value="Central African Republic (CAR)">Central African Republic
                                                    (CAR)</option>
                                                <option value="Chad">Chad</option>
                                                <option value="Chile">Chile</option>
                                                <option value="China">China</option>
                                                <option value="Colombia">Colombia</option>
                                                <option value="Comoros">Comoros</option>
                                                <option value="Congo, Democratic Republic of the">Congo, Democratic
                                                    Republic of the</option>
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
                                                <option value="Saint Vincent and the Grenadines">Saint Vincent and the
                                                    Grenadines</option>
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
                                                <option value="United States of America" selected>United States of
                                                    America (USA)</option>
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
                                            <?php $input->label('Province', ''); ?>
                                            <select class="form_field" id="province" name="Province"></select>
                                        </div>
                                        <div class="group ifdelivery" id="city_con">
                                            <?php
                                            $input->label('City', '*');
                                            $input->fields('City', 'form_field', 'city_usa', 'placeholder="Enter city here"');
                                            ?>
                                        </div>
                                    </div>
                                    <div class="form_box_col2">
                                        <div class="group" id="muni_city">
                                            <?php $input->label('Municipality/City', ''); ?>
                                            <select class="form_field" id="city" name="Municipality_or_City"></select>
                                        </div>
                                        <div class="group" id="zip_ph_con">
                                            <?php
                                            $input->label('Zip Code', '*');
                                            $input->fields('Zip_Code', 'form_field', 'zip_ph', 'placeholder="Enter zip code here"');
                                            ?>
                                        </div>
                                    </div>
                                </div>
                                <div class="form_box">
                                    <div class="form_box_col2 forZip">
                                        <div class="group" id="state_con">
                                            <?php
                                            $input->label('State', '');
                                            $input->select('State', 'form_field', $state, 'state_usa');
                                            ?>
                                        </div>
                                        <div class="group ifdelivery" id="zip_con">
                                            <?php
                                            $input->label('Zip Code', '*');
                                            $input->fields('_Zip_Code', 'form_field', 'zip_usa', 'placeholder="Enter zip code here"');
                                            ?>
                                        </div>
                                    </div>
                                </div>


                                <div class="form_box">
                                    <div class="form_box_col1">
                                        <div class="group ifdelivery">
                                            <?php
                                            $input->label('Additional Details', '*');
                                            $input->textarea('_Additional_Details', 'form_field', '', 'placeholder="House No., Building Name, Apartment No., Landmark or other navigation instructions"');
                                            ?>
                                        </div>
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
    <script type="text/javascript" src="js/city_state.min.js"></script>
    <script type="text/javascript" src="js/addressFunctionality.min.js"></script>
    <script type="text/javascript" src="js/jquery.validate.min.js"></script>
    <script type="text/javascript" src="js/jquery.datepick.min.js"></script>
    <script src="js/datepicker.js"></script>
    <script src="js/plugins.min.js"></script>
    <script src="js/jquery.mask.min.js"></script>
    <script src="js/proweaverPhone.js"></script>
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
                ignore: ":hidden",
                rules: {
                    Name: "required",
                    Contact_Number: "required",
                    Email_Address: {
                        required: true,
                        email: true
                    },
                    Address: "required",
                    City: "required",
                    _Zip_Code: "required",
                    _Additional_Details: "required",
                    '1)_Prescription_Name_': "required",

                    Quantity_: "required",
                    Pick_up_or_Delivery: "required"
                },
                messages: {
                    Name: "",
                    Contact_Number: "",
                    Email_Address: "",
                    '1)_Prescription_Name_': "",

                    Quantity_: "",
                    Address: "",
                    City: "",
                    _Zip_Code: "",
                    _Additional_Details: "",
                    Pick_up_or_Delivery: ""

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

            // 	//  hide/show function

            // $("input[name='Pick_up_or_Delivery']").change(function(){
            // 	if($(this).val() == "Delivery")	{
            // 		$(".ifdelivery .required_filed").html("*");
            // 		$('.ifdelivery input, .ifdelivery textarea').each(function(){
            // 			$(this).rules( "add", {
            // 			required: true,
            // 			messages: {required:""}
            // 		});
            // 		});


            // 	}else{
            // 		$(".ifdelivery .required_filed").html("");
            // 		$('.ifdelivery input, .ifdelivery textarea').each(function(){
            // 			$(this).rules( "remove", "required");
            // 			$(this).removeClass("error");

            // 		});

            // 	}
            // });

            //  hide/show function
            $('#Delivery_Address').hide();
            $("input[name='Pick_up_or_Delivery']").change(function() {
                if ($(this).val() == "Delivery") {
                    $("#Delivery_Address").slideDown();
                    $("#Delivery_Address").find(':input').attr('disabled', false);
                } else {
                    $("#Delivery_Address").slideUp();
                    $("#Delivery_Address").find(':input').attr('disabled', 'disabled');
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