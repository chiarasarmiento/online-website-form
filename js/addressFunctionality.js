var prov = document.getElementById("prov");
var muni_city = document.getElementById("muni_city");
var state_con = document.getElementById("state_con");
var state_con2 = document.getElementById("state_con2");
var city_con = document.getElementById("city_con");
var zip_con = document.getElementById("zip_con");
var zip_ph_con = document.getElementById("zip_ph_con");

var	province = document.getElementById("province");
var	city = document.getElementById("city");
var state_usa = document.getElementById("state_usa");
var city_usa = document.getElementById("city_usa");
var zip_usa = document.getElementById("zip_usa");
var zip_ph = document.getElementById("zip_ph");
var zip_ph = document.getElementById("zip_ph");

prov.style.display = "none";
city_con.style.display = "";
muni_city.style.display = "none";
zip_ph_con.style.display = "none";

state_usa.disabled = "";
city_usa.disabled = "";
zip_usa.disabled = "";

province.disabled = false;
city.disabled = false;

$('select[name="Country"]').change(function(){
//   if($(this).val() == "Philippines"){
//
//     province.disabled = false;
//     city.disabled = false;
//     zip_ph.disabled = false;
//     state_usa.disabled = true;
//     city_usa.disabled = true;
//     zip_usa.disabled = true;
//
//     prov.style.display = "block";
//     muni_city.style.display = "block";
//     zip_ph_con.style.display = "block";
//     state_con.style.display = "none";
//     city_con.style.display = "none";
//     zip_con.style.display = "none";
//
//   }
  // else
  if($(this).val() == "United States of America"){


    $("#country").removeClass("form_box_col3");
    $("#country").addClass("form_box_col2");

    province.disabled = true;
    city.disabled = true;
    zip_ph.disabled = true;
    state_usa.disabled = false;
    city_usa.disabled = false;
    zip_usa.disabled = false;

    prov.style.display = "none";
    muni_city.style.display = "none";
    zip_ph_con.style.display = "none";
    state_con.style.display = "block";
    city_con.style.display = "block";
    zip_con.style.display = "block";

    $('#state_usa').prop('disabled', false);
  } else {
  // $("#country").removeClass("form_box_col2");
  // $("#country").addClass("form_box_col3");
  // $(".forZip").removeClass("form_box_col2");
  // $(".forZip").addClass("form_box_col3");


    zip_ph_con.style.display = "none";
    zip_con.style.display = "block";
    prov.style.display = "none";
    muni_city.style.display = "none";
    state_con.style.display = "block";
    $('#state_usa').prop('disabled', true);
    $("#state_usa").prop("selectedIndex", 0);
    $("#state_usa").css({
            'color':'#a9a9a9',
            'font-style':'italic'
          });
    city_con.style.display = "block";

    zip_ph.disabled = true;
    state_usa.disabled = true;
    city_usa.disabled = false;
    zip_usa.disabled = false;
    province.disabled = true;
    city.disabled = true;
  }
});

// secondary

var prov_ = document.getElementById("prov_");
var muni_city_ = document.getElementById("muni_city_");
var state_con_ = document.getElementById("state_con_");
var state_con2_ = document.getElementById("state_con2_");
var city_con_ = document.getElementById("city_con_");
var zip_con_ = document.getElementById("zip_con_");
var zip_ph_con_ = document.getElementById("zip_ph_con_");

var	provinc_e = document.getElementById("province_");
var	city_ = document.getElementById("city_");
var state_usa_ = document.getElementById("state_usa_");
var city_usa_ = document.getElementById("city_usa_");
var zip_usa_ = document.getElementById("zip_usa_");
var zip_ph_ = document.getElementById("zip_ph_");
var zip_ph_ = document.getElementById("zip_ph_");

prov_.style.display = "none";
city_con_.style.display = "";
muni_city_.style.display = "none";
zip_ph_con_.style.display = "none";

state_usa_.disabled = "";
city_usa_.disabled = "";
zip_usa_.disabled = "";

province_.disabled = false;
city_.disabled = false;

$('select[name="Country_"]').change(function(){
//   if($(this).val() == "Philippines"){
//
//     province.disabled = false;
//     city.disabled = false;
//     zip_ph.disabled = false;
//     state_usa.disabled = true;
//     city_usa.disabled = true;
//     zip_usa.disabled = true;
//
//     prov.style.display = "block";
//     muni_city.style.display = "block";
//     zip_ph_con.style.display = "block";
//     state_con.style.display = "none";
//     city_con.style.display = "none";
//     zip_con.style.display = "none";
//
//   }
  // else
  if($(this).val() == "United States of America"){


    $("#country_").removeClass("form_box_col3");
    $("#country_").addClass("form_box_col2");

    province_.disabled = true;
    city_.disabled = true;
    zip_ph_.disabled = true;
    state_usa_.disabled = false;
    city_usa_.disabled = false;
    zip_usa_.disabled = false;

    prov_.style.display = "none";
    muni_city_.style.display = "none";
    zip_ph_con_.style.display = "none";
    state_con_.style.display = "block";
    city_con_.style.display = "block";
    zip_con_.style.display = "block";

    $('#state_usa_').prop('disabled', false);
  } else {
  // $("#country").removeClass("form_box_col2");
  // $("#country").addClass("form_box_col3");
  // $(".forZip").removeClass("form_box_col2");
  // $(".forZip").addClass("form_box_col3");


    zip_ph_con_.style.display = "none";
    zip_con_.style.display = "block";
    prov_.style.display = "none";
    muni_city_.style.display = "none";
    state_con_.style.display = "block";
    $('#state_usa_').prop('disabled', true);
    $("#state_usa_").prop("selectedIndex", 0);
    $("#state_usa_").css({
            'color':'#a9a9a9',
            'font-style':'italic'
          });
    city_con_.style.display = "block";

    zip_ph_.disabled = true;
    state_usa_.disabled = true;
    city_usa_.disabled = false;
    zip_usa_.disabled = false;
    province_.disabled = true;
    city_.disabled = true;
  }
});
