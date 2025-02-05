<?php
set_time_limit(0);
$requestURI = $_SERVER['REQUEST_URI'];
$url = substr(strstr($requestURI, 'url='), strlen('url='));
//缺少url
if (!$url) {
    echo 'lack url';
    http_response_code(400);
    exit;
}
parse_str(parse_url($url, PHP_URL_QUERY), $url_query);
// 不是github的链接
if (!in_array(parse_url($url,PHP_URL_HOST),['raw.githubusercontent.com','github.com','objects.githubusercontent.com'])) {
    echo 'host not allowed';
    http_response_code(400);
    exit;
}
$headers = get_headers($url, 1);
// 可能跳转
if ($headers && (strpos($headers[0], '301') || strpos($headers[0], '302'))) {
    header('Location: '.$_SERVER['SCRIPT_NAME'].'?url=' . $headers['Location']);
    exit;
}
//如果没有
if (!$headers || !strpos($headers[0], '200')) {
    echo 'code: ' . $headers[0];
    http_response_code(400);
    exit;
}
//对github调整文件名
if ($url_query['response-content-disposition']) {
    header('Content-Disposition: inline;filename= ' . substr(strstr($url_query['response-content-disposition'], 'filename='), strlen('filename=')));
} else if (!strstr(basename($url), '.')&&strstr($headers['Content-Type'],'application/zip')) {
    header('Content-Disposition: inline;filename= '.basename($url).'.zip');
}else{
    header('Content-Disposition: inline;filename= '.basename($url));
}
//其他请求头
if (isset($headers['Content-Length'])) {
    header('Content-Length: ' . $headers['Content-Length']);
}
if (isset($headers['Content-Type'])) {
    header('Content-Type: ' . $headers['Content-Type']);
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
