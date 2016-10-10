var path = require('path');
var fs = require('fs');
var JSZip = require("jszip");
var node_ssh = require('node-ssh');
var ssh = new node_ssh();
var zip = new JSZip();

zip.file("Hello.txt", "Hello World\n");

//var img = zip.folder("images");
//img.file("smile.gif", imgData, {base64: true});

zip.generateNodeStream({type:'nodebuffer',streamFiles:true})
    .pipe(fs.createWriteStream('example.zip'))
    .on('finish', function () {
        // JSZip generates a readable stream with a "end" event,
        // but is piped here in a writable stream which emits a "finish" event.
        console.log("example.zip written.");
    });

var activeConnection = ssh.connect({
  host: 'www.d1b0.local',
  username: 'developer',
  privateKey: '../../server/play/ssh/developer.key'
});

activeConnection.then(function() {

  // Array<Shape('local' => string, 'remote' => string)>
  ssh.putFiles([
      { local: '../../applications/workspace/composer.json', remote: '/var/www/workspace.local/composer.json' }
    ]).then(function() {
    console.log("The File thing is done");
  }, function(error) {
    console.log("Something's wrong");
    console.log(error);
  });

  // Putting entire directories
  var failed = [];
  var successful = [];
  ssh.putDirectory('../../applications/workspace/src/', '/var/www/workspace.local/src/', {
    recursive: true,
    validate: function(itemPath) {
      var baseName = path.basename(itemPath);
      return baseName.substr(0, 1) !== '.' && baseName !== 'node_modules' && baseName !== 'vendor';
    },
    tick: function(localPath, remotePath, error) {
      if (error) {
        failed.push(localPath);
      } else {
        successful.push(localPath);
      }
    }
  }).then(function(successful) {
    console.log('the directory transfer was', successful ? 'successful' : 'unsuccessful');
    console.log('failed transfers', failed.join(', '));
    console.log('successful transfers', successful.join(', '));
  });

  // Command with escaped params
  ssh.exec('composer', ['install', '--no-dev', '--optimize-autoloader', '--no-interaction', '--no-progress', '--no-scripts'], { cwd: '/var/www/workspace.local/' }).then(function(result) {
    console.log('STDOUT: ' + result);
  });

});
