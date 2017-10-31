var http = require('http');

http.createServer(function (request,response) {
	response.writeHead(200, { 'Content-Type' : 'text/html'});
	response.end('<h1>Hello World..</h1>');
}).listen(52273, function() {
	console.log('server running at http://127.0.0.1:52273/');
});
