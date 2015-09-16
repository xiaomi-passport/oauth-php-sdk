# 小米帐号开放平台OAuth PHP SDK使用说明

------
### 小米OAuth简介
http://dev.xiaomi.com/docs/passport/oauth2/

### 小米帐号开放平台文档
http://dev.xiaomi.com/docs/passport/user_guide/

### PHP SDK说明
> * php-sdk/httpclient/XMApiClient.php -- 基础Http请求封装
> * php-sdk/httpclient/XMOAuthClient.php -- 针对OAuth授权流程相关http请求封装
> * php-sdk/httpclient/XMHttpClient.php -- 针对api请求相关http请求封装

### DEMO
#### 1.  获取授权URL DEMO
```PHP
参见[getCode.php](https://github.com/xiaomipassport/oauth-php-sdk/blob/master/oauth-php-sdk/php-sdk-demo/demo/getCode.php)
```
#### 2.  获取accessToken DEMO
```php
参见[getToken.php](https://github.com/xiaomipassport/oauth-php-sdk/blob/master/oauth-php-sdk/php-sdk-demo/demo/getToken.php)
```
#### 3.  通过refreshToken 换取 accessToken DEMO
```java
参见[getTokenByRefreshToken.php](https://github.com/xiaomipassport/oauth-php-sdk/blob/master/oauth-php-sdk/php-sdk-demo/demo/getTokenByRefreshToken.php)
```
#### 3.  访问open api DEMO(以获取userprofile为例)
```java
参见https://github.com/xiaomipassport/oauth-php-sdk/blob/master/oauth-php-sdk/php-sdk-demo/demo/getToken.php
相关代码如下:
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
$sign = XMUtil::buildSignature($nonce, $method,  $xmApiClient->getApiHost(), $path, $params,$token->getMacKey());

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
```
