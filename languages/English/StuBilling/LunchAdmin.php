<?php
// UPDATE AND ADD ITEMS TO THE LUNCH MENU
if($_REQUEST['modfunc']=='update')
{
	DBQuery("UPDATE LUNCH_CONFIG SET NEGATIVE_BALANCE='".str_replace('$','',$_REQUEST[negative_balance])."',WARNING_BALANCE='".str_replace('$','',$_REQUEST[warning_balance])."' WHERE SCHOOL='".UserSchool()."'");

	if(count($_REQUEST[title]))
	{
		foreach($_REQUEST[title] as $id=>$title)
			DBQuery("UPDATE LUNCH_MENU SET TITLE='$title' WHERE ID='$id'");
	}
	if(count($_REQUEST[price]))
	{
		foreach($_REQUEST[price] as $id=>$price)
		{
			$price = str_replace('$','',$price);
			DBQuery("UPDATE LUNCH_MENU SET PRICE='$price' WHERE ID='$id'");
		}
	}
	if(count($_REQUEST[fprice]))
	{
		foreach($_REQUEST[fprice] as $id=>$price)
		{
			$price = str_replace('$','',$price);
			DBQuery("UPDATE LUNCH_MENU SET FREE_PRICE='$price' WHERE ID='$id'");
		}
	}
	if(count($_REQUEST[rprice]))
	{
		foreach($_REQUEST[rprice] as $id=>$price)
		{
			$price = str_replace('$','',$price);
			DBQuery("UPDATE LUNCH_MENU SET REDUCED_PRICE='$price' WHERE ID='$id'");
		}
	}	
	
	if(count($_REQUEST[button]))
	{
		foreach($_REQUEST[button] as $id=>$button)
		{
			DBQuery("UPDATE LUNCH_MENU SET BUTTON='$button' WHERE ID='$id'");
		}
	}
	
	if(count($_REQUEST[new_title]))
	{
		foreach($_REQUEST[new_title] as $category_id=>$title)
		{
			if($title)
			{
				$price = str_replace('$','',$_REQUEST[new_price][$category_id]);
				$fprice = str_replace('$','',$_REQUEST[new_fprice][$category_id]);
				$rprice = str_replace('$','',$_REQUEST[new_rprice][$category_id]);

				DBQuery("INSERT INTO LUNCH_MENU (SCHOOL,ID,TITLE,PRICE,FREE_PRICE,REDUCED_PRICE,CATEGORY_ID,BUTTON) values('".UserSchool()."',".db_seq_nextval('LUNCH_MENU_SEQ').",'$title','$price','$fprice','$rprice','$category_id','".$_REQUEST[new_button][$category_id]."')");
				unset($_REQUEST[new_price][$category_id]);
			}
		}
	}
	
	if($_REQUEST[category_title])
	{
		foreach($_REQUEST[category_title] as $category_id=>$title)
			DBQuery("UPDATE LUNCH_CATEGORIES SET TITLE='$title' WHERE CATEGORY_ID='$category_id'");
	}
	
	if($_REQUEST[new_category_title] && $_REQUEST[new_category_title]!='New Category')
		DBQuery("INSERT INTO LUNCH_CATEGORIES (SCHOOL,CATEGORY_ID,TITLE) values('".UserSchool()."',".db_seq_nextval('LUNCH_CATEGORY_SEQ').",'$_REQUEST[new_category_title]')");	
	
	unset($_REQUEST['modfunc']);
}

// DELETE AN ITEM
if($_REQUEST['modfunc']=='delete')
{
	if(DeletePrompt('lunch item'))
	{
		if($_REQUEST[id])
			DBQuery("DELETE FROM LUNCH_MENU WHERE ID='$_REQUEST[id]'");
		unset($_REQUEST['modfunc']);
	}
}

if($_REQUEST['modfunc']=='delete_category')
{
	if(DeletePrompt('category'))
	{
		DBQuery("DELETE FROM LUNCH_CATEGORIES WHERE CATEGORY_ID='$_REQUEST[id]'");
		DBQuery("DELETE FROM LUNCH_MENU WHERE CATEGORY_ID='$_REQUEST[id]'");
		unset($_REQUEST['modfunc']);
	}
}


