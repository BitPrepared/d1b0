var raml2html = require('raml2html');
var chalk = require('chalk');
var emoji = require('node-emoji');
var fs = require('fs');
var path = require('path');
var http = require('http');
var url = require('url');

var enviroment = process.env.npm_package_config_enviroment;

var apiDir = path.join(__dirname, '../../api/');
var ramlFile = path.join(__dirname, '../../api/api.raml');

process.stdout.write(chalk.gray(emoji.emojify('[  ] Server Documentazione API (' + enviroment + ") from "+ ramlFile))+ "\n");

var server = http.createServer(function (req, res) {
  // console.log(`${req.method} ${req.url}`);
  process.stdout.write(chalk.gray(emoji.emojify('[  ] Request ' + `${req.method} ${req.url}`))+ "\n");

  // parse URL
  var parsedUrl = url.parse(req.url);
  // extract URL path
  let pathname = `${parsedUrl.pathname}`;
  // based on the URL path, extract the file extention. e.g. .js, .doc, ...
  var ext = path.parse(pathname).ext;
  // maps file extention to MIME types
  var mimeType = {
    '.html': 'text/html',
    '.json': 'application/json'
  };
  let pathnameFull = path.normalize(apiDir + pathname);

  if ( pathname == '/end' ) {
    // console.log('/end');
    res.statusCode = '204';
    res.end();
    process.exit(0);
  }

  fs.exists(pathnameFull, function (exist) {
    if(!exist) {
      // if the file is not found, return 404
      res.statusCode = 404;
      res.end(`File ${pathname} not found!`);
      return;
    }
    // if is a directory, then look for index.html
    if (fs.statSync(pathnameFull).isDirectory()) {
      pathnameFull += '/index.html';
    }
    // read file from file system
    fs.readFile(pathnameFull, function(err, data){
      if(err){
        res.statusCode = 500;
        res.end(`Error getting the file: ${err}.`);
      } else {
        // if the file is found, set Content-type and send data
        res.setHeader('Content-type', mimeType[ext] || 'text/plain' );
        res.end(data);
      }
    });
  });
});

server.listen(9999, '127.0.0.1');

process.stdout.write(chalk.gray(emoji.emojify('[  ] Server listening ' + apiDir + ' on port ' + 9999))+ "\n");
