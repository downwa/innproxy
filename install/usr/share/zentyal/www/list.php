<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">

<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="X-UA-Compatible" content="IE=9; IE=8; IE=7; IE=EDGE" />
	<title>InnProxy Management</title>
</head>
<body>
<input type="text" name="first_name" placeholder="Your first name...">
<div id="main">Loading...</div>
<iframe src="" style="width:90%; height:10%;" frameBorder="1" id="ajax">
</iframe>
<script>
function load() {
	try {
		var x=document.getElementById('ajax');
		var y=(x.contentWindow || x.contentDocument);
		if(y.document) { y=y.document; }
		x.src='listinactive.php';
	}
	catch(e) { alert('listinactive.load: '+e.message); }
}
function done(doc) {
	try {
		var html=doc.getElementsByTagName('HTML')[0];
		var main=document.getElementById('main');
		document.getElementsByTagName('TITLE')[0].innerHTML+='-'+html.getElementsByTagName('TITLE')[0].innerHTML;
		main.innerHTML=html.getElementsByTagName('BODY')[0].innerHTML;
		document.styleSheets[0]=doc.styleSheets[0];
		alert(document.styleSheets[0]+';len='+document.styleSheets.length);
	}
	catch(e) { alert('listinactive.done: '+e.message); }
}

load();
</script>
</body>
</html>
