/* 
#
#
#Billing Module
#Copyright (C) 2008 Billboard.Net
#Designer(s): Christopher Whiteley
#Contributor(s): Russell Holmes
#
#
*/ 

var billing = {};
billing.STUDENT = {};
billing.STUDENTS;

billing.CheckForm = function(form)
{
	var length = form.elements.length;
	for(var i = 0; i < length; i++)
	{
		if(form.elements[i].type == "text" || form.elements[i].type == "password" || form.elements[i].type == "textarea")
		{
			if(form.elements[i].value == '' || form.elements[i].value == null)
			{
				alert("Please Complete All Fields");
				return false;	
			}
		}
	}
	return true;
};

billing.formatCurrency = function(num) {
num = num.toString().replace(/\$|\,/g,'');
if(isNaN(num))
num = "0";
sign = (num == (num = Math.abs(num)));
num = Math.floor(num*100+0.50000000001);
cents = num%100;
num = Math.floor(num/100).toString();
if(cents<10)
cents = "0" + cents;
for (var i = 0; i < Math.floor((num.length-(1+i))/3); i++)
num = num.substring(0,num.length-(4*i+3))+','+
num.substring(num.length-(4*i+3));
return (((sign)?'':'-') + '$' + num + '.' + cents);
}

billing.showBalances = function(){
	check_content('ajax.php?modname=Billing/reports.php&TAB=1');
};

billing.showDaliyTrans = function(){
	check_content('ajax.php?modname=Billing/reports.php&TAB=2');
};

billing.showPayments = function(){
	check_content('ajax.php?modname=Billing/fees.php&TAB=2');
};

billing.showFees = function(){
	check_content('ajax.php?modname=Billing/fees.php&TAB=1');
};

billing.showMassFees = function(){
	check_content('ajax.php?modname=Billing/fees.php&TAB=3');
};

billing.showMassPayments = function(){
	check_content('ajax.php?modname=Billing/fees.php&TAB=4');
};

/*Fee Section*/
billing.buildStudentSearchResults = function(){
	var html =  '<table style="width:150px">';
	
	var length = billing.STUDENTS.length;
	for(var i = 0; i < length; i++){
		html += '<tr style="cursor:pointer;" onclick="billing.selectStudent_Fees('+i+');" onmouseout="this.style.backgroundColor=\'\'" onmouseover="this.style.backgroundColor=\'#FFFF99\'"><td>'+billing.STUDENTS[i].last+' , '+billing.STUDENTS[i].first+' '+billing.STUDENTS[i].middle+'.</td></tr>';
	}
	   
	html += "</table>";
	$('searchResultsDiv').innerHTML = html;
};

