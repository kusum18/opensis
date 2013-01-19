/* 
#
#
#Discipline Module
#Copyright (C) 2008 Billboard.Net
#Designer(s): Christopher Whiteley
#Contributor(s): Russell Holmes
#
#
*/ 


var discipline = {};

discipline.showPerpetrator = function(identifier){
	check_content('ajax.php?modname=Discipline/incidenteditor.php&module=1&identifier='+identifier);
};

discipline.showDetails = function(identifier){
	check_content('ajax.php?modname=Discipline/incidenteditor.php&identifier='+identifier);
};

discipline.showVictim = function(identifier){
	check_content('ajax.php?modname=Discipline/incidenteditor.php&module=2&identifier='+identifier);
};

discipline.showDiscipline = function(identifier){
	check_content('ajax.php?modname=Discipline/incidenteditor.php&module=3&identifier='+identifier);
};

discipline.schools;

discipline.clearIdentifier = function(elem){
	elem.value = "";
};

discipline.checkIdentifier = function(elem){
	if(elem.value == "" || elem.value == null){
		elem.value="Indentifier Search";
	}
};

discipline.urlencode = function(str) {
str = escape(str);
str = str.replace('+', '%2B');
str = str.replace('%20', '+');
str = str.replace('*', '%2A');
str = str.replace('/', '%2F');
str = str.replace('@', '%40');
str = str.replace('[]','%5B%5D');
return str;
}

discipline.urldecode = function(str) {
str = str.replace(/%5B%5D/g, '[]');
var length = str.length;
for(var i=0;i<length;i++)
{	
	str = str.replace('+', ' ');
}
str = unescape(str);
return str;
}

discipline.getDesc = function(elem){
	var id = elem.value;
	if(id == 0){
		return;
	}
	var tableName = elem.id;
	
	var ajax = new Ajax.Request('modules/Discipline/ajax/getDescription.php',   
			{   method:'post',    
			    parameters: {id:id,tablename:tableName},
			       	    onSuccess: function(transport)
			       	    {     
					try
					{
						var jsonData = transport.responseText || "no response text";
						var jsonObject = eval('('+jsonData+')');
						if(jsonObject.result[0].success)
						{
							var desc = jsonObject.desc[0].value;
							$(tableName+'_td').innerHTML = desc;
						}
						else
						{
							alert('Error Getting Description');
						}
					}
					catch(ex)
					{
						alert('Error Getting Description');
					}
				    },     
				    onFailure: function()
				    { 
					alert('Something went wrong...');
				    }   
        });
};

discipline.save = function(){
	var form = $('incidentFrm');
	var data = form.serialize();
	
	var ajax = new Ajax.Request('modules/Discipline/ajax/updateIncident.php',   
				{   method:'post',    
				    parameters: data,
				       	    onSuccess: function(transport)
				       	    {     
						try
						{
							var jsonData = transport.responseText || "no response text";
							var jsonObject = eval('('+jsonData+')');
							if(jsonObject.result[0].success)
							{
								alert('Incident Updated');
							}
							else
							{
								alert('Error Updating Incident');
							}
						}
						catch(ex)
						{
							alert('Error Updating Incident');
						}
					    },     
					    onFailure: function()
					    { 
						alert('Something went wrong...');
					    }   
        });
}

discipline.createNew = function(){
	var form = $('incidentFrm');
	var data = form.serialize();
	
	var ajax = new Ajax.Request('modules/Discipline/ajax/createIncident.php',   
				{   method:'post',    
				    parameters: data,
				       	    onSuccess: function(transport)
				       	    {     
						try
						{
							var jsonData = transport.responseText || "no response text";
							var jsonObject = eval('('+jsonData+')');
							if(jsonObject.result[0].success)
							{
								alert('Incident Created');
								var identifier = jsonObject.identifier[0].value;
								discipline.editIncident(identifier);
							}
							else
							{
								alert('Error Generating Incident');
							}
						}
						catch(ex)
						{
							//alert(ex);
							alert('Error Generating Incident');
						}
					    },     
					    onFailure: function()
					    { 
						alert('Something went wrong...');
					    }   
        });
}

discipline.reset = function(){

	check_content('ajax.php?modname=Discipline/incidenteditor.php');
};

discipline.editIncident = function(identifier){

	check_content('ajax.php?modname=Discipline/incidenteditor.php&identifier='+identifier);
};

discipline.filterDashboard = function(){
	var schoolId   = $('schoolFilter').value;
	var timestamp  = $('timestampFilter').value;
	var timeId     = $('timeFilter').value;
	var openClosed = $('openClsoedFilter').value;
	
	check_content('ajax.php?modname=Discipline/dashboard.php&openClosed='+openClosed+'&schoolid='+schoolId+'&timestamp='+timestamp+'&timeid='+timeId);
};

discipline.searchIdentifier = function(){
	var value  = $('searchIdentifier').value;
	if(value == '' || value == null){
		return;
	}
	
	check_content('ajax.php?modname=Discipline/dashboard.php&identifier='+value);
};

/*
##########################################################################
#Code Editor Section #####################################################
##########################################################################
##########################################################################
*/

discipline.codes;

discipline.showWorkArea = function(elem, area, action){
	$(area+"Section").style.display = "";
	elem.innerHTML = 'Cancel Edit '+area;
	elem.onclick = function(){
		discipline.hideWorkArea(this, area, action);
	};
	if(action != null){
		action();
	}
};

