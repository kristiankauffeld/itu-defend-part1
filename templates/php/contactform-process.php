<?php
$errorMSG = "";

if (empty($_POST["name"])) {
    $errorMSG = "Name is required ";
} else {
    $name = $_POST["name"];
    $escaped_name = escapeshellcmd($name);
}

if (empty($_POST["email"])) {
    $errorMSG = "Email is required ";
} else {
    $email = $_POST["email"];
    $escaped_email = escapeshellcmd($email);
}

if (empty($_POST["message"])) {
    $errorMSG = "Message is required ";
} else {
    $message = $_POST["message"];
    $escaped_message = escapeshellcmd($message);
}

if (empty($_POST["terms"])) {
    $errorMSG = "Terms is required ";
} else {
    $terms = $_POST["terms"];
    $escaped_terms = escapeshellcmd($terms);
}

$EmailTo = "yourname@domain.com";
$Subject = "New message from Aria landing page";

// prepare email body text
$Body = "";
$Body .= "Name: ";
$Body .= $escaped_name;
$Body .= "\n";
$Body .= "Email: ";
$Body .= $escaped_email;
$Body .= "\n";
$Body .= "Message: ";
$Body .= $escaped_message;
$Body .= "\n";
$Body .= "Terms: ";
$Body .= $escaped_terms;
$Body .= "\n";

// send email
$success = mail($EmailTo, $Subject, $Body, "From:".$escaped_email);

// redirect to success page
if ($success && $errorMSG == ""){
   echo "success";
}else{
    if($errorMSG == ""){
        echo "Something went wrong :(";
    } else {
        echo $errorMSG;
    }
}
?>