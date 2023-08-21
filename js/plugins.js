$(document).ready(function(){


	// var get_height = $('#contact_us_form_1 .form_frame_b').height();
	// $('#contact_us_form_1 .form_frame_a').height(get_height);

	$('body').append("<div class='load_holder'><div class='spinner'><div class='bounce1'></div><div class='bounce2'></div><div class='bounce3'></div></div>	<p>Submitting Form...</p></div>");


	$('.close').click(function(){
		$('#success').fadeOut();
		$('#recaptcha-error').fadeOut();
	});


	$('.rclose').click(function(){
		$('#recaptcha-error').fadeOut();
	});

	$('.error-close').click(function(){
		$('#error-msg').fadeOut();
	});


	$('input[type="radio"]').click(function(){
		$('input[type="radio"].error + label.error').remove();
	});


var wskCheckbox = function() {
  var wskCheckboxes = [];
  var SPACE_KEY = 32;

  function animateCircle(checkboxElement) {
    var circle =
        checkboxElement.parentNode.getElementsByClassName('wskCircle')[0];
    var restore = '';
    if (circle.className.indexOf('flipColor') < 0) {
      restore = circle.className + ' flipColor';
    } else {
      restore = 'wskCircle';
    }
    circle.className = restore  + ' show';
    setTimeout(function(){
      circle.className = restore;
    }, 150);
  }

  function addEventHandler(elem, eventType, handler) {
    if (elem.addEventListener) {
      elem.addEventListener (eventType, handler, false);
    }
    else if (elem.attachEvent) {
      elem.attachEvent ('on' + eventType, handler);
    }
  }

  function clickHandler(e) {
    e.stopPropagation();
    if (this.className.indexOf('checked') < 0) {
      this.className += ' checked';
    } else {
      this.className = 'wskCheckbox';
    }
    animateCircle(this);
  }

  function keyHandler(e) {
    e.stopPropagation();
    if (e.keyCode === SPACE_KEY) {
      clickHandler.call(this, e);
      // Also update the checkbox state.
      var cbox = document.getElementById(this.parentNode.getAttribute('for'));
      cbox.checked = !cbox.checked;
    }
  }

  function clickHandlerLabel(e) {
    var id = this.getAttribute('for');
    var i = wskCheckboxes.length;
    while (i--) {
      if (wskCheckboxes[i].id === id) {
        if (wskCheckboxes[i].checkbox.className.indexOf('checked') < 0) {
          wskCheckboxes[i].checkbox.className += ' checked';
        } else {
          wskCheckboxes[i].checkbox.className = 'wskCheckbox';
        }
        animateCircle(wskCheckboxes[i].checkbox);
        break;
      }
    }
  }

  function checkboxValues(inputAttrName) {
		var inputAttrName = inputAttrName;
		var inputHidden = $('input[name="'+inputAttrName+'"]').attr('value');
		var checkedValues = '';
		var checkboxClass = $('input.'+inputAttrName+'');

		$.each(checkboxClass, function(index) {
			$(this).on('change', function() {
				var x = $(this).attr('value') + ', ';
				if($(this).is(':checked')) {
					inputHidden += x;
					checkedValues = inputHidden.replace(/,\s*$/, "");
					$('input[name="'+inputAttrName+'"]').attr('value', checkedValues);
				} else {
					inputHidden = inputHidden.replace(x, '');
					checkedValues = inputHidden.replace(/,\s*$/, "");
					$('input[name="'+inputAttrName+'"]').attr('value', checkedValues);
				}
			});
		});
	}

  function findCheckBoxes() {
    var labels =  document.getElementsByTagName('label');
    var i = labels.length;
    while (i--) {
      var posCheckbox = document.getElementById(labels[i].getAttribute('for'));
      if (posCheckbox !== null && posCheckbox.type === 'checkbox' &&
          (posCheckbox.className.indexOf('wskCheckbox') >= 0)) {
        var text = labels[i].innerText;
        var span = document.createElement('span');
        span.className = 'wskCheckbox';
        span.tabIndex = i;
        var span2 = document.createElement('span');
        span2.className = 'wskCircle flipColor';
        labels[i].insertBefore(span2, labels[i].firstChild);
        labels[i].insertBefore(span, labels[i].firstChild);
        addEventHandler(span, 'click', clickHandler);
        addEventHandler(span, 'keyup', keyHandler);
        addEventHandler(labels[i], 'click', clickHandlerLabel);
        var cbox = document.getElementById(labels[i].getAttribute('for'));
        if (cbox.getAttribute('checked') !== null) {
          span.click();
        }

        wskCheckboxes.push({'checkbox': span,
            'id': labels[i].getAttribute('for')});
      }
    }
  }

  return {
    init: findCheckBoxes
  };
}();

wskCheckbox.init();


/* start add more */
 
var cloneCount = 1; 

$('.addMore').click(function(){

  var html = $('.cloneField').html();

  $('.addreferral').append("<div class='clone_data' id='mainCloneCount_0'>"+html+"<a href='javascript:;' style=' background: #f95858;  top: 7px;     padding: 3px 5px; color: #fff; border-radius: 3px; position: relative; bottom: 0;' class='removeCls' onclick='removeHTML()'><i class='fas fa-minus-circle'></i> Remove</a><hr></div>");

  $('.addreferral').each(function(){
	var i = 1;

	$(this).find('.clone_data').each(function(){
	  i = parseInt(i + 1);
	  $(this).attr('id','mainCloneCount_'+i);
	  $(this).find('.referral_name input').attr('name', 'Name'+' ('+i+')');
	  $(this).find('.referral_email input').attr('name', 'Email_Address_'+' ('+i+')').attr('id',  'Email_Address_'+i);
	  $(this).find('.referral_phone input').attr('name', 'Contact_Number'+' ('+i+')').addClass('Contact_Number');
	  $(this).find('.referral_count input').val(i);
	  $(this).find('.removeCls').attr('onClick', 'removeHTML('+i+')');
	});

	$('#Email_Address_'+i).rules( "add", {
		email: true,
		messages: {email: "" }
	});
  });

  $(".Phone").keypress(function(e) {
	  var verified = (e.which == 8 || e.which == undefined || e.which == 0) ? null : String.fromCharCode(e.which).match(/[^0-9 -]/);
	  if (verified) {e.preventDefault();}
  });


});




  $('#error-message').hide();
	var MAX_FILE_SIZE = 10 * 1024 * 1024; // 10MB
	$("#file").change(function () {
		 var ul_file = $(this).val();
		 var extension = ul_file.substr((ul_file.lastIndexOf('.') + 1));
		 var accepted_file_endings = ["pdf", "docx", "doc"];
		 extension = extension.toLowerCase();
		 fileSize = this.files[0].size;

		 if ($(this).val() !== "") {
			 if (fileSize > MAX_FILE_SIZE) {
				$('.suberror').text('File size exceeds maximum limit: 10 MB');
				$('#error-message').slideDown();
				$(".js-labelFile").addClass('uploaderror');
				$(".js-fileName").css({ "color": "#5c5c5c"});
				$("#file").val('');
			 }
			 else {
				if ($.inArray(extension, accepted_file_endings) !== -1) {
					$(".js-labelFile").removeClass('uploaderror');
					$('#error-message').hide();
				}else {
					$('.suberror').html('File type is not allowed. You can only upload <span style="left: 0; bottom: 0;  font-style:italic; color: unset; font-size: unset;">doc, docx, pdf</span> files.');
					$('#error-message').slideDown();
					$(".js-labelFile").addClass('uploaderror');
					$(".js-fileName").css({ "color": "#5c5c5c"});
					$("#file").val('');
				}
			}
		 }else {
			$(".btn-tertiary").css({ "box-shadow": "0 7px 10px rgba(182,182,182,.05) !important"});
			$('#error-message').hide();
		 }
	});


(function() {

	  'use strict';

	  $('.input-file').each(function() {
		var $input = $(this),
			$label = $input.next('.js-labelFile'),
			labelVal = $label.html();

	   $input.on('change', function(element) {
		  var fileName = '';
		  if (element.target.value) fileName = element.target.value.split('\\').pop();
		  fileName ? $label.addClass('has-file').find('.js-fileName').html(fileName) : $label.removeClass('has-file').html(labelVal);
	   });
	  });

	})();

	function scaleCaptcha(elementWidth) {
	  // Width of the reCAPTCHA element, in pixels
	  var reCaptchaWidth = 304;
	  // Get the containing element's width
		var containerWidth = $('.form_box5').width();

	  // Only scale the reCAPTCHA if it won't fit
	  // inside the container
	  if(reCaptchaWidth > containerWidth) {
		// Calculate the scale
		var captchaScale = containerWidth / reCaptchaWidth;
		// Apply the transformation
		$('.g-recaptcha').css({
		  'transform':'scale('+captchaScale+')'
		});
	  }
	}

	$(function() {

	  // Initialize scaling
	  scaleCaptcha();

	  // Update scaling on window resize
	  // Uses jQuery throttle plugin to limit strain on the browser
	  $(window).resize( $.throttle( 100, scaleCaptcha ) );

	});
   $(".Alphanumeric, label:contains('Social Security Number'), input[name='Social_Security_Number']").keyup(function() {
      if (this.value.match(/[^a-zA-Z0-9 ]/g)) {
        this.value = this.value.replace(/[^a-zA-Z0-9 ]/g, '');
      }
    });

    $(".Alphanumeric, label:contains('Social Security Number'), input[name='Social_Security_Number']").focusout(function() {
      this.value = this.value.trim();
    });

	$("#Phone, input[name='Phone'], input[name='Phone_Number'], input[name='Cell_Number'], input[name='Telephone'], input[name='Telephone_Number'], input[name='Fax_Number'], .numberinput, input[name='Contact_Number']").keypress(function(e) {
            var verified = (e.which == 8 || e.which == undefined || e.which == 0) ? null : String.fromCharCode(e.which).match(/[^0-9 -]/);
            if (verified) {e.preventDefault();}
    });

  $("label:contains('Phone Number'), label:contains('Cell Number'), label:contains('Cellphone Number'), label:contains('Telephone'), label:contains('Telephone Number'), label:contains('Fax Number'), label:contains('Fax'), label:contains('Cel'), label:contains('Contact Number')").each(function(){
    $(this).parent().next('div').find(':input').keypress(function(e) {
              var verified = (e.which == 8 || e.which == undefined || e.which == 0) ? null : String.fromCharCode(e.which).match(/[^0-9 -]/);
              if (verified) {e.preventDefault();}
      });
  });


	$('#Phone, input[name="Phone_Number"]').keypress(function() {
		 if($(this).val().length >= 12) {
			$(this).val($(this).val().slice(0, 12));
		}
	});

    $('select').each(function() {
        if ($(this).val() == "")
          $(this).css({
            'color':'#b1b1b1',
			'font-style':'normal'
          });
        else
          $(this).css({
            'color':'#5c5c5c',
			'font-style':'normal'
          });
    });

    $('select').change(function() {
        if ($(this).val() == "")
          $(this).css({
            'color':'#b1b1b1',
			'font-style':'normal'
          });
        else
          $(this).css({
            'color':'#5c5c5c',
			'font-style':'normal'
          });
    });

});

	$('input:radio').click(function() {
		$("input:radio").each(function() {
			$(this).closest("td").toggleClass("highlight", $(this).is(":checked"));
		});
	});

	$("label:contains('Date of Birth'), label:contains('Birthdate'), label:contains('How soon can you start'), label:contains('Preferred Date'), label:contains('on what date can you start work')").each(function(){
		$(this).parent().next('div').find(':input').removeClass('Date').addClass('Date1');
	});
	$("label:contains('Date of Birth'), label:contains('Birthdate')").each(function(){
	$(this).parent().next('div').find(':input').addClass('DisableFuture');
	});
	$("label:contains('How soon can you start'), label:contains('Preferred Date'),  label:contains('on what date can you start work')").each(function(){
	$(this).parent().next('div').find(':input').addClass('DisablePast');
	});



	// NORMAL DATE FIELDS
	$('.Date').datepicker({
			autoHide: true,
			pick: function(e) {
			  e.preventDefault(); //prvent any default action..
			  var pickedDate = e.date; //get date
			  var date = e.date.getDate()
			  var month = $(this).datepicker('getMonthName')
			  var year = e.date.getFullYear()
			  var new_date = month + " " + date + ", " + year
			  //set date
			  // $(this).val(`${date} ${month} ${year}`)
			  $(this).val(new_date)

			}
	});

	// DISABLE FUTURE DATES
	var today = Date.now();
	$('.DisableFuture').datepicker({
		autoHide: true,
		zIndex: 2048,
		endDate: today,
			pick: function (e) {
			e.preventDefault(); //prvent any default action..
			var pickedDate = e.date; //get date
			var date = e.date.getDate()
			var month = $(this).datepicker('getMonthName')
			var year = e.date.getFullYear()
			var new_date = month + " " + date + ", " + year
			$(this).val(new_date)
		}
	});

	// DISABLE PAST DATES
	$('.DisablePast').datepicker({
		autoHide: true,
		zIndex: 2048,
		startDate: today,
			pick: function (e) {
			e.preventDefault(); //prvent any default action..
			var pickedDate = e.date; //get date
			var date = e.date.getDate()
			var month = $(this).datepicker('getMonthName')
			var year = e.date.getFullYear()
			var new_date = month + " " + date + ", " + year

			$(this).val(new_date)
		}
	});


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


	


$(function() {
	$('input[name="First_Name"], input[name="Last_Name"], input[name="Name"], input[name="Full_Name"]').keydown(function(e) {
		var key = e.keyCode;
			if (!((key == 8) || (key == 32) || (key == 46) || (key >= 35 && key <= 40) || (key >= 65 && key <= 90))) {
				e.preventDefault();
			}
	});
});