discipline.hideWorkArea = function(elem, area, action){
	$(area+"Section").style.display = "none";
	elem.innerHTML = 'Edit '+area;
	elem.onclick = function(){
		discipline.showWorkArea(this, area, action);
	};
	if(action != null){
		action();
	}
};

/*##School Section##########################################################*/

discipline.schools;

discipline.getSchools = function(){
	$('schoolWorkArea').innerHTML = "<img src='assets/ajax_loader.gif' width='15px' height='15px' />";
	var ajax = new Ajax.Request('modules/Discipline/ajax/getSchools.php',   
				{   method:'post',    
				    parameters: {},
				       	    onSuccess: function(transport)
				       	    {     
						try
						{
							var jsonData = transport.responseText || "no response text";
							var jsonObject = eval('('+jsonData+')');
							if(jsonObject.result[0].success)
							{
								var schools = jsonObject.schools;
								discipline.schools = schools;
								discipline.buildSchoolTbl();
							}
							else
							{
								alert('Error Getting Schools');
							}
						}
						catch(ex)
						{
							//alert(ex);
							alert('Error Getting Schools');
						}
					    },     
					    onFailure: function()
					    { 
						alert('Something went wrong...');
					    }   
        });	
};

discipline.buildSchoolTbl = function(){
	var html = '<table style="width:550px;border:solid 1px black;padding:2px 2px 2px 2px" cellspacing="0" cellpadding="0">'+
		     '<thead style="background-color:#CCC;font-weight:bold;">'+
		     '<tr>'+
		     '<td align="center" style="width:150px;">Name</td>'+
		     '<td align="center" style="width:100px;">School Code</td>'+
		     '<td align="center" style="width:100px">District Code</td>'+
		     '<td align="center" style="width:100px">State Code</td>'+
		     '<td align="center" style="width:100px;">Hidden</td>'+
		     '</tr>'+
		     '</thead>';
		     
	var length = discipline.schools.length;
	var hidden;
	for(var i = 0; i < length; i++){
		if(discipline.schools[i].hidden == 0){
			hidden = "No";
		}
		else{
			hidden = "Yes";
		}
		
		if(i % 2 == 0){
			html += '<tr onclick="discipline.showEditSchool('+i+');" style="background-color:#FFFF99;cursor:pointer;vertical-align:top;" onmouseover="this.style.backgroundColor = \'yellow\'" onmouseout="this.style.backgroundColor=\'#FFFF99\'">'+
				'<td align="center">'+discipline.schools[i].desc+'</td>'+
				'<td align="center">'+discipline.schools[i].schoolCode+'</td>'+
				'<td align="center">'+discipline.schools[i].districtCode+'</td>'+
				'<td align="center">'+discipline.schools[i].stateCode+'</td>'+
				'<td align="center">'+hidden+'</td>'+
				'</tr>';
		}
		else{
			html += '<tr onclick="discipline.showEditSchool('+i+');" style="cursor:pointer;vertical-align:top;" onmouseover="this.style.backgroundColor = \'yellow\'" onmouseout="this.style.backgroundColor=\'\'" >'+
				'<td align="center">'+discipline.schools[i].desc+'</td>'+
				'<td align="center">'+discipline.schools[i].schoolCode+'</td>'+
				'<td align="center">'+discipline.schools[i].districtCode+'</td>'+
				'<td align="center">'+discipline.schools[i].stateCode+'</td>'+
				'<td align="center">'+hidden+'</td>'+
				'</tr>';
		}
	}
	html += '<tr><td colspan="5">&nbsp;</td></tr><tr><td colspan="5"><input type="button" value="Add" style="cursor:pointer;width:75px;" onclick="discipline.showNewSchool();" /></td></tr></table>';
	$('schoolWorkArea').innerHTML = html;
};

discipline.showEditSchool = function(index){
	
	var schoolCode    = discipline.schools[index].schoolCode;
	var schoolName    = discipline.schools[index].desc;
	var districtCode  = discipline.schools[index].districtCode;
	var stateCode     = discipline.schools[index].stateCode;
	var id            = discipline.schools[index].schoolId;
	var hidden        = discipline.schools[index].hidden;
	
	var html = '<form id="editSchoolFrm"><table><tr><td style="font-weight:bold;" >Hidden</td><td><select name="hidden">';
	
	if(hidden == 1){
			html += '<option value="1" selected="true">Yes</option><option value="0">No</option>';
		}
		else{
			html += '<option value="1">Yes</option><option value="0" selected="true">No</option>';
		}
	html += '</select></td></tr>';
	
	html += '<tr><td style="font-weight:bold;">School Name</td><td><input type="text" size="30" value="'+schoolName+'" name="schooldesc" /></td></tr>'+
		'<tr><td style="font-weight:bold;">School Code</td><td><input type="text" size="10" value="'+schoolCode+'" name="schoolcode" /></td></tr>'+
		'<tr><td style="font-weight:bold;">District Code</td><td><input type="text" size="10" value="'+districtCode+'" name="districtcode" /></td></tr>'+
		'<tr><td style="font-weight:bold;">State Code</td><td><input type="text" size="10" value="'+stateCode+'" name="statecode" /></td></tr>'+
		'<input type="hidden" name="schoolid" value="'+id+'" /></form><br/><input type="button" value="Save" onclick="discipline.updateSchool();" /> | <input type="button" value="Cancel" onclick="discipline.cancelSchoolEdit();" />';
	$('schoolWorkArea').innerHTML = html;
};

