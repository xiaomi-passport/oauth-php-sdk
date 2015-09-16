<?php

/***************************************************************************
 *
 * Copyright (c) 2013 xiaomi.com, Inc. All Rights Reserved
 *
 **************************************************************************/

require_once('../../php-sdk/httpclient/XMHttpClient.php');
require_once('../../php-sdk/httpclient/XMOAuthClient.php');
require_once('../../php-sdk/httpclient/XMApiClient.php');
require_once('../../php-sdk/utils/XMUtil.php');
require_once('../../php-sdk/utils/AccessToken.php');

function assertTrue($result) {
    if(!$result) {
        throw new Exception($result);
    }
}

function assertFalse($result) {
    assertTrue(!$result) ;
}

function assertEquals($left, $right) {
    if($left !== $right) {
        throw new Exception($right . "not equals ". $left);
    }
}

function assertNull($left) {
    if($left !== null) {
        throw new Exception($left . "not null");
    }
}

//////////////////////////////////////////////
// AccessToken test
/////////////////////////////////////////////
function accessTokenTest() {
    // error test
    $errorToken = array('error' => 910654, 'error_description'=>'错误测试，error test');
    $accessToken = new AccessToken($errorToken);
    assertTrue($accessToken->isError());
    assertEquals($accessToken->getError(),910654);
    assertEquals($accessToken->getErrorDescription(),'错误测试，error test');

    // mac type
    $errorToken = array('token_type' => 'mac', 'access_token'=>'access_token_id_content', 'mac'=>'Zeaqerwqer','mac_key'=> 'testkey', 'scope'=>'1 3');
    $accessToken = new AccessToken($errorToken);
    assertTrue($accessToken->isMacType());
    assertFalse($accessToken->isBearerType());
    assertFalse($accessToken->isError());
    assertEquals($accessToken->getAccessTokenId(),'access_token_id_content');
    assertEquals($accessToken->getMac(),'Zeaqerwqer');
    assertEquals($accessToken->getMacKey(),'testkey');
    assertEquals($accessToken->getScope(),'1 3');
    assertNull($accessToken->getRefreshToken());
    assertNull($accessToken->getExpiresIn());

    // bearer type
    $errorToken = array('token_type' => 'bearer', 'access_token'=>'access_token_id_content', 'refresh_token'=>'refresh_token_test', 'scope'=>'1 3', 'expires_in'=>146454784);
    $accessToken = new AccessToken($errorToken);
    assertFalse($accessToken->isMacType());
    assertTrue($accessToken->isBearerType());
    assertFalse($accessToken->isError());
    assertEquals($accessToken->getAccessTokenId(),'access_token_id_content');
    assertNull($accessToken->getMac(),'Zeaqerwqer');
    assertNull($accessToken->getMacKey(),'testkey');
    assertEquals($accessToken->getScope(),'1 3');
    assertEquals($accessToken->getRefreshToken(), "refresh_token_test");
    assertEquals($accessToken->getExpiresIn(), 146454784);
}

//////////////////////////////////////////////
// XMHttpClient
/////////////////////////////////////////////
function testXMHttpClient () {
    // error url test
    $isPass = false;
    try{
        $xmHttpClient = new XMHttpClient("");
    } catch(Exception $e) {
        $isPass =true;
    }
    assertTrue($isPass);

    // test http url
    $isPass = true;
    try{
        $xmHttpClient = new XMHttpClient('http://xiaomi.com');
    } catch(Exception $e) {
        $isPass =false;
    }
    assertTrue($isPass);

    // test https url

    $isPass = true;
    try{
        $xmHttpClient = new XMHttpClient('https://xiaomi.com');
    } catch(Exception $e) {
        $isPass =false;
    }
    assertTrue($isPass);

    // test get
    $isPass = true;
    try{
        $xmHttpClient = new XMHttpClient('http://xiaomi.com');
        $result = $xmHttpClient->get("");
        assertTrue($result['succ']);
    } catch(Exception $e) {
        $isPass =false;
    }
    assertTrue($isPass);

    // test post
    $isPass = true;
    try{
        $xmHttpClient = new XMHttpClient('http://xiaomi.com');
        $result = $xmHttpClient->post("",  array());
        assertTrue($result['succ']);
    } catch(Exception $e) {
        $isPass =false;
    }
    assertTrue($isPass);

    // test error get
    $isPass = true;
    try{
        $xmHttpClient = new XMHttpClient('http://1212xiaomi.com');
        $result = $xmHttpClient->get("");
        assertFalse($result['succ']);
        assertEquals($result['errno'], 6);
        assertEquals($result['errmsg'], "Couldn't resolve host '1212xiaomi.com'");
    } catch(Exception $e) {
        $isPass =false;
    }
    assertTrue($isPass);

    ////////////////////

}
function  testXMOAuthHttpClient() {
    $code = "5D4E65FB831C4C6144F311C997618639";
    $clientId = 179887661252608;
    $clientSecret = 'KIV/4Ittm17a4pIvzNM2wA==';
    $redirectUri = 'http://xiaomi.com';

    $oauthClient = new XMOAuthClient($clientId, $clientSecret );
    $oauthClient->setRedirectUri($redirectUri);

    $token = $oauthClient->getAccessTokenByAuthorizationCode($code);
    assertTrue($token->isError());
    assertEquals($token->getError(),96013);
    assertEquals($token->getErrorDescription(),'授权码无效或已经过期');
}
function testSign() {
    $macKey = "Bg9DU17fmBPoZEKtVhotXSUxTw=";
    $method = "GET";
    $host = "oneboxhost.open.account.xiaomi.com";

    $qs = array("clientId" => "3","token" => "eJxjYGAQeTRhYWT1XK9rXSpCR9QNkj8I5p4_xMDAwMgQDyQZUpZNrAfRgbf6wDRDjFMjAwQwg7ChghGIyk1MBgBDExGE" );
    $uri = "/user/profile";
    $nonce = "9408e7700ca632008ad96681013a46b4";
    $sigString = XMUtil::getSignString($nonce, $method,$host,$uri,$qs);
    print  "auto:". $sigString;
    print "\n-----------------------------\n";
    print XMUtil::buildSignature($sigString, $macKey);

    $sigString = $nonce."\n".$method."\n".$host."\n".$uri."\nclientId=3&token=eJxjYGAQeTRhYWT1XK9rXSpCR9QNkj8I5p4_xMDAwMgQDyQZUpZNrAfRgbf6wDRDjFMjAwQwg7ChghGIyk1MBgBDExGE\n";
    print  "mmmmm:".$sigString;
    print "\n-----------------------------\n";
    print XMUtil::buildSignature($sigString, $macKey);
    flush();
}
/////////////////////////////////////////////////
//accessTokenTest();
//testXMHttpClient ();
//testXMOAuthHttpClient();
//
$nonce = XMUtil::getNonce();
print $nonce."\n";
//
//$nonce = XMUtil::getNonce();
//print $nonce."\n";
//testSign();
?>
