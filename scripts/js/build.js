var program = require('commander');
var shell = require('shelljs');
var fs = require('fs');
var chalk = require('chalk');
var emoji = require('node-emoji');
var co = require('co');
var prompt = require('co-prompt');

var enviroment = process.env.npm_package_config_enviroment;

var servername = process.env.npm_package_config_server;

shell.mkdir('-p',['applications/assets/icons']);

process.stdout.write(chalk.gray(emoji.emojify('[  ] Build Server Vagrant (' + enviroment + ")")) + "\n");

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
    process.stdout.write(chalk.yellow(emoji.emojify("[:raised_hand: ] Vagrant server not found")) + "\n");

    //gKey = shell.exec('./scripts/generatekey.sh', {silent:true});
    if (!fs.existsSync('./server/plays/ssh/root.key')) {
      process.stdout.write(chalk.gray(emoji.emojify('[  ] ssh-keygen root key')) + "\n");
      gKey = shell.exec('ssh-keygen -t rsa -b 4096 -N "" -f ./server/plays/ssh/root.key', {silent:true});
      if ( gKey.code !== 0 ){
        process.stderr.write(chalk.bgRed.white(emoji.emojify("[:heavy_multiplication_x: ] Errore generazione ssh root key per vagrant"))+ "\n");
        process.stderr.write(chalk.gray(gKey.stdout+"\n"));
        process.stderr.write(chalk.red(gKey.stderr+"\n"));
        process.exit(1);
      }
    } else {
      process.stdout.write(chalk.yellow(emoji.emojify("[:raised_hand: ] root key already exist")) + "\n");
    }

    if (!fs.existsSync('./server/plays/ssh/developer.key')) {
      process.stdout.write(chalk.gray(emoji.emojify('[  ] ssh-keygen developer key')) + "\n");
      dKey = shell.exec('ssh-keygen -t rsa -b 4096 -N "" -f ./server/plays/ssh/developer.key', {silent:true});
      if ( dKey.code !== 0 ){
        process.stderr.write(chalk.bgRed.white(emoji.emojify("[:heavy_multiplication_x: ] Errore generazione ssh developer key per vagrant")) + "\n");
        process.stderr.write(chalk.gray(dKey.stdout+"\n"));
        process.stderr.write(chalk.red(dKey.stderr+"\n"));
        process.exit(1);
      }
    } else {
      process.stdout.write(chalk.yellow(emoji.emojify("[:raised_hand: ] root key already exist")) + "\n");
    }

    //gSSL = shell.exec('./scripts/generateSSL.sh', {silent:true});
    if (!fs.existsSync('./server/plays/ssl/selfsigned.crt')) {
      process.stdout.write(chalk.gray(emoji.emojify('[  ] openssl generate self certificate')) + "\n");
      gSSL = shell.exec('openssl req -x509 -nodes -days 365 -newkey rsa:2048 -subj "/C=US/ST=Denial/L=Springfield/O=Dis/CN=' + servername + '" -keyout ./server/plays/ssl/selfsigned.key -out ./server/plays/ssl/selfsigned.crt', {silent:true});
      if ( gSSL.code !== 0 ){
        process.stderr.write(chalk.bgRed.white(emoji.emojify("[:heavy_multiplication_x: ] Errore generazione certificati SSL per vagrant"))+ "\n");
        process.stderr.write(chalk.gray(gSSL.stdout+"\n"));
        process.stderr.write(chalk.red(gSSL.stderr+"\n"));
        process.exit(1);
      }

      process.stdout.write(chalk.gray(emoji.emojify('[  ] assemble combo pem certificate with certificate'))+ "\n");
      contentsScrt = fs.readFileSync('./server/plays/ssl/selfsigned.crt','utf8');
      fs.writeFileSync('./server/plays/ssl/selfsigned.combo.pem', contentsScrt);


      // gSSLcrt1 = shell.exec('cat ./server/plays/ssl/selfsigned.crt > ./server/plays/ssl/selfsigned.combo.pem', {silent:false});
      // if ( gSSLcrt1.code !== 0 ){
      //   process.stderr.write(chalk.bgRed.white(emoji.emojify("[:heavy_multiplication_x: ] Errore creazione combo certificato SSL per vagrant"))+ "\n");
      //   process.stderr.write(chalk.gray(gSSLcrt1.stdout+"\n"));
      //   process.stderr.write(chalk.red(gSSLcrt1.stderr+"\n"));
      //   process.exit(1);
      // }

      process.stdout.write(chalk.gray(emoji.emojify('[  ] assemble combo pem certificate with key'))+ "\n");

      contentsSkey = fs.readFileSync('./server/plays/ssl/selfsigned.key','utf8');
      fs.appendFileSync('./server/plays/ssl/selfsigned.combo.pem', contentsSkey);

      // gSSLcrt2 = shell.exec('cat  >> ', {silent:false});
      // if ( gSSLcrt2.code !== 0 ){
      //   process.stderr.write(chalk.bgRed.white(emoji.emojify("[:heavy_multiplication_x: ] Errore generazione combo certificato SSL per vagrant"))+ "\n");
      //   process.stderr.write(chalk.gray(gSSLcrt2.stdout+"\n"));
      //   process.stderr.write(chalk.red(gSSLcrt2.stderr+"\n"));
      //   process.exit(1);
      // }

      //# RICHIEDE MOLTO TEMPO!
      dhparamLength = 4096;
      if ( enviroment === 'dev' || enviroment === 'vagrant' ){
        dhparamLength = 1024;
      }
      process.stdout.write(chalk.gray(emoji.emojify('[  ] generate '+ dhparamLength +' dhparam key'))+ "\n");
      gSSLcrt3 = shell.exec('openssl dhparam -out ./server/plays/ssl/dhparam.pem ' + dhparamLength, {silent:true});
      if ( gSSLcrt3.code !== 0 ){
        process.stderr.write(chalk.bgRed.white(emoji.emojify("[:heavy_multiplication_x: ] Errore generazione dhparam SSL random key per vagrant"))+ "\n");
        process.stderr.write(chalk.gray(gSSLcrt3.stdout+"\n"));
        process.stderr.write(chalk.red(gSSLcrt3.stderr+"\n"));
        process.exit(1);
      }

      process.stdout.write(chalk.gray(emoji.emojify('[  ] assemble combo pem with dhparam'))+ "\n");
      contentsDparam = fs.readFileSync('./server/plays/ssl/dhparam.pem','utf8');
      fs.appendFileSync('./server/plays/ssl/selfsigned.combo.pem', contentsDparam);

      // gSSLcrt4 = shell.exec('cat ./server/plays/ssl/dhparam.pem >> ./server/plays/ssl/selfsigned.combo.pem', {silent:false});
      // if ( gSSLcrt4.code !== 0 ){
      //   process.stderr.write(chalk.bgRed.white(emoji.emojify("[:heavy_multiplication_x: ] Errore generazione dhparam SSL random key per vagrant"))+ "\n");
      //   process.stderr.write(chalk.gray(gSSLcrt4.stdout+"\n"));
      //   process.stderr.write(chalk.red(gSSLcrt4.stderr+"\n"));
      //   process.exit(1);
      // }

    } else {
      process.stdout.write(chalk.yellow(emoji.emojify("[:raised_hand: ] ssl certificate already exist\n")));
    }

    process.stdout.write(chalk.gray(emoji.emojify('[  ] update ansible external role'))+ "\n");
    rUpdate = shell.exec('./scripts/roles_update.sh', {silent:true});
    if ( rUpdate.code !== 0 ){
      process.stderr.write(chalk.bgRed.white(emoji.emojify("[:heavy_multiplication_x: ] Errore download role ansible esterni"))+ "\n");
      process.stderr.write(chalk.gray(rUpdate.stdout+"\n"));
      process.stderr.write(chalk.red(rUpdate.stderr+"\n"));
      process.exit(1);
    }

    shell.cd('server/');

    process.stdout.write(chalk.gray(emoji.emojify("[  ] Vagrant server UP."))+ "\n");

    upVagrant = shell.exec("vagrant up", {silent:true});
    if ( upVagrant.code !== 0 ){
      process.stderr.write(chalk.bgRed.white(emoji.emojify("[:heavy_multiplication_x: ] Errore: "+upVagrant.stderr+ " - "+upVagrant.output)));
      process.exit(1);
    }

    process.stdout.write(chalk.gray(emoji.emojify("[  ] Install Server Required Package and Config.")) + "\n");
    ansibleProc = shell.exec('ansible-playbook -i '+enviroment+'.hosts site.yml', {silent:true});
    if ( ansibleProc.code !== 0 ){
      process.stderr.write(chalk.bgRed.white(emoji.emojify("[:heavy_multiplication_x: ] Errore ansible site.yml"))+ "\n");
      process.stderr.write(chalk.gray(ansibleProc.stdout+"\n"));
      process.stderr.write(chalk.red(ansibleProc.stderr+"\n"));

      process.exit(1);
    }

    process.stdout.write(chalk.bgGreen.black(emoji.emojify('[:heavy_check_mark: ] Vagrant server installation packages completed.' + "\n")));

    shell.cd('..');

  } else {
    process.stdout.write(chalk.gray(emoji.emojify("[:world_map: ] Find vagrant server: "+vagrantCode+". Skip creation." ))+ "\n");
    // non funziona vagrant up by name or uuid
    // var upVagrant = shell.exec("vagrant up "+vagrantCode);

    shell.cd('server/');

    upVagrant = shell.exec("vagrant up", {silent:true});
    if ( upVagrant.code !== 0 ){
      process.stderr.write(chalk.bgRed.white(emoji.emojify("[:heavy_multiplication_x: ] Errore ansible site.yml")) + "\n");
      process.stderr.write(chalk.gray(ansibleProc.stdout+"\n"));
      process.stderr.write(chalk.red(ansibleProc.stderr+"\n"));
      process.exit(1);
    }

    process.stdout.write(chalk.bgGreen.black(emoji.emojify('[:heavy_check_mark: ] Vagrant server UP!'))+ "\n");

    shell.cd('..');
  }

} else {

    process.stdout.write(chalk.gray(emoji.emojify("[  ] Install Server Required Package and Config."))+ "\n");

    ansibleProc = shell.exec('ansible-playbook -i '+enviroment+'.hosts site.yml', {silent:true});
    if ( ansibleProc.code !== 0 ){
      process.stderr.write(chalk.bgRed.white(emoji.emojify("[:heavy_multiplication_x: ] Errore: "+ansibleProc.stderr+ " - "+ansibleProc.output))+ "\n");
      process.exit(1);
    }

}
