<html>
<body>
<head>
	<title>Custom File Browser</title>
	<link rel="stylesheet" href="asl.css" type="text/css" media="screen" />
<script language="javascript" type="text/javascript" src="../tinymce/jscripts/tiny_mce/tiny_mce_popup.js"></script> 
<script language="javascript" type="text/javascript">
  var win = tinyMCE.getWindowArg("window");
  var input = tinyMCE.getWindowArg("input");
  var res = tinyMCE.getWindowArg("resizable");
  var inline = tinyMCE.getWindowArg("inline");
  var id = tinyMCE.getWindowArg("editor_id");

  if (document.location.search.indexOf('submit=ok') != -1) {
    var xURL = document.location.search.replace('submit=ok&','');
    alert(xURL); // testing that url is correct
    getit(xURL);
  }

  function getit(URL) {
    if (input == 'src')  {
      win.document.getElementById(input).value = URL;
      if (type == 'images')  {
        win.showPreviewImage(URL);
      }
      else {
        win.switchType(URL);
        win.generatePreview();
      }
    }
    else {
      win.document.getElementById(input).value = URL;
    }
    tinyMCEPopup.close();
  }

  function submitProcess() {
    document.getElementById('frmImageBrowse').action =
         document.getElementById('frmImageBrowse').action + '?submit=ok' +
         '&url=' + '../images/' + document.getElementById('lblImage').innerHTML;
    document.getElementById('frmImageBrowse').submit();
  }
</script>
</head>
<body>   
    <form id="frmImageBrowse" runat="server" onsubmit="submitProcess();">
        <table border="0" cellpadding="0" cellspacing="0" style="width: 325px">
            <tr>
                <td colspan="2" style="height: 14px">
                    Upload your photos:</td>
            </tr>
            <tr>
                <td colspan="2">
					<input type="file" name="fullImage" />
				</td>
            </tr>
            <tr>
                <td colspan="2">
                </td>
            </tr>
            <tr>
                <td colspan="2" valign="top" style="height: 30px">
                    hello</td>
            </tr>
            <tr>
                <td style="width: 51px">
					<input type="submit" name="btnSubmit" value="Upload" onclick="btnSubmit_Click" />
				</td>
                <td style="width: 108px;">
					<input type="submit" name="btnSubmit" value="Close" onclick="tinyMCEPopup.close();" />
                </td>
            </tr>
        </table>
    </form>
</body>
</html>