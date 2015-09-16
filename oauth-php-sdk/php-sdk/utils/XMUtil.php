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
class  XMUtil {
    /**
     * mac type签名算法
     * @param  $sigString
     * @param  $secret
     */
    static public function buildSignature($nonce,$method, $host, $path, $params, $secret) {
        $signString = self::getSignString($nonce,$method, $host, $path, $params);
        $sign = base64_encode(hash_hmac('sha1', $signString, $secret, true));
        return urlencode($sign);
    }
    /**
     *
     * 获取mac type access token请求api的头部信息
     * @param $tokenId
     * @param $nonce
     * @param $sign
     */
    static  public function buildMacRequestHead($tokenId, $nonce, $sign) {
        $macHead = "MAC access_token=\"$tokenId\", nonce=\"$nonce\",mac=\"$sign\" ";
        $head = array("Authorization:". $macHead);
        return $head;
    }
    /**
     * 构造mac type签名串
     * @param $nonce
     * @param $method
     * @param $host
     * @param $path
     * @param $params
     */
    static public function getSignString($nonce,$method, $host, $path, $params) {
        $signString = '';
        // nonce
        $signString .= $nonce."\n";
        //method
        $signString .= $method."\n";
        // host
        $signString .= $host."\n";
        // path
        $signString .= $path."\n";
        //query
        ksort($params);
        reset($params);
        $str = '';
        foreach($params AS $k=>$v){
            $arr[$k]=$v;
            $str .= $k.'='.$v.'&';
        }
        if (strlen($str) > 1) {
            $str = substr($str,  0, -1);
        }
        $signString .= $str."\n";
        return $signString;
    }

    /**
     * 获取一个随机字符串
     * 获取随机nonce值 : 格式为  随机数:分钟数
     */
    static public function  getNonce() {
        list($usec, $sec) = explode(' ', microtime());
        $nonce = (float)mt_rand();
        $minutes = (int)($sec / 60);
        $nonce = $nonce.":".$minutes;
        return $nonce;
    }
}
?>