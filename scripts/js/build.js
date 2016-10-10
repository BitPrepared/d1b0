var program = require('commander');
var shell = require('shelljs');
var chalk = require('chalk');
var co = require('co');
var prompt = require('co-prompt');

  // .arguments('<file>')
  // .option('-u, --username <username>', 'The user to authenticate as')
  // .option('-p, --password <password>', 'The user\'s password')
program
  .usage('[options] <enviroment ...>')
  // .option('-u, --username <username>', 'The user to authenticate as')
  .arguments('<enviroment>')
  .action(function(enviroment) {

    shell.mkdir('-p',['applications/assets/icons']);

    shell.echo('Build Server Vagrant ('+enviroment+')');

    if ( enviroment === 'dev' ){

      vagrantId = shell.exec("vagrant global-status | grep d1b0server | awk '{ print $1}'", {silent:true});

      var vagrantCode;
      if ( vagrantId.code !== 0 ){
        //ERRORE
        process.stderr.write(chalk.red("Errore: "+vagrantId.stderr+ " - "+vagrantId.output));
        process.exit(1);
      } else {
        vagrantCode = vagrantId.output.trim();
      }

      if ( !vagrantCode ) {
        process.stdout.write(chalk.bold.cyan("Vagrant server not found")+"\n");

        shell.exec('./scripts/generatekey.sh');
        shell.exec('./scripts/generateSSL.sh');
        shell.exec('./scripts/roles_update.sh');

        shell.cd('server/');

        upVagrant = shell.exec("vagrant up", {silent:true});
        if ( upVagrant.code !== 0 ){
          //ERRORE
          process.stderr.write(chalk.red("Errore: "+upVagrant.stderr+ " - "+upVagrant.output));
          process.exit(1);
        }

        process.stdout.write("Vagrant server UP.\n");

        shell.echo('Install Server Required Package and Config');
        ansibleProc = shell.exec('ansible-playbook -i '+enviroment+'.hosts site.yml');
        if ( ansibleProc.code !== 0 ){
          //ERRORE
          process.stderr.write(chalk.red("Errore ... "));
          process.exit(1);
        }

        process.stdout.write("Vagrant server installation packages completed.\n");

        shell.cd('..');

      } else {
        process.stdout.write("Find vagrant server: "+vagrantCode+". Skip creation.\n");
        // non funziona vagrant up by name or uuid
        // var upVagrant = shell.exec("vagrant up "+vagrantCode);

        shell.cd('server/');

        upVagrant = shell.exec("vagrant up", {silent:true});
        if ( upVagrant.code !== 0 ){
          //ERRORE
          process.stderr.write(chalk.red("Errore: "+upVagrant.stderr+ " - "+upVagrant.output));
          process.exit(1);
        }

        process.stdout.write("Vagrant server UP.\n");

        shell.cd('..');
      }

    } else {

        shell.echo('Install Server Required Package and Config');
        ansibleProc = shell.exec('ansible-playbook -i '+enviroment+'.hosts site.yml', {silent:true});
        if ( ansibleProc.code !== 0 ){
          //ERRORE
          process.stderr.write(chalk.red("Errore ... "));
          process.exit(1);
        }

        shell.echo('Completed');

    }

  })
  .parse(process.argv);


if (!process.argv.slice(2).length) {
  program.outputHelp(make_red);
}

function make_red(txt) {
  return chalk.red(txt); //display the help text in red on the console
}

//console.log(shell.pwd());
