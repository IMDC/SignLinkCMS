$(document).ready(function() {

	if(typeof(subject)=="undefined") {

		if ($("input[@name='subject']:checked").val() == "image") {
			$("#subject-image").show();
			$("#subject-video").hide();
			$("#subject-text").hide();

		} else if ($("input[@name='subject']:checked").val() == "video") {
			$("#subject-image").hide();
			$("#subject-video").show();
			$("#subject-text").hide();

		} else if ($("input[@name='subject']:checked").val() == "text") {
			$("#subject-image").hide();
			$("#subject-video").hide();
			$("#subject-text").show();
		} else {
			$("#subject-image").hide();
			$("#subject-video").hide();
			$("#subject-text").hide();
		}
	}

	if ($("input[@name='message']:checked").val() == "signlink") {
		$("#message-sl").show();
		$("#message-video").hide();
		$("#message-text").hide();

	} else if ($("input[@name='message']:checked").val() == "video") {
		$("#message-sl").hide();
		$("#message-video").show();
		$("#message-text").hide();

	} else if ($("input[@name='message']:checked").val() == "text") {
		$("#message-sl").hide();
		$("#message-video").hide();
		$("#message-text").show();
	} else {
		$("#message-sl").hide();
		$("#message-video").hide();
		$("#message-text").hide();
	}

	$("input[@name='subject']").change(
	function() {

		if ($("input[@name='subject']:checked").val() == "image") {
			$("#subject-image").show();
			$("#subject-video").hide();
			$("#subject-text").hide();

		} else if ($("input[@name='subject']:checked").val() == "video") {
			$("#subject-image").hide();
			$("#subject-video").show();
			$("#subject-text").hide();

		} else if ($("input[@name='subject']:checked").val() == "text") {
			$("#subject-image").hide();
			$("#subject-video").hide();
			$("#subject-text").show();
		}

	});

	$("input[@name='message']").change(
	function() {

		if ($("input[@name='message']:checked").val() == "signlink") {
			$("#message-sl").show();
			$("#message-video").hide();
			$("#message-text").hide();

		} else if ($("input[@name='message']:checked").val() == "video") {
			$("#message-sl").hide();
			$("#message-video").show();
			$("#message-text").hide();

		} else if ($("input[@name='message']:checked").val() == "text") {
			$("#message-sl").hide();
			$("#message-video").hide();
			$("#message-text").show();
		}

	});

});


function validateSubject() {
	var er_text = new Array();
	var er_string = "";
	var count = 0;

	if ($("input[@name='subject']:checked").val() == "image") {
		if ($("#isub-file").val()==null || $("#isub-file").val()=="") {
			er_text[count] = "Image subject file missing.";
			count++;
		}
		if ($("#isub-alt").val()==null || $("#isub-alt").val()=="") {
			er_text[count] = "Image subject alt text missing.";
			count++;
		}

	} else if ($("input[@name='subject']:checked").val() == "video") {
		if ($("#vsub-file").val()==null || $("#vsub-file").val()=="") {
			er_text[count] = "Video subject file missing.";
			count++;
		}
		if ($("#vsub-alt").val()==null || $("#vsub-alt").val()=="") {
			er_text[count] = "Video subject alt text missing.";
			count++;
		}

	} else if ($("input[@name='subject']:checked").val() == "text") {
		if ($("#sub-text").val()==null || $("#sub-text").val()=="") {
			er_text[count] = "Subject text missing.";
			count++;
		}
	} else {
		er_text[count] = "Subject missing.";
		count++;
	}

	return er_text;
} 

function validateMessage() {
	var er_text = new Array();
	var er_string = "";
	var count = 0;

	//check message
	if ($("input[@name='message']:checked").val() == "signlink") {
		if ($("#sl1msg-file").val()==null || $("#sl1msg-file").val()=="" ||
			$("#sl2msg-file").val()==null || $("#sl2msg-file").val()=="" ) {
			er_text[count] = "Signlink message file missing.";
			count++;
		}

	} else if ($("input[@name='message']:checked").val() == "video") {
		if ($("#vmsg-file").val()==null || $("#vmsg-file").val()=="") {
			er_text[count] = "Video message file missing.";
			count++;
		}
		if ($("#vmsg-alt").val()==null || $("#vmsg-alt").val()=="") {
			er_text[count] = "Video message alt text missing.";
			count++;
		}

	} else if ($("input[@name='message']:checked").val() == "text") {
		if ($("#msg-text").val()==null || $("#msg-text").val()=="") {
			er_text[count] = "Message text missing.";
			count++;
		}
	} else {
		er_text[count] = "Message missing.";
		count++;
	}

	return er_text;
};

function validateOnSubmit(area) {
	var er_text = new Array();
	var er_string = "";
	var myform = "";
	var count = 0;

	if (area=="subject") {
		myform = $('#form_sub');
		er_text = validateSubject();

	} else if (area=="message") {
		myform = $('#form_msg');
		er_text = validateMessage();

	} else if (area=="reply") {
		myform = document.form;
		er_text = validateMessage();

	} else {
		myform = document.form;
		er_text1 = validateSubject();
		er_text2 = validateMessage();

		er_text = er_text1.concat(er_text2);
	}

	if (er_text!="") {
		for (var i=0; i < er_text.length; i++) {
			er_string = er_string + '\n' + er_text[i];
		}
		alert('Corrections required: ' + er_string);
	} else {
		myform.submit();
	}
} 