discipline.showNewSchool = function(){
	
	var html = '<form id="newSchoolFrm"><table><tr><td style="font-weight:bold;" >Hidden</td><td><select name="hidden">'+
		   '<option value="1">Yes</option>'+
		   '<option value="0" selected="true">No</option></select></td></tr>'+
		   '<tr><td style="font-weight:bold;">School Name</td><td><input type="text" size="30" name="schooldesc" /></td></tr>'+
		   '<tr><td style="font-weight:bold;">School Code</td><td><input type="text" size="10" name="schoolcode" /></td></tr>'+
		   '<tr><td style="font-weight:bold;">District Code</td><td><input type="text" size="10" name="districtcode" /></td></tr>'+
		   '<tr><td style="font-weight:bold;">State Code</td><td><input type="text" size="10" name="statecode" /></td></tr>'+
		   '</form><br/><input type="button" value="Save" onclick="discipline.saveNewSchool();" /> | <input type="button" value="Cancel" onclick="discipline.cancelSchoolEdit();" />';
	$('schoolWorkArea').innerHTML = html;
};

discipline.saveNewSchool = function(){
	var form = $('newSchoolFrm');
	var data = form.serialize();
	
	var ajax = new Ajax.Request('modules/Discipline/ajax/addSchool.php',   
				{   method:'post',    
				    parameters: data,
				       	    onSuccess: function(transport)
				       	    {     
						try
						{
							var jsonData = transport.responseText || "no response text";
							var jsonObject = eval('('+jsonData+')');
							if(jsonObject.result[0].success)
							{
								alert('Added');
								discipline.getSchools();
							}
							else
							{
								alert('Error Adding');
							}
						}
						catch(ex)
						{
							//alert(ex);
							alert('Error Updating');
						}
					    },     
					    onFailure: function()
					    { 
						alert('Something went wrong...');
					    }   
        });
};

discipline.updateSchool = function(){
	var form = $('editSchoolFrm');
	var data = form.serialize();
	
	var ajax = new Ajax.Request('modules/Discipline/ajax/updateSchool.php',   
				{   method:'post',    
				    parameters: data,
				       	    onSuccess: function(transport)
				       	    {     
						try
						{
							var jsonData = transport.responseText || "no response text";
							var jsonObject = eval('('+jsonData+')');
							if(jsonObject.result[0].success)
							{
								alert('Updated');
								discipline.getSchools();
							}
							else
							{
								alert('Error Updating');
							}
						}
						catch(ex)
						{
							//alert(ex);
							alert('Error Updating');
						}
					    },     
					    onFailure: function()
					    { 
						alert('Something went wrong...');
					    }   
        });
};

discipline.cancelSchoolEdit = function(){
	discipline.buildSchoolTbl();
};

/*##Code Section############################################################*/

discipline.getCodes = function(codeType){
	$('codesTable').innerHTML = "<img src='assets/ajax_loader.gif' width='15px' height='15px' />";
	if(codeType == 0){
		$('codesTable').innerHTML = "";
		return;
	}
	var ajax = new Ajax.Request('modules/Discipline/ajax/getCurrentCodes.php',   
				{   method:'post',    
				    parameters: {codetype:codeType},
				       	    onSuccess: function(transport)
				       	    {     
						try
						{
							var jsonData = transport.responseText || "no response text";
							var jsonObject = eval('('+jsonData+')');
							if(jsonObject.result[0].success)
							{
								var codes = jsonObject.codes;
								discipline.codes = codes;
								discipline.buildCodeTbl(codes);
							}
							else
							{
								alert('Error Getting Codes');
							}
						}
						catch(ex)
						{
							//alert(ex);
							alert('Error Getting Codes');
						}
					    },     
					    onFailure: function()
					    { 
						alert('Something went wrong...');
					    }   
        });	
};

discipline.buildCodeTbl = function(codes){
	$('codeTypeSel').style.display = "";
	var html = '<table style="width:700px;border:solid 1px black;padding:2px 2px 2px 2px" cellspacing="0" cellpadding="0">'+
		   '<thead style="background-color:#CCC;font-weight:bold;">'+
		   '<tr>'+
		   '<td align="center" style="width:50px;">Code</td>'+
		   '<td align="center" style="width:200px;">Display</td>'+
		   '<td align="center" style="width:400px">Description</td>'+
		   '<td align="center" style="width:50px;">Hidden</td>'+
		   '</tr>'+
		   '</thead>';
	var length = codes.length;
	var hidden;
	for(var i = 0; i < length; i++){
		if(codes[i].hidden == 0){
			hidden = "No";
		}
		else{
			hidden = "Yes";
		}
		
		if(i % 2 == 0){
			html += '<tr onclick="discipline.showEditCode('+i+');" style="background-color:#FFFF99;cursor:pointer;vertical-align:top;" onmouseover="this.style.backgroundColor = \'yellow\'" onmouseout="this.style.backgroundColor=\'#FFFF99\'"><td align="center">'+codes[i].code+'</td><td>'+codes[i].display+'</td><td>'+codes[i].desc+'</td><td align="center">'+hidden+'</td></tr>';
		}
		else{
			html += '<tr onclick="discipline.showEditCode('+i+');" style="cursor:pointer;vertical-align:top;" onmouseover="this.style.backgroundColor = \'yellow\'" onmouseout="this.style.backgroundColor=\'\'" ><td align="center">'+codes[i].code+'</td><td>'+codes[i].display+'</td><td>'+codes[i].desc+'</td><td align="center">'+hidden+'</td></tr>';
		}
	}
	html += '<tr><td colspan="4" align="left">&nbsp;</td></tr><tr><td colspan="4" align="left"><input type="button" onclick="discipline.showNewCode();" value="Add" style="width:75px;cursor:pointer;" /></td></tr></table>';
	$('codesTable').innerHTML = html;
};

