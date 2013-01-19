////////////////////////////////////  Page By Page Validation Start //////////////////////////////////////////////////

//////////////////////////////////////// School Setup Start //////////////////////////////////////////////////////////

		function formcheck_school_setup_school()
		{
			var sel = document.getElementsByTagName('input');
			for(var i=1; i<sel.length; i++)
			{
				var inp_value = sel[i].value;
				if(inp_value == "")
				{
					var inp_name = sel[i].name;
					if(inp_name == 'values[TITLE]')
					{
						document.getElementById('divErr').innerHTML="<b><font color=red>Please Enter School Name</font></b>";
						return false;
					}
					else if(inp_name == 'values[ADDRESS]')
					{
						document.getElementById('divErr').innerHTML="<b><font color=red>Please Enter Address</font></b>";
						return false;
					}
					else if(inp_name == 'values[CITY]')
					{
						document.getElementById('divErr').innerHTML="<b><font color=red>Please Enter City</font></b>";
						return false;
					}
					else if(inp_name == 'values[STATE]')
					{
						document.getElementById('divErr').innerHTML="<b><font color=red>Please Enter State</font></b>";
						return false;
					}
					else if(inp_name == 'values[ZIPCODE]')
					{
						document.getElementById('divErr').innerHTML="<b><font color=red>Please Enter Zip</font></b>";
						return false;
					}
					else if(inp_name == 'values[PHONE]')
					{
						document.getElementById('divErr').innerHTML="<b><font color=red>Please Enter Phone</font></b>";
						return false;
					}
					else if(inp_name == 'values[PRINCIPAL]')
					{
						document.getElementById('divErr').innerHTML="<b><font color=red>Please Enter Principal</font></b>";
						return false;
					}
					else if(inp_name == 'values[REPORTING_GP_SCALE]')
					{
						document.getElementById('divErr').innerHTML="<b><font color=red>Please Enter Base Grading Scale</font></b>";
						return false;
					}
					/*
					else
					{
						document.school.submit();
					}
					*/
				}
				else if(inp_value != "")
				{
					var val = inp_value;
					var inp_name1 = sel[i].name;
					
					if(inp_name1 == 'values[ZIPCODE]')
					{
					//	var phoneRegxp = /^\+?[\d\s]+\(?[\d\s]{10,}$/;
					//	alert(phoneRegxp.test(val));
					//	alert(charpos);
						var charpos = val.search("[^0-9]");								 
						if (charpos >= 0)
						{
							document.getElementById('divErr').innerHTML="<b><font color=red>Please Enter a Numeric Value.</font></b>";
							return false;
						}
					}
					else if(inp_name1 == 'values[PHONE]')
					{
					//	var phoneRegxp = /^\+?[\d\s]+\(?[\d\s]{10,}$/;
					//	alert(phoneRegxp.test(val));
					//	alert(charpos);
						var charpos = val.search("[^0-9-\(\)\, ]");								 
						if (charpos >= 0)
						{
							document.getElementById('divErr').innerHTML="<b><font color=red>Please Enter a Valid Phone Number.</font></b>";
							return false;
						}
					}
					else if(inp_name1 == 'values[REPORTING_GP_SCALE]')
					{
					//	var bgRegxp = /^0-9.$/;
						var charpos = val.search("[^0-9.]");
						if (charpos >= 0)
						{
							document.getElementById('divErr').innerHTML="<b><font color=red>Please Enter Decimal Value Only.</font></b>";
							return false;
						}
					}
					else if(inp_name1 == 'values[E_MAIL]')
					{
						var emailRegxp = /^(.+)@(.+)$/;
						if (emailRegxp.test(val) != true)
						{
							document.getElementById('divErr').innerHTML="<b><font color=red>Please Enter a Valid Email.</font></b>";
							return false;
						}
					}
					else if(inp_name1 == 'values[WWW_ADDRESS]')
					{
						var urlRegxp = /^(http:\/\/www.|https:\/\/www.|ftp:\/\/www.|www.){1}([\w]+)(.[\w]+){1,2}$/;
						if (urlRegxp.test(val) != true)
						{
							document.getElementById('divErr').innerHTML="<b><font color=red>Please Enter a Valid url.</font></b>";
							return false;
						}
					}
				}
			}
			document.school.submit();
		}

