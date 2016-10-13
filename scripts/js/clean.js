var program = require('commander');
var shell = require('shelljs');
var chalk = require('chalk');
var emoji = require('node-emoji');
var co = require('co');
var prompt = require('co-prompt');
var rimraf = require('rimraf');

var enviroment = process.env.npm_package_config_enviroment;

shell.mkdir('-p',['applications/assets/icons']);

process.stdout.write(chalk.gray(emoji.emojify("[  ] Clean Server Vagrant ("+ enviroment +").\n")));

if ( enviroment === 'dev' ){

  // SSH
  rimraf.sync('server/plays/ssh/*.key');
  rimraf.sync('server/plays/ssh/*.pub');

  // SSL
  rimraf.sync('server/plays/ssl/*.key');
  rimraf.sync('server/plays/ssl/*.crt');
  rimraf.sync('server/plays/ssl/*.pem');

  // ANSIBLE ROLE
  rimraf.sync('server/roles/external/*');

  shell.cd('server');

  destroyVagrantServer = shell.exec('vagrant destroy -f');

  if ( destroyVagrantServer.code !== 0 ){
    process.stderr.write(chalk.bgRed.white(emoji.emojify("[:heavy_multiplication_x: ] Errore vagrant destroy")));
    process.stderr.write(chalk.gray(destroyVagrantServer.stdout+"\n"));
    process.stderr.write(chalk.red(destroyVagrantServer.stderr+"\n"));
    process.exit(1);
  }

  shell.cd('..');

  process.stdout.write(chalk.bgGreen.black(emoji.emojify('[:heavy_check_mark: ] Clean server COMPLETED.' + "\n")));

} else {

    process.stdout.write(chalk.yellow(emoji.emojify("[:raised_hand: ] Ambiente non gestito ("+ enviroment +").\n")));
}