discipline.showNewCode = function(){
	
	var codeType = $('codeSelBox').value;
	
	var html = '<form id="newCodeFrm"><table><tr><td style="font-weight:bold;" >Hidden</td><td><select name="hidden">'+
		   '<option value="0">No</option><option value="1">Yes</option></select></td></tr>'+
		   '<tr><td style="font-weight:bold;">Code</td><td><input type="text" size="10" name="code" /></td></tr>'+
		   '<tr><td style="font-weight:bold;">Display</td><td><input type="text" size="35" name="display" /></td></tr>'+
		   '<tr><td colspan="2" style="font-weight:bold;">Description</td></tr>'+
		   '<tr><td colspan="2"><textarea cols="40" rows="5" name="description"></textarea></td></tr></table><input type="hidden" value="'+codeType+'" name="codetype" />'+
		   '</form><br/><input type="button" value="Save" onclick="discipline.saveNew();" /> | <input type="button" value="Cancel" onclick="discipline.cancelEdit();" />';
	$('codesTable').innerHTML = html;
	$('codeTypeSel').style.display = "none";
};

discipline.saveNew = function(){
	var form = $('newCodeFrm');
	var data = form.serialize();
	
	var ajax = new Ajax.Request('modules/Discipline/ajax/addCode.php',   
				{   method:'post',    
				    parameters: data,
				       	    onSuccess: function(transport)
				       	    {     
						try
						{
							var jsonData = transport.responseText || "no response text";
							var jsonObject = eval('('+jsonData+')');
							if(jsonObject.result[0].success)
							{
								alert('Added');
								discipline.getCodes($('codeSelBox').value);
							}
							else
							{
								alert('Error Adding');
							}
						}
						catch(ex)
						{
							//alert(ex);
							alert('Error Updating');
						}
					    },     
					    onFailure: function()
					    { 
						alert('Something went wrong...');
					    }   
        });
};

discipline.showEditCode = function(index){
	
	var code     = discipline.codes[index].code;
	var display  = discipline.codes[index].display;
	var desc     = discipline.codes[index].desc;
	var hidden   = discipline.codes[index].hidden;
	var id       = discipline.codes[index].id;
	var codeType = $('codeSelBox').value;
	
	var html = '<form id="editCodeFrm"><table><tr><td style="font-weight:bold;" >Hidden</td><td><select name="hidden">';
	
	if(hidden == 1){
			html += '<option value="1" selected="true">Yes</option><option value="0">No</option>';
		}
		else{
			html += '<option value="1">Yes</option><option value="0" selected="true">No</option>';
		}
	html += '</select></td></tr>';
	
	html += '<tr><td style="font-weight:bold;">Code</td><td><input type="text" size="10" value="'+code+'" name="code" /></td></tr>'+
		'<tr><td style="font-weight:bold;">Display</td><td><input type="text" size="35" value="'+display+'" name="display" /></td></tr>'+
		'<tr><td colspan="2" style="font-weight:bold;">Description</td></tr>'+
		'<tr><td colspan="2"><textarea cols="40" rows="5" name="description">'+desc+'</textarea></td></tr></table><input type="hidden" value="'+id+'" name="codeid" /><input type="hidden" value="'+codeType+'" name="codetype" />'+
		'</form><br/><input type="button" value="Save" onclick="discipline.saveEdit();" /> | <input type="button" value="Cancel" onclick="discipline.cancelEdit();" />';
	$('codesTable').innerHTML = html;
	$('codeTypeSel').style.display = "none";
};

discipline.cancelEdit = function(){
	$('codeTypeSel').style.display = "";
	discipline.buildCodeTbl(discipline.codes);
};

discipline.saveEdit = function(){
	var form = $('editCodeFrm');
	var data = form.serialize();
	
	var ajax = new Ajax.Request('modules/Discipline/ajax/updateCode.php',   
				{   method:'post',    
				    parameters: data,
				       	    onSuccess: function(transport)
				       	    {     
						try
						{
							var jsonData = transport.responseText || "no response text";
							var jsonObject = eval('('+jsonData+')');
							if(jsonObject.result[0].success)
							{
								alert('Updated');
								discipline.getCodes($('codeSelBox').value);
							}
							else
							{
								alert('Error Updating');
							}
						}
						catch(ex)
						{
							//alert(ex);
							alert('Error Updating');
						}
					    },     
					    onFailure: function()
					    { 
						alert('Something went wrong...');
					    }   
        });
};

/*
##########################################################################
#Perpetrator Section #####################################################
##########################################################################
##########################################################################
*/

discipline.users;

