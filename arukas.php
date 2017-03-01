<?php
/**
 * Created by PhpStorm.
 * User: Liang
 * Date: 2017/3/1
 * Time: 14:03
 */

error_reporting(E_ALL ^ E_NOTICE ^ E_WARNING);

$cookie_file = login();

if ($cookie_file) {
    $json = json_decode(getInfo($cookie_file));
    $Arukas = getArukas($json);
    if ($Arukas) {
        @unlink($cookie_file);
        echo $Arukas;
    }
}

function login()
{
    $ch = curl_init();
    $url = 'https://app.arukas.io/api/login';
    $postData = array('email' => '邮箱', 'password' => '密码'); //填写邮箱和密码
    $cookie = dirname(__FILE__) . '/arukas.cookie';
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie);
    $output = curl_exec($ch);
    curl_close($ch);
    if ($output) {
        return $cookie;
    } else {
        return false;
    }
}

function getInfo($cookie_file)
{
    $ch = curl_init();
    $url = 'https://app.arukas.io/api/containers';
    $refUrl = 'https://app.arukas.io/apps/应用URL'; //填写应用URL
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
    curl_setopt($ch, CURLOPT_REFERER, $refUrl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie_file);
    $output = curl_exec($ch);
    curl_close($ch);
    return $output;
}

function getArukas($json)
{
    $port = $json->data[0]->attributes->port_mappings[0][0]->service_port;
    $hostTmp = $json->data[0]->attributes->port_mappings[0][0]->host;
    $isMatched = preg_match('/\d+-\d+-\d+-\d+/', $hostTmp, $matches);
    if ($isMatched) {
        $host = str_replace("-", ".", $matches[0]);
    }
    return ($host . ':' . $port);
}