/*
		function formcheck_school_setup_school(){
		
		var frmvalidator  = new Validator("school");
		frmvalidator.addValidation("values[TITLE]","req","Please enter the School Name");
		frmvalidator.addValidation("values[TITLE]","maxlen=100", "Max length for School Name is 100");
		
		frmvalidator.addValidation("values[ADDRESS]","req","Please enter the Address");
		frmvalidator.addValidation("values[ADDRESS]","maxlen=100", "Max length for Address is 100");
	
		frmvalidator.addValidation("divvalues[ADDRESS]","req","Please enter the Address");
		
		frmvalidator.addValidation("values[CITY]","req","Please enter City");
		frmvalidator.addValidation("values[CITY]","maxlen=100", "Max length for City is 100");
		
		frmvalidator.addValidation("values[STATE]","req","Please enter State");
			
		frmvalidator.addValidation("values[ZIPCODE]","req","Please enter ZIP");
		frmvalidator.addValidation("values[ZIPCODE]","num", "Zip Code allows only numeric value");
			
		frmvalidator.addValidation("values[PHONE]","maxlen=30", "Max length for Phone is 30");
		frmvalidator.addValidation("values[PHONE]","phone", "Enter a valid telephone number");
		
		frmvalidator.addValidation("values[PRINCIPAL]","req","Please enter Principal Name");
		frmvalidator.addValidation("values[PRINCIPAL]","maxlen=100", "Max length for Principal is 100");
		frmvalidator.addValidation("values[PRINCIPAL]","alphanumeric","Principal Name cannot be numbers");
		
		frmvalidator.addValidation("values[REPORTING_GP_SCALE]","req","Please enter Base Grading Scale");
		frmvalidator.addValidation("values[REPORTING_GP_SCALE]","dec", "Grading Scale allows only decimal value");
		frmvalidator.addValidation("values[REPORTING_GP_SCALE]","maxlen=100", "Max length for Base Grading Scale is 100");

		frmvalidator.addValidation("values[E_MAIL]","email","");
		frmvalidator.addValidation("values[E_MAIL]","maxlen=100", "Max length for email is 100");

		frmvalidator.addValidation("values[CEEB]","maxlen=10", "Max length for CEEB is 100");
		frmvalidator.addValidation("values[WWW_ADDRESS]","url","");
		frmvalidator.addValidation("values[WWW_ADDRESS]","maxlen=100", "Max length for URL is 100");
		
		}
		*/

//////////////////////////////////////// School Setup End //////////////////////////////////////////////////////////

///////////////////////////////////////// Portal Notes Start ///////////////////////////////////////////////////////

	function formcheck_school_setup_portalnotes()
	{
	
		var frmvalidator  = new Validator("F2");
		
		frmvalidator.addValidation("values[new][TITLE]","alphanumeric", "Title allows only alphanumeric value");
		frmvalidator.addValidation("values[new][TITLE]","maxlen=50", "Max length for Title is 50");
		
		frmvalidator.addValidation("values[new][SORT_ORDER]","num", "Sort Order allows only numeric value");
		frmvalidator.addValidation("values[new][SORT_ORDER]","maxlen=5", "Max length for Sort Order is 5");
		
		frmvalidator.setAddnlValidationFunction("ValidateDate_Portal_Notes");

	
	}
	
	
	
		
	function ValidateDate_Portal_Notes()
	{
		var sm, sd, sy, em, ed, ey, psm, psd, psy, pem, ped, pey ;
		var frm = document.forms["F2"];
		var elem = frm.elements;
		for(var i = 0; i < elem.length; i++)
		{
			if(elem[i].name=="month_values[new][START_DATE]")
			{
				sm=elem[i];
			}
			
			if(elem[i].name=="day_values[new][START_DATE]")
			{
				sd=elem[i];
			}
			
			if(elem[i].name=="year_values[new][START_DATE]")
			{
				sy=elem[i];
			}
			
			if(elem[i].name=="month_values[new][END_DATE]")
			{
				em=elem[i];
			}
			
			if(elem[i].name=="day_values[new][END_DATE]")
			{
				ed=elem[i];
			}
			
			if(elem[i].name=="year_values[new][END_DATE]")
			{
				ey=elem[i];
			}
		}
		
		try
		{
		   if (false==CheckDate(sm, sd, sy, em, ed, ey))

		   {
			   em.focus();
			   return false;
		   }
		}
		catch(err)
		{
		
		}

		try
		{  
		   if (false==isDate(psm, psd, psy))
		   {
			   alert("Please enter the Grade Posting Start Date");
			   psm.focus();
			   return false;
		   }
		}   
		catch(err)
		{
		
		}
		
		try
		{  
		   if (true==isDate(pem, ped, pey))
		   {
			   if (false==CheckDate(psm, psd, psy, pem, ped, pey))
			   {
				   pem.focus();
				   return false;
			   }
		   }
		}   
		catch(err)
		{
		
		}
		   
		   return true;
		
	}



