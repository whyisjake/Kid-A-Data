<?php

require 'vendor/autoload.php';

$session = new SpotifyWebAPI\Session(
    '',
    ''
);

$session->requestCredentialsToken();
$accessToken = $session->getAccessToken();

var_dump($accessToken);

// Store the access token somewhere. In a database for example.

// Send the user along and fetch some data!
header('Location: app.php');
die();