discipline.removePerpetrator = function(perpId){
	var ajax = new Ajax.Request('modules/Discipline/ajax/removePerpetrator.php',   
				{   method:'post',    
				    parameters: {perpid:perpId},
				       	    onSuccess: function(transport)
				       	    {     
						try
						{
							var jsonData = transport.responseText || "no response text";
							var jsonObject = eval('('+jsonData+')');
							if(jsonObject.result[0].success)
							{
								alert('Perpetrator Removed');
								var identifier = $('identifier').value;
								discipline.showPerpetrator(identifier);
							}
							else
							{
								alert('Error Removing Perpetrator');
							}
						}
						catch(ex)
						{
							//alert(ex);
							alert('Error Removing Perpetrator');
						}
					    },     
					    onFailure: function()
					    { 
						alert('Something went wrong...');
					    }   
        });
};

discipline.selectPerpType = function(elem){
	if(elem.value == 0){
		$('perpetratorTR').style.display = "none";
		$('injuryTR').style.display = "none";
		$('buttonsTR').style.display = "none";
		$('searchBtn').style.display = "none";
		return;
	}
	
	$('perpetratorTR').style.display = "";
	$('injuryTR').style.display = "";
	$('buttonsTR').style.display = "";
	
	if(elem.value == 100 || elem.value == 200 || elem.value == 300){
		$('searchBtn').style.display = "";
	}
	else{
		$('searchBtn').style.display = "none";
	}
	
	if(elem.value == 999){
		$('searchBtn').style.display = "none";
		$('perpInput').value = "N/A";
		$('perpInput').readOnly = true;
	}
	else{
		$('perpInput').value = "";
		$('perpInput').readOnly = false;
	}
};

discipline.searchUsers = function(){
	var searchValue = $('perpInput').value;
	if(searchValue == ''){
		return;
	}
	var perpType = $('perpTypeSel').value;
	
	var ajax = new Ajax.Request('modules/Discipline/ajax/searchUsers.php',   
				{   method:'post',    
				    parameters: {value:searchValue,type:perpType},
				       	    onSuccess: function(transport)
				       	    {     
						try
						{
							var jsonData = transport.responseText || "no response text";
							var jsonObject = eval('('+jsonData+')');
							if(jsonObject.result[0].success)
							{
								discipline.users = jsonObject.users;
								if(perpType <= 100){
									discipline.buildStudentSearchResults(discipline.users);
								}
								else{
									discipline.buildStaffSearchResults(discipline.users);
								}
							}
							else
							{
								alert('Error Searching');
							}
						}
						catch(ex)
						{
							//alert(ex);
							alert('Error Searching');
						}
					    },     
					    onFailure: function()
					    { 
						alert('Something went wrong...');
					    }   
        });	
	
};

discipline.buildStudentSearchResults = function(users){
	var html = '<table style="width:370px;border:solid 1px black;padding:2px 2px 2px 2px" cellspacing="0" cellpadding="0">'+
			'<thead style="background-color:#CCC;font-weight:bold;">'+
			'<tr>'+
			'<td align="center" style="width:50px;">ID</td>'+
			'<td align="center" style="width:100px;">First</td>'+
			'<td align="center" style="width:20px">M</td>'+
			'<td align="center" style="width:100px;">Last</td>'+
			'<td align="center" style="width:100px;">Grade</td>'+
			'</tr>'+
		   '</thead>';
	var length = users.length;
	for(var i = 0; i < length; i++){
		if(i % 2 == 0){
			html += '<tr onclick="discipline.selectSearchResult('+i+');" style="background-color:#FFFF99;cursor:pointer;" onmouseover="this.style.backgroundColor = \'yellow\'" onmouseout="this.style.backgroundColor=\'#FFFF99\'">'+
				'<td align="center">'+users[i].id+'</td>'+
				'<td align="center">'+users[i].first+'</td>'+
				'<td align="center">'+users[i].middle+'</td>'+
				'<td align="center">'+users[i].last+'</td>'+
				'<td align="center">'+users[i].grade+'</td>'+
				'</tr>';
		}
		else{
				html += '<tr style="cursor:pointer;" onclick="discipline.selectSearchResult('+i+');" onmouseover="this.style.backgroundColor = \'yellow\';cursor:pointer;" onmouseout="this.style.backgroundColor=\'\'">'+
				'<td align="center">'+users[i].id+'</td>'+
				'<td align="center">'+users[i].first+'</td>'+
				'<td align="center">'+users[i].middle+'</td>'+
				'<td align="center">'+users[i].last+'</td>'+
				'<td align="center">'+users[i].grade+'</td>'+
				'</tr>';
		}
	}
	if(length == 0){
		html += '<tr><td colspan="5">No Results</td></tr>';
	}
	$('searchResults').innerHTML = html;
	$('searchResults').style.display = "";
};

discipline.buildStaffSearchResults = function(users){
	var html = '<table style="width:270px;border:solid 1px black;padding:2px 2px 2px 2px" cellspacing="0" cellpadding="0">'+
			'<thead style="background-color:#CCC;font-weight:bold;">'+
			'<tr>'+
			'<td align="center" style="width:50px;">ID</td>'+
			'<td align="center" style="width:100px;">First</td>'+
			'<td align="center" style="width:20px">M</td>'+
			'<td align="center" style="width:100px;">Last</td>'+
			'</tr>'+
		   '</thead>';
	var length = users.length;
	for(var i = 0; i < length; i++){
		if(i % 2 == 0){
			html += '<tr onclick="discipline.selectSearchResult('+i+');" style="background-color:#FFFF99;cursor:pointer;" onmouseover="this.style.backgroundColor = \'yellow\'" onmouseout="this.style.backgroundColor=\'#FFFF99\'">'+
				'<td align="center">'+users[i].id+'</td>'+
				'<td align="center">'+users[i].first+'</td>'+
				'<td align="center">'+users[i].middle+'</td>'+
				'<td align="center">'+users[i].last+'</td>'+
				'</tr>';
		}
		else{
				html += '<tr style="cursor:pointer;" onclick="discipline.selectSearchResult('+i+');" onmouseover="this.style.backgroundColor = \'yellow\';cursor:pointer;" onmouseout="this.style.backgroundColor=\'\'">'+
				'<td align="center">'+users[i].id+'</td>'+
				'<td align="center">'+users[i].first+'</td>'+
				'<td align="center">'+users[i].middle+'</td>'+
				'<td align="center">'+users[i].last+'</td>'+
				'</tr>';
		}
	}
	if(length == 0){
		html += '<tr><td colspan="5">No Results</td></tr>';
	}
	$('searchResults').innerHTML = html;
	$('searchResults').style.display = "";
};

