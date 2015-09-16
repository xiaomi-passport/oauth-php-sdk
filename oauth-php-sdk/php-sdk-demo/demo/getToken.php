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
            // mac access type
            //  token有较长的有效期，可以存储下来，不必每次去获取token
            var_dump($token);
            // 拿到token id
            $tokenId = $token->getAccessTokenId();

            // 创建api client
            $xmApiClient = new XMApiClient($clientId, $tokenId);
             
            // 获取nonce  随机数:分钟
            $nonce = XMUtil::getNonce();

            $path = $userProfilePath;
            $method = "GET";
            $params = array('token' => $tokenId, "clientId" => $clientId);
             
            // 计算签名
            $sign = XMUtil::buildSignature($nonce, $method,  $xmApiClient->getApiHost(), $path, $params, $token->getMacKey());

            // 构建header
            $head =XMUtil::buildMacRequestHead($tokenId, $nonce, $sign);
            // 访问api
            $result = $xmApiClient->callApi($userProfilePath, $params, false, $head);
            // 返回json
            print '<br><br>';
            var_dump($result);
            print '<br><br>';
            $result = $xmApiClient->callApiSelfSign($userProfilePath, array(), $token->getMacKey());
            // 返回json
            var_dump($result);
        }
    }else {
        print "Get token Error";
    }
} else {
    print "Get code error : ".  $_GET["error"]. "  error description : ".  $_GET["error_description"];
}
?>
