<?php
/**
 * 1. 获取Authorize  code 示例
 * （1） 引导用户到小米的授权登陆页面
 *  （2）用户授权后会跳转到$redirectUri
 */
require_once('xiaomi.inc.php');

$responseType = 'code';

$redirectUri = $redirectHost."/getTokenByRefreshToken.php";

$oauthClient = new XMOAuthClient($clientId, $clientSecret );
$oauthClient->setRedirectUri($redirectUri);
$state = 'state';
$url = $oauthClient->getAuthorizeUrl($responseType, $state);

Header("HTTP/1.1 302 Found");
Header("Location: $url");

?>