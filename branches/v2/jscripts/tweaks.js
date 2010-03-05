$(document).ready(function() {
   // do stuff when DOM is ready
   
	// this enables highlighting on mouse over of posts (image, video or text)
	$("div.title").children().each(function() {
		  $(this).hover(
							 function () { $(this).addClass("highlight"); },
							 function () { $(this).removeClass("highlight"); }
				);
	});
	
   /*
	// this animates the home icon in the navigation menu
	$("#homenavicon").hover(
			  function () { $(this).attr("src", "images/vlogicon3.gif"); },
			  function () { $(this).attr("src", "images/vlog.png"); }
		);
   
	// this animates the pages icon in the navigation menu
	$("#pagesnavicon").hover(
			  function () { $(this).attr("src", "images/vlogicon3.gif"); },
			  function () { $(this).attr("src", "images/vlog.png"); }
		);
	*/
   
	// this animates the forum icon in the navigation menu
	$("#forumnavicon").hover(
			  function () { $(this).attr("src", "images/forum.gif"); },
			  function () { $(this).attr("src", "images/group.png"); }
		);
	
	// this animates the vlog icon in the navigation menu
	$("#vlognavicon").hover(
			  function () { $(this).attr("src", "images/vlogicon3.gif"); },
			  function () { $(this).attr("src", "images/vlog.png"); }
		);
	
	// this animates the help icon in the navigation menu
	$("#helpnavicon").hover(
			  function () { $(this).attr("src", "images/help.gif"); },
			  function () { $(this).attr("src", "images/help.png"); }
		);
	/*
	$("#registerSubmitButton").click(
							  function () { $("form:last").submit(); alert("some alert");
				});
	*/
});
