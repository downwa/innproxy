// Initialize calendar
$(function() {$("#datepicker").datepicker(); });

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
    for(var key in data) {
       appendClone(obj,data[key],dsname);
     }
  }
  catch(e) { alert('fillTable: '+e.message); }
}

function appendClone(obj,data,dsname) {
  var oclone=obj.cloneNode(true);
  if(obj.nextSibling) { obj.parentNode.insertBefore(oclone, obj.nextSibling); }
  else { obj.parentNode.appendChild(oclone); }
  oclone.setAttribute("data-iglooware-datasrc","CLONE-"+dsname);
  oclone.style.display='';
  // Fill clone with data
  for(var key in data) {
    oclone.innerHTML=oclone.innerHTML.replace("$"+key,data[key]);
  }
}