///////////////////////////////////////// Portal Notes End /////////////////////////////////////////////////////////

///////////////////////////////////////// Marking Periods Start ////////////////////////////////////////////////////
	function formcheck_school_setup_marking(){

  	var frmvalidator  = new Validator("marking_period");
  	frmvalidator.addValidation("tables[new][TITLE]","req","Please enter the title");
  	frmvalidator.addValidation("tables[new][TITLE]","maxlen=50", "Max length for title is 50");
	
	frmvalidator.addValidation("tables[new][SHORT_NAME]","req","Please enter the Short Name");
  	frmvalidator.addValidation("tables[new][SHORT_NAME]","maxlen=10", "Max length for Short Name is 10");
	
	//frmvalidator.addValidation("tables[new][SORT_ORDER]","req","Please enter the Short Order");
  	frmvalidator.addValidation("tables[new][SORT_ORDER]","maxlen=5", "Max length for Short Order is 5");
  	frmvalidator.addValidation("tables[new][SORT_ORDER]","num", "Enter Only Numeric Value");
	
	frmvalidator.setAddnlValidationFunction("ValidateDate_Marking_Periods");
}

function ValidateDate_Marking_Periods()
{
var sm, sd, sy, em, ed, ey, psm, psd, psy, pem, ped, pey, grd ;
var frm = document.forms["marking_period"];
var elem = frm.elements;
for(var i = 0; i < elem.length; i++)
{

if(elem[i].name=="month_tables[new][START_DATE]")
{
sm=elem[i];
}
if(elem[i].name=="day_tables[new][START_DATE]")
{
sd=elem[i];
}
if(elem[i].name=="year_tables[new][START_DATE]")
{
sy=elem[i];
}


if(elem[i].name=="month_tables[new][END_DATE]")
{
em=elem[i];
}
if(elem[i].name=="day_tables[new][END_DATE]")
{
ed=elem[i];
}
if(elem[i].name=="year_tables[new][END_DATE]")
{
ey=elem[i];
}


if(elem[i].name=="month_tables[new][POST_START_DATE]")
{
psm=elem[i];
}
if(elem[i].name=="day_tables[new][POST_START_DATE]")
{
psd=elem[i];
}
if(elem[i].name=="year_tables[new][POST_START_DATE]")
{
psy=elem[i];
}


if(elem[i].name=="month_tables[new][POST_END_DATE]")
{
pem=elem[i];
}
if(elem[i].name=="day_tables[new][POST_END_DATE]")
{
ped=elem[i];
}
if(elem[i].name=="year_tables[new][POST_END_DATE]")
{
pey=elem[i];
}

if(elem[i].name=="tables[new][DOES_GRADES]")
{
grd=elem[i];
}

}


try
{
if (false==isDate(sm, sd, sy))
   {
   document.getElementById("divErr").innerHTML="<b><font color=red>"+"Please enter the Start Date."+"</font></b>";
   sm.focus();
   return false;
   }
}
catch(err)
{

}
try
{  
   if (false==isDate(em, ed, ey))
   {
  document.getElementById("divErr").innerHTML="<b><font color=red>"+"Please Enter the End Date."+"</font></b>";
   em.focus();
   return false;
   }
}   
catch(err)
{

}
try
{
   if (false==CheckDate(sm, sd, sy, em, ed, ey))
   {
   em.focus();
   return false;
   }
}
catch(err)
{

}

if (true==validate_chk(grd))
{

try
{  
   if (false==isDate(psm, psd, psy))
   {
  document.getElementById("divErr").innerHTML="<b><font color=red>"+"Please enter the Grade Posting Start Date."+"</font></b>";
   psm.focus();
   return false;
   }
}   
catch(err)
{

}

try
{  
   if (true==isDate(pem, ped, pey))
   {
   if (false==CheckDate(psm, psd, psy, pem, ped, pey))
   {
   pem.focus();
   return false;
   }
   }

}   
catch(err)
{

}






try
{
   if (false==CheckDateMar(sm, sd, sy, psm, psd, psy))
   {
	   psm.focus();
	   return false;
   }
}
catch(err)
{

}



try
{
   if (false==CheckDateMarEnd(pem, ped, pey, em, ed, ey))
   {
	   pem.focus();
	   return false;
   }
}
catch(err)
{

}

}




   return true;
}


