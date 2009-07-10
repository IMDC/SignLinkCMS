$(document).ready(function() {
   // do stuff when DOM is ready
   
	// this enables highlighting on mouse over of posts (image, video or text)
	$("div.title").children().each(function() {
		  $(this).hover(
							 function () { $(this).addClass("highlight"); },
							 function () { $(this).removeClass("highlight"); }
				);
	});
	
	// this animates the vlog icon in the upper menu
	$("#vlogicon").hover(
							  function () { $(this).attr("src", "images/vlogicon3.gif"); },
							  function () { $(this).attr("src", "images/vlog.png"); }
				);
	/*
	$("#registerSubmitButton").click(
							  function () { $("form:last").submit(); alert("some alert");
				});
	*/
});
