
var easycom = {};

/*
##########################################################################
#Auto Message Section Parent##############################################
##########################################################################
*/

easycom.autoMsgParent = {};
easycom.autoMsgParent.autoMessages = null;
easycom.autoMsgParent.currentAutoMessages = null;

easycom.autoMsgParent.deleteAttendanceMessage = function(id)
{
	var ajax = new Ajax.Request('modules/EasyCom/ajax/DeleteAutoMessageParent.php',   
	{   method:'post',    
	    parameters: {id:id},
	       	    onSuccess: function(transport)
	       	    {     
			try
			{
				var response = transport.responseXML || "no response text";
				var resp = response.getElementsByTagName('response')[0].firstChild.nodeValue;
				if(resp == 1)
				{
					check_content('ajax.php?modname=EasyCom/automaticMessages.php');
				}
				else
				{
					alert('Somthing went wrong....');
				}
			}
			catch(ex)
			{
				alert(ex);
			}
		    },     
		    onFailure: function()
		    { 
			alert('Something went wrong...');
		    }   
        });
};

easycom.autoMsgParent.saveAttendanceMessage = function(stuId)
{
	var text = 0;
	var email = 0;
	var student;
	var id;
	
	if($('sendText').checked)
	{
		text = 1;
	}
	if($('sendEmail').checked)
	{
		email = 1;
	}
	student = $('student').value;
	id = $('attendanceCode').value;
	if(id === 0)
	{
		return;
	}
	
	var ajax = new Ajax.Request('modules/EasyCom/ajax/AddAutoMessageParent.php',   
		{   method:'post',    
		    parameters: {
			    	  text:text,
			    	  email:email,
			    	  student:student,
			    	  attendanceCode:id
			    	},
	       	    onSuccess: function(transport)
	       	    {     
			try
			{
				var response = transport.responseXML || "no response text";
				var resp = response.getElementsByTagName('response')[0].firstChild.nodeValue;
				if(resp == 1)
				{
					check_content('ajax.php?modname=EasyCom/automaticMessages.php');
				}
				else
				{
					alert('Somthing went wrong....');
				}
			}
			catch(ex)
			{
				alert(ex);
			}
		    },     
		    onFailure: function()
		    { 
			alert('Something went wrong...') 
		    }   
        });
};

easycom.autoMsgParent.buildAttendenceMsgArrays = function(stuId)
{
	var ajax = new Ajax.Request('modules/EasyCom/ajax/GetAutoMessagesParent.php',   
		{   method:'post',    
		    parameters: {},
	       	    onSuccess: function(transport)
	       	    {     
			try
			{
				var response = transport.responseXML || "no response text";
				var resp = response.getElementsByTagName('response')[0].firstChild.nodeValue;
				if(resp == 1)
				{
					easycom.autoMsgParent.currentAutoMessages = new Array();
					easycom.autoMsgParent.autoMessages = new Array();
					
					var attIds = response.getElementsByTagName('attid');
					var stuIds = response.getElementsByTagName('stuid');
					var ids = response.getElementsByTagName('id');
					var titles = response.getElementsByTagName('title');
					
					var length = attIds.length;
					for(var i =0; i< length; i++)
					{
						easycom.autoMsgParent.currentAutoMessages.push({
												stuId:stuIds[i].firstChild.nodeValue,
												attId:attIds[i].firstChild.nodeValue
											      })
					}
					
					var length = ids.length;
					for(var i =0; i< length; i++)
					{
						easycom.autoMsgParent.autoMessages.push({
											id:ids[i].firstChild.nodeValue,
											title:titles[i].firstChild.nodeValue
										       })
					}
					easycom.autoMsgParent.buildAttSelect(stuId);
				}
				else
				{
					alert('Somthing went wrong....');
				}
			}
			catch(ex)
			{
				alert(ex);
			}
		    },     
		    onFailure: function()
		    { 
			alert('Something went wrong...') 
		    }   
        });
};