// DISPLAY THE LUNCH MENU
if(!isset($_REQUEST['modfunc']))
{
	echo "<FORM action=Modules.php?modname=$_REQUEST[modname]&modfunc=update METHOD=POST>";
	DrawHeader(ProgramTitle());
	DrawHeader('Click on any title or price to modify the menu.',SubmitButton('Save','','class=btn_medium'));

	$QI = DBQuery("SELECT NEGATIVE_BALANCE,WARNING_BALANCE FROM LUNCH_CONFIG WHERE SCHOOL='".UserSchool()."'");
	$config = DBGet($QI);
	$config = $config[1];

	echo '<CENTER><TABLE><TR><TD><b><small>Lowest Allowed Balance</small></B></TD><TD><INPUT type=text name=negative_balance value='.Currency($config[NEGATIVE_BALANCE]).' size=8> &nbsp; &nbsp; </TD><TD><b><small>Warning Balance</small></B></TD><TD><INPUT type=text name=warning_balance value='.Currency($config[WARNING_BALANCE]).' size=8></TD></TR></TABLE><BR>';

	echo '<TABLE><TR><TD align=center>';
	$QI = DBQuery("SELECT CATEGORY_ID,TITLE FROM LUNCH_CATEGORIES WHERE SCHOOL='".UserSchool()."' ORDER BY CATEGORY_ID");
	$categories_RET = DBGet($QI,array(),array('CATEGORY_ID'));
	
	$QI = DBQuery("SELECT ID,CATEGORY_ID,TITLE,PRICE,FREE_PRICE,REDUCED_PRICE,BUTTON FROM LUNCH_MENU WHERE SCHOOL='".UserSchool()."' ORDER BY CATEGORY_ID");
	$foods_RET = DBGet($QI,array('TITLE'=>'makeTitleInput','PRICE'=>'makePriceInput','FREE_PRICE'=>'makeFPriceInput','REDUCED_PRICE'=>'makeRPriceInput','BUTTON'=>'makeButtonInput'),array('CATEGORY_ID'));

	if(count($foods_RET))
	{
		echo '<TABLE border=0><TR>';
		foreach($foods_RET as $category_id => $food)
		{
			echo '<TD valign=top>';
			echo '<center><table><tr><td>'.
					"<A HREF=Modules.php?modname=$_REQUEST[modname]&modfunc=delete_category&id=$category_id&user_school=".UserSchool().">
						<IMG SRC=assets/remove_button.gif></A></td>
						<td><b>".makeCategoryInput($categories_RET[$category_id][1]['TITLE'],$category_id).'</b></td></tr></table></center>';
			$link['remove']['link'] = "Modules.php?modname=$_REQUEST[modname]&modfunc=delete&user_school=".UserSchool();
			$link['remove']['variables'] = array('id'=>'ID');
			$link['add']['html'] = array('TITLE'=>makeTitleInput('',$category_id),'PRICE'=>makePriceInput('',$category_id),'FREE_PRICE'=>makeFPriceInput('',$category_id),'REDUCED_PRICE'=>makeRPriceInput('',$category_id),'BUTTON'=>makeButtonInput('',$category_id));
			ListOutput($food,array('TITLE'=>'Name','BUTTON'=>'Key','PRICE'=>'Price','REDUCED_PRICE'=>'Reduced Price','FREE_PRICE'=>'Free Price'),'','',$link,array(),array('save'=>false));
			echo '</TD>';
		}
	}

	if(count($categories_RET))
	{
		foreach($categories_RET as $category_id=>$value)
		{
			if(!count($foods_RET[$category_id]))
			{
				echo '<TD valign=top>';
				echo '<center><table><tr><td>'.
						"<A HREF=Modules.php?modname=$_REQUEST[modname]&modfunc=delete_category&id=$category_id&user_school=".UserSchool().">
							<IMG SRC=assets/remove_button.gif></A></td>
							<td><b>".makeCategoryInput($categories_RET[$category_id][1]['TITLE'],$category_id).'</b></td></tr></table></center>';
				$link[add][html] = array('TITLE'=>makeTitleInput('',$category_id),'PRICE'=>makePriceInput('',$category_id),'FREE_PRICE'=>makeFPriceInput('',$category_id),'REDUCED_PRICE'=>makeRPriceInput('',$category_id),'BUTTON'=>makeButtonInput('',$category_id));
				ListOutput(array(),array('TITLE'=>'Name','BUTTON'=>'Key','PRICE'=>'Price','REDUCED_PRICE'=>'Reduced Price','FREE_PRICE'=>'Free Price'),'','',$link,array(),array('save'=>false));
				echo '</TD>';
			}	
		}
	}

	echo '<TD valign=top>';
	echo '<table><TR><TD><IMG SRC=assets/add_button.gif></TD><TD><INPUT type=text size=20 name=new_category_title value="New Category"></TD></TR></TABLE>';
	echo '</TD>';

	echo '</TR>';
	echo '<TR><TD colspan='.(count($categories_RET)*2 + 1).' align=center>';
	echo '<INPUT type=submit value="Save the Menu">';
	echo '</TD></TR>';
	echo '</TABLE>';
	echo '</FORM>';

	echo "</TD></TR></TABLE></CENTER>";
}


