<html>
  <head><title><?=$site_name?> Internet portal</title>
    <META HTTP-EQUIV="CACHE-CONTROL" CONTENT="NO-CACHE" />
    <LINK rel="stylesheet" type="text/css" href="styles/public.css" />
  <script type="text/javascript"><!--
    function resize() {
        var innerWidth = window.innerWidth || document.documentElement.clientWidth || document.body.clientWidth;
        var innerHeight = window.innerHeight || document.documentElement.clientHeight || document.body.clientHeight;
        var targetWidth = 700;
        var targetHeight = 480;
        var xAdjust=targetWidth-innerWidth;
        if(xAdjust < 0) { xAdjust=0; }
        var yAdjust=targetHeight-innerHeight;
        if(yAdjust < 0) { yAdjust=0; }
        if(xAdjust > 0 || yAdjust > 0) { window.resizeBy(xAdjust, yAdjust); }
    }
    try { resize(); }
    catch(e) {}
    //-->
  </script>
  </head>
  
  <body onload="document.getElementById('user').focus();">
  <!-- NOTE: DO NOT REMOVE THIS NOTICE (used by redirect.php): IGLOOPORTAL LOGIN PAGE -->
  <script src="scripts/capsLock.js" type="text/javascript">//</script>
  <script type="text/javascript">
		function showStatus() {
			window.open('http://192.168.42.1:8080/status/?session=<?=$private_id?>&count=0','_blank'); // New Tab/Window
			//var sWin=window.open('http://192.168.42.1:8080/status/?session=<?=$private_id?>','sessionPopup', // New popup window
			//	config='height=100,width=400,toolbar=no,menubar=no,scrollbars=no,resizeable=no,location=no,directories=no,status=no');
			focus();
			return true;
		}
  </script>
  <center>
    <form name='login' method='post'>
      <div class="warning" id="capsWarning" style="display: none">
        Warning: Caps Lock is enabled
      </div>
        <table id="login" border=0 style="width:800px">
          <tr>
            <td rowspan="4" id="logotd">
              <img src="images/hotelsunset.jpg" alt="<?=$site_name?> Logo" id="logo" />
            </td>
            <td colspan="2">
                <center><h2 style="color: #333"><?=$site_name?><br />Internet portal</h2></center>
                <span style="color:red;font-size:8pt;">
                        Internet usage is monitored
                        and limited to 100 Mb/day.
                        Illegal activities, pornography, etc.
                        may cause account suspension.
                </span>
                <span style="color:blue;font-size:8pt;">
                        NOTE: The status popup must remain open
                        to validate access.
                </span>
                <br />
            <div class="warning" id="capsWarning" style="display: none">
                Warning: Caps Lock is enabled
            </div>
<?php       if(strlen($reason) > 0) { ?>
              <div id='reason' class="warning"><?=$reason?></div>
<?php       } ?>
            </tr>
            <tr>
              <td class="labeltd">Username</td>
              <td><input class='inputTextLogin' type='text' name='user' id='user' value="<?=$user?>" /></td>
            </tr>
            <tr>
              <td class="labeltd">Password</td>
              <td><input class='inputTextLogin' type='password' name='pass' id='pass' value="<?=$pass?>" /></td>
                  <input type="hidden" name="redirect" value="<?=$redirect?>" />
            </tr>
            <tr>
              <td></td>
              <td><input class='inputButton' type='submit' name="submit" id='loginButton' value="Enter" title="Login and redirect to original site" onclick="showStatus();"/></td>
            </tr>
          </table>
        </form>
      </div>
    </center>
  </body>
</html>