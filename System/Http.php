<?php

namespace Floxim\Floxim\System;

class Http
{

    protected $status_values = array(
        200 => 'OK',
        301 => 'Moved Permanently',
        302 => 'Moved Temporarily',
        403 => 'Forbidden',
        404 => 'Not Found'
    );
    
    protected $last_response_headers = null;

    public function status($code)
    {
        if (headers_sent()) {
            return false;
        }
        header("HTTP/1.1 " . $code . " " . $this->status_values[$code]);
    }

    public function redirect($target_url, $status = 301)
    {
        $target_url = fx::path()->http($target_url);
        if (fx::env('ajax')) {
            ob_start();
            ?>
            <script type="text/javascript">
            document.location.href = '<?= $target_url ?>';    
            </script>
            <?php
            echo trim(ob_get_clean());
            fx::complete();
            die();
        }
        $this->status($status);
        header("Location: " . $target_url);
        fx::complete();
        die();
    }

    public function refresh()
    {
        $this->redirect($_SERVER['REQUEST_URI'], 200);
    }

    public function header($name, $value = null)
    {
        if (headers_sent()) {
            return false;
        }
        if (is_array($value) || is_object($value)) {
            $value = json_encode($value);
        }
        if (!$value) {
            // send header only if the first arg contains full header text, e.g.
            // My-Header: something
            if (!preg_match("~\:[^\s+]~", $name)) {
                return;
            }
            header($name);
        }
        header($name . ": " . $value);
    }
    
    public function get($url, $headers = array(), $context_options = array())
    {
        $header_string = '';
        if (is_array($headers)) {
            foreach ($headers as $h => $v) {
                $header_string .= $h.': '.$v."\r\n";
            }
        }
        
        $options = array(
            'http' => array_merge(
                array(
                    'header'  => $header_string,
                    'method'  => 'GET'
                ),
                $context_options
            )
        );
        $context  = stream_context_create($options);
        $result = file_get_contents($url, false, $context);
        $this->last_response_headers = $http_response_header;
        return $result;
    }
    
    public function getWithHeaders($url, $headers = array(), $context_options = array())
    {
        $time = microtime(true);
        $body = $this->get($url, $headers, $context_options);
        $res = array(
            'body' => $body,
            'response_time' => microtime(true) - $time,
            'headers' => $this->getLastHeaders(),
            'charset' => null
        );
        
        $res = array_merge($res, $this->getLastStatus());
        
        if (
            isset($res['headers']['content-type']) 
            && preg_match("~charset=(.+)~i", $res['headers']['content-type'], $charset)
        ) {
            $res['charset'] = $charset[1];
        }
        return $res;
    }
    
    public function getLastStatus()
    {
        $headers = $this->last_response_headers;
        $status = $headers[0];
        preg_match("~\d\d\d~", $status, $status_code);
        return array(
            'status' => $status,
            'status_code' => $status_code ? (int) $status_code[0] : null
        );
    }
    
    public function getLastHeaders() 
    {
        $headers = $this->last_response_headers;
        $res = array();
        
        if (!is_array($headers)) {
            return $res;
        }
        
        foreach (array_slice($headers, 1) as $header) {
            $parts = explode(":", $header, 2);
            $res[strtolower($parts[0])] = $parts[1];
        }
        return $res;
    }
    
    public function head($url, $headers, $context_options)
    {
        $header_string = '';
        if (is_array($headers)) {
            foreach ($headers as $h => $v) {
                $header_string .= $h.': '.$v."\r\n";
            }
        }
        
        $options = array(
            'http' => array_merge(
                array(
                    'header'  => $header_string,
                    'method'  => 'HEAD'
                ),
                $context_options
            )
        );
        
        $context  = stream_context_create($options);
        file_get_contents($url, false, $context);
        $this->last_response_headers = $http_response_header;
        return $this->getLastHeaders();
    }
    
    
    public function post($url, $data, $headers = array(), $context_options = array())
    {
        $has_content_type = false;
        $header_string = '';
        $serialize_type = 'urlencode';
        
        foreach ($headers as $h => $v) {
            $header_string .= $h.': '.$v."\r\n";
            if (strtolower($h) === 'content-type') {
                $has_content_type = true;
                if ($v === 'application/json'){
                    $serialize_type = 'json_encode';
                }
            }
        }
        
        if (!$has_content_type) {
            $header_string .= "Content-type: application/x-www-form-urlencoded\r\n";
        }
        
        if (!is_scalar($data)) {
            if ($serialize_type === 'urlencode') {
                foreach ($data as $k => $v) {
                    if (!is_scalar($v)) {
                        $data[$k] = json_encode($v);
                    }
                }
                $data = http_build_query($data, null, '&');
            } else {
                $data = json_encode($data);
            }
        }
        
        $options = array(
            'http' => array_merge(
                array(
                    'header'  => $header_string,
                    'method'  => 'POST',
                    'content' => $data
                ),
                $context_options
            )
        );
        $context  = stream_context_create($options);
        $result = @ file_get_contents($url, false, $context);
        return $result;
    }
}