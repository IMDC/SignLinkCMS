$(document).ready(function() {
	// this enables highlighting on mouse over of posts (image, video or text)
	$("div.title").children().each(function() {
		  $(this).hover(
							 function () { $(this).addClass("highlight"); },
							 function () { $(this).removeClass("highlight"); }
				);
	});
   
	// this animates the home icon in the navigation menu
	$(".homenavicon").hover(
			  function () { $(this).attr("src", "../images/homeicon3.gif"); },
			  function () { $(this).attr("src", "../images/house_shadow.png"); }
		);
   
	// this animates the pages icon in the navigation menu
	$(".pagesnavicon").hover(
			  function () { $(this).attr("src", "../images/contentfaster.gif"); },
			  function () { $(this).attr("src", "../images/content.png"); }
		);
	
	// this animates the forum icon in the navigation menu
	$(".forumnavicon").hover(
			  function () { $(this).attr("src", "../images/forumicon2.gif"); },
			  function () { $(this).attr("src", "../images/group.png"); }
		);
	
	// this animates the vlog icon in the navigation menu
	$(".vlognavicon").hover(
			  function () { $(this).attr("src", "../images/vlogiconfaster.gif"); },
			  function () { $(this).attr("src", "../images/vlog.png"); }
		);
	
	// this animates the help icon in the navigation menu
	$(".helpnavicon").hover(
			  function () { $(this).attr("src", "../images/helpfaster.gif"); },
			  function () { $(this).attr("src", "../images/help3.png"); }
		);

  // this toggles highlighting for forum replies
  $("tr.reply_tr").bind("mouseenter mouseleave", function(event){
    $(this).toggleClass("reply_tr_highlight");
  });

   $("#testbutton").click(
      function () { 
         $("form:last").submit();
      }
   );
  
  $("img.quickView").hide();

   
     // assign 2 functions to mousein/mouseout events on the 'imgzoom_container' div of any post title
  $("div.imgzoom_container")
    .mouseenter(function() {
      // this line is required on the admin side as the image title's are displayed smaller and the magnifying glass shows up on the bottom right of the image
      $(this).find("a.quickViewLink").css("top", "-25 px").css("left","-70 px");
      
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
    
      $(".quickViewLink").fancybox();
    
});