easycom.autoMsgParent.buildAttSelect = function(stuId)
{
	var html = "<select id=\"attendanceCode\" style=\"width:150px\"><option value=\"0\">Select An Event</option>";
  	var length = easycom.autoMsgParent.autoMessages.length;
  	var length2 = easycom.autoMsgParent.currentAutoMessages.length;
  	for(var i = 0; i< length; i++)
  	{
  		var found = false;
  		for(var y = 0; y < length2; y++)
  		{
  			if(easycom.autoMsgParent.currentAutoMessages[y].stuId == stuId)
  			{
  				if(easycom.autoMsgParent.currentAutoMessages[y].attId == easycom.autoMsgParent.autoMessages[i].id)
  				{
  					found = true;
  					continue;
  				}
  			}
  		}
  		if(!found)
  		{
  			html += "<option value=\'"+easycom.autoMsgParent.autoMessages[i].id+"\'>"+easycom.autoMsgParent.autoMessages[i].title+"</option>";
  			continue;
  		}
  		else
  		{
  			continue;
  		}
  	}
  	html += "</select>";
  	document.getElementById("attCodeDIV").innerHTML = html;
};

/*
##########################################################################
#Edit Auto Message Section################################################
##########################################################################
*/

easycom.adminEdit = {};
easycom.adminEdit.messageArray = null;

easycom.adminEdit.deleteAutoMessage = function(id)
{
	new Ajax.Request('modules/EasyCom/ajax/deleteAutoMessage.php',   
	{   method:'post',    
	    parameters: {
	    		deleteId:id			
	    		},
       	    onSuccess: function(transport)
       	    {     
		try
		{
			var response = transport.responseXML || "no response text";
			var resp = response.getElementsByTagName('response')[0].firstChild.nodeValue;
			if(resp == 1)
			{
				//alert('Message deleted');
				check_content('ajax.php?modname=EasyCom/EditAutomaticMessaging.php');
			}
			else
			{
				alert('Error deleting message');
			}
		}
		catch(ex)
		{
			alert(ex);
		}
	    },     
	    onFailure: function()
	    { 
		alert('Something went wrong...') 
	    }   
        });	
};

easycom.adminEdit.saveAutoMessage = function()
{
	var message = $('msgTextTA').value;
	var messageId = $('autoMsgId').value;
	
	new Ajax.Request('modules/EasyCom/ajax/saveAutoMessage.php',   
	{   method:'post',    
	    parameters: {
	    		autoMsgText:message,
	    		autoMsgID:messageId			
	    		},
       	    onSuccess: function(transport)
       	    {     
		try
		{
			var response = transport.responseXML || "no response text";
			var resp = response.getElementsByTagName('response')[0].firstChild.nodeValue;
			if(resp == 1)
			{
				alert('Message updated');
			}
			else
			{
				alert('Error updating message');
			}
		}
		catch(ex)
		{
			alert(ex);
		}
	    },     
	    onFailure: function()
	    { 
		alert('Something went wrong...') 
	    }   
        });	
};

easycom.adminEdit.addAttendanceCode = function()
{
	var code = $('attendanceCode').value;
	new Ajax.Request('modules/EasyCom/ajax/addAutoMessage.php',   
	{   method:'post',    
	    parameters: {attendanceCode:code},
       	    onSuccess: function(transport)
       	    {     
		try
		{
			var response = transport.responseXML || "no response text";
			var resp = response.getElementsByTagName('response')[0].firstChild.nodeValue;
			if(resp == 1)
			{
				check_content('ajax.php?modname=EasyCom/EditAutomaticMessaging.php');
			}
			else
			{
				alert('Error Adding Attendance Code');
			}
		}
		catch(ex)
		{
			alert(ex);
		}
	    },     
	    onFailure: function()
	    { 
		alert('Something went wrong...') 
	    }   
        });	
};

easycom.adminEdit.loadAutoMessages = function(autoId, attTitle)
{
	if(easycom.adminEdit.messageArray != null)
	{
		easycom.adminEdit.showEditMsg(autoId, attTitle);
	}
	else
	{
		new Ajax.Request('modules/EasyCom/ajax/getAutoMessages.php',   
		{   method:'post',    
		    parameters: {},
				onSuccess: function(transport)
				{     
					try
					{
						easycom.adminEdit.messageArray = new Array();
						var response = transport.responseXML || "no response text";       
						ids = response.getElementsByTagName('id');
						txts = response.getElementsByTagName('text');

						var length = ids.length;
						for(var i =0; i < length;i++)
						{
							easycom.adminEdit.messageArray.push({
											     autoId:ids[i].firstChild.nodeValue,
											     msgText:easycom.urldecode(txts[i].firstChild.nodeValue)
											   });
						}
						easycom.adminEdit.showEditMsg(autoId, attTitle);
					}
					catch(ex)
					{
						alert(ex);
					}
				},     
				onFailure: function()
				{ 
					alert('Something went wrong...') 
				}   
		});
    	}
};

