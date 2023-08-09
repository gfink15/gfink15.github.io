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
if ($_SESSION['auth'] == true) {
    function show_comments($comments) {
        $html = '';
        // If the comments are replies sort them by the "submit_date" column
        array_multisort(array_column($comments, 'time_stamp'), SORT_ASC, $comments);
        // Iterate the comments using the foreach loop
        foreach ($comments as $comment) {
            // Add the comment to the $html variable
            $html .= '
                    -------------------------------------\n
                    From: ' . $comment['user_from'] . '\n
                    To: ' . $comment['user_to'] . '\n
                    At ' . $comment['time_stamp'] . '\n
                    Begin Message: ' . nl2br(htmlspecialchars($comment['msg_content'], ENT_QUOTES)) . ' :End Message\n
                
            ';
        }
        return $html;
    }
    
// Page ID needs to exist, this is used to determine which comments are for which page
    
    // Get all comments by the Page ID ordered by the submit date
    $stmt = $pdo->prepare('SELECT * FROM messages WHERE user_to = ? ORDER BY time_stamp DESC');
    $stmt->execute([ $_SESSION['userfrom'] ]);
    $commentss = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
}
?>
<html>
<body>
Recieved Comments:\n
<?=show_comments($commentss);?>
-------------------------------------\n
</body>
</html>