
<?php
// Get data from form 
$name = $_POST['name'];
$number= $_POST['number'];
$subject= $_POST['subject'];
$message= $_POST['message'];
 
$to = "harisaran693@gmail.com";
$subject = "Customer Contact Detail";
 
// The following text will be sent
// Name = user entered name
// Email = user entered email
// Message = user entered message
$txt ="Name = ". $name . "Mobile Number = ". $number . "\r\n  Subject = "
    . $subject . "\r\n Message =" . $message;
 
$headers = "From: noreply@demosite.com" . "\r\n" .
            "CC: haridesigner198@gmail.com";
if($email != NULL) {
    mail($to, $subject, $txt, $headers);
}
 
// Redirect to
header("Location:last.html");
?>