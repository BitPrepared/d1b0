var chalk = require('chalk');
var hostile = require('hostile');
var emoji = require('node-emoji');

var enviroment = process.env.npm_package_config_enviroment;
var server_name = process.env.npm_package_config_server;
var server_ip = process.env.npm_package_config_ip;
var found = false;
var preserveFormatting = false;
hostile.get(preserveFormatting, function (err, lines) {
  if (err) {
    console.error(err.message);
  }
  lines.forEach(function (line) {
    if ( line[0].trim() === '127.0.0.1' ) {
      line[1].trim().split(' ').forEach(function(elem){
        if ( elem === server_name){
          found = true;
        }
      });
    }
  });
  if ( !found ){
    process.stderr.write(chalk.yellow(emoji.emojify("[:raised_hand: ] Server hostname non found on local /etc/hosts \n")));
    if ( enviroment === 'dev' ){
      server_ip = '127.0.0.1';
    }
    hostile.set(server_ip, server_name, function (err) {
      if (err) {
        if ( err.code === 'EACCES' && err.path === '/etc/hosts' ){
          process.stderr.write(chalk.bgRed.white(emoji.emojify("[:heavy_multiplication_x: ] Impossibile modificare il file automaticamente, \nrieseguire 'npm run check:hostfile' come admin o editare a mano opportuanmente il file\n")));
        } else {
          process.stderr.write(chalk.bgRed.white(emoji.emojify("[:heavy_multiplication_x: ] Errore nel settaggio di /etc/hosts\n")));
          process.stderr.write(chalk.red(err+"\n"));
        }

        process.exit(1);
      } else {
        process.stdout.write(chalk.bgGreen.black(emoji.emojify('[:heavy_check_mark: ] set /etc/hosts successfully!' + "\n")));
      }
    });
  } else {
    process.stdout.write(chalk.bgGreen.black(emoji.emojify('[:heavy_check_mark: ] /etc/hosts correct!' + "\n")));
  }
});
