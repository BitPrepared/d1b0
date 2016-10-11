var program = require('commander');
var shell = require('shelljs');
var chalk = require('chalk');
var co = require('co');
var prompt = require('co-prompt');
var fs = require('fs');

program
  .usage('[options] <enviroment ...>')
  .arguments('<enviroment>')
  .action(function(enviroment) {

    shell.echo('Compile Workspace ('+enviroment+')');

    shell.cd('applications/workspace/');

    fs.readFile('config.php.dist', 'utf8', function (err,data) {

      if (err) {
        process.stderr.write(chalk.red("Errore in lettura: "+err));
        process.exit(1);
      }

      var result;
      if ( enviroment === 'dev' ){
        result = data.replace(/##TYPE##/g, 'sqlite');
        result = result.replace(/##HOST##/g, 'ROOT_PATH.\'/../database/workspace.sqlite\'');
        result = result.replace(/##DBNAME##/g, '');
        result = result.replace(/##PASSWORD##/g, '');
        result = result.replace(/##USERNAME##/g, '');
        result = result.replace(/##LOGLEVEL##/g, '\\Monolog\\Logger::DEBUG');
      } else if ( enviroment === 'vagrant' ){
        result = data.replace(/##TYPE##/g, 'mysql');
        result = result.replace(/##HOST##/g, '\'localhost\'');
        result = result.replace(/##DBNAME##/g, 'workspace');
        result = result.replace(/##PASSWORD##/g, 'workspace');
        result = result.replace(/##USERNAME##/g, 'workspace');
        result = result.replace(/##LOGLEVEL##/g, '\\Monolog\\Logger::DEBUG');
      } else {
        // STAGING / PROD
        result = data.replace(/##TYPE##/g, 'mysql');
        if (
          !process.env.npm_package_config_database_host ||
          !process.env.npm_package_config_database_host ||
          !process.env.npm_package_config_database_dbname ||
          !process.env.npm_package_config_database_password ||
          !process.env.npm_package_config_database_username
        ) {
          process.stderr.write(chalk.red("Errore manca la configurazione in .npmrc"));
          process.exit(1);
        }
        result = result.replace(/##HOST##/g, '\''+process.env.npm_package_config_database_host+'\'');
        result = result.replace(/##DBNAME##/g, process.env.npm_package_config_database_dbname);
        result = result.replace(/##PASSWORD##/g, process.env.npm_package_config_database_password);
        result = result.replace(/##USERNAME##/g, process.env.npm_package_config_database_username);
        result = result.replace(/##LOGLEVEL##/g, '\\Monolog\\Logger::INFO');
      }


      fs.writeFile('config.php', result, 'utf8', function (err) {
         if (err) {
           process.stderr.write(chalk.red("Errore in scrittura: "+err));
           process.exit(1);
         }
      });
    });

  })
  .parse(process.argv);


if (!process.argv.slice(2).length) {
  program.outputHelp(make_red);
}

function make_red(txt) {
  return chalk.red(txt);
}