billing.searchStudents = function(){
	var value = $('studentSearchTB').value;
	if(value == null || value == ''){
		return;
	}
	var ajax = new Ajax.Request('modules/Billing/ajax/searchStudents.php',   
				{   method:'post',    
				    parameters: {VALUE:value},
				       	    onSuccess: function(transport)
				       	    {     
						try
						{
							var jsonData = transport.responseText || "no response text";
							var jsonObject = eval('('+jsonData+')');
							if(jsonObject.result[0].success)
							{
								billing.STUDENTS = jsonObject.users;
								billing.buildStudentSearchResults();
							}
							else
							{
								alert('Error Searching Students');
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

billing.selectStudent_Fees = function(index){
	billing.STUDENT = billing.STUDENTS[index];
	$('searchResultsDiv').innerHTML = '';
	$('studentSearchTB').value = '';
	billing.hideAddFee();
	billing.getStudentFees(billing.STUDENT.id);
};

billing.buildStudentInformation = function(){
	var nocommas     = /,/ig; 	
	var totalFee     = billing.STUDENT.balance.totalFee.replace(nocommas,'');
	var totalPayment = billing.STUDENT.balance.totalPayment.replace(nocommas,'');
	
	totalFee      = parseFloat(totalFee.substring(1));
	totalPayment  = parseFloat(totalPayment.substring(1));
	var balance   = billing.formatCurrency((totalFee - totalPayment));
		
	var html = 'Student: '+billing.STUDENT.last+', '+billing.STUDENT.first+
	  	   '<br/>'+
	  	   '<table>'+
	  	   '<tr><td>Total From Fees:</td><td>'+billing.STUDENT.balance.totalFee+'</td></tr>'+
	  	   '<tr><td>Total From Payments:</td><td>'+billing.STUDENT.balance.totalPayment+'</td></tr>'+
	  	   '<tr><td>Balance:</td><td>'+balance+'</td></tr>'+
	  	   '</table>';
		
	$('selectedStuH').innerHTML = html;
	
};

billing.getStudentFees = function(studentId){
	var ajax = new Ajax.Request('modules/Billing/ajax/getFees.php',   
					{   method:'post',    
					    parameters: {STUDENT_ID:studentId},
					       	    onSuccess: function(transport)
					       	    {     
							try
							{
								var jsonData = transport.responseText || "no response text";
								var jsonObject = eval('('+jsonData+')');
								if(jsonObject.result[0].success)
								{
									billing.STUDENT.fees    = jsonObject.fees;
									billing.STUDENT.balance = jsonObject.balance[0];
									billing.buildFeeTbl();
								}
								else
								{
									alert('Error Searching Students');
								}
							}
							catch(ex)
							{
								alert(ex);
								//alert('Error Searching Students');
							}
						    },     
						    onFailure: function()
						    { 
							alert('Something went wrong...');
						    }   
        });
};

billing.buildFeeTbl = function(){
	var html = '<table style="width:550px;" cellspacing="0" cellpadding="0">'+
				'<thead style="border:solid 2px black;background-color:#09C;font-weight:bold;">'+
				'<tr align="center">'+
					'<td style="color:#FFF;">Title</td>'+
					'<td style="color:#FFF;">Amount</td>'+
					'<td style="color:#FFF;">Assigned</td>'+
					'<td style="color:#FFF;">Due</td>'+
					'<td style="color:#FFF;">Comment</td>'+
					'<td style="color:#FFF;">Action</td>'+
				'</tr>'+
				'</thead>';
	
	var length = billing.STUDENT.fees.length;
	for(var i=0; i < length; i++){
		if(i % 2 == 0){
			html += '<tr align="center" style="background-color:#FFFF99">';
		}
		else{
			html += '<tr align="center">';
		}
					
		if(billing.STUDENT.fees[i].waived == 1){
			html += '<td>'+billing.STUDENT.fees[i].title+'</td>'+
				'<td>'+billing.STUDENT.fees[i].amount+'</td>'+
				'<td>'+billing.STUDENT.fees[i].assignedDate+'</td>'+
				'<td>'+billing.STUDENT.fees[i].dueDate+'</td>'+
				'<td>'+billing.STUDENT.fees[i].comment+'</td>'+
				'<td>Waived | <a href="javascript:billing.deleteFee('+billing.STUDENT.fees[i].id+')">Delete</a></td>'+
				'</tr>';
			
			if(i % 2 != 0){
				html += '<tr align="center" style="background-color:#FFFF99">';
			}
			else{
				html += '<tr align="center">';
			}
			
			html += '<td>'+billing.STUDENT.fees[i].title+' Waiver</td>'+
			        '<td>-'+billing.STUDENT.fees[i].amount+'</td>'+
				'<td>'+billing.STUDENT.fees[i].assignedDate+'</td>'+
				'<td>'+billing.STUDENT.fees[i].dueDate+'</td>'+
				'<td>Waiver</td>'+
				'<td><a href="javascript:billing.removeWaiveFee('+billing.STUDENT.fees[i].id+')">Remove Waiver</a></td>'+
				'</tr>';
		}
		else{
			html += '<td>'+billing.STUDENT.fees[i].title+'</td>'+
			        '<td>'+billing.STUDENT.fees[i].amount+'</td>'+
				'<td>'+billing.STUDENT.fees[i].assignedDate+'</td>'+
				'<td>'+billing.STUDENT.fees[i].dueDate+'</td>'+
				'<td>'+billing.STUDENT.fees[i].comment+'</td>'+
				'<td><a href="javascript:billing.waiveFee('+billing.STUDENT.fees[i].id+')">Waive</a> | <a href="javascript:billing.deleteFee('+billing.STUDENT.fees[i].id+')">Delete</a></td>'+
				'</tr>';
		}
		
		
		
	}
	if(length == 0){
		html += '<tr style="background-color:#FFFF99"><td colspan="6">No Fees</td></tr>';
	}
	html += '<tr><td colspan="6"><a href="javascript:billing.showAddFee();">Add Fee [+]</a></td></tr></table>';
	$('feesTblDiv').innerHTML = html;
	billing.buildStudentInformation();
};

billing.showAddFee = function(){
	$('title').value = '';
	$('amount').value = '';
	$('comment').value = '';
	$('addFeeDiv').style.display = '';
};

billing.hideAddFee = function(){
	$('addFeeDiv').style.display = 'none';
};

billing.removeWaiveFee = function(feeId){
		var ajax = new Ajax.Request('modules/Billing/ajax/removeWaiver.php',   
					{   method:'post',    
					    parameters: {FEE_ID:feeId},
					       	    onSuccess: function(transport)
					       	    {     
							try
							{
								var jsonData = transport.responseText || "no response text";
								var jsonObject = eval('('+jsonData+')');
								if(jsonObject.result[0].success)
								{
									alert('Waiver Removed');
									billing.getStudentFees(billing.STUDENT.id);
								}
								else
								{
									alert('Error Adding Fee');
								}
							}
							catch(ex)
							{
								//alert(ex);
								alert('Error Adding Fee');
							}
						    },     
						    onFailure: function()
						    { 
							alert('Something went wrong...');
						    }   
        	});
};

billing.waiveFee = function(feeId){
		var ajax = new Ajax.Request('modules/Billing/ajax/waiveFee.php',   
					{   method:'post',    
					    parameters: {FEE_ID:feeId},
					       	    onSuccess: function(transport)
					       	    {     
							try
							{
								var jsonData = transport.responseText || "no response text";
								var jsonObject = eval('('+jsonData+')');
								if(jsonObject.result[0].success)
								{
									alert('Fee Waived');
									billing.getStudentFees(billing.STUDENT.id);
								}
								else
								{
									alert('Error Adding Fee');
								}
							}
							catch(ex)
							{
								//alert(ex);
								alert('Error Adding Fee');
							}
						    },     
						    onFailure: function()
						    { 
							alert('Something went wrong...');
						    }   
        	});
};

billing.deleteFee = function(feeId){
		var ajax = new Ajax.Request('modules/Billing/ajax/deleteFee.php',   
					{   method:'post',    
					    parameters: {FEE_ID:feeId},
					       	    onSuccess: function(transport)
					       	    {     
							try
							{
								var jsonData = transport.responseText || "no response text";
								var jsonObject = eval('('+jsonData+')');
								if(jsonObject.result[0].success)
								{
									alert('Fee Deleted');
									billing.getStudentFees(billing.STUDENT.id);
								}
								else
								{
									alert('Error Adding Fee');
								}
							}
							catch(ex)
							{
								//alert(ex);
								alert('Error Adding Fee');
							}
						    },     
						    onFailure: function()
						    { 
							alert('Something went wrong...');
						    }   
        	});
};

billing.saveFee = function(){
	if(billing.CheckForm($('newFeeFrm'))){
		var form = $('newFeeFrm');
		var data = form.serialize();
		var ajax = new Ajax.Request('modules/Billing/ajax/addFee.php',   
					{   method:'post',    
					    parameters: data+'&MODULE=billing&STUDENT_ID='+billing.STUDENT.id,
					       	    onSuccess: function(transport)
					       	    {     
							try
							{
								var jsonData = transport.responseText || "no response text";
								var jsonObject = eval('('+jsonData+')');
								if(jsonObject.result[0].success)
								{
									alert('Fee Added');
									billing.getStudentFees(billing.STUDENT.id);
									billing.hideAddFee();
								}
								else
								{
									alert('Error Adding Fee');
								}
							}
							catch(ex)
							{
								//alert(ex);
								alert('Error Adding Fee');
							}
						    },     
						    onFailure: function()
						    { 
							alert('Something went wrong...');
						    }   
        	});
	}
	else{
		return;
	}
};

/*End Student Fees*/
/*Start Student Payments*/

billing.buildStudentSearchResults_payment = function(){
	var html =  '<table style="width:150px">';
	
	var length = billing.STUDENTS.length;
	for(var i = 0; i < length; i++){
		html += '<tr style="cursor:pointer;" onclick="billing.selectStudent_payment('+i+');" onmouseout="this.style.backgroundColor=\'\'" onmouseover="this.style.backgroundColor=\'#FFFF99\'"><td>'+billing.STUDENTS[i].last+', '+billing.STUDENTS[i].first+' '+billing.STUDENTS[i].middle+'.</td></tr>';
	}
	   
	html += "</table>";
	$('searchResultsDiv').innerHTML = html;
};

billing.searchStudents_payment = function(){
	var value = $('studentSearchTB').value;
	if(value == null || value == ''){
		return;
	}
	var ajax = new Ajax.Request('modules/Billing/ajax/searchStudents.php',   
				{   method:'post',    
				    parameters: {VALUE:value},
				       	    onSuccess: function(transport)
				       	    {     
						try
						{
							var jsonData = transport.responseText || "no response text";
							var jsonObject = eval('('+jsonData+')');
							if(jsonObject.result[0].success)
							{
								billing.STUDENTS = jsonObject.users;
								billing.buildStudentSearchResults_payment();
							}
							else
							{
								alert('Error Searching Students');
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

billing.selectStudent_payment = function(index){
	billing.STUDENT = billing.STUDENTS[index];
	$('searchResultsDiv').innerHTML = '';
	$('studentSearchTB').value = '';
	billing.hideAddPayment();
	billing.getStudentPayments(billing.STUDENT.id);
};

billing.getStudentPayments = function(studentId){
	var ajax = new Ajax.Request('modules/Billing/ajax/getPayments.php',   
					{   method:'post',    
					    parameters: {STUDENT_ID:studentId},
					       	    onSuccess: function(transport)
					       	    {     
							try
							{
								var jsonData = transport.responseText || "no response text";
								var jsonObject = eval('('+jsonData+')');
								if(jsonObject.result[0].success)
								{
									billing.STUDENT.payments  = jsonObject.payments;
									billing.STUDENT.balance   = jsonObject.balance[0];
									billing.buildPaymentTbl();
								}
								else
								{
									alert('Error Getting Payments');
								}
							}
							catch(ex)
							{
								alert(ex);
								//alert('Error Getting Payments');
							}
						    },     
						    onFailure: function()
						    { 
							alert('Something went wrong...');
						    }   
        });
};

billing.buildPaymentTbl = function(){
	var html = '<table style="width:550px;" cellspacing="0" cellpadding="0">'+
				'<thead style="border:solid 2px black;background-color:#09C;font-weight:bold;">'+
				'<tr align="center">'+
					'<td style="color:#FFF;">Amount</td>'+
					'<td style="color:#FFF;">Type</td>'+
					'<td style="color:#FFF;">Date</td>'+
					'<td style="color:#FFF;">Comment</td>'+
					'<td style="color:#FFF;">Action</td>'+
				'</tr>'+
				'</thead>';
	
	var length = billing.STUDENT.payments.length;
	for(var i=0; i < length; i++){
		if(i % 2 == 0){
			html += '<tr align="center" style="background-color:#FFFF99">';
		}
		else{
			html += '<tr align="center">';
		}
					
		if(billing.STUDENT.payments[i].refunded == 1){
			html += '<td>'+billing.STUDENT.payments[i].amount+'</td>'+
				'<td>'+billing.STUDENT.payments[i].type+'</td>'+
				'<td>'+billing.STUDENT.payments[i].date+'</td>'+
				'<td>'+billing.STUDENT.payments[i].comment+'</td>'+
				'<td>Refunded | <a href="javascript:billing.deletePayment('+billing.STUDENT.payments[i].id+')">Delete</a></td>'+
				'</tr>';
			
			if(i % 2 != 0){
				html += '<tr align="center" style="background-color:#FFFF99">';
			}
			else{
				html += '<tr align="center">';
			}
			
			html += '<td>-'+billing.STUDENT.payments[i].amount+'</td>'+
				'<td>'+billing.STUDENT.payments[i].type+'</td>'+
				'<td>'+billing.STUDENT.payments[i].date+'</td>'+
				'<td>'+billing.STUDENT.payments[i].comment+'</td>'+
				'<td><a href="javascript:billing.removeRefund('+billing.STUDENT.payments[i].id+')">Remove Refund</a></td>'+
				'</tr>';
		}
		else{
			html += '<td>'+billing.STUDENT.payments[i].amount+'</td>'+
				'<td>'+billing.STUDENT.payments[i].type+'</td>'+
				'<td>'+billing.STUDENT.payments[i].date+'</td>'+
				'<td>'+billing.STUDENT.payments[i].comment+'</td>'+
				'<td><a href="javascript:billing.refund('+billing.STUDENT.payments[i].id+')">Refund</a> | <a href="javascript:billing.deletePayment('+billing.STUDENT.payments[i].id+')">Delete</a></td>'+
				'</tr>';
		}
		
		
		
	}
	if(length == 0){
		html += '<tr style="background-color:#FFFF99"><td colspan="6">No Payments</td></tr>';
	}
	html += '<tr><td colspan="6"><a href="javascript:billing.showAddPayment();">Add Payment [+]</a></td></tr></table>';
	$('paymentTblDiv').innerHTML = html;
	billing.buildStudentInformation();
};

billing.buildStudentInformation = function(){
	var nocommas     = /,/ig; 
	var totalFee     = billing.STUDENT.balance.totalFee.replace(nocommas,'');
	var totalPayment = billing.STUDENT.balance.totalPayment.replace(nocommas,'');
	
	totalFee      = parseFloat(totalFee.substring(1));
	totalPayment  = parseFloat(totalPayment.substring(1));
	var balance   = billing.formatCurrency((totalFee - totalPayment));
	var html = 'Student: '+billing.STUDENT.last+', '+billing.STUDENT.first+
	  	   '<br/>'+
	  	   '<table>'+
	  	   '<tr><td>Total From Fees:</td><td>'+billing.STUDENT.balance.totalFee+'</td></tr>'+
	  	   '<tr><td>Total From Payments:</td><td>'+billing.STUDENT.balance.totalPayment+'</td></tr>'+
	  	   '<tr><td>Balance:</td><td>'+balance+'</td></tr>'+
	  	   '</table>';
		
	$('selectedStuH').innerHTML = html;
	
};

billing.showAddPayment = function(){
	$('amount').value = '';
	$('comment').value = '';
	$('addPaymentDiv').style.display = '';
};

billing.hideAddPayment = function(){
	$('addPaymentDiv').style.display = 'none';
};

billing.savePayment = function(){
	if(billing.CheckForm($('newPaymentFrm'))){
		var form = $('newPaymentFrm');
		var data = form.serialize();
		var ajax = new Ajax.Request('modules/Billing/ajax/addPayment.php',   
					{   method:'post',    
					    parameters: data+'&MODULE=billing&STUDENT_ID='+billing.STUDENT.id,
					       	    onSuccess: function(transport)
					       	    {     
							try
							{
								var jsonData = transport.responseText || "no response text";
								var jsonObject = eval('('+jsonData+')');
								if(jsonObject.result[0].success)
								{
									alert('Payment Added');
									billing.getStudentPayments(billing.STUDENT.id);
									billing.hideAddPayment();
								}
								else
								{
									alert('Error Adding Payment');
								}
							}
							catch(ex)
							{
								//alert(ex);
								alert('Error Adding Payment');
							}
						    },     
						    onFailure: function()
						    { 
							alert('Something went wrong...');
						    }   
        	});
	}
	else{
		return;
	}
};

billing.removeRefund = function(paymentId){
		var ajax = new Ajax.Request('modules/Billing/ajax/removeRefund.php',   
					{   method:'post',    
					    parameters: {PAYMENT_ID:paymentId},
					       	    onSuccess: function(transport)
					       	    {     
							try
							{
								var jsonData = transport.responseText || "no response text";
								var jsonObject = eval('('+jsonData+')');
								if(jsonObject.result[0].success)
								{
									alert('Refund Removed');
									billing.getStudentPayments(billing.STUDENT.id);
								}
								else
								{
									alert('Error Removing Refund');
								}
							}
							catch(ex)
							{
								//alert(ex);
								alert('Error Removing Refund');
							}
						    },     
						    onFailure: function()
						    { 
							alert('Something went wrong...');
						    }   
        	});
};

billing.refund = function(paymentId){
		var ajax = new Ajax.Request('modules/Billing/ajax/refundPayment.php',   
					{   method:'post',    
					    parameters: {PAYMENT_ID:paymentId},
					       	    onSuccess: function(transport)
					       	    {     
							try
							{
								var jsonData = transport.responseText || "no response text";
								var jsonObject = eval('('+jsonData+')');
								if(jsonObject.result[0].success)
								{
									alert('Payment Refunded');
									billing.getStudentPayments(billing.STUDENT.id);
								}
								else
								{
									alert('Error Refunding');
								}
							}
							catch(ex)
							{
								//alert(ex);
								alert('Error Refunding');
							}
						    },     
						    onFailure: function()
						    { 
							alert('Something went wrong...');
						    }   
        	});
};

billing.deletePayment = function(feeId){
		var ajax = new Ajax.Request('modules/Billing/ajax/deletePayment.php',   
					{   method:'post',    
					    parameters: {PAYMENT_ID:paymentId},
					       	    onSuccess: function(transport)
					       	    {     
							try
							{
								var jsonData = transport.responseText || "no response text";
								var jsonObject = eval('('+jsonData+')');
								if(jsonObject.result[0].success)
								{
									alert('Payment Deleted');
									billing.getStudentPayments(billing.STUDENT.id);
								}
								else
								{
									alert('Error Deleting Payment');
								}
							}
							catch(ex)
							{
								//alert(ex);
								alert('Error Deleting Payment');
							}
						    },     
						    onFailure: function()
						    { 
							alert('Something went wrong...');
						    }   
        	});
};

/*End Student Payments*/
/*Start Student Mass Fees/Payments*/

billing.selectAll = function(frm, elem){
	var form = $(frm);
	if(!elem.checked){
		var length = form.elements.length;
		for(var i = 0; i < length; i++)
		{
			if(form.elements[i].type == "checkbox")
			{
				form.elements[i].checked = false;
			}
		};
	}
	else{
		var length = form.elements.length;
		for(var i = 0; i < length; i++)
		{
			if(form.elements[i].type == "checkbox")
			{
				form.elements[i].checked = true;
			}
		}
	}
};

billing.submitMassFeeForm = function(){
	if(billing.CheckForm($('newMassFeeFrm'))){
		var form = $('newMassFeeFrm')
		var data = form.serialize();
		var ajax = new Ajax.Request('modules/Billing/ajax/addMassFee.php',   
					{   method:'post',    
					    parameters: data+'&MODULE=billing',
					       	    onSuccess: function(transport)
					       	    {     
							try
							{
								var jsonData = transport.responseText || "no response text";
								var jsonObject = eval('('+jsonData+')');
								if(jsonObject.result[0].success)
								{
									alert('Fees Added');
								}
								else
								{
									alert('Error Adding Fees');
								}
							}
							catch(ex)
							{
								//alert(ex);
								alert('Error Adding Fees');
							}
						    },     
						    onFailure: function()
						    { 
							alert('Something went wrong...');
						    }   
        	});
	}
	else{
		return;
	}
};

billing.submitMassPaymentForm = function(){
	var form = $('newMassPaymentFrm');
	if(billing.CheckForm(form)){
		var data = form.serialize();
		var ajax = new Ajax.Request('modules/Billing/ajax/addMassPayment.php',   
					{   method:'post',    
					    parameters: data+'&MODULE=billing',
					       	    onSuccess: function(transport)
					       	    {     
							try
							{
								var jsonData = transport.responseText || "no response text";
								var jsonObject = eval('('+jsonData+')');
								if(jsonObject.result[0].success)
								{
									alert('Payments Added');
								}
								else
								{
									alert('Error Adding Payments');
								}
							}
							catch(ex)
							{
								//alert(ex);
								alert('Error Adding Payments');
							}
						    },     
						    onFailure: function()
						    { 
							alert('Something went wrong...');
						    }   
        	});
	}
	else{
		return;
	}
};

billing.filterTransReport = function(tab){
	var form = $('filterFrm');
	var data = form.serialize();
	check_content('ajax.php?modname=Billing/reports.php&TAB='+tab+'&'+data)
};

billing.filterTransReportAll = function(tab){
	$('studentFilterTB').value = '';
	var form = $('filterFrm');
	var data = form.serialize();
	check_content('ajax.php?modname=Billing/reports.php&TAB='+tab+'&'+data)
};

billing.showTransactionsPDF = function(){
	var form = $('filterFrm');
	var data = form.serialize();
	
	window.location.href="modules/Billing/reports_transactions_pdf.php?"+data;
};

billing.showBalancesPDF = function(){
	var form = $('filterFrm');
	var data = form.serialize();
		
	window.location.href="modules/Billing/reports_balances_pdf.php?"+data;
};

billing.getAllPaymentTypes = function(){
	var ajax = new Ajax.Request('modules/Billing/ajax/getPaymentTypes.php',   
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
					billing.buildTypesTable(jsonObject.types);
					
				}
				else
				{
					alert('Error Deleting Payment Type');
				}
			}
			catch(ex)
			{
				//alert(ex);
				alert('Error Deleting Payment Type');
			}
		    },     
		    onFailure: function()
		    { 
			alert('Something went wrong...');
	    	}   
       	});
};

billing.buildTypesTable = function(types){
	html = '<div align="center" id="edit_new_Area"</div>';
	html += '<table style="width:300px;" cellspacing="0" cellpadding="1">'+
			'<thead style="border:solid 2px black;background-color:#09C;font-weight:bold;">'+
			'<tr>'+
				'<td style="color:#FFF;">Description</td>'+
				'<td style="color:#FFF;">Delete</td>'+
			'</tr>'+
			'</thead>';
	var length = types.length;
	for(var i = 0; i < length; i++){
		if(i % 2 == 0){
			html += '<tr style="background-color:#FFFF99">';
		}
		else{
			html += '<tr>';
		}
		
		html += '<td>'+types[i].desc+'</td>'+
		  	'<td><a href="javascript:billing.deletePaymentType('+types[i].id+');">Delete</a></td>'+
		  	'</tr>';
	}
	
	html += '<tr><td colspan="3"><a align="left" href="javascript:billing.showNewPaymentType();">[+] Add New</a></td></tr></table>';
	$('main').innerHTML = html;
};

billing.showNewPaymentType = function(){
	html = '<table><tr><td>Description</td><td><input type="text" size="30" id="newPaymentType" /></td></tr><tr><td colspan="2" align="center"><input type="button" onclick="billing.saveNewPaymentType();" value="Save" />&nbsp;&nbsp;<input type="button" onclick="billing.cancelNewPaymentType();" value="Cancel" /></td></tr></table>';
	$('edit_new_Area').innerHTML = html;
};

billing.cancelNewPaymentType = function(){
	$('edit_new_Area').innerHTML = '';
};

billing.deletePaymentType = function(id){
	var ajax = new Ajax.Request('modules/Billing/ajax/deletePaymentType.php',   
		{   method:'post',    
		    parameters: {ID:id},
	       	    onSuccess: function(transport)
	       	    {     
			try
			{
				var jsonData = transport.responseText || "no response text";
				var jsonObject = eval('('+jsonData+')');
				if(jsonObject.result[0].success)
				{
					alert('Payment Type Deleted');
					billing.getAllPaymentTypes();
				}
				else
				{
					alert('Error Deleting Payment Type');
				}
			}
			catch(ex)
			{
				//alert(ex);
				alert('Error Deleting Payment Type');
			}
		    },     
		    onFailure: function()
		    { 
			alert('Something went wrong...');
	    	}   
       	});
};

billing.saveNewPaymentType = function(){
	var desc = $('newPaymentType').value;
	if(desc == null || desc == ''){
		return;
	}
	var ajax = new Ajax.Request('modules/Billing/ajax/addPaymentType.php',   
		{   method:'post',    
		    parameters: {DESC:desc},
	       	    onSuccess: function(transport)
	       	    {     
			try
			{
				var jsonData = transport.responseText || "no response text";
				var jsonObject = eval('('+jsonData+')');
				if(jsonObject.result[0].success)
				{
					alert('Payment Type Added');
					billing.getAllPaymentTypes();
				}
				else
				{
					alert('Error Adding Payment Type');
				}
			}
			catch(ex)
			{
				//alert(ex);
				alert('Error Adding Payment Type');
			}
		    },     
		    onFailure: function()
		    { 
			alert('Something went wrong...');
	    	}   
       	});
};