easycom.adminEdit.checkCount = function(elem)
{

      	var length = elem.value.length;
       	if(length >= 150)
       	{
       		var txt = elem.value;
       		txt = txt.substring(0, 150);
       		elem.value = txt;
       		length = elem.value.length;
       	}
};

easycom.adminEdit.showEditMsg = function(autoId, attTitle)
{
	document.getElementById("msgTextDIV").style.display = "";
	var length = easycom.adminEdit.messageArray.length;
	for(var i=0; i < length; i++)
	{
		if(easycom.adminEdit.messageArray[i].autoId == autoId)
		{
			document.getElementById("attendanceTitle").innerHTML = attTitle;
			document.getElementById("msgTextTA").value = easycom.adminEdit.messageArray[i].msgText;
			document.getElementById("autoMsgId").value = autoId;
			break;
		}
		else
		{
			continue;
		}
	}
};

/*
##########################################################################
#Auto Message Section#####################################################
##########################################################################
*/

easycom.admin = {};

easycom.admin.changePage = function(page)
{
	check_content('ajax.php?modname=EasyCom/AdminSendAutoMessages.php?pageNum='+page);
};

easycom.admin.refresh = function()
{
	check_content('ajax.php?modname=EasyCom/AdminSendAutoMessages.php');
};

easycom.admin.sendAutoMessages = function()
{
	$('sendMessagesDiv').innerHTML = "<img src='assets/ajax_loader.gif' width='15px' height='15px' />";
	new Ajax.Request('modules/EasyCom/ajax/SendAutoMessages.php',   
		{   method:'post',    
		    parameters: {},
				onSuccess: function(transport)
				{     
					try
					{
						var response = transport.responseXML || "no response text";       
						var resp = response.getElementsByTagName('response')[0].firstChild.nodeValue;
						if(resp == 1)
						{
						
							check_content('ajax.php?modname=EasyCom/EditAutomaticMessaging.php');
						}
						else
						{
							alert('Error Sending Messages');
						}
						check_content('ajax.php?modname=EasyCom/AdminSendAutoMessages.php');
					}
					catch(ex)
					{
						alert(ex);
					}
				},     
				onFailure: function()
				{ 
					alert('Something went wrong...') 
				}   
		});
};

/*
##########################################################################
#Individual Messages Section##############################################
##########################################################################
*/

easycom.buildConfimationArea = function(holderId, number)
{
	var html ='<table><tr><td>Confirmation Code: </td><td><input type="text" id="confirmationCode" size="25" /></td></tr>';
	html +='<tr><td colspan="2" align="center"><input type="button" value="confirm" onclick="easycom.ConfirmNumber();" /></td></tr>';
	html +='</table><img src="assets/ajax_loader.gif" id="loadingImage" style="display:none;" width="15px;" height="15px;" /><input type="hidden" value="'+holderId+'" id="confId" /><input type="hidden" value="'+number+'" id="contact" />';
	
	$('displayArea').innerHTML = html;
};

easycom.sendConfirmationNumber = function()
{
	$('loadingImage').style.display = "";
	var number = $('contactInfo').value;
	new Ajax.Request('modules/EasyCom/ajax/SendConfirmation.php',   
	{   method:'post',    
	    parameters: {number:number
	    		},
		    	onSuccess: function(transport)
		    	{     
		    		try
		    		{
		    			var response = transport.responseXML || "no response text";       
		    			resp = response.getElementsByTagName('response')[0].firstChild.nodeValue;
		    			if(resp == 1)
		    			{
		    				var holderId = response.getElementsByTagName('id')[0].firstChild.nodeValue;
		    				$('loadingImage').style.display = "none";
		    				easycom.buildConfimationArea(holderId, number);
		    			}
		    			else
		    			{
		    				alert('Error Sending Confirmation');
		    			}
		    			
		    		}
		    		catch(ex)
		    		{
		    			alert(ex);
		    		}
		    	},     
		    	onFailure: function()
		    	{ 
		    		alert('Something went wrong...') 
		    	}   
    	});
};