discipline.selectSearchResult = function(index){
	$('perpIdHI').value = discipline.users[index].id;
	$('perpInput').value  = discipline.users[index].first+' '+discipline.users[index].middle+' '+discipline.users[index].last;
	$('searchResults').innerHTML = "";
	$('searchResults').style.display = "none";
};

discipline.addPerp = function(){
	var userId = $('perpIdHI').value;
	if(userId == '' || userId == null){
		userId = 0;
	}
	var perpName = $('perpInput').value;
	if(perpName == '' || perpName == null){
		return;
	}
	var perpType    = $('perpTypeSel').value;
	var injuryId    = $('injuryType').value;
	var identifier  = $('identifier').value;
	
	var ajax = new Ajax.Request('modules/Discipline/ajax/addPerpetrator.php',   
				{   method:'post',    
				    parameters: {
				    		 userid:userId,
				                 name:perpName,
				                 injury:injuryId,
				                 identifier:identifier,
				                 typecode:perpType
				                },
				       	    onSuccess: function(transport)
				       	    {     
						try
						{
							var jsonData = transport.responseText || "no response text";
							var jsonObject = eval('('+jsonData+')');
							if(jsonObject.result[0].success)
							{
								alert('Perpetrator Added');
								discipline.showPerpetrator(identifier);
							}
							else
							{
								alert('Error Adding Perpetrator');
							}
						}
						catch(ex)
						{
							//alert(ex);
							alert('Error Adding Perpetrator');
						}
					    },     
					    onFailure: function()
					    { 
						alert('Something went wrong...');
					    }   
        });
};


/*
##########################################################################
#Victim Section ##########################################################
##########################################################################
##########################################################################
*/

discipline.removeVictim = function(victimId){
	var identifier  = $('identifier').value;
	var ajax = new Ajax.Request('modules/Discipline/ajax/removeVictim.php',   
				{   method:'post',    
				    parameters: {
				    		 victimid:victimId
				                },
				       	    onSuccess: function(transport)
				       	    {     
						try
						{
							var jsonData = transport.responseText || "no response text";
							var jsonObject = eval('('+jsonData+')');
							if(jsonObject.result[0].success)
							{
								alert('Victim Removed');
								discipline.showVictim(identifier);
							}
							else
							{
								alert('Error Removing Victim');
							}
						}
						catch(ex)
						{
							//alert(ex);
							alert('Error Removing Victim');
						}
					    },     
					    onFailure: function()
					    { 
						alert('Something went wrong...');
					    }   
        });	
};

discipline.addVictim = function(){
	
	var victimId = $('victimTypeSel').value;
	if(victimId == 0){
		return;
	}
	
	var victimName = $('victimName').value;
	if(victimName == '' || victimName == null){
		victimName = "Not Provided";
	}
	var injuryId    = $('injuryType').value;
	var identifier  = $('identifier').value;
	
	var ajax = new Ajax.Request('modules/Discipline/ajax/addVictim.php',   
				{   method:'post',    
				    parameters: {
				    		 name:victimName,
				                 injury:injuryId,
				                 identifier:identifier,
				                 victimid:victimId
				                },
				       	    onSuccess: function(transport)
				       	    {     
						try
						{
							var jsonData = transport.responseText || "no response text";
							var jsonObject = eval('('+jsonData+')');
							if(jsonObject.result[0].success)
							{
								alert('Victim Added');
								discipline.showVictim(identifier);
							}
							else
							{
								alert('Error Adding Victim');
							}
						}
						catch(ex)
						{
							//alert(ex);
							alert('Error Adding Victim');
						}
					    },     
					    onFailure: function()
					    { 
						alert('Something went wrong...');
					    }   
        });
};


/*
##########################################################################
#Discipline Section ######################################################
##########################################################################
##########################################################################
*/

discipline.disciplineActions;

discipline.getDisciplineActionDetails = function(id, perpId){
	$('disciplineDetails_'+id).innerHTML = "<img src='assets/ajax_loader.gif' width='15px' height='15px' />";
	var identifier = $('identifierHi').value;
	var ajax = new Ajax.Request('modules/Discipline/ajax/getDisciplineActionDetails.php',   
				{   method:'post',    
				    parameters: {
				    		 id:id
				                },
				       	    onSuccess: function(transport)
				       	    {     
						try
						{
							var jsonData = transport.responseText || "no response text";
							var jsonObject = eval('('+jsonData+')');
							if(jsonObject.result[0].success)
							{
								
								discipline.disciplineActions = jsonObject.disciplines;
								discipline.buildDisciplineDetailsTable(id, perpId);
							}
							else
							{
								alert('Error Getting Disciplinary Actions');
							}
						}
						catch(ex)
						{
							alert(ex);
							//alert('Error Getting Disciplinary Actions');
						}
					    },     
					    onFailure: function()
					    { 
						alert('Something went wrong...');
					    }   
        });
};

