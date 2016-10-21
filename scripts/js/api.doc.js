var raml2html = require('raml2html');
var chalk = require('chalk');
var emoji = require('node-emoji');
var fs = require('fs');
var path = require('path');
var http = require('http');
var url = require('url');
var childProcess = require('child_process');

scriptPath = 'scripts/js/api.server.js';

var options = {
  env: process.env
};

var childProcessInstance = childProcess.fork(scriptPath, options);

// listen for errors as they may prevent the exit event from firing
childProcessInstance.on('error', function (err) {
  if ( err ){
    process.stderr.write(chalk.bgRed.white(emoji.emojify("[:heavy_multiplication_x: ] Errore server documentazione API (raml)"))+ "\n");
    process.stderr.write(chalk.red(err+"\n"));
    process.exit(1);
  }
});

var enviroment = process.env.npm_package_config_enviroment;

var apiDir = path.join(__dirname, '../../api/');

process.stdout.write(chalk.gray(emoji.emojify('[  ] Generazione Documentazione API (' + enviroment + ") from "+ ramlFile))+ "\n");

var ramlFile = path.join(__dirname, '../../api/api.raml');

var ramlHtml = path.join(__dirname, '../../api/api-generated.html');

var configWithCustomTemplates = raml2html.getDefaultConfig('template.nunjucks', path.normalize(apiDir + '/template') );

// source can either be a filename, url, file contents (string) or parsed RAML object
raml2html.render(ramlFile, configWithCustomTemplates).then(function(result) {
  // Save the result to a file or do something else with the result
  // api-generated.html
  //
  // console.log(result);

  fs.writeFile(ramlHtml, result, function(err) {
      if(err) {
        process.stderr.write(chalk.bgRed.white(emoji.emojify("[:heavy_multiplication_x: ] Errore generazione documentazione API (raml)\n")));
        process.stderr.write(chalk.red(err)+ "\n");
        process.exit(1);
      }

      process.stdout.write(chalk.gray(emoji.emojify('[  ] Server must stop '+"\n")));

      var options = {
        host: 'localhost',
        port: 9999,
        path: '/end',
        method: 'GET'
      };

      var reqCloseServer = http.request(options, function(res) {
        statusCode = `${res.statusCode}`;
        process.stdout.write(chalk.gray(emoji.emojify('[  ] Response '+ statusCode +"\n")));

        process.stdout.write(chalk.bgGreen.black(emoji.emojify('[:heavy_check_mark: ] API Documentation created!' + "\n")));
      });

      reqCloseServer.on('error', (e) => {
        error = `${e.message}`;
        process.stderr.write(chalk.bgRed.white(emoji.emojify("[:heavy_multiplication_x: ] Errore generazione documentazione API (raml)"))+ "\n");
        process.stderr.write(chalk.red(error)+ "\n");
        process.exit(1);
      });

      reqCloseServer.end();

  });

}, function(error) {
  console.log(error);
  process.stderr.write(chalk.bgRed.white(emoji.emojify("[:heavy_multiplication_x: ] Errore generazione documentazione API (raml)"))+ "\n");
  process.stderr.write(chalk.red(error)+ "\n");
  process.exit(1);
});