easycom.buildConfirmedMessageArea = function(messages)
{
	var html ="<div algin='right'>";
	html +='Search Another contact:<br/><input type="text" name="contactInfo" id="contactInfo" size="25" /><br/><input type="button" value="Search" onclick="easycom.sendConfirmationNumber();" /><br/><img src="assets/ajax_loader.gif" id="loadingImage" style="display:none;" width="15px;" height="15px;" /></div>';
	html +='<table border="1" style="width:600px;background-color:#FFFFFF;font-family: arial, lucida console, sans-serif"><tr><td align="center" style="font-size:14px;width:450px">Message</td><td align="center" style="font-size:14px;widht:150px;">Sent</td></tr>';
	
	var length = messages.length;
	for(var i=0; i< length; i++)
	{
		html +='<tr onMouseover="this.style.backgroundColor=\'#FFFF99\';" onMouseout="this.style.backgroundColor=\'#FFFFFF\';"><td><p style="text-align:left;width:449px;">'+messages[i].msg+'</p></td><td>'+messages[i].insert+'</td></tr>';
	}
	html +='</table>';
	$('displayArea').innerHTML = html;
};

easycom.ConfirmNumber = function()
{
	$('loadingImage').style.display = "";
	var number = $('contact').value;
	var confId = $('confId').value;
	var confirmationCode = $('confirmationCode').value;
	new Ajax.Request('modules/EasyCom/ajax/ConfirmNumber.php',   
	{   method:'post',    
	    parameters: {number:number,
	    		 confid:confId,
	    		 confcode:confirmationCode
	    		},
		    	onSuccess: function(transport)
		    	{     
		    		try
		    		{
		    			var response = transport.responseXML || "no response text";       
		    			resp = response.getElementsByTagName('response')[0].firstChild.nodeValue;
		    			if(resp == 1)
		    			{
		    				easycom.userMessages = new Array();
		    				
		    				msgs = response.getElementsByTagName('text');
		    				inserts = response.getElementsByTagName('insert');
		    				
		    				var length=msgs.length;
		    				for(var i = 0; i < length; i++)
		    				{
		    					easycom.userMessages.push({
		    								   msg:easycom.urldecode(msgs[i].firstChild.nodeValue),
		    								   insert:easycom.urldecode(inserts[i].firstChild.nodeValue)
		    								 });
		    					$('loadingImage').style.display = "none";
		    					easycom.buildConfirmedMessageArea(easycom.userMessages);
		    				}
		    			}
		    			else
		    			if(resp == 3)
		    			{
		    				var html = '<span style="color:red;font-size:16px">Incorrect Confirmation Number</span>';
		    				html += "<div algin='right'>";
						html += 'Search Another contact:<br/><input type="text" name="contactInfo" id="contactInfo" size="25" /><br/><input type="button" value="Search" onclick="easycom.sendConfirmationNumber();" /></div>';
		    				$('displayArea').innerHTML = html;
		    			}
		    			else
		    			if(resp == 4)
		    			{
		    				alert('No Messages Found');
		    			}
		    			else
		    			{
		    				alert('Error Confirming Number');
		    			}
		    			
		    		}
		    		catch(ex)
		    		{
		    			alert(ex);
		    		}
		    	},     
		    	onFailure: function()
		    	{ 
		    		alert('Something went wrong...') 
		    	}   
    	});
};


/*
##########################################################################
#List Messages Section####################################################
##########################################################################
*/

easycom.recArray;
easycom.msgArray;
easycom.RECMSG;
easycom.CURRENTMSGID;
easycom.MAXPAGE;
easycom.PAGENUM = 1;

easycom.changePage = function(page)
{
	easycom.PAGENUM = page;
	easycom.getAllMessages();
}

easycom.BuildMsgListTbl = function()
{
	var html ='<table border="1" style="width: 600px;background-color:#FFFFFF;font-family: arial">';
	html += '<tr><td align="center">Message ID</td><td align="center">Message Text</td><td align="center">Sent Date</td></tr>';
	var length = easycom.msgArray.length;
	for(var i = 0; i < length; i++)
	{
		html += '<tr onMouseover="this.style.backgroundColor=\'#FFFF99\';this.style.cursor=\'pointer\'" onMouseout="this.style.backgroundColor=\'#FFFFFF\'" onclick="easycom.getMessageRec('+easycom.msgArray[i].id+');" ><td align="center">'+easycom.msgArray[i].id+'</td><td>'+easycom.msgArray[i].text+'</td><td>'+easycom.msgArray[i].create+'</td></tr>';
	}
	html += '</table><br/><div align="center">';
	
	var nav = '';
	var prev = '';
	var first = '';
	
	for(var page = 1; page <= easycom.MAXPAGE; page++)
	{
		  if (page == easycom.PAGENUM)
		  {
		     nav += page;
		  }
		  else
		  {
		     nav += " <a href=\"javascript:easycom.changePage("+page+")\">"+page+"</a> ";
		  }
	}

	if (easycom.PAGENUM > 1)
	{
		  var page  = easycom.PAGENUM - 1;
		  prev  = " <a href=\"javascript:easycom.changePage("+page+")\">[Prev]</a> ";

		  //first = " <a href=\"javascript:easycom.changePage(1)\">[First Page]</a> ";
	}
	else
	{
		  prev  = '&nbsp;'; // we're on page one, don't print previous link
		  first = '&nbsp;'; // nor the first page link
	}

	if (easycom.PAGENUM < easycom.MAXPAGE)
	{
		  var page = easycom.PAGENUM + 1;
		  next = " <a href=\"javascript:easycom.changePage("+page+")\">[Next]</a> ";

		  //last = " <a href=\"javascript:easycom.changePage("+easycom.MAXPAGE+")\">[Last Page]</a> ";
	}
	else
	{
		  next = '&nbsp;'; // we're on the last page, don't print next link
		  last = '&nbsp;'; // nor the last page link
	}
	
	
	html+= prev+nav+next+'</div>';
	$('container').innerHTML = html;
}

