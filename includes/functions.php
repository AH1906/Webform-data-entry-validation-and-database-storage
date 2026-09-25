<?php

function createTable($pdo) {
	#SQL query to create the usersTable table, the fields, data type and settings
	$createSQL = "CREATE TABLE IF NOT EXISTS usersTable (
		userID int NOT NULL AUTO_INCREMENT,
		firstName varchar(45) NOT NULL, 
		middleNames varchar(45),
		surname varchar(45) NOT NULL,
		email varchar(45) NOT NULL,
		username varchar(30) NOT NULL,
		password varchar(30) NOT NULL,
		PRIMARY KEY (userID),
		UNIQUE KEY (email),
		UNIQUE KEY (username)
		) ENGINE=InnoDB DEFAULT CHARSET=latin1;";
	
	#Execute SQL query to my database
	try {
		$pdo -> exec($createSQL);
	} catch (PDOException $e) {
		$errorCode = $e->getCode();
		$errorMessage = $e->getMessage();
		echo htmlParagraph($errorCode . " : ".  $errorMessage); #Set errors and return null
		return null;
	}
}

function clearData() {
	#Set all placeholders to empty strings
	$placeholders = ['[+firstname+]' => '',
						'[+firstnameError+]' => '',
						'[+middlename+]' => '',
						'[+middlenameError+]' => '',
						'[+surname+]' => '',
						'[+surnameError+]' => '',
						'[+email+]' => '',
						'[+emailError+]' => '',
						'[+username+]' => '',
						'[+usernameError+]' => '',
						'[+password+]' => '',
						'[+passwordError+]' => ''
						];
	
	return $placeholders;
}

function saveData($pdo) {
	#Boolean variable to ensure all validation rules are met
	#Array set to store current data being checked
	#Initialise all placeholders as empty strings
	$validData = true;
	$cleanData = array();
	$placeholders = clearData();
	$message = "";
	
	if (isset($_POST['userDataSubmitted'])) { #Checks if user pressed submit
		$formData = validateFormData($_POST); #Validate and sanitise the submitted form data
		$validData = $formData[0]; #Set boolean variable to whatever boolean is in validateFormData function
		$cleanData = $formData[1]; #Array set to current data in the form
		$placeholders = $formData[2]; #Retrieve the updated placeholders containing the current values and any validation errors
	
	
		if ($validData AND !empty($cleanData)) { #Check if data is all valid and the array is not empty
			
			if (duplicateEmail($pdo, $cleanData['email'])) { #Check for duplicate emails and usernames, expected duplicate error messages should show
				$placeholders['[+emailError+]'] = 'this email is already registered!';
				$validData = false;
			}
			
			if (duplicateUsername($pdo, $cleanData['username'])) {
				$placeholders['[+usernameError+]'] = 'another user already has this username!';
				$validData = false;
			}
			#Data is not valid if duplicates found and login cannot proceed then
			if ($validData) { #If validation passes fully, then the data is inserted to the usersTable database
				$insertSQL = "INSERT INTO usersTable(firstName, middleNames, surname, email, username, password)
						VALUES (?, ?, ?, ?, ?, ?)";
				
				$stmt = $pdo -> prepare($insertSQL);
				$stmt -> execute([ 
					$cleanData['firstname'], 
					$cleanData['middlename'],
					$cleanData['surname'],
					$cleanData['email'],
					$cleanData['username'],
					$cleanData['password']
					]);
					
				$message = htmlParagraph("New user " . $cleanData['username'] . " successfully inserted into database"); #Display a confirmation message after the new user has been added
				
			$placeholders = clearData(); #Clear the form after successful registration
		}
	}
}	
	
	return [$placeholders, $message];
}

function duplicateEmail($pdo, $email) {
	$sql = "SELECT userID FROM usersTable WHERE email = ?"; #Check whether the email address already exists in usersTable
	
	$stmt = $pdo->prepare($sql);
	$stmt->execute([$email]);
	
	return ($stmt->fetch() !== false); #If email is fetched, there is a duplicate
}

function duplicateUsername($pdo, $username) { #Same with duplicateEmail, but focuses on the username
	$sql = "SELECT userID FROM usersTable WHERE username = ?";
	
	$stmt = $pdo->prepare($sql);
	$stmt->execute([$username]);
	
	return ($stmt->fetch() !== false);
}

