var path = require('path');
var fs = require('fs');
var JSZip = require("jszip");
var rsync = require("rsyncwrapper");
var chalk = require('chalk');
var node_ssh = require('node-ssh');
var ssh = new node_ssh();

ssh.connect({
  host: 'www.d1b0.local',
  username: 'developer',
  privateKey: './server/plays/ssh/developer.key',
  port: 2222
}).then(function() {
  var nextCommand = ssh.mkdir('/var/www/workspace.local/src/');
  // @see: https://github.com/steelbrain/node-ssh/blob/master/src/index.js
  nextCommand.then( function(){
    ssh.exec('rm', ['-rf', 'public'], { cwd: '/var/www/workspace.local', stream: 'stdout' }).then( function(output){
      ssh.exec('ln', ['-s','web','public'], { cwd: '/var/www/workspace.local', stream: 'stdout' }).then( function(output){
          ssh.dispose();
      });
    });
  });
});

rsync({
    src: "./applications/workspace/",
    dest: "developer@www.d1b0.local:/var/www/workspace.local/",
    ssh: true,
    privateKey: './server/plays/ssh/developer.key',
    port: 2222,
    exclude: [ 'test', '.DS_Store', 'phpunit.xml', 'README.md', 'vendor' ],
    recursive: true,
    deleteAll: false //senno elimina log!
},function (error,stdout,stderr,cmd) {
    if ( error ) {
        process.stderr.write('CMD: '+cmd+"\n");
        process.stderr.write(chalk.red(error.message));
        process.exit(1);
    }
});

ssh.connect({
  host: 'www.d1b0.local',
  username: 'developer',
  privateKey: './server/plays/ssh/developer.key',
  port: 2222
}).then(function() {
  ssh.exec('composer', ['install', '--no-dev', '--optimize-autoloader', '--no-interaction', '--no-progress', '--no-scripts'], { cwd: '/var/www/workspace.local/', stream: 'stdout' }).then(function(output) {
    process.stdout.write(chalk.cyan(output));
    ssh.dispose();
  });
});