easycom.getAllMessages = function()
{
	new Ajax.Request('modules/EasyCom/ajax/GetAllMessages.php',   
	{   method:'post',    
	    parameters: {pagenum:easycom.PAGENUM},
		    	onSuccess: function(transport)
		    	{     
		    		try
		    		{
		    			var response = transport.responseXML || "no response text";       
		    			resp = response.getElementsByTagName('response')[0].firstChild.nodeValue;
		    			if(resp == 1)
		    			{
		    				easycom.msgArray = new Array();
		    				
		    				var ids = response.getElementsByTagName('id');
		    				var texts = response.getElementsByTagName('text');
		    				var creates = response.getElementsByTagName('create');
		    				easycom.MAXPAGE = response.getElementsByTagName('maxpage')[0].firstChild.nodeValue;
		    				
		    				var length = ids.length;
		    				for(var i = 0; i < length; i++)
		    				{
		    					easycom.msgArray.push({id:ids[i].firstChild.nodeValue,
		    							       text:easycom.urldecode(texts[i].firstChild.nodeValue),
		    							       create:easycom.urldecode(creates[i].firstChild.nodeValue)
		    							      });
		    				}
		    				
		    				easycom.BuildMsgListTbl();
		    			}
		    			else
		    			if(resp == 2)
		    			{
		    				alert('No Recipents');
		    			}
		    			else
		    			{
		    				alert('Error Getting Recipents');
		    			}
		    			
		    		}
		    		catch(ex)
		    		{
		    			alert(ex);
		    		}
		    	},     
		    	onFailure: function()
		    	{ 
		    		alert('Something went wrong...') 
		    	}   
    	});
};

easycom.buildRecTBL = function()
{
	var html = '<span style="font-size:18px;">Message</span><br/><textarea style="font-family: arial" cols="40" rows="5" readonly="true">'+easycom.RECMSG+'</textarea><br/>';
	html += '<input type="button" value="Refresh" onclick="easycom.getMessageRec('+easycom.CURRENTMSGID+');" />&nbsp;&nbsp;|&nbsp;&nbsp;<input type="button" value="Back" onclick="easycom.getAllMessages();" /><br/><span style="font-size:18px;">Recipients</span><br/>';
	html += '<table border="1" style="background-color:#FFFFFF;font-family: arial, lucida console, sans-serif">';
	html += '<tr><td align="center">Contact Info</td><td align="center">Time Sent</td></tr>';
	var length = easycom.recArray.length;
	for(var i = 0; i < length; i++)
	{
		html += '<tr onMouseover="this.style.backgroundColor=\'#FFFF99\';this.style.cursor=\'pointer\'" onMouseout="this.style.backgroundColor=\'#FFFFFF\'"><td>'+easycom.recArray[i].contactInfo+'</td><td>'+easycom.recArray[i].insert+'</td></tr>';
	}
	html += '</table><br />';
	html += '<br/>';
	$('container').innerHTML = html;
}

