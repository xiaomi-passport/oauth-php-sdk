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
class XMHttpClient {

    var $url;
    var $path;
    var $method;
    var $cookies = array();

    function XMHttpClient($url) {
        if (is_string($url) && substr($url, 0, 8) !== 'https://' && substr($url, 0, 7) !== 'http://') {
            throw new Exception("url protocal error, must be http:// or https://");
        }
        $this->url = $url;
    }

    function get($path, $params = false, $cookies = false, $header = false) {
        $this->path = $path;
        $this->method = 'GET';
        $curl = $this->url.$this->path;
        return self::quickRequest($curl, $params, $cookies, $header, $this->method);
    }

    function post($path, $params = array(), $cookies = false, $header = false) {
        $this->path = $path;
        $this->method = 'POST';
        $curl = $this->url.$this->path;
        return self::quickRequest($curl, $params, $cookies, $header, $this->method);
    }

    static public function buildQueryString($params) {
        $querystring = '';
        if (is_array($params)) {
            foreach ($params as $key => $val) {
                if($val) {
                    if (is_array($val)) {
                        foreach ($val as $val2) {
                            $querystring .= urlencode($key).'='.urlencode($val2).'&';
                        }
                    } else {
                        $querystring .= urlencode($key).'='.urlencode($val).'&';
                    }
                }
            }
            $querystring = substr($querystring, 0, -1);
        } else if($params) {
            $querystring = $params;
        }
        return $querystring;
    }


    static public function buildCookieString($cookies) {
        $cookie_string = '';
        if (is_array($cookies)) {
            foreach ($cookies as $key => $value) {
                if($value) {
                    array_push($cookie_string, $key . '=' . $value);
                }
            }
            $cookie_string = join('; ', $cookie_string);
        } else {
            $cookie_string = $cookies;
        }
        return $cookie_string;
    }

    /**
     * 执行一个 HTTP 请求
     *
     * @param string 	$url
     * @param $params 表单参数(array)
     * @param $cookie cookie参数 (array)
     * @param string	$method (post / get)
     * @return array 结果数组
     */
    static public function quickRequest($url, $params = false, $cookie= false, $header= false, $method='get') {
        $query_string = self::buildQueryString($params);
        $cookie_string = self::buildCookieString($cookie);

        $ch = curl_init();

        if ('GET' == strtoupper($method)) {
            if(!empty($query_string)) {
                curl_setopt($ch, CURLOPT_URL, $url."?".$query_string);
            } else {
                curl_setopt($ch, CURLOPT_URL, $url);
            }
        }
        else {
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $query_string);
        }

        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 3);

        // disable 100-continue
        if (is_array($header)) {
            $header[] = 'Expect:';
            curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        } else {
            curl_setopt($ch, CURLOPT_HTTPHEADER, array('Expect:'));
        }

        if (!empty($cookie_string)) {
            curl_setopt($ch, CURLOPT_COOKIE, $cookie_string);
        }
         
        if (stripos($url, 'https://') === 0) {
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        }

        $result = curl_exec($ch);
        $err = curl_error($ch);
         
        if (false === $result || !empty($err)) {
            $errno = curl_errno($ch);
            $info = curl_getinfo($ch);
            curl_close($ch);

            return array(
	        	'succ' => false,
	        	'errno' => $errno,
	            'errmsg' => $err,
	        	'info' => $info,
            );
        }
        curl_close($ch);
        $result = str_replace("&&&START&&&","",$result);
        return array(
        	'succ' => true,
            'result' => $result,
        );
    }
}
?>