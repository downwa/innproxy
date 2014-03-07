// Initialize calendar
$(function() {$("#datepicker").datepicker(); });

var COLUMNS=3;
var clsidx=0; // Start at end since we build in reverse

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

function fillTable(obj,data,dsname) {
  try {
    obj.style.display='none';
    if(data==null) { return; }
    var printClasses=obj.getAttribute("data-iglooware-printclasses").split(",");
    for(var key in data) {
      var appendClass=printClasses[clsidx];
        appendClone(obj,data[key],dsname,appendClass);
        clsidx--;
        if(clsidx < 0) { clsidx=COLUMNS-1; }
    }
  }
  catch(e) { alert('fillTable: '+e.message); }
}

/** Clone obj, fill with data, mark datasource=dsname, and append specified appendClass to object class **/
function appendClone(obj,data,dsname,appendClass) {
  var oclone=obj.cloneNode(true);
  if(obj.nextSibling) { obj.parentNode.insertBefore(oclone, obj.nextSibling); }
  else { obj.parentNode.appendChild(oclone); }
  oclone.setAttribute("data-iglooware-datasrc","CLONE-"+dsname);
  oclone.className+=' '+appendClass;
  oclone.style.display='';
  // Fill clone with data
  for(var key in data) {
    oclone.innerHTML=oclone.innerHTML.replace("$"+key,data[key]);
  }
}