easycom.getMessageRec = function(msgId)
{
	easycom.CURRENTMSGID = msgId;
	new Ajax.Request('modules/EasyCom/ajax/GetMessageRec.php',   
	{   method:'post',    
	    parameters: {msgid:msgId},
		    	onSuccess: function(transport)
		    	{     
		    		try
		    		{
		    			var response = transport.responseXML || "no response text";       
		    			resp = response.getElementsByTagName('response')[0].firstChild.nodeValue;
		    			if(resp == 1)
		    			{
		    				easycom.recArray = new Array();
		    				var inserts = response.getElementsByTagName('insert');
		    				var contactinfos = response.getElementsByTagName('contactInfo');
		    				easycom.RECMSG = easycom.urldecode(response.getElementsByTagName('msg')[0].firstChild.nodeValue);
		    				var length = inserts.length;
		    				for(var i = 0; i < length; i++)
		    				{
		    					easycom.recArray.push({contactInfo:easycom.urldecode(contactinfos[i].firstChild.nodeValue),
		    								  insert:easycom.urldecode(inserts[i].firstChild.nodeValue)});
		    				}
		    				easycom.buildRecTBL();
		    			}
		    			else
		    			if(resp == 2)
		    			{
		    				alert('No Recipents');
		    			}
		    			else
		    			{
		    				alert('Error Getting Recipents');
		    			}
		    			
		    		}
		    		catch(ex)
		    		{
		    			alert(ex);
		    		}
		    	},     
		    	onFailure: function()
		    	{ 
		    		alert('Something went wrong...') 
		    	}   
    	});
};
	

/*
##########################################################################
#Send Message Section#####################################################
##########################################################################
*/

easycom.schoolArray;
easycom.gradesArray;
easycom.gradesArrayAllDistricts;
easycom.staffArray;
easycom.maxLength;

easycom.checkCount = function(elem)
      {
	if(easycom.maxLength == null)
	{
		easycom.maxLength = elem.getAttribute('maxlength');
		
	}
	else
	{
		elem.setAttribute('maxlength', easycom.maxLength);
	}
	var length = elem.value.length;
	if(length >= easycom.maxLength)
      	{
      		var txt = elem.value;
      		txt = txt.substring(0, easycom.maxLength);
      		elem.value = txt;
      		length = elem.value.length;
      	}
      	var charsLeft = easycom.maxLength - length;
      	document.getElementById("charCount").value = charsLeft;
      }

easycom.checkForm = function()
{
   value = document.getElementById("txtArea").value;

   if(value == "" || value == null)
   {
    	alert("Please enter a message");
    	return false;
   }
   else
   {
    	return true;
   }
};

easycom.changeSendTo = function(type)
{
	var elem = document.getElementById("txtArea");
	
	if(type == 0)
	{
		easycom.maxLength = 3000;
		document.getElementById("charCount").value = "3000";
		easycom.checkCount(elem)
	}
	else
	if(type == 1)
	{
		easycom.maxLength = 150;
		document.getElementById("charCount").value = "150";
		easycom.checkCount(elem)
	}
	else
	if(type == 2)
	{
		easycom.maxLength = 150;
		document.getElementById("charCount").value = "150";
		easycom.checkCount(elem)
	}
};

easycom.checkSchool = function()
{
    elem = document.getElementById("schoolID_sel");
    if(elem.value != -1 && elem.value != -2)
    {
    	new Ajax.Request('modules/EasyCom/ajax/SendMessageGetGradeLevels.php',   
		{   method:'post',    
		    parameters: {schoolid:elem.value},
			    	onSuccess: function(transport)
			    	{     
			    		try
			    		{
			    			var response = transport.responseXML || "no response text";       
			    			resp = response.getElementsByTagName('response')[0].firstChild.nodeValue;
			    			if(resp == 1)
			    			{
			    				easycom.gradesArray = new Array();
			    				var ids = response.getElementsByTagName('gradeid');
			    				var titles = response.getElementsByTagName('gradetitle');
			    				var length = ids.length;
			    				for(var i = 0; i < length; i++)
			    				{
			    					easycom.gradesArray.push({gradeID:ids[i].firstChild.nodeValue,
			    								  gradeTitle:titles[i].firstChild.nodeValue});
			    				}
			    				easycom.createGradesDD();
			    			}
			    			else
			    			if(resp == 2)
			    			{
			    				alert('No Schools');
			    			}
			    			else
			    			{
			    				alert('Error Getting Schools');
			    			}
			    			
			    		}
			    		catch(ex)
			    		{
			    			alert(ex);
			    		}
			    	},     
			    	onFailure: function()
			    	{ 
			    		alert('Something went wrong...') 
			    	}   
    		});
     }	
     else if(elem.value == -2)
     {
     	new Ajax.Request('modules/EasyCom/ajax/SendMessageGetDistrictGradeLevels.php',   
			{   method:'post',    
			    parameters: {},
				    	onSuccess: function(transport)
				    	{     
				    		try
				    		{
				    			var response = transport.responseXML || "no response text";       
				    			resp = response.getElementsByTagName('response')[0].firstChild.nodeValue;
				    			if(resp == 1)
				    			{
				    				easycom.gradesArrayAllDistricts = new Array();
				    				var shortname = response.getElementsByTagName('shortname');
				    				var titles = response.getElementsByTagName('title');
				    				var length = titles.length;
				    				for(var i = 0; i < length; i++)
				    				{
				    					easycom.gradesArrayAllDistricts.push({short_name:shortname[i].firstChild.nodeValue,
				    								  	      title:titles[i].firstChild.nodeValue});
				    				}
				    				easycom.createGradesDistrictDD();
				    			}
				    			else
				    			if(resp == 2)
				    			{
				    				alert('No Schools');
				    			}
				    			else
				    			{
				    				alert('Error Getting Schools');
				    			}
				    			
				    		}
				    		catch(ex)
				    		{
				    			alert(ex);
				    		}
				    	},     
				    	onFailure: function()
				    	{ 
				    		alert('Something went wrong...') 
				    	}   
    		});
     	
     	
     }
     else
     {
	document.getElementById("txtArea").readOnly = true;
     }
};

