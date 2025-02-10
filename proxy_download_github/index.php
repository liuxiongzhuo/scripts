<?php
set_time_limit(0);

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: *');

$requestURI = $_SERVER['REQUEST_URI'];
$url = substr(strstr($requestURI, 'url='), strlen('url='));
//缺少url
if ($url=='') {
    http_response_code(400);
    echo 'lack url';
    exit;
}
//处理302
if (isset($headers['Location'])) {
    header('Location: '.'/?url='.$headers['Location']);
    exit;
}
//处理不是200的
if (!strpos($headers['0'],'200')) {
    http_response_code(400);
    echo 'status is '.$headers['0'];
}
//在url处理响应头
if (isset($headers['Content-Length'])) {
    header('Content-Length: '.$headers['Content-Length']);
}
if (isset($headers['Content-Type'])) {
    header('Content-Type: '.$headers['Content-Type']);
}
if (isset($headers['Content-Disposition'])) {
    header('Content-Disposition: '.$headers['Content-Disposition']);
}
//开始下载
$fp = fopen($url, 'rb');
if ($fp) {
    while (!feof($fp)) {
        echo fread($fp, 1024 * 8);
        flush();
        ob_flush();
    }
    fclose($fp);
} else {
    http_response_code(500);
}