discipline.buildDisciplineDetailsTable = function(id, perpId){
	var html ='<table style="padding 2px 2px 2px 2px;" style="width:650px">'+
			  '<tr><td style="width:200px"> Disciplinary Action</td><td style="width:375px">'+discipline.disciplineActions[0].display+'</td><td style="width:75px;vertical-align:top;">&nbsp;<a id="editLink_'+id+'" href="javascript:void(\'0\');" onclick="discipline.editDisciplineAction('+perpId+', \''+discipline.disciplineActions[0].shortExp+'\', \''+discipline.disciplineActions[0].fullExp+'\', \''+discipline.disciplineActions[0].zeroTol+'\', \''+discipline.disciplineActions[0].specialEd+'\', \''+discipline.disciplineActions[0].startDate+'\', \''+discipline.disciplineActions[0].endDate+'\', '+discipline.disciplineActions[0].typeId+', '+id+');">Edit</a>&nbsp;&nbsp;&nbsp;<img onclick="discipline.removeDisciplineAction('+discipline.disciplineActions[0].id+', '+perpId+');" style="cursor:pointer;" src="modules/Discipline/images/remove_button.gif" /></td></tr>'+
			  '<tr><td>Start Date of Discipline Action</td><td colspan="2">'+discipline.disciplineActions[0].startDate+'</td></tr>'+
			  '<tr><td>End Date of Discipline Action</td><td colspan="2">'+discipline.disciplineActions[0].endDate+'</td></tr>'+
			  '<tr><td>Related to Special Education<br/>Manifestation Hearing</td><td colspan="2">'+discipline.disciplineActions[0].specialEd+'</td></tr>'+
			  '<tr><td>Related to Zero Tolerance Policy</td><td colspan="2">'+discipline.disciplineActions[0].zeroTol+'</td></tr>'+
			  '<tr><td>Full Year Expulsion</td><td colspan="2">'+discipline.disciplineActions[0].fullExp+'</td></tr>'+
			  '<tr><td>Shortened Expulsion</td><td colspan="2">'+discipline.disciplineActions[0].shortExp+'</td></tr>'+
		         '</table><br/>';
	
	$('disciplineDetails_'+id).innerHTML = html;
	$('disciplineDetails_'+id).style.display = "";
	$('showDetialsA_'+id).innerHTML = '[-]';
	$('showDetialsA_'+id).onclick = function(){
		$('disciplineDetails_'+id).style.display = "none";
		discipline.cancelEditDisciplinaryAction(id);
		this.innerHTML = "[+]";
		this.onclick = function(){
			discipline.getDisciplineActionDetails(id, perpId);
		};
	};
};

discipline.showDisciplineWorkarea = function(id){
	$('disciplineWorkArea_'+id).style.display = '';
};

discipline.hideDisciplineWorkarea = function(id){
	$('disciplineWorkArea_'+id).style.display = 'none';
};	

discipline.removeDisciplineAction = function(disciplineId, perpId){
	var ajax = new Ajax.Request('modules/Discipline/ajax/removeDisciplineAction.php',   
					{   method:'post',    
					    parameters: {disciplineid:disciplineId},
					       	    onSuccess: function(transport)
					       	    {     
							try
							{
								var jsonData = transport.responseText || "no response text";
								var jsonObject = eval('('+jsonData+')');
								if(jsonObject.result[0].success)
								{
									alert('Discipline Action Removed');
									discipline.showDiscipline($('identifier').value);
								}
								else
								{
									alert('Error Removing Discipline Action');
								}
							}
							catch(ex)
							{
								//alert(ex);
								alert('Error Removing Discipline Action');
							}
						    },     
						    onFailure: function()
						    { 
							alert('Something went wrong...');
						    }   
        });
}

discipline.addDisciplineAction = function(id){
	var form = $('newDisciplineFrm_'+id);
	var data = form.serialize();
	
	var ajax = new Ajax.Request('modules/Discipline/ajax/addDisciplineAction.php',   
					{   method:'post',    
					    parameters: data,
					       	    onSuccess: function(transport)
					       	    {     
							try
							{
								var jsonData = transport.responseText || "no response text";
								var jsonObject = eval('('+jsonData+')');
								if(jsonObject.result[0].success)
								{
									alert('Disciplinary Action Added');
									discipline.showDiscipline($('identifier').value);
								}
								else
								{
									alert('Error Adding Disciplinary Action');
								}
							}
							catch(ex)
							{
								//alert(ex);
								alert('Error Adding Disciplinary Action');
							}
						    },     
						    onFailure: function()
						    { 
							alert('Something went wrong...');
						    }   
        });
};

discipline.disciplineYesNoHelper = function(value){
	if(value == 'No'){
		return 2;
	}
	else{
		return 1;
	}
};

discipline.convertMonth = function(month){
	month = parseFloat(month);
	var months = new Array("JAN","FEB","MAR","APR","MAY","JUN", "JUL","AUG","SEP","OCT","NOV","DEC");
	return months[month-1];
};

discipline.takeYear = function(theDate){
	x = theDate.getYear();
	var y = x % 100;
	y += (y < 38) ? 2000 : 1900;
	return y;
};