easycom.createGradesDistrictDD = function()
{
	var html = "<select name=\"gradeID\" id=\"gradeID\"><option value=\"-1\">All Grades</option>";
	var length = easycom.gradesArrayAllDistricts.length;
	for(i = 0;i < length;i++)
	{
		 html+="<option value=\""+easycom.gradesArrayAllDistricts[i].short_name+"\">"+easycom.gradesArrayAllDistricts[i].title+"</option>";
	}
	html += "</select>";
	document.getElementById("grade_sel").innerHTML = html;
	document.getElementById("txtArea").readOnly = false;
}

easycom.createGradesDD = function()
{
	document.getElementById("txtArea").readOnly = false;
		var html = "<select name=\"gradeID\" id=\"gradeID\"><option value=\"-1\">All Grades</option>";
		var length = easycom.gradesArray.length;
	
		for(i = 0;i < length;i++)
		{
			html+="<option value=\""+easycom.gradesArray[i].gradeID+"\">"+easycom.gradesArray[i].gradeTitle+"</option>";
		}
		html += "</select>";
	document.getElementById("grade_sel").innerHTML = html;
};

easycom.createSchoolDD = function()
{
	var html = "<select name=\"schoolID\" id=\"schoolID_sel\" onchange=\"easycom.checkSchool()\"  >";
	html += "<option value=\"-1\">Select A School</option><option value=\"-2\" >All Schools</option>";
	var length = easycom.schoolArray.length;
	for(i = 0;i < length;i++)
	{
		html+="<option value=\""+easycom.schoolArray[i].school_id+"\">"+easycom.schoolArray[i].title+"</option>";
	}
	html += "</select>";
      	document.getElementById("schoolID_div").innerHTML = html;
};

easycom.createStaffDD = function()
{
	document.getElementById("txtArea").readOnly = false;
	var html = "<select id=\"profileID\" name=\"profileID\"><option value=\"-1\">All Staff</option>";
	var length = easycom.staffArray.length;
	
	for(i = 0;i < length;i++)
	{
		html+="<option value=\""+easycom.staffArray[i].staff_id+"\">"+easycom.staffArray[i].title+"</option>";
	}
	html += "</select>";
	document.getElementById("grade_sel").innerHTML = html;
}

