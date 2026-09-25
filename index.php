<?php
#Your PHP solution code should go here...
		
#User-defined functions and database configuration is included once
require_once('includes/functions.php');
require_once('includes/config.php');
		
createTable($pdo); #usersTable is created in my database if the table does not already exist
		
$result = saveData($pdo); #Validate the submitted form data and save it to usersTable if validation succeeds
$placeholders = $result[0]; #placeholders are set to their updated values for each in the html files
$message = $result[1]; #Message to let the user know they have logged in successfully

#Form template will have all placeholders be replace by the updated values
$template = file_get_contents('html/userDataForm.html');
	
$form = str_replace(array_keys($placeholders), array_values($placeholders), $template);
	
$stored = storeParagraph($pdo); #Below the form will store all the users details, showing their data is saved after logged in

$content = $message . htmlHeading('Users stored in the Database', 2) . $stored; #Build the page content containing the success message, heading and stored users

#Page template will replace the placeholders with the main form and content
$template = file_get_contents('html/template.html');
echo str_replace(['[+form+]', '[+content+]'], [$form, $content] , $template);
?>