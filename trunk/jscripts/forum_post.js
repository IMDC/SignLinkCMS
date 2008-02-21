$(document).ready(function() {

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
		$("#subject-text").hied();
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

function validateOnSubmit() {
	var er_text = new Array();
	var er_string = "";
	var count = 0;

	//check subject if not a reply

	if (typeof(document.form.subject) == "undefined") {
		if (document.form.subject[0].checked) {
			if (document.getElementById('isub-file').value==null || document.getElementById('isub-file').value=="") {
				er_text[count] = "Image subject file missing.";
				count++;
			}
			if (document.getElementById('isub-alt').value==null || document.getElementById('isub-alt').value=="") {
				er_text[count] = "Image subject alt text missing.";
				count++;
			}

		} else if (document.form.subject[1].checked) {
			if (document.getElementById('vsub-file').value==null || document.getElementById('vsub-file').value=="") {
				er_text[count] = "Video subject file missing.";
				count++;
			}
			if (document.getElementById('vsub-alt').value==null || document.getElementById('vsub-alt').value=="") {
				er_text[count] = "Video subject alt text missing.";
				count++;
			}

		} else if (document.form.subject[2].checked) {
			if (document.getElementById('sub-text').value==null || document.getElementById('sub-text').value=="") {
				er_text[count] = "Subject text missing.";
				count++;
			}
		} else {
			er_text[count] = "Subject missing.";
			count++;
		}
	}

	//check message
	if (document.form.message[0].checked) {
		if (document.getElementById('sl1msg-file').value==null || document.getElementById('sl1msg-file').value=="" ||
			document.getElementById('sl2msg-file').value==null || document.getElementById('sl2msg-file').value=="" ) {
			er_text[count] = "Signlink message file missing.";
			count++;
		}

	} else if (document.form.message[1].checked) {
		if (document.getElementById('vmsg-file').value==null || document.getElementById('vmsg-file').value=="") {
			er_text[count] = "Video message file missing.";
			count++;
		}
		if (document.getElementById('vmsg-alt').value==null || document.getElementById('vmsg-alt').value=="") {
			er_text[count] = "Video message alt text missing.";
			count++;
		}

	} else if (document.form.message[2].checked) {
		if (document.getElementById('msg-text').value==null || document.getElementById('msg-text').value=="") {
			er_text[count] = "Message text missing.";
			count++;
		}
	} else {
		er_text[count] = "Message missing.";
		count++;
	}

	if (er_text!="") {
		for (var i=0; i < er_text.length; i++) {
			er_string = er_string + '\n' + er_text[i];
		}
		alert('Corrections required: ' + er_string);
	} else {
		document.form.submit();
	}

	return;
};