easycom.draw_user_selection = function(elem)
      {

      	// 0 = parent
      	// 1 = staff
      	var profile_sel = elem.value;
      	if(profile_sel == -1)
      	{
      		document.getElementById("txtArea").readOnly = true;
      		document.getElementById("schoolID_div").innerHTML = "";
      		document.getElementById("grade_sel").innerHTML = "";
      		return;
      	}
      	if(profile_sel == 0)
      	{
      		new Ajax.Request('modules/EasyCom/ajax/SendMessageGetSchools.php',   
		    {   method:'post',    
		    	parameters: {},
		    	onSuccess: function(transport)
		    	{     
		    		try
		    		{
		    			var response = transport.responseXML || "no response text";       
		    			resp = response.getElementsByTagName('response')[0].firstChild.nodeValue;
		    			if(resp == 1)
		    			{
		    				easycom.schoolArray = new Array();
		    				var ids = response.getElementsByTagName('id');
		    				var titles = response.getElementsByTagName('title');
		    				var length = ids.length;
		    				for(var i = 0; i < length; i++)
		    				{
		    					easycom.schoolArray.push({school_id:ids[i].firstChild.nodeValue,
		    								  title:titles[i].firstChild.nodeValue});
		    				}
		    				easycom.createSchoolDD();
		    			}
		    			else
		    			if(resp == 2)
		    			{
		    				alert('No Schools');
		    			}
		    			else
		    			{
		    				alert('Error Getting Schools');
		    			}
		    			
		    		}
		    		catch(ex)
		    		{
		    			alert(ex);
		    		}
		    	},     
		    	onFailure: function()
		    	{ 
		    		alert('Something went wrong...') 
		    	}   
    		});
      	}
      	else
      	{
      		new Ajax.Request('modules/EasyCom/ajax/SendMessageGetStaff.php',   
				    {   method:'post',    
				    	parameters: {},
				    	onSuccess: function(transport)
				    	{     
				    		try
				    		{
				    			var response = transport.responseXML || "no response text";       
				    			resp = response.getElementsByTagName('response')[0].firstChild.nodeValue;
				    			if(resp == 1)
				    			{
				    				easycom.staffArray = new Array();
				    				var ids = response.getElementsByTagName('id');
				    				var titles = response.getElementsByTagName('title');
				    				var length = ids.length;
				    				for(var i = 0; i < length; i++)
				    				{
				    					easycom.staffArray.push({staff_id:ids[i].firstChild.nodeValue,
				    								  title:titles[i].firstChild.nodeValue});
				    				}
				    				easycom.createStaffDD();
				    			}
				    			else
				    			if(resp == 2)
				    			{
				    				alert('No Schools');
				    			}
				    			else
				    			{
				    				alert('Error Getting Schools');
				    			}
				    			
				    		}
				    		catch(ex)
				    		{
				    			alert(ex);
				    		}
				    	},     
				    	onFailure: function()
				    	{ 
				    		alert('Something went wrong...') 
				    	}   
    		});
      		
      		document.getElementById("schoolID_div").innerHTML = "<input type=\"hidden\" name=\"schoolID\" value=\"-2\" />";	
      	}

      };
      
      easycom.sendMessage = function()
      {
      	var gradeId = $('gradeID').value;
	var schoolId = $('schoolID_sel').value;
	var msg = easycom.urlencode($('txtArea').value);
	var sendTo;
	var profileId;
	if($('profileID'))
	{
		$('profileID').value;
	}
	
	if($('email').checked)
	{
		sendTo = 0;
	}
	else
	if($('text').checked)
	{
		sendTo = 1;
	}
	else
	if($('both').checked)
	{
		sendTo = 2;
	}
	
	if(msg == '' || msg == null)
	{
		alert('Please insert a message');
		return;
	}
      	
      	$('submitFrmArea').innerHTML = "<img src='assets/ajax_loader.gif' width='15px' height='15px' />";
      	new Ajax.Request('modules/EasyCom/ajax/SendMessageContactAPI.php',   
			{   method:'post',    
			parameters: {
					gradeID:gradeId,
					schoolID:schoolId,
					messageTxt:msg,
					sendTo:sendTo,
					profileID:profileId
				    },
			onSuccess: function(transport)
		    	{     
		    		try
		    		{
		    			var response = transport.responseXML || "no response text";       
		  			var resp = response.getElementsByTagName('response')[0].firstChild.nodeValue;
		  			var msg = response.getElementsByTagName('msg')[0].firstChild.nodeValue;
		  			if(resp == 1)
		  			{
		  				$("txtArea").value = '';
		  				alert(msg);
		  			}
		  			else
		  			if(resp == 2)
		  			{
	  					alert(msg);
		  			}
		    			$('submitFrmArea').innerHTML = '<input type="button" value="Send Message" onclick="easycom.sendMessage();" />';
					    			
		    		}
		    		catch(ex)
		    		{
		    			alert(ex);
		    		}
		    	},     
		    	onFailure: function()
		    	{ 
		    		alert('Something went wrong...') 
		    	}   
    	});
      };
      
easycom.urlencode = function(str) {
str = escape(str);
str = str.replace('+', '%2B');
str = str.replace('%20', '+');
str = str.replace('*', '%2A');
str = str.replace('/', '%2F');
str = str.replace('@', '%40');
str = str.replace('[]','%5B%5D');
return str;
}

easycom.urldecode = function(str) {
str = str.replace(/%5B%5D/g, '[]');
var length = str.length;
for(var i=0;i<length;i++)
{	
	str = str.replace('+', ' ');
}
str = unescape(str);
return str;
}