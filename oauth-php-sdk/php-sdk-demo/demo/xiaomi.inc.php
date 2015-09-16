<?php

require_once('../../php-sdk/utils/XMUtil.php');
require_once('../../php-sdk/utils/AccessToken.php');
require_once('../../php-sdk/httpclient/XMHttpClient.php');
require_once('../../php-sdk/httpclient/XMOAuthClient.php');
require_once('../../php-sdk/httpclient/XMApiClient.php');

$clientId = 3;
$clientSecret = 'key3';
$redirectHost = 'http://localhost:8080';

// api
$userProfilePath = '/user/profile'
?>