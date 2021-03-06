// Initialize calendar
//$(function() {$("#datepicker").datepicker(); });
 $(function() {$("#datepicker").datepicker({
  buttonImage: 'lib/img/cal.gif',
  buttonImageOnly: true,
  changeMonth: true,
  changeYear: true,
  showOn: 'both',
})});

var COLUMNS=3;

function post(fn,form) { php('async',fn,$('#'+form).serialize()); }
function run(fn) {
  var parmurl='';
  for(key in arguments[1]) {
    if(parmurl.length>0) { parmurl+='&'; }
    parmurl+=(encodeURIComponent(key)+'='+encodeURIComponent(arguments[1][key]));
  }
  php('async',fn,parmurl);
}
function data(divName) {
  run('datasrc',{question:'universe', answer: 43});
}

function filterObject(fun, obj) {
		var len = this.length;
		if (typeof fun != "function") { throw new TypeError(); }
		var res = new Object();
		var thisp = arguments[1];

		for (prop in obj) {
			var val = obj[prop]; // in case fun mutates this
			if (fun.call(thisp, val, prop, obj)) { res[prop]=val; }
		}		
		return res;

}

function fillTables(dsname, data) {  
  try {
    var items=document.getElementsByTagName("*");
    for(var xa=items.length-1; xa>=0; xa--) {
      if(items[xa].getAttribute("data-iglooware-datasrc") == dsname) {
        fillTable(items[xa],data,dsname);
      }
      else if(items[xa].getAttribute("data-iglooware-datasrc") == "CLONE-"+dsname) {
        items[xa].parentNode.removeChild(items[xa]);
      }
    }
    //alert(document.getElementsByTagName('body')[0].innerHTML);
  }
  catch(e) { alert('fillTable: '+e.message); }
}

function categoryMatches(element, index, array) { return (index.startsWith('diner')); }
function categoryNotMatches(element, index, array) { return !(index.startsWith('diner')); }
function fillTable(obj,data,dsname) {
  try {
    obj.style.display='none';
    if(data==null) { return; }
    
    if(dsname == 'users') {
			if(document.location.hash == '#diner') {
				data=filterObject(categoryMatches,data);
				var userCreateForm=document.getElementById('userCreateForm');
				userCreateForm.style.display='none';
			}
			else {
				data=filterObject(categoryNotMatches,data);
				var userCreateForm=document.getElementById('userCreateForm');
				userCreateForm.style.display='';
			}
		}
    
    var printClasses=obj.getAttribute("data-iglooware-printclasses").split(",");
    var sortedKeys=keys(data).sort().reverse(); // ["a", "b", "z"]
    var clsidx=(sortedKeys.length%COLUMNS)-1; // Start at end since we build in reverse
    for(var xa=0; xa<sortedKeys.length; xa++) { // key in data) {
      if(clsidx < 0) { clsidx=COLUMNS-1; }
      var appendClass=printClasses[clsidx];
      //alert('clsidx='+clsidx);
      appendClone(obj,data[sortedKeys[xa]],dsname,appendClass);
      clsidx--;
    }
  }
  catch(e) { alert('fillTable: '+e.message); }
}

/** Clone obj, fill with data, mark datasource=dsname, and append specified appendClass to object class **/
function appendClone(obj,data,dsname,appendClass) {
  var oclone=obj.cloneNode(true);
  if(obj.nextSibling) { obj.parentNode.insertBefore(oclone, obj.nextSibling); }
  else { obj.parentNode.appendChild(oclone); }
	// Special cases
	if(data['bytes']) { data['pct']=data['bytes']*100/100000000; data['mbytes']=Math.round(data['bytes']/1000000); }
	else { data['pct']=0; data['mbytes']=0; }
	if(data['ipaddr'] != "")     { appendClass+=' hasIP'; }
	if(data['pct'] > 100)        { appendClass+=' isOver'; }
	if(data['disabled'] == true) { appendClass+=' isDisabled'; }
	
	// Fill clone with data
	for(var key in data) {
		var regex = new RegExp("\\$"+key, 'g');
		oclone.innerHTML=oclone.innerHTML.replace(regex, data[key]);
	}
	// Set style
	oclone.setAttribute("data-iglooware-datasrc","CLONE-"+dsname);
	oclone.className+=' '+appendClass;
	oclone.style.display='';
	}

function keys(obj) {
  var keys = [];  
  for(var key in obj) {
    if(obj.hasOwnProperty(key)) { keys.push(key); }
  }
  return keys;
}
