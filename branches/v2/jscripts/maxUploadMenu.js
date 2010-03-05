function menu_change(menu) {
		var sel = menu.selectedIndex;
		
		if (menu.options[sel].text == "Custom") {
			document.getElementById("customSizeDiv").style.visibility="visible";
		}
		else
			document.getElementById("customSizeDiv").style.visibility="hidden";
	}

function menu_setCustom(menu) {
	document.getElementById("custom_name").value=document.getElementById("custom_size").value;
}