///////////////////////////////////////// Marking Periods End //////////////////////////////////////////////////////


///////////////////////////////////////// Copy School Start ////////////////////////////////////////////////////////////

function formcheck_school_setup_copyschool()
{
	var frmvalidator  = new Validator("prompt_form");
	frmvalidator.addValidation("title","req","Please enter the New School's Title");
	frmvalidator.addValidation("title","maxlen=100", "Max length for Title is 100");
}


///////////////////////////////////////// Copy School End ////////////////////////////////////////////////////


///////////////////////////////////////// Calender Start ////////////////////////////////////////////////////////////

function formcheck_school_setup_calender()
{
	var frmvalidator  = new Validator("prompt_form");
	frmvalidator.addValidation("title","req","Please enter the Title");
	frmvalidator.addValidation("title","maxlen=100", "Max length for Title is 100");
}


///////////////////////////////////////// Calender End //////////////////////////////////////////////////////////////

///////////////////////////////////////// Periods Start ////////////////////////////////////////////////////////////

function formcheck_school_setup_periods()
{
  	var frmvalidator  = new Validator("F1");
	
	frmvalidator.addValidation("values[new][TITLE]","alnum", "Title allows only alphanumeric value");
	frmvalidator.addValidation("values[new][TITLE]","maxlen=50", "Max length for Title is 50");
	
	frmvalidator.addValidation("values[new][SHORT_NAME]","alnum", "Short Name allows only alphanumeric value");
	frmvalidator.addValidation("values[new][SHORT_NAME]","maxlen=50", "Max length for Short Name is 50");
	
	frmvalidator.addValidation("values[new][SORT_ORDER]","num", "Sort Order allows only numeric value");
	frmvalidator.addValidation("values[new][SORT_ORDER]","maxlen=5", "Max length for Short Order is 5");
	
	frmvalidator.addValidation("values[new][LENGTH]","num", "Length (minutes) allows only numeric value");
}

///////////////////////////////////////// Periods End //////////////////////////////////////////////////////////////

///////////////////////////////////////// Grade Levels Start //////////////////////////////////////////////////////////

function formcheck_school_setup_grade_levels()
{
		var frmvalidator  = new Validator("F1");
		
		frmvalidator.addValidation("values[new][TITLE]","alnum", "Title allows only alphanumeric value");
		frmvalidator.addValidation("values[new][TITLE]","maxlen=50", "Max length for Title is 50");
		
		frmvalidator.addValidation("values[new][SHORT_NAME]","alnum", "Short Name allows only alphanumeric value");
		frmvalidator.addValidation("values[new][SHORT_NAME]","maxlen=50", "Max length for Short Name is 50");
		
		frmvalidator.addValidation("values[new][SORT_ORDER]","num", "Sort Order allows only numeric value");
		frmvalidator.addValidation("values[new][SORT_ORDER]","maxlen=5", "Max length for Sort Order is 5");
		
}

///////////////////////////////////////// Grade Levels End ////////////////////////////////////////////////////////////

///////////////////////////////////////// Student Start ////////////////////////////////////////////////////////////

///////////////////////////////////////// Add Student Start ////////////////////////////////////////////////////////////

