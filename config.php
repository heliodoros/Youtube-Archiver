<?php
ini_set('max_execution_time', 300); //300 seconds = 5 minutes
require_once __DIR__ . '/vendor/autoload.php';
session_start();
if (!isset($_SESSION['user_name']) && $thisPage !== 'index') { //if login in session is not set
  header("Location: /");
}
$OAUTH2_CLIENT_ID = ''; //obtained from Google API Console https://console.developers.google.com/
$OAUTH2_CLIENT_SECRET = ''; //obtained from Google API Console https://console.developers.google.com/
$client = new Google_Client();
$youtube = new Google_Service_YouTube($client);
$client->setClientId($OAUTH2_CLIENT_ID);
$client->setClientSecret($OAUTH2_CLIENT_SECRET);
$client->setScopes(array(
  "https://www.googleapis.com/auth/youtube",
  "https://www.googleapis.com/auth/youtube.force-ssl",
  "https://www.googleapis.com/auth/youtube.readonly",
  "https://www.googleapis.com/auth/youtubepartner",
  "https://www.googleapis.com/auth/youtubepartner-channel-audit",
  "https://www.googleapis.com/auth/yt-analytics-monetary.readonly",
  "https://www.googleapis.com/auth/yt-analytics.readonly"
));
$client->setAccessType('offline');
$client->setIncludeGrantedScopes(true);
mysqli_report(MYSQLI_REPORT_STRICT | MYSQLI_REPORT_ALL);
$redirect = filter_var(
  'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF'],
  FILTER_SANITIZE_URL
);
$client->setRedirectUri($redirect);
$tokenSessionKey = 'token-' . $client->prepareScopes();
if (isset($_GET['code']) && $thisPage == 'index') {
  $token = $client->fetchAccessTokenWithAuthCode($_GET['code']);
  $_SESSION['googletoken'] = $token;
  $client->setAccessToken($token['access_token']);
  // get profile info
  $google_oauth = new Google_Service_Oauth2($client);
  $google_account_info = $google_oauth->userinfo->get();
  $email =  $google_account_info->email;
  $name =  $google_account_info->name;
  $_SESSION['user_name'] = $name;
  $_SESSION[$tokenSessionKey] = $client->getAccessToken();
}
