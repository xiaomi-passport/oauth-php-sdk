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
class AccessToken {

    protected $access_token_id;
    protected $refresh_token;
    protected $scope;
    protected $expires_in;
    protected $token_type;
    protected $mac_key;
    protected $mac_algorithm;

    protected $is_error = false;
    protected $error = 0;
    protected $error_description = '';

    function AccessToken($jsonResult) {
        if(isset($jsonResult['error'])) {
            $this->is_error = true;
            $this->error = $jsonResult['error'];
            $this->error_description = $jsonResult['error_description'];
        } else {
            $this->is_error = false;

            $this->access_token_id =isset($jsonResult['access_token'])? $jsonResult['access_token'] : null;
            $this->token_type= isset($jsonResult['token_type'])? $jsonResult['token_type'] : null;
            $this->refresh_token = isset($jsonResult['refresh_token'])? $jsonResult['refresh_token'] : null;
            $this->scope= isset($jsonResult['scope'])? $jsonResult['scope'] : null;
            $this->expires_in= isset($jsonResult['expires_in'])? $jsonResult['expires_in'] : null;
            $this->mac_key= isset($jsonResult['mac_key'])? $jsonResult['mac_key'] : null;
            $this->mac_algorithm= isset($jsonResult['mac_algorithm'])? $jsonResult['mac_algorithm'] : null;
        }
    }

    public function isError() {
        return $this->is_error;
    }

    public function isMacType() {
        return $this->token_type!==null && strtoupper($this->token_type)  === 'MAC';
    }

    public function  getError() {
        return $this->error;
    }

    public function  getErrorDescription() {
        return $this->error_description;
    }

    public function getAccessTokenId() {
        return	$this->access_token_id;
    }

    public function getRefreshToken() {
        return	$this->refresh_token;
    }

    public function getScope() {
        return	$this->scope;
    }

    public function getExpiresIn() {
        return	$this->expires_in;
    }

    public function getMacKey() {
        return	$this->mac_key;
    }

    public function getMacAlgorithm() {
        return	$this->mac_algorithm;
    }
}
?>