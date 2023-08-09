<?php
// Initialize the session
session_start();

// Include config file
require_once "config.php";
 
$DATABASE_HOST = 'sql302.epizy.com';
$DATABASE_USER = 'epiz_30990792';
$DATABASE_PASS = 'qNW403aTFqz';
$DATABASE_NAME = 'epiz_30990792_accounts';
try {
    $pdo = new PDO('mysql:host=' . $DATABASE_HOST . ';dbname=' . $DATABASE_NAME . ';charset=utf8', $DATABASE_USER, $DATABASE_PASS);
} catch (PDOException $exception) {
    // If there is an error with the connection, stop the script and display the error
    exit('ERR01_DATABASE_CONN_ERR<br>');
}

// Define variables and initialize with empty values
$username = $password = "";
$username_err = $password_err = $login_err = $recipient_err = $message_err = "";
$message = $recipient = "";
$auth = false;
 
// Processing form data when form is submitted
if($_SERVER["REQUEST_METHOD"] == "GET"){
    
    // Check if function id is empty
    if(empty(trim($_GET["function_id"]))){
        $username_err = "ERR02_EMPTY_FUNCTION_ID<br>";
    } else{
        $username = trim($_GET["username"]);
    }

    // Check if username is empty
    if(empty(trim($_GET["username"]))){
        $username_err = "ERR03_EMPTY_UNAME<br>";
    } else{
        $username = trim($_GET["username"]);
    }
    
    // Check if password is empty
    if(empty(trim($_GET["password"]))){
        $password_err = "ERR04_EMPTY_PASS<br>";
    } else{
        $password = trim($_GET["password"]);
    }

    // Check if password is empty
    if(empty(trim($_GET["recipient"]))){
        $recipient_err = "ERR05_EMPTY_RECIPIENT<br>";
    } else{
        $recipient = trim($_GET["recipient"]);
    }

    // Check if password is empty
    if(empty(trim($_GET["message"]))){
        $message_err = "ERR06_EMPTY_MESSAGE<br>";
    } else{
        $message = trim($_GET["message"]);
    }

    // Validate credentials
    if(empty($username_err) && empty($password_err)){
        // Prepare a select statement
        $sql = "SELECT id, username, password FROM users WHERE username = ?";
        
        if($stmt = mysqli_prepare($link, $sql)){
            
            // Bind variables to the prepared statement as parameters
            mysqli_stmt_bind_param($stmt, "s", $param_username);
            
            // Set parameters
            $param_username = $_GET['username'];
            
            // Attempt to execute the prepared statement
            if(mysqli_stmt_execute($stmt)){
                // Store result
                mysqli_stmt_store_result($stmt);
                
                // Check if username exists, if yes then verify password
                if(mysqli_stmt_num_rows($stmt) == 1){                    
                    // Bind result variables
                    mysqli_stmt_bind_result($stmt, $id, $username, $hashed_password);
                    if(mysqli_stmt_fetch($stmt)){
                        
                        if($password == $hashed_password){

                            // Password is correct, so proceed with functions

                            $auth = true;

                            $_SESSION['auth'] = true;
                            $_SESSION['userfrom'] = $username;
                            $_SESSION['userto'] = $recipient;
                            $_SESSION['message'] = $message;

                             // Get Recieved Messages
                            if ($_GET['function_id'] == "zero" && $auth == true) {
                               header('Location: https://www.gfink.space/message_app/function0.php');
                            }

                            // Send Message
                            else if ($_GET['function_id'] == "one" && $auth == true) {
                                header('Location: https://www.gfink.space/message_app/function1.php');
                            }

                            // Get Sent Messages
                            else if ($_GET['function_id'] == "two" && $auth == true) {
                               header('Location: https://www.gfink.space/message_app/function2.php');
                            }
                            else {
                                $username_err = "ERR07_ID_OUT_OF_BOUNDS<br>";
                            }
                            
                        } else{
                            // Password is not valid, display a generic error message
                            $login_err = "ERR08_CREDENTIALS_REJECTED<br>";
                        }
                    }
                    else {
                        $username_err = "ERR09_SQL_ERR<br>";
                    }
                } else{
                    // Username doesn't exist, display a generic error message
                    $login_err = "ERR10_CREDENTIALS_REJECTED<br>";
                }
            } else{
                echo "ERR11_SQL_ERR<br>";
            }

            // Close statement
            mysqli_stmt_close($stmt);
        }
        else {
            echo "ERR12_SQL_ERR<br>";
        }
    }
    else {
        echo "ERR13_RESERVED_VARS_NOT_EMPTY<br>";
    }
    
    // Close connection                     
    mysqli_close($link);
}
else {
    echo "ERR14_USE_GET<br>";
}
echo $username_err;
echo $password_err;
echo $recipient_err;
echo $login_err;
echo $message_err;
?>