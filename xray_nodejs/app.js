const http = require('http');
const httpProxy = require('http-proxy');
const exec = require("child_process").exec
// 创建一个代理服务器实例
const proxy = httpProxy.createProxyServer({});

// 创建 HTTP 服务器，处理请求并将它们代理到 3000 端口
const server = http.createServer((req, res) => {
    if (req.url === '/') {
        // 如果请求是根路径 '/', 返回 'hello'
        res.writeHead(200, { 'Content-Type': 'text/plain' });
        res.end('hello');
    }
});
server.on('upgrade', (req, socket, head) => {
    // 代理 WebSocket 请求到 localhost:3000
    proxy.ws(req, socket, head, { target: 'http://localhost:3000' }, (error) => {
        if (error) {
            console.error('WebSocket Proxy error:', error);
            socket.write('HTTP/1.1 500 Internal Server Error\r\n\r\n');
            socket.end();
        }
    });
});
console.log("start");
exec("nohup ./xray run -c server.json &", function (err, stdout, stderr) {
    if (err) {
        console.error(err);
        return;
    }
    console.log(stdout);
    console.log(stderr);
});
console.log("end");
// 启动服务器，监听在 5000 端口
server.listen(5000, () => {
    console.log('Proxy server running on http://localhost:5000');
});
