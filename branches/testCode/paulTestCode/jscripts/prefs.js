$.validator.addMethod("passwordRegEx", function(value) {
      
   // Regex to enforce upper or lowercase letters, one or more
   var letterRE = /(?=[a-zA-Z]{1,})/;
   // Regex to enforce occurence of one digit or more
   var numberRE = /(?=\d{1,})/;
   // Regex to enforce total length of value is eight characters or more
   var eightRE = /(?=.{8,})/;
   // Regex to enforce at least one symbol character or more
   var symbolRE = /(?=[^\w\s]{1,})/;
   //Regex to enforce whitespace one or more times
   var whitespaceRE = /(?=(\s){1,})/;

   // simply used so empty password input fields do not validate unneccessarily
   if (value == "") return true;

   // test function value against our password requirements
   if (eightRE.test(value) && !whitespaceRE.test(value) && 
            ( ( letterRE.test(value) && numberRE.test(value) ) || 
               ( letterRE.test(value) && symbolRE.test(value) ) )) {
               return true;
   }
   else {
      return false;
   }      
   }, 'Passwords must contain a letter, number or symbol and be 8 characters or more'
);
$.validator.addMethod("nameRegEx", function(value) {
   // Regex to enforce upper or lowercase letters, three or more
   var letterRE = /(?=[a-zA-Z]{3,})/;
   //Regex to enforce no whitespace in value whatsoever
   var whitespacebookendRE = /^\s{1,}.*\s{1,}$/;
   // Regex to enforce at least one symbol character or more
   var symbolRE = /(?=[^\w\s]{1,})/;
   
   if (value == "") return false;
   
   if (letterRE.test(value) && !whitespacebookendRE.test(value) && !symbolRE.test(value)) {
      return true;
   }
   else {
      return false;
   }
}, 'Name must be at least 3 letters or more and contain no symbols');


$(document).ready(function(){
   
   //using the JQuery validate plugin
   // validate signup form on keyup and submit 
   var validator = $("#prefForm").validate({
       
   rules: {
//      prefname: {
//         required: true,
//         minlength: 3,
//         maxlength: 40
//      },
      prefname: 'nameRegEx',
      email: { 
         required: true,
         email: true
      },
      oldpass: 'passwordRegEx',
      /*{
          required: false,
          minlength: 8
      },
      */
      newpass1: 'passwordRegEx',
      newpass2: {
         required: false, 
         minlength: 8, 
         equalTo: "#newpass1"
      }
   }, 
   messages: { 
      prefname: jQuery.format("You must enter a name of at least {0} characters"),
      email: {
          required: "You must enter an email address", 
          minlength: jQuery.format("Enter at least {0} characters")
      }
   }, 
   // error placement        
   errorPlacement: function(error, element) { 
      
      var elemname = element.attr("name");
      if (elemname == "name" || elemname == "email") {
         error.appendTo(element.parent());
      }
      else {
         error.insertAfter(element);
      }
      
      
//      if (element.attr("name") == "oldpass") {
//         error.insertAfter("#oldpass");
//      }
//      else if (element.attr("name") == "newpass1") {
//         error.insertAfter("#newpass1");
//      }
//      else if (element.attr("name") == "newpass2") {
//         error.insertAfter("#newpass2");
//      }
//      else {
//         error.appendTo(element.parent());
//      }
      element.scrollTop();
   }, 

   // set this class to error-labels to indicate valid fields 
   success: function(label) { 
      // set as text for IE
      label.html(" ").addClass("success");
   }});

    // validate the user's name and email upon page load
   $("#prefForm").validate().element( "#prefname" );
   $("#prefForm").validate().element( "#prefemail" );
   
   // hide the success checkmark for blank input on the oldpass input field
   $("#oldpass").blur(function() {
      var value = $(this).val();
      if (value == "") {
         $("label[for=oldpass]").removeClass("success error");
      }
   });
   
   // hide the success checkmark for blank input on the newpass1 input field
   $("#newpass1").blur(function() {
      var value = $(this).val();
      if (value == "") {
         $("label[for=newpass1]").removeClass("success error");
      }
   });
});

