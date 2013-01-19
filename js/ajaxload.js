function loadajax(frmname)
{
  this.formobj=document.forms[frmname];
	if(!this.formobj)
	{
	  alert("BUG: couldnot get Form object "+frmname);
		return;
	}
	if(this.formobj.onsubmit)
	{
	 this.formobj.old_onsubmit = this.formobj.onsubmit;
	 this.formobj.onsubmit=null;
	}
	else
	{
	 this.formobj.old_onsubmit = null;
	}
	this.formobj.onsubmit=ajax_handler;
	
}

function ajax_handler()
{
	if(ajaxform(this, this.action) =='failed')
	return true;
	
	return false;
}

function formload_ajax(frm){
		var frmloadajax  = new loadajax(frm);
}



var hand = function(str){
	window.document.getElementById('response_span').innerHTML=str;
}
/*function validateUsername(user){
	var strDomain='';
	window.document.getElementById('response_span').innerHTML="Validating username...";
	var valajax = new ValAjax();
	valajax.doGet(strDomain+'validator.php?action=validateUsername&username='+user,hand,'text');
}*/


function ajax_call (url, callback_function, error_function) {
	var xmlHttp = null;
	try {
		// for standard browsers
		xmlHttp = new XMLHttpRequest ();
	} catch (e) {
		// for internet explorer
		try {
			xmlHttp = new ActiveXObject ("Msxml2.XMLHTTP");
	    } catch (e) {
			xmlHttp = new ActiveXObject ("Microsoft.XMLHTTP");
	    }
	}
	xmlHttp.onreadystatechange = function () {
		if (xmlHttp.readyState == 4)
			try {
				if (xmlHttp.status == 200) {
					// we got the data, let's call the callback function
					callback_function (xmlHttp.responseText);
				}
			} catch (e) {
				// oops we have an error, call the error function
				error_function (e.description);
			}
	 }
	 // open the url
	 xmlHttp.open ("GET", url);
	 // and send the data
	 xmlHttp.send (null);
 }
 // --------------------------------------------------- USER ----------------------------------------------------------------------------------- //
 
 function usercheck_init(i) {
	var obj = document.getElementById('ajax_output');
	obj.innerHTML = ''; // <- clear the status field
	
	if (i.value.length < 1) return;
	
 	var err = new Array ();
	if (i.value.match (/[^A-Za-z0-9_]/)) err[err.length] = 'Username can only contain letters, numbers and underscores';
 	if (i.value.length < 3) err[err.length] = 'Username too short';
 	if (err != '') {
	 	obj.style.color = '#ff0000';
	 	obj.innerHTML = err.join ('<br />');
	 	return;
 	}
 	ajax_call ('validator.php?u='+i.value+'user', usercheck_callback, usercheck_error); 
 }
 
  function usercheck_callback (data) {
 	var response = (data == '1');

 	var obj = document.getElementById('ajax_output');
 	obj.style.color = (response) ? '#008800' : '#ff0000';
 	obj.innerHTML = (response == '1') ? 'Username OK' : 'Username already taken';
 }
 
  function usercheck_error (err) {
 	alert ("Error: " + err);
 }

// ------------------------------------------------------ USER ---------------------------------------------------------------------------------- //

// ------------------------------------------------------ Student ------------------------------------------------------------------------------ //

 function usercheck_init_student(i) {
	var obj = document.getElementById('ajax_output_st');
	obj.innerHTML = ''; // <- clear the status field
	
	if (i.value.length < 1) return;
	
 	var err = new Array ();
	if (i.value.match (/[^A-Za-z0-9_]/)) err[err.length] = 'Username can only contain letters, numbers and underscores';
 	if (i.value.length < 3) err[err.length] = 'Username too short';
 	if (err != '') {
	 	obj.style.color = '#ff0000';
	 	obj.innerHTML = err.join ('<br />');
	 	return;
 	}
 	ajax_call ('validator.php?u='+i.value+'stud', usercheck_callback_student, usercheck_error_student); 
 }

 function usercheck_callback_student (data) {
 	var response = (data == '1');

 	var obj = document.getElementById('ajax_output_st');
 	obj.style.color = (response) ? '#008800' : '#ff0000';
 	obj.innerHTML = (response == '1') ? 'Username OK' : 'Username already taken';
 }

 function usercheck_error_student (err) {
 	alert ("Error: " + err);
 }

// ------------------------------------------------------ Student ------------------------------------------------------------------------------ //

// ------------------------------------------------------ Student ID------------------------------------------------------------------------------ //

 function usercheck_student_id(i) {
	var obj = document.getElementById('ajax_output_stid');
	obj.innerHTML = ''; // <- clear the status field
	
	if (i.value.length < 1) return;
	
 	var err = new Array ();
	if (i.value.match (/[^0-9_]/)) err[err.length] = 'Student ID can only contain numbers';
 	//if (i.value.length < 3) err[err.length] = 'Username too short';
 	if (err != '') {
	 	obj.style.color = '#ff0000';
	 	obj.innerHTML = err.join ('<br />');
	 	return;
 	}
 	ajax_call ('validator_int.php?u='+i.value+'stid', usercheck_callback_student_id, usercheck_error_student_id); 
 }

 function usercheck_callback_student_id (data) {
 	var response = (data == '1');

 	var obj = document.getElementById('ajax_output_stid');
 	obj.style.color = (response) ? '#008800' : '#ff0000';
 	obj.innerHTML = (response == '1') ? 'Student ID OK' : 'Student ID already taken';
 }

 function usercheck_error_student_id (err) {
 	alert ("Error: " + err);
 }

// ------------------------------------------------------ Student ID------------------------------------------------------------------------------ //

