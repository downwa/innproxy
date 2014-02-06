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
