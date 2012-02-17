$(document).ready(function() {
	// this enables highlighting on mouse over of posts (image, video or text)
	$("div.title, div.title-upper").hover(
         function () { $(this).addClass("highlight"); },
         function () { $(this).removeClass("highlight"); }
    );
    $("div.title, div.title-upper").children().each(function() {
		  $(this).hover(
              function () { $(this).addClass("highlight"); },
              function () { $(this).removeClass("highlight"); }
          );
	});
   $("div.title-goto-wrap").hover(
      function() { $(this).addClass("highlight");
                   $(this).parent().children().first().addClass("highlight"); 
                 },
      function() { $(this).removeClass("highlight");
                   $(this).parent().children().first().removeClass("highlight"); 
                 }
   );
      
   $("div.cat").hover(
         function() { $(this).addClass("outerShadow"); },
         function() { $(this).removeClass("outerShadow");}
   );
   
   // On focus of any text input field, the background is highlighted
   $("input[type=text]").focus(function() {
      $(this).css("background", "#66ff99");
   });
   $("input[type=text]").blur(function() {
      $(this).css("background", "#ffffff");
   })
   
	// this animates the home icon in the navigation menu
	$(".homenavicon").hover(
			  function () { $(this).attr("src", "images/homeicon3.gif"); },
			  function () { $(this).attr("src", "images/house_shadow.png"); }
		);
   
	// this animates the pages icon in the navigation menu
	$(".pagesnavicon").hover(
			  function () { $(this).attr("src", "images/contentfaster.gif"); },
			  function () { $(this).attr("src", "images/content.png"); }
		);
	
	// this animates the forum icon in the navigation menu
	$(".forumnavicon").hover(
			  function () { $(this).attr("src", "images/forumicon2.gif"); },
			  function () { $(this).attr("src", "images/group.png"); }
		);
	
	// this animates the vlog icon in the navigation menu
	$(".vlognavicon").hover(
			  function () { $(this).attr("src", "images/vlogiconfaster.gif"); },
			  function () { $(this).attr("src", "images/vlog.png"); }
		);
	
	// this animates the help icon in the navigation menu
	$(".helpnavicon").hover(
			  function () { $(this).attr("src", "images/helpfaster.gif"); },
			  function () { $(this).attr("src", "images/help.png"); }
		);

  $("#login-button-container").click( function() {
      $("#loginform").submit();
  }).hover(
      function() {
         $(this).find("input#submitLogin").addClass("submitLoginHover");
      },
      function() {
         $(this).find("input#submitLogin").removeClass("submitLoginHover");
      }
   );


  // this fires login lightbox
  $("a#inline").fancybox({
      'hideOnContentClick': false 
  });


  // this toggles highlighting for forum replies
  /*
  $("tr.reply_tr").bind("mouseenter mouseleave", function(event){
    $(this).toggleClass("reply_tr_highlight");
  });
  */

  // this javascript
  $("#testbutton").click(
     function () {
        $("form:last").submit();
     }
  );

  $("a.findmehere").fancybox();

  $("a.quickViewLink").fancybox();

  $("img.quickView").hide();


  // assign 2 functions to mousein/mouseout events on the 'imgzoom_container' div of any post title
  $("div.imgzoom_container")
    .mouseenter(function() {
      // find the title image inside the div container
      $results = $(this).children("a.quickViewLink");
      // check to see if the title of the post is an image with class 'expand'
      if ($results.length != 0) {
        //$results.children("img.quickView").show();
        $results.children("img.quickView").css("display", "inline").css("visibility", "visible");
        //$results.show();
        $results.css("display", "inline").css("visibility", "visible");
      }
    })
    .mouseleave(function() {
      $results = $(this).children("a.quickViewLink");
      if ($results.length != 0) {
        //$results.children("img.quickView").hide();
        $results.children("img.quickView").css("visibility", "hidden").css("display", "none");
        //$results.hide();
        $results.css("visibility", "hidden").css("display", "none");
      }
    });
});

function expThis(text) {
  if (text == null) {
    alert("default text");
  }
  else {
    alert(source);
  }
  return false;
}

/**
 * This function will be used with an <input type='file'> element
 * and should be called when the user finishes selecting a video file to upload.
 * This function will check the filename as a string and make sure the extension
 * matches any extension from the video_filetypes defined constant array in
 * constants.inc.php
 * 
 * param inputElement  the input element
 */
function checkVideoFileType(inputElement) {
   // TODO: write this function to check the filename against the video_filetypes array
   
}

/**
 * This function will be used with an <input type='file'> element
 * and should be called when the user finishes selecting an image file to upload.
 * This function will check the filename as a string and make sure the extension
 * matches any extension from the video_filetypes defined constant array in
 * constants.inc.php
 * 
 * param inputElement  the input element
 */
function checkImageFileType(inputElement) {
   
}

/**
 * This function will be used to check if a file selected by the user
 * for upload is too large as defined by the installations max filesize
 */
function checkInputSize(inputElement) {
   

}