function formcheck_student_student()
{

  	var frmvalidator  = new Validator("student");
  	frmvalidator.addValidation("students[FIRST_NAME]","req","Please enter the First Name");
	frmvalidator.addValidation("students[FIRST_NAME]","alphabetic", "Student first name allows only alphabetic value");
  	frmvalidator.addValidation("students[FIRST_NAME]","maxlen=100", "Max length for School Name is 100");
	
	frmvalidator.addValidation("students[LAST_NAME]","req","Please enter the Last Name");
	frmvalidator.addValidation("students[LAST_NAME]","alphabetic", "Student last name allows only alphabetic value");
  	frmvalidator.addValidation("students[LAST_NAME]","maxlen=100", "Max length for Address is 100");
	
	frmvalidator.addValidation("assign_student_id","num", "Student ID allows only numeric value");



  	frmvalidator.addValidation("values[STUDENT_ENROLLMENT][new][GRADE_ID]","req","Please select a Grade");
	
	frmvalidator.addValidation("students[USERNAME]","maxlen=50", "Max length for Username is 50");
	
	frmvalidator.addValidation("students[PASSWORD]","maxlen=20", "Max length for Password is 20");
	
	frmvalidator.addValidation("students[CUSTOM_200000000]","req","Please select Gender");
  		
	frmvalidator.addValidation("students[CUSTOM_200000001]","req","Please select Ethnicity");
	
	frmvalidator.addValidation("values[STUDENT_ENROLLMENT][new][NEXT_SCHOOL]","req","Please select Rolling / Retention Options");
	
	frmvalidator.addValidation("values[ADDRESS][ADDRESS]","req","Please enter address");
	
	frmvalidator.addValidation("values[ADDRESS][CITY]","req","Please enter city");
	
	frmvalidator.addValidation("values[ADDRESS][STATE]","req","Please enter state");
		
	frmvalidator.addValidation("values[ADDRESS][ZIPCODE]","req","Please enter zipcode");	
	
	//frmvalidator.addValidation("values[ADDRESS][new][PRIM_STUDENT_RELATION]","req","Please select a Grade");
	frmvalidator.addValidation("values[ADDRESS][PRIM_STUDENT_RELATION]","req","Relation");
//	frmvalidator.addValidation("values[ADDRESS][PRIM_STUDENT_RELATION]","req","Please select a Grade");
	
	frmvalidator.addValidation("values[ADDRESS][PRI_FIRST_NAME]","req","Please enter First Name");	
	
	frmvalidator.addValidation("values[ADDRESS][PRI_LAST_NAME]","req","Please enter Last Name");	
	
	frmvalidator.addValidation("values[ADDRESS][SEC_STUDENT_RELATION]","req","Please enter Secondary Relation");
	
	frmvalidator.addValidation("values[ADDRESS][SEC_FIRST_NAME]","req","Please enter Secondary Emergency Contact Frist Name ");	
	
	frmvalidator.addValidation("values[ADDRESS][SEC_LAST_NAME]","req","Please enter  Secondary Emergency Contact Last Name");	
	
	frmvalidator.addValidation("values[STUDENTS_JOIN_PEOPLE][STUDENT_RELATION]","req","Relation");
	
	
	
//###################################Validation For Add New Contact Section######################################################	
	frmvalidator.addValidation("values[PEOPLE][FIRST_NAME]","req","Please enter First Name");		
	
	frmvalidator.addValidation("values[PEOPLE][LAST_NAME]","req","Please enter Last Name");		


//#################################################################################################################	
	// -------------------------------- Include Part Start --------------------------------------------//
	
 	frmvalidator.addValidation("values[ADDRESS][ADDRESS]","req","Please Enter Address");
	frmvalidator.addValidation("values[ADDRESS][PHONE]","ph","Please enter a valid phone number");
	
	frmvalidator.addValidation("values[PEOPLE][FIRST_NAME]","alphabetic","first name allows only alphabetic value");
	frmvalidator.addValidation("values[PEOPLE][LAST_NAME]","alpha","last name allows only alphabetic value");
	
	frmvalidator.addValidation("students[CUSTOM_200000006]","req","Please enter the Physician name");
	
	frmvalidator.addValidation("students[CUSTOM_200000007]","ph","Phone Number Should not be alphabetic.");
	
	// --------------------------------- Include Part End ---------------------------------------------//
	
	frmvalidator.setAddnlValidationFunction("ValidateDate_Student");


}


function ValidateDate_Student()
{
var bm, bd, by ;
var frm = document.forms["student"];
var elem = frm.elements;
for(var i = 0; i < elem.length; i++)
{

if(elem[i].name=="month_students[CUSTOM_200000004]")
{
bm=elem[i];
}
if(elem[i].name=="day_students[CUSTOM_200000004]")
{
bd=elem[i];
}
if(elem[i].name=="year_students[CUSTOM_200000004]")
{
by=elem[i];
}


}

try
{
if (false==CheckBirthDate(bm, bd, by))
   {
   bm.focus();
   return false;
   }
}
catch(err)
{

}

return true;

}

   


///////////////////////////////////////// Add Student End ////////////////////////////////////////////////////////////


