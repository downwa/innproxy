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
  <script src="scripts/capsLock.js" type="text/javascript">//</script>
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
                <div style="color:red;font-size:8pt;">
                        Internet usage is monitored
                        and limited to 100 Mb/day.
                        Illegal activities, pornography, etc.
                        may cause account suspension.
                </div>
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
              <td><input class='inputButton' type='submit' name="submit" id='loginButton' value="Enter" /></td>
            </tr>
          </table>
        </form>
      </div>
    </center>
  </body>
</html>