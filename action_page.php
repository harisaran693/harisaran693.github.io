<?php

ini_set("session.cookie_httponly", "True");
ini_set("session.cookie_secure", "True");

################################################################
#                                                              #
# PHP Script to Send Push Notifications From Pushbullet        #
# Author: Santhosh Veer (https://santhoshveer.com)             #
#                                                              #
################################################################

session_start();

// Load phpdotenv - https://github.com/vlucas/phpdotenv
require_once dirname(__FILE__) . '/vendor/autoload.php';
$dotenv = new Dotenv\Dotenv(__DIR__);
$dotenv->load();

// Get APP KEY and APP ID From .env File
$APIURL = getenv('APIURL');
$APIKEY = getenv('APIKEY');

function getUserIpAddr()
{
    if (!empty($_SERVER['HTTP_CLIENT_IP']))
    {
        //ip from share internet
        $ip = $_SERVER['HTTP_CLIENT_IP'];
    }
    else if (!empty($_SERVER['HTTP_X_FORWARDED_FOR']))
    {
        //ip pass from proxy
        $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
    }
    else
    {
        $ip = $_SERVER['REMOTE_ADDR'];
    }
    return $ip;
}

$userip = getUserIpAddr();

if ($_SERVER["REQUEST_METHOD"] == "POST")
{
    if (isset($_SESSION['token']) && $_POST['token'] == $_SESSION['token'])
    {

        $name = $_POST["name"];
        $email = $_POST["email"];
        $body = $_POST["body"];

        $name = htmlspecialchars($name, ENT_COMPAT);
        $email = htmlspecialchars($email, ENT_COMPAT);
        $body = htmlspecialchars($body, ENT_COMPAT);

        $data = ["title" => "Message From $name ($email - $userip)", "body" => "$body", "type" => "note"];

        $data_string = json_encode($data);

        $url = $APIURL;

        $headers = ["Access-Token: " . $APIKEY, "Content-Type: application/json; charset=utf-8"];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);

        $result = curl_exec($ch);
        $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        curl_close($ch);

        switch ($code)
        {
            case "200":
                echo "<div class='alert alert-success text-center'><strong>your message was submitted successfully</strong></div>";
            break;
            case "400":
                echo "<div class='alert alert-warning text-center'><strong>Bad Request - missing a required parameter</strong></div>";
            break;
            case "401":
                echo "<div class='alert alert-warning text-center'><strong>No valid access token provided</strong></div>";
            break;
            case "403":
                echo "<div class='alert alert-warning text-center'><strong>The access token is not valid</strong></div>";
            break;
            case "404":
                echo "<div class='alert alert-warning text-center'><strong>API URL Not Found</strong></div>";
            break;
            default:
                echo "<div class='alert alert-warning text-center'><strong>Hmm Something Went Wrong or HTTP Status Code is Missing</strong></div>";
        }

    }
}

//Generate Random Tokens
$token = $_SESSION['token'] = bin2hex(random_bytes(32));

?>
