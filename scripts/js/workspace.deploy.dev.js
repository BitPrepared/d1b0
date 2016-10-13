var path = require('path');
var fs = require('fs');
var JSZip = require("jszip");
var rsync = require("rsyncwrapper");
var chalk = require('chalk');
var emoji = require('node-emoji');
var node_ssh = require('node-ssh');
var ssh = new node_ssh();

process.stdout.write(chalk.gray(emoji.emojify("[  ] Deploy workspace on ("+ enviroment +").\n")));

var enviroment = process.env.npm_package_config_enviroment;
var server_name = process.env.npm_package_config_server;
var server_ip = process.env.npm_package_config_ip;

ssh.connect({
  host: server_name,
  username: 'developer',
  privateKey: './server/plays/ssh/developer.key',
  port: 2222
}).then(function() {
  var nextCommand = ssh.mkdir('/var/www/workspace.local/src/');
  // @see: https://github.com/steelbrain/node-ssh/blob/master/src/index.js
  nextCommand.then( function(){
    ssh.exec('rm', ['-rf', 'public'], { cwd: '/var/www/workspace.local', stream: 'both' }).then( function(both){
      ssh.exec('ln', ['-s','web','public'], { cwd: '/var/www/workspace.local', stream: 'both' }).then( function(both){
          ssh.dispose();
          process.stdout.write(chalk.gray(emoji.emojify("Prepare dir remote completed.\n")));

          process.stdout.write(chalk.gray(emoji.emojify("Start rsync src.\n")));

          rsync({
              src: "./applications/workspace/",
              dest: 'developer@' + server_name + ':/var/www/workspace.local/',
              ssh: true,
              privateKey: './server/plays/ssh/developer.key',
              port: 2222,
              exclude: [ 'test', '.DS_Store', 'phpunit.xml', 'README.md', 'vendor', 'config.php.dist' ],
              recursive: true,
              deleteAll: false //senno elimina log!
          },function (error,stdout,stderr,cmd) {
              if ( error ) {
                process.stderr.write(chalk.bgRed.white(emoji.emojify("[:heavy_multiplication_x: ] "+'CMD: '+cmd+"\n")));
                process.stderr.write(chalk.red(error.message+"\n"));
                process.exit(1);
              }
          });

          process.stdout.write(chalk.gray(emoji.emojify("Avvio composer install\n")));

          ssh.connect({
            host: server_name,
            username: 'developer',
            privateKey: './server/plays/ssh/developer.key',
            port: 2222
          }).then(function() {
            ssh.exec('composer', ['install', '--no-dev', '--optimize-autoloader', '--no-interaction', '--no-progress', '--no-scripts'], { cwd: '/var/www/workspace.local/', stream: 'both' }).then(function(both) {
              process.stdout.write(chalk.gray(emoji.emojify(both.stdout+"\n")));
              if ( both.code === 0 ){
                process.stdout.write(chalk.gray(emoji.emojify(both.stderr+"\n")));
              } else {
                process.stderr.write(chalk.bgRed.white(emoji.emojify("[:heavy_multiplication_x: ] Errore composer\n")));
                process.stderr.write(chalk.red(both.stderr+"\n"));
                process.exit(1);
              }
              process.stdout.write(chalk.bgGreen.black(emoji.emojify('[:heavy_check_mark: ] Deploy COMPLETED.' + "\n")));
              ssh.dispose();
            });
          });

      });
    });
  });
});