function makeCategoryInput($value,$category='')
{
	return "<DIV id='c$category'><div onclick='javascript:addHTML(\"<INPUT type=text name=category_title[$category] value=\\\"$value\\\" size=10>\",\"c$category\",true)'>$value</div></DIV>";
}

function makeTitleInput($value='',$category='')
{	global $THIS_RET;

	if($value!='')
		return "<DIV id='t$THIS_RET[ID]'><div onclick='javascript:addHTML(\"<INPUT type=text name=title[$THIS_RET[ID]] value=\\\"$value\\\" size=10>\",\"t$THIS_RET[ID]\",true)'>$value</div></DIV>";
	elseif($THIS_RET[PRICE])
		return "<INPUT TYPE=TEXT NAME=title[$THIS_RET[ID]] size=10>";
	else
		return "<INPUT TYPE=TEXT NAME=new_title[$category] size=10>";
		//return "<INPUT TYPE=TEXT NAME=title[$THIS_RET[ID]] VALUE=$value size=10>";
}

function makePriceInput($value='',$category='')
{	global $THIS_RET;
	
	if($value!='')
	{
		$value = Currency($value);
		return "<DIV id='p$THIS_RET[ID]'><div onclick='javascript:addHTML(\"<INPUT type=text name=price[$THIS_RET[ID]] value=$value size=5>\",\"p$THIS_RET[ID]\",true)'>$value</div></DIV>";
	}
	elseif($THIS_RET[TITLE])
	{
		return "<INPUT TYPE=TEXT NAME=price[$THIS_RET[ID]] size=5>";	
	}
	else
		return "<INPUT TYPE=TEXT NAME=new_price[$category] size=5>";	
		//return "<INPUT TYPE=TEXT NAME=price[$THIS_RET[ID]] VALUE=$value size=5>";
}	

function makeFPriceInput($value='',$category='')
{	global $THIS_RET;
	
	if($value!='')
	{
		$value = Currency($value);
		return "<DIV id='fp$THIS_RET[ID]'><div onclick='javascript:addHTML(\"<INPUT type=text name=fprice[$THIS_RET[ID]] value=$value size=5>\",\"fp$THIS_RET[ID]\",true)'>$value</div></DIV>";
	}
	elseif($THIS_RET[TITLE])
	{
		return "<INPUT TYPE=TEXT NAME=fprice[$THIS_RET[ID]] size=5>";	
	}
	else
		return "<INPUT TYPE=TEXT NAME=new_fprice[$category] size=5>";	
		//return "<INPUT TYPE=TEXT NAME=fprice[$THIS_RET[ID]] VALUE=$value size=5>";
}

function makeRPriceInput($value='',$category='')
{	global $THIS_RET;
	
	if($value!='')
	{
		$value = Currency($value);
		return "<DIV id='rp$THIS_RET[ID]'><div onclick='javascript:addHTML(\"<INPUT type=text name=rprice[$THIS_RET[ID]] value=$value size=5>\",\"rp$THIS_RET[ID]\",true)'>$value</div></DIV>";
	}
	elseif($THIS_RET[TITLE])
	{
		return "<INPUT TYPE=TEXT NAME=rprice[$THIS_RET[ID]] size=5>";	
	}
	else
		return "<INPUT TYPE=TEXT NAME=new_rprice[$category] size=5>";	
		//return "<INPUT TYPE=TEXT NAME=rprice[$THIS_RET[ID]] VALUE=$value size=5>";
}

function makeButtonInput($value='',$category='')
{	global $THIS_RET;
	
	if($value!='')
		return "<DIV id='b$THIS_RET[ID]'><div onclick='javascript:addHTML(\"<INPUT type=text name=button[$THIS_RET[ID]] value=$value size=5>\",\"b$THIS_RET[ID]\",true)'>$value</div></DIV>";
	elseif($THIS_RET[TITLE])
		return "<INPUT TYPE=TEXT NAME=button[$THIS_RET[ID]] size=5>";	
	else
		return "<INPUT TYPE=TEXT NAME=new_button[$category] size=5>";	
		//return "<INPUT TYPE=TEXT NAME=price[$THIS_RET[ID]] VALUE=$value size=5>";
}
	
?>