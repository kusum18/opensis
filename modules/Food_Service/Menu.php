<?php
$menu['Food_Service']['admin'] = array(
						'Food_Service/Accounts.php'=>'Accounts',
						'Food_Service/Statements.php'=>'Statements',
						'Food_Service/Transactions.php'=>'Transactions',
						'Food_Service/ServeMenus.php'=>'Serve Meals',
						1=>'Reports',
						'Food_Service/ActivityReport.php'=>'Activity Report',
						'Food_Service/TransactionsReport.php'=>'Transactions Report',
						'Food_Service/MenuReports.php'=>'Meal Reports',
						'Food_Service/Reminders.php'=>'Reminders',
						2=>'Setup',
						'Food_Service/DailyMenus.php'=>'Daily Menus',
						'Food_Service/MenuItems.php'=>'Meal Items',
						'Food_Service/Menus.php'=>'Meals',
						'Food_Service/Kiosk.php'=>'Kiosk Preview'
//						3=>'Utilities',
//						'Food_Service/AssignSchool.php'=>'Assign School'
					);

$menu['Food_Service']['teacher'] = array(
						'Food_Service/Accounts.php'=>'Accounts',
						'Food_Service/Statements.php'=>'Statements',
						1=>'Setup',
						'Food_Service/DailyMenus.php'=>'Daily Menus',
						'Food_Service/MenuItems.php'=>'Meal Items'
					);

$menu['Food_Service']['parent'] = array(
						'Food_Service/Accounts.php'=>'Accounts',
						'Food_Service/Statements.php'=>'Statements',
						1=>'Setup',
						'Food_Service/DailyMenus.php'=>'Daily Menus',
						'Food_Service/MenuItems.php'=>'Meal Items'
					);

$exceptions['Food_Service'] = array(
						'Food_Service/ServeMenus.php'=>true
					);
?>