discipline.ShowAddDisciplineAction = function(id){
	var date  = new Date();
	var day   = date.getDate();
	var month = discipline.convertMonth((date.getMonth()+1));
	var year  = discipline.takeYear(date);
	
	$('monthSelect'+id+'99').value = month;
	$('daySelect'+id+'99').value   = day;
	$('yearSelect'+id+'99').value  = year;
	
	$('monthSelect'+id+'98').value = month;
	$('daySelect'+id+'98').value   = day;
	$('yearSelect'+id+'98').value  = year;
	
	$('shortExp_'+id).value = 2;
	$('fullExp_'+id).value = 2;
	$('zeroPolicy_'+id).value = 2;
	$('specialEd_'+id).value = 2;
	$('disciplineType_'+id).options[0].selected = true;
	$('saveFrmBtn_'+id).innerHTML = 'Add';
	$('saveFrmBtn_'+id).onclick= function(){
		discipline.addDisciplineAction(id);
	};
		
	discipline.showDisciplineWorkarea(id);
};

discipline.editDisciplineAction = function(id, shortExp, fullExp, zeroPolicy, specialEd, startDate, endDate, type, disciplineId){
	try{
	var index;
	var startMonth;
	var startDay;
	var startYear;
	var endMonth;
	var endDay;
	var endYear;
	
	$('shortExp_'+disciplineId+id).value = discipline.disciplineYesNoHelper(shortExp);
	$('fullExp_'+disciplineId+id).value = discipline.disciplineYesNoHelper(fullExp);
	$('zeroPolicy_'+disciplineId+id).value = discipline.disciplineYesNoHelper(zeroPolicy);
	$('specialEd_'+disciplineId+id).value = discipline.disciplineYesNoHelper(specialEd);
	
	index = startDate.indexOf('-');
	startMonth = startDate.substr(0,index);
	startDate = startDate.substr(index+1);
	index = startDate.indexOf('-');
	startDay = startDate.substr(0,index);
	startYear = startDate.substr(index+1);
	
	$('monthSelect'+disciplineId+id+'99').value = discipline.convertMonth(startMonth);
	$('daySelect'+disciplineId+id+'99').value   = startDay;
	$('yearSelect'+disciplineId+id+'99').value  = startYear;
	
	index = endDate.indexOf('-');
	endMonth = endDate.substr(0,index);
	endDate = endDate.substr(index+1);
	index = endDate.indexOf('-');
	endDay = endDate.substr(0,index);
	endYear = endDate.substr(index+1);
	
	$('monthSelect'+disciplineId+id+'98').value = discipline.convertMonth(endMonth);
	$('daySelect'+disciplineId+id+'98').value   = endDay;
	$('yearSelect'+disciplineId+id+'98').value  = endYear;
	$('disciplineType_'+disciplineId+id).value = type;
	
	$('saveFrmBtn_'+disciplineId+id).onclick= function(){
		discipline.updateDisciplineAction(id, disciplineId);
	};
	
	$('editDisciplineArea_'+disciplineId).style.display = "";
	
	$('editLink_'+disciplineId).innerHTML = "Close";
	$('editLink_'+disciplineId).onclick=function(){
		discipline.cancelEditDisciplinaryAction(disciplineId, id);
		this.innerHTML = "Edit";
		this.onclick=function(){
			discipline.editDisciplineAction(id, 
							discipline.disciplineActions[0].shortExp, 
							discipline.disciplineActions[0].fullExp, 
							discipline.disciplineActions[0].zeroTol,
							discipline.disciplineActions[0].specialEd,
							discipline.disciplineActions[0].startDate,
							discipline.disciplineActions[0].endDate,
							discipline.disciplineActions[0].typeId,
							disciplineId);
		};
	};
	}catch(ex){alert(ex);}
};

discipline.cancelEditDisciplinaryAction = function(id, perpId){
	$('editDisciplineArea_'+id).style.display = "none";
	$('editLink_'+id).innerHTML = "Edit";
	$('editLink_'+id).onclick=function(){
		discipline.editDisciplineAction(perpId, 
		discipline.disciplineActions[0].shortExp, 
		discipline.disciplineActions[0].fullExp, 
		discipline.disciplineActions[0].zeroTol,
		discipline.disciplineActions[0].specialEd,
		discipline.disciplineActions[0].startDate,
		discipline.disciplineActions[0].endDate,
		discipline.disciplineActions[0].typeId,
		id);
	};
};

discipline.updateDisciplineAction = function(id, disciplineId){
	
	var form = $('editDisciplineFrm_'+disciplineId);
	var data = form.serialize();
	
	var ajax = new Ajax.Request('modules/Discipline/ajax/updateDisciplineAction.php',   
					{   method:'post',    
					    parameters: data+'&disciplineid='+disciplineId,
					       	    onSuccess: function(transport)
					       	    {     
							try
							{
								var jsonData = transport.responseText || "no response text";
								var jsonObject = eval('('+jsonData+')');
								if(jsonObject.result[0].success)
								{
									alert('Disciplinary Action Updated');
									discipline.showDiscipline($('identifier').value);
								}
								else
								{
									alert('Error Updating Disciplinary Action');
								}
							}
							catch(ex)
							{
								//alert(ex);
								alert('Error Updating Disciplinary Action');
							}
						    },     
						    onFailure: function()
						    { 
							alert('Something went wrong...');
						    }   
        });
};