var program = require('commander');
var shell = require('shelljs');
var chalk = require('chalk');
var emoji = require('node-emoji');
var co = require('co');
var prompt = require('co-prompt');

var enviroment = process.env.npm_package_config_enviroment;

shell.mkdir('-p',['applications/assets/icons']);

process.stdout.write(chalk.gray(emoji.emojify('[  ] Build Server Vagrant (' + enviroment + ")\n")));

if ( enviroment === 'dev' ){

  vagrantId = shell.exec("vagrant global-status | grep d1b0server | awk '{ print $1}'", {silent:true});

  var vagrantCode;
  if ( vagrantId.code !== 0 ){
    process.stderr.write(chalk.bgRed.white(emoji.emojify("[:heavy_multiplication_x: ] Errore: "+vagrantId.stderr+ " - "+vagrantId.output)));
    process.exit(1);
  } else {
    vagrantCode = vagrantId.output.trim();
  }

  if ( !vagrantCode ) {
    process.stdout.write(chalk.yellow(emoji.emojify("[:raised_hand: ] Vagrant server not found \n")));

    shell.exec('./scripts/generatekey.sh');
    shell.exec('./scripts/generateSSL.sh');
    shell.exec('./scripts/roles_update.sh');

    shell.cd('server/');

    upVagrant = shell.exec("vagrant up", {silent:true});
    if ( upVagrant.code !== 0 ){
      process.stderr.write(chalk.bgRed.white(emoji.emojify("[:heavy_multiplication_x: ] Errore: "+upVagrant.stderr+ " - "+upVagrant.output)));
      process.exit(1);
    }

    process.stdout.write("Vagrant server UP.\n");


    shell.echo('Install Server Required Package and Config');
    ansibleProc = shell.exec('ansible-playbook -i '+enviroment+'.hosts site.yml', {silent:true});
    if ( ansibleProc.code !== 0 ){
      process.stderr.write(chalk.bgRed.white(emoji.emojify("[:heavy_multiplication_x: ] Errore ansible site.yml")));
      process.stderr.write(chalk.gray(ansibleProc.stdout+"\n"));
      process.stderr.write(chalk.red(ansibleProc.stderr+"\n"));

      process.exit(1);
    }

    process.stdout.write(chalk.bgGreen.black(emoji.emojify('[:heavy_check_mark: ] Vagrant server installation packages completed.' + "\n")));

    shell.cd('..');

  } else {
    process.stdout.write(chalk.gray(emoji.emojify("[:world_map: ] Find vagrant server: "+vagrantCode+". Skip creation.\n" )));
    // non funziona vagrant up by name or uuid
    // var upVagrant = shell.exec("vagrant up "+vagrantCode);

    shell.cd('server/');

    upVagrant = shell.exec("vagrant up", {silent:true});
    if ( upVagrant.code !== 0 ){
      process.stderr.write(chalk.bgRed.white(emoji.emojify("[:heavy_multiplication_x: ] Errore ansible site.yml")));
      process.stderr.write(chalk.gray(ansibleProc.stdout+"\n"));
      process.stderr.write(chalk.red(ansibleProc.stderr+"\n"));
      process.exit(1);
    }

    process.stdout.write(chalk.bgGreen.black(emoji.emojify('[:heavy_check_mark: ] Vagrant server UP!' + "\n")));

    shell.cd('..');
  }

} else {

    shell.echo('Install Server Required Package and Config');
    ansibleProc = shell.exec('ansible-playbook -i '+enviroment+'.hosts site.yml', {silent:true});
    if ( ansibleProc.code !== 0 ){
      process.stderr.write(chalk.bgRed.white(emoji.emojify("[:heavy_multiplication_x: ] Errore: "+ansibleProc.stderr+ " - "+ansibleProc.output)));
      process.exit(1);
    }

}