function validateFormData($formData) {
	#Boolean initialised to true, array is initialised and placeholders is empty
	$validData = true;
	$cleanData = array();
	$placeholders = clearData();
	
	#placeholders hold the current sanitised data inputted
	$placeholders['[+firstname+]'] = trim(htmlentities($formData['firstname']));
	$placeholders['[+middlename+]'] = trim(htmlentities($formData['middlename']));
	$placeholders['[+surname+]'] = trim(htmlentities($formData['surname']));
	$placeholders['[+email+]'] = trim(htmlentities($formData['email']));
	$placeholders['[+username+]'] = trim(htmlentities($formData['username']));
	$placeholders['[+password+]'] = trim(htmlentities($formData['password']));
	
	#All data is checked by calling each function validating each data
	#All placeholders that hold errors will update with error messages
	if (validFirstname(trim($formData['firstname']))) {
		$cleanData['firstname'] = trim($formData['firstname']);
	} else {
		$validData = false;
		$placeholders['[+firstnameError+]'] = 'Name must be alphanumeric!';
	}
	
	if (validMiddlename(trim($formData['middlename']))) {
		$cleanData['middlename'] = trim($formData['middlename']);
	} else {
		$validData = false;
	}
	
	if (validSurname(trim($formData['surname']))) {
		$cleanData['surname'] = trim($formData['surname']);
	} else {
		$validData = false;
		$placeholders['[+surnameError+]'] = 'Name must be alphanumeric!';
	}
	
	if (validEmail(trim($formData['email']))) {
		$cleanData['email'] = trim($formData['email']);
	} else {
		$validData = false;
		$placeholders['[+emailError+]'] = 'invalid email format';
	}
	
	if (validUsername(trim($formData['username']))) {
		$cleanData['username'] = trim($formData['username']);
	} else {
		$validData = false;
		$placeholders['[+usernameError+]'] = 'Less than 10 characters and/or NOT alphanumeric!';
	}
	
	if (validPassword(trim($formData['password']))) {
		$cleanData['password'] = trim($formData['password']);
	} else {
		$validData = false;
		$placeholders['[+passwordError+]'] = 'password does not satisfy requirememts. Hover mouse over field to see rules!';
	}
	
	return [$validData, $cleanData, $placeholders]; #Return the validation result, cleaned data and updated placeholders
}

function validFirstname($firstname) {
	if (ctype_alnum($firstname)) { #Checks if firstname is alphanumeric
		return true;
	} else {
		return false;
	}
}

function validMiddlename($middlename) {
	if ($middlename === "") #Middlename is optional
		return true;
	
	for ($i=0; $i<strlen($middlename); $i++) {
		$char = $middlename[$i];
		
		if (!ctype_alnum($char) AND $char !== " ") { #Allow spaces between multiple middle names
			return False;
		}
	}
	
	return true;
}

function validSurname($surname) {
	if (ctype_alnum($surname)) { #Surname must be alphanumeric
		return true;
	} else {
		return false;
	}
}

function validEmail($email) {
	if (filter_var($email, FILTER_VALIDATE_EMAIL)) { #Email must follow a specific format
		return true;
	} else {
		return false;
	}
}

function validUsername($username) {
	if (strlen($username) >= 10 AND ctype_alnum($username)) { #Username must be 10 or more characters long and is all alphanumeric
		return true;
	} else {
		return false;
	}
}

function validPassword($password) {
	#Regular expression to check uppercase, lowercase, one of the special characters are added, one digit
	return preg_match('/^(?=.*[A-Z])(?=.*[a-z])(?=.*\d)(?=.*[£\?\$%&*!#])[A-Za-z\d£\?\$%&*!#]{10,30}$/u', $password) === 1;
}

function storeParagraph($pdo) {
	#Count the total number of users stored in the database
	$sql = "SELECT COUNT(*) FROM usersTable";
		
	$stmt = $pdo->query($sql);
		
	$count = $stmt->fetchColumn(); #Retrieve the total number of stored users
		
	$stored = "";
		
	if ($count > 0) { #Display each stored user's ID, email and username
		$sql = "SELECT * FROM usersTable";
		$stmt = $pdo->query($sql);
		$dataset = $stmt->fetchAll();
		foreach($dataset as $row) {
			$stored .= htmlParagraph("ID:".$row['userID'].", Email:".$row['email'].", Username:".$row['username']."");
		}
	} else {
		$stored = htmlParagraph('No users stored in the Database');
	}
	
	return $stored;
}

function htmlParagraph($html) {
	#Return sanitised paragraph html text
	$html = htmlentities(trim($html));
	return "<p>$html</p>";
}

function htmlHeading($text, $level) {
	#Return sanitised paragraph html text for the heading level chosen
    $heading = trim(strtolower($text));
    switch ($level) {
        case 1 :
        case 2 :
            $heading = ucwords($heading);
            break;
        case 3 :
        case 4 :
        case 5 :
        case 6 :
        default: #traps unknown heading level exception
            $heading = '<FONT COLOR="#ff0000">Unknown heading level:' . $level . '</FONT>';
        }
    return '<h' . $level . '>' . htmlentities($heading) . '</h' . $level .  '>';
}
?>