///////////////////////////////////////// Student Field Start //////////////////////////////////////////////////////////


	function formcheck_student_studentField_F2()
	{
		var frmvalidator  = new Validator("F2");
		frmvalidator.addValidation("tables[new][TITLE]","req","Please enter the title");
		frmvalidator.addValidation("values[TITLE]","maxlen=100", "Max length for School Name is 100");
		
		frmvalidator.addValidation("tables[new][SORT_ORDER]","num", "sort order Code allows only numeric value");
	}
	
	



	function formcheck_student_studentField_F1()
	{
		var frmvalidator  = new Validator("F1");
		frmvalidator.addValidation("tables[new][TITLE]","req","Please enter the field name");
		
		
		frmvalidator.addValidation("tables[new][TYPE]","req","Please select the Data type");
		
		frmvalidator.addValidation("tables[new][SORT_ORDER]","num", "sort order allows only numeric value");
	}
	
	

///////////////////////////////////////// Student Field End ////////////////////////////////////////////////////////////

///////////////////////////////////////// Address Field Start //////////////////////////////////////////////////////////



	function formcheck_student_addressField_F2()
	{
		var frmvalidator  = new Validator("F2");
		frmvalidator.addValidation("tables[new][TITLE]","req","Please enter the title");
		frmvalidator.addValidation("values[TITLE]","maxlen=100", "Max length for School Name is 100");
		
		frmvalidator.addValidation("tables[new][SORT_ORDER]","num", "sort order Code allows only numeric value");
	}
	
	


	function formcheck_student_addressField_F1()
	{
		var frmvalidator  = new Validator("F1");
		frmvalidator.addValidation("tables[new][TITLE]","req","Please enter the field name");
		
		
		frmvalidator.addValidation("tables[new][TYPE]","req","Please select the Data type");
		
		frmvalidator.addValidation("tables[new][SORT_ORDER]","num", "sort order allows only numeric value");
	}
	
	



///////////////////////////////////////// Address Field End ////////////////////////////////////////////////////////////

///////////////////////////////////////// Contact Field Start //////////////////////////////////////////////////////////


	
	function formcheck_student_contactField_F2()
	{
		var frmvalidator  = new Validator("F2");
		frmvalidator.addValidation("tables[new][TITLE]","req","Please enter the title");
		frmvalidator.addValidation("values[TITLE]","maxlen=100", "Max length for School Name is 100");
		
		frmvalidator.addValidation("tables[new][SORT_ORDER]","num", "sort order Code allows only numeric value");
	}
	
	


	function formcheck_student_contactField_F1()
	{
		var frmvalidator  = new Validator("F1");
		frmvalidator.addValidation("tables[new][TITLE]","req","Please enter the field name");
		
		
		frmvalidator.addValidation("tables[new][TYPE]","req","Please select the Data type");
		
		frmvalidator.addValidation("tables[new][SORT_ORDER]","num", "sort order allows only numeric value");
	}
	
	


///////////////////////////////////////// Contact Field End ////////////////////////////////////////////////////////////


///////////////////////////////////////// Enrollment Code Start //////////////////////////////////////////////////////////

	
	function formcheck_student_enrollment_code_F1()
	{
		var frmvalidator  = new Validator("F1");
		frmvalidator.addValidation("values[new][TITLE]","alphanumeric", "Title allows only alphanumeric value");
		
		frmvalidator.addValidation("values[new][SHORT_NAME]","alphanumeric", "Short Name Code allows only alphanumeric value");
		
	}
	
	



///////////////////////////////////////// Enrollment Code End ////////////////////////////////////////////////////////////



///////////////////////////////////////// Student End ////////////////////////////////////////////////////////////



///////////////////////////////////////// User Start ////////////////////////////////////////////////////////////

///////////////////////////////////////// Add User Start ////////////////////////////////////////////////////////////

	function formcheck_user_user(){

  	var frmvalidator  = new Validator("staff");
  	frmvalidator.addValidation("staff[FIRST_NAME]","req","Please enter the First Name");
	frmvalidator.addValidation("staff[FIRST_NAME]","alphabetic", "First name allows only alphabetic value");
  	frmvalidator.addValidation("staff[FIRST_NAME]","maxlen=100", "Max length for First Name is 100");
	
	frmvalidator.addValidation("staff[LAST_NAME]","req","Please enter the Last Name");
	frmvalidator.addValidation("staff[LAST_NAME]","alphabetic", "Last Name allows only alphabetic value");
  	frmvalidator.addValidation("staff[LAST_NAME]","maxlen=100", "Max length for Address is 100");

	frmvalidator.addValidation("staff[PROFILE]","req","Please Select the User Profile");
	
//	frmvalidator.addValidation("staff[EMAIL]","email");
	frmvalidator.addValidation("staff[PHONE]","ph","Please enter a valid telephone nymber");
	
	}

