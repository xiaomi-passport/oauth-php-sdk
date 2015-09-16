<?php

/***************************************************************************
 *
 * Copyright (c) 2013 xiaomi.com, Inc. All Rights Reserved
 *
 **************************************************************************/

/**
 * @version 1.0
 * @author passport.xiaom.com
 */
require_once('XMHttpClient.php');
require_once('xiaomi.conf.php');
class XMOAuthClient extends XMHttpClient {

    protected $clientId;
    protected $clientSecret;
    protected $redirectUri;
    protected  $oauthUrl;
    public static $OAUTH2_PATH = array(
    	'authorize'	=> '/oauth2/authorize',
    	'token'		=> '/oauth2/token',
    );

    public function XMOAuthClient($clientId, $clientSecret) {
        global  $OAUTH2_URL;
        $this->clientId = $clientId;
        $this->clientSecret = $clientSecret;
        $this->oauthUrl = trim($OAUTH2_URL, ' /');
    }
    public function getAuthorizeEndpoint() {
        if(!isset($this->oauthUrl)) {
            throw  new Exception(" OAUTH2 HOST  IS NULL");
        }
        return$this->oauthUrl.self::$OAUTH2_PATH['authorize'];
    }
    public function getTokenEndpoint() {
        if(!isset($this->oauthUrl)) {
            throw  new Exception(" OAUTH2 HOST  IS NULL");
        }
        return $this->oauthUrl.self::$OAUTH2_PATH['token'];
    }
    public function setRedirectUri($redirectUri) {
        $this->redirectUri = $redirectUri;
    }

    public function getRedirectUri(){
        return $this->redirectUri;
    }

    public function getAuthorizeUrl($responseType = 'code',  $state = '', $scope = '') {
        $params = array(
               'client_id'		=> $this->clientId,
                'response_type'	=> $responseType,
                'redirect_uri'	=> $this->redirectUri,
                'scope'			=> $scope,
                'state'			=> $state,
        );
        return $this->getAuthorizeEndpoint() . '?' . XMHttpClient::buildQueryString($params);
    }

    public function getAccessTokenByAuthorizationCode($code) {
        $params = array(
			 'grant_type'	=> 'authorization_code',
			 'code'			=> $code,
			 'client_id'		=> $this->clientId,
			 'client_secret'	=> $this->clientSecret,
			 'redirect_uri'	=> $this->redirectUri,
		     'token_type'    => 'mac',
        );
        return $this->getAccessToken($params);
    }

    public function getAccessTokenByRefreshToken($refreshToken) {
        $params = array(
			 'grant_type'	=> 'refresh_token',
			 'refresh_token'	=> $refreshToken,
			 'client_id'		=> $this->clientId,
			 'client_secret'	=> $this->clientSecret,
		     'token_type'    => 'mac',
             'redirect_uri'	=> $this->redirectUri,
        );
        return $this->getAccessToken($params);
    }
    /**
     *
     * @param  $params  array
     * @return error array | AccessToken
     */
    public function getAccessToken($params) {
        $result = $this->get($this->getTokenEndpoint(), $params);
        if ($result && $result['succ']) {
            $result = json_decode($result['result'], true);
            return new AccessToken($result);
        }
        return $result;
    }
}
?>