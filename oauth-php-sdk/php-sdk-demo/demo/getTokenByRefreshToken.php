<?php
/**
 *   获取access token 示例
 * （1） 引导用户到小米的授权登陆页面
 *  （2）用户授权后会跳转到$redirectUri
 */

require_once('xiaomi.inc.php');

$code = $_GET["code"];

$redirectUri = $redirectHost.'/getToken.php';

if($code) {
    $oauthClient = new XMOAuthClient($clientId, $clientSecret );
    $oauthClient->setRedirectUri($redirectUri);
    $token = $oauthClient->getAccessTokenByAuthorizationCode($code);
    if($token) {
        // 如果有错误，可以获取错误号码和错误描述
        if  ($token->isError()) {
            $errorNo = $token->getError();
            $errordes = $token->getErrorDescription();
            print "error no : ".$errorNo. "   error description : ".$errordes."<br>";
        } else {
            $tokenId = $token->getAccessTokenId();
             print "第一次拿到的token : " . $tokenId ."<br>";
             // 拿到refresh token 
            $refreshToken = $token->getRefreshToken();
            // 用refresh token去刷新token (refresh token只能使用一次)
            $token = $oauthClient->getAccessTokenByRefreshToken($refreshToken);
            print "Refresh token 拿到的token : " . $tokenId ."<br>";
        }
    }else {
        print "Get token Error";
    }
} else {
    print "Get code error : ".  $_GET["error"]. "  error description : ".  $_GET["error_description"];
}
?>