/////////////////////////////////////////  Add User End  ////////////////////////////////////////////////////////////

/////////////////////////////////////////  User Fields Start  //////////////////////////////////////////////////////////

	function formcheck_user_userfields_F2()
	{
		var frmvalidator  = new Validator("F2");
		frmvalidator.addValidation("tables[new][TITLE]","req","Please enter the Title");
		frmvalidator.addValidation("tables[new][TITLE]","alphabetic", "Title allows only alphabetic value");
		frmvalidator.addValidation("tables[new][TITLE]","maxlen=50", "Max length for Title is 100");
	}
	
	function formcheck_user_userfields_F1()
	{
		var frmvalidator1  = new Validator("F1");
		frmvalidator1.addValidation("tables[new][TITLE]","req","Please enter the Field Name");
		frmvalidator1.addValidation("tables[new][TITLE]","alnum", "Field name allows only alphanumeric value");
		frmvalidator1.addValidation("tables[new][TITLE]","maxlen=50", "Max length for Field Name is 100");
	}

/////////////////////////////////////////  User Fields End  ////////////////////////////////////////////////////////////

/////////////////////////////////////////  User End  ////////////////////////////////////////////////////////////

//////////////////////////////////////// Scheduling start ///////////////////////////////////////////////////////

//////////////////////////////////////// Course start ///////////////////////////////////////////////////////

function formcheck_scheduling_course_F4()
{
	var frmvalidator  = new Validator("F4");
  	frmvalidator.addValidation("tables[COURSE_SUBJECTS][new][TITLE]","req","Please enter the Title");
  	frmvalidator.addValidation("tables[COURSE_SUBJECTS][new][TITLE]","maxlen=100", "Max length for Title is 100");
}

function formcheck_scheduling_course_F3()
{
	var frmvalidator  = new Validator("F3");
  	frmvalidator.addValidation("tables[COURSES][new][TITLE]","req","Please enter the Title");
  	frmvalidator.addValidation("tables[COURSES][new][TITLE]","maxlen=50", "Max length for Title is 100");
	
  	frmvalidator.addValidation("tables[COURSES][new][SHORT_NAME]","req","Please enter the Short Name");
  	frmvalidator.addValidation("tables[COURSES][new][SHORT_NAME]","maxlen=10", "Max length for Short Name is 100");
}

function formcheck_scheduling_course_F2()
{
	var frmvalidator  = new Validator("F2");
  	frmvalidator.addValidation("tables[COURSE_PERIODS][new][SHORT_NAME]","req","Please enter the Short Name");
  	frmvalidator.addValidation("tables[COURSE_PERIODS][new][SHORT_NAME]","maxlen=10", "Max length for Short Name is 100");

  	frmvalidator.addValidation("tables[COURSE_PERIODS][new][TEACHER_ID]","req","Please select the Teacher");

  	frmvalidator.addValidation("tables[COURSE_PERIODS][new][ROOM]","req","Please enter the Room");
  	frmvalidator.addValidation("tables[COURSE_PERIODS][new][ROOM]","maxlen=10", "Max length for Room is 10");
	
  	frmvalidator.addValidation("tables[COURSE_PERIODS][new][PERIOD_ID]","req","Please select the Period");
	
	frmvalidator.addValidation("tables[COURSE_PERIODS][new][TOTAL_SEATS]","req","Please input Total Seats");
	frmvalidator.addValidation("tables[COURSE_PERIODS][new][TOTAL_SEATS]","maxlen=100","Max length for Seats is 100");
	
}

///////////////////////////////////////// Course End ////////////////////////////////////////////////////////

//////////////////////////////////////// Scheduling End ///////////////////////////////////////////////////////

//////////////////////////////////////// Grade Start ///////////////////////////////////////////////////////

//////////////////////////////////////// Report Card Comment Start ///////////////////////////////////////////////////////

function formcheck_grade_comment()
{

		var frmvalidator  = new Validator("F1");
		
		frmvalidator.addValidation("values[new][SORT_ORDER]","num", "ID allows only numeric value");
		
		frmvalidator.addValidation("values[new][TITLE]","maxlen=50", "Max length for Comment is 50");
	
}

