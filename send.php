<?php
// Error reporting: log errors to a file instead of displaying them
ini_set('log_errors', 1);
ini_set('error_log', 'php_errors.log');
error_reporting(E_ALL);
ini_set('display_errors', 0);

// This script sends an email to your email address from the contact form. Change the variable below to your own email address:
$my_email = 'my_lovely_horse@domain.co.uk';

// Function to validate email address
function validateEmail($email){
    if (!preg_match("#^[A-Za-z0-9._-]+@[a-z0-9._-]{2,}\.[a-z]{2,4}$#", $email)) {
        return false;
    } else {
        return true;
    }
}

if(validateEmail($_POST['email'])){
    // Sanitize data
    $first_name = htmlspecialchars($_POST['first_name']);
    $last_name = htmlspecialchars($_POST['last_name']);
    $email = $_POST['email'];
    $url = htmlspecialchars($_POST['website']);
    $message = htmlspecialchars($_POST['message']);
    
    $phone = empty($_POST['phone']) ? '--None--' : htmlspecialchars($_POST['phone']);

    // Check if images are uploaded
    $image_urls = [];
    if(isset($_FILES['images']) && !empty($_FILES['images']['name'][0])) {
        foreach($_FILES['images']['tmp_name'] as $key => $tmp_name) {
            $file_tmp_name = $_FILES['images']['tmp_name'][$key];
            $file_name = $_FILES['images']['name'][$key];
            
            // Process each uploaded image as needed
            $upload_dir = 'uploads/';
            if (!file_exists($upload_dir)) {
                mkdir($upload_dir, 0755, true);
            }
            $target_file = $upload_dir . basename($file_name);

            if(move_uploaded_file($file_tmp_name, $target_file)) {
                $image_urls[] = $target_file;
            } else {
                $image_urls[] = "Error uploading $file_name";
            }
        }
    } else {
        $image_urls[] = "No images uploaded.";
    }

    $content = "<p>Hey hey,</p>
        <p>You have received an email from $first_name via the website's 'Contact Us' form. Here's the message:</p>
        <p>$message</p>
        <p>
            From: $first_name $last_name
            <br />
            Phone: $phone
            <br />
            Email: $email
            <br />
            Website: $url
            <br />
            Images: " . implode(', ', $image_urls) . "
        </p>";

    $try = mail($my_email, "$last_name has emailed via the website", $content, "Content-Type: text/html;");

    if(!$try){
        $result = 'error';
        $result = 'There was an error when trying to send your email. Please try again.';
    } else {
        $result = 'success';
        $result = "Thank you $first_name. We will reply to you at <em>$email</em> or via your phone number on <em>$phone</em>";
    }
} else {
    $result = 'error';
    $result = 'There was an error with the email address you entered. Please try again.';
}

echo json_encode($result);
?>