////////////////////////////////////////  Report Card Comment End  ///////////////////////////////////////////////////////


//////////////////////////////////////// Grade End ///////////////////////////////////////////////////////

///////////////////////////////////////// Eligibility Start ////////////////////////////////////////////////////

///////////////////////////////////////// Activies Start //////////////////////////////////////////////////

function formcheck_eligibility_activies()
{
	
	var frmvalidator  = new Validator("F1");
	
	frmvalidator.addValidation("values[new][TITLE]","maxlen=50", "Max length for Title is 50");
	
	frmvalidator.setAddnlValidationFunction("ValidateDate_eligibility_activies");

}


	
	function ValidateDate_eligibility_activies()
	{
		var sm, sd, sy, em, ed, ey, psm, psd, psy, pem, ped, pey ;
		var frm = document.forms["F1"];
		var elem = frm.elements;
		for(var i = 0; i < elem.length; i++)
		{
			if(elem[i].name=="month_values[new][START_DATE]")
			{
				sm=elem[i];
			}
			
			if(elem[i].name=="day_values[new][START_DATE]")
			{
				sd=elem[i];
			}
			
			if(elem[i].name=="year_values[new][START_DATE]")
			{
				sy=elem[i];
			}
			
			if(elem[i].name=="month_values[new][END_DATE]")
			{
				em=elem[i];
			}
			
			if(elem[i].name=="day_values[new][END_DATE]")
			{
				ed=elem[i];
			}
			
			if(elem[i].name=="year_values[new][END_DATE]")
			{
				ey=elem[i];
			}
		}
		
		try
		{
		   if (false==CheckDate(sm, sd, sy, em, ed, ey))
		   {
			   em.focus();
			   return false;
		   }
		}
		catch(err)
		{
		
		}

		try
		{  
		   if (false==isDate(psm, psd, psy))
		   {
			   alert("Please enter the Grade Posting Start Date");
			   psm.focus();
			   return false;
		   }
		}   
		catch(err)
		{
		
		}
		
		try
		{  
		   if (true==isDate(pem, ped, pey))
		   {
			   if (false==CheckDate(psm, psd, psy, pem, ped, pey))
			   {
				   pem.focus();
				   return false;
			   }
		   }
		}   
		catch(err)
		{
		
		}
		   
		   return true;
		
	}




///////////////////////////////////////// Activies End ////////////////////////////////////////////////////



///////////////////////////////////////// Entry Times Start ////////////////////////////////////////////////

function formcheck_eligibility_entrytimes()
{
  	var frmvalidator  = new Validator("F1");
	frmvalidator.setAddnlValidationFunction("ValidateTime_eligibility_entrytimes");
}

	function ValidateTime_eligibility_entrytimes()
	{
		var sd, sh, sm, sp, ed, eh, em, ep, psm, psd, psy, pem, ped, pey ;
		var frm = document.forms["F1"];
		var elem = frm.elements;
		for(var i = 0; i < elem.length; i++)
		{
			if(elem[i].name=="values[START_DAY]")
			{
				sd=elem[i];
			}
			if(elem[i].name=="values[START_HOUR]")
			{
				sh=elem[i];
			}
			if(elem[i].name=="values[START_MINUTE]")
			{
				sm=elem[i];
			}
			if(elem[i].name=="values[START_M]")
			{
				sp=elem[i];
			}
			if(elem[i].name=="values[END_DAY]")
			{
				ed=elem[i];
			}
			if(elem[i].name=="values[END_HOUR]")
			{
				eh=elem[i];
			}
			if(elem[i].name=="values[END_MINUTE]")
			{
				em=elem[i];
			}
			if(elem[i].name=="values[END_M]")
			{
				ep=elem[i];
			}
		}
		
		try
		{
		   if (false==CheckTime(sd, sh, sm, sp, ed, eh, em, ep))
		   {
			   sh.focus();
			   return false;
		   }
		}
		catch(err)
		{
		}
		try
		{  
		   if (true==isDate(pem, ped, pey))
		   {
			   if (false==CheckDate(psm, psd, psy, pem, ped, pey))
			   {
				   pem.focus();
				   return false;
			   }
		   }
		}   
		catch(err)
		{
		}
		
		   return true;
	}




///////////////////////////////////////// Entry Times End //////////////////////////////////////////////////


///////////////////////////////////////// Eligibility End ////////////////////////////////////////////////////

/////////////////////////////////////  Page By Page Validation End /////////////////////////////////////////////////