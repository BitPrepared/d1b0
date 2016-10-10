var program = require('commander');
var shell = require('shelljs');
var chalk = require('chalk');
var co = require('co');
var prompt = require('co-prompt');
var rimraf = require('rimraf');

program
  .usage('[options] <enviroment ...>')
  .arguments('<enviroment>')
  .action(function(enviroment) {

    shell.mkdir('-p',['applications/assets/icons']);

    shell.echo('Build Server Vagrant ('+enviroment+')');

    if ( enviroment === 'dev' ){

      // SSH
      rimraf('server/plays/ssh/*.key');
      rimraf('server/plays/ssh/*.pub');

      // SSL
      rimraf('server/plays/ssl/*.key');
      rimraf('server/plays/ssl/*.crt');
      rimraf('server/plays/ssl/*.pem');

      // ANSIBLE ROLE
      rimraf('server/roles/external/*');

      shell.cd('server');

      destroyVagrantServer = shell.exec('vagrant destroy -f');

      if ( destroyVagrantServer.code !== 0 ){
        //ERRORE
        process.stderr.write(chalk.red("Errore: "+destroyVagrantServer.stderr+ " - "+destroyVagrantServer.output));
        process.exit(1);
      }

      shell.cd('..');

    } else {

        //NOTHING ???

    }

  })
  .parse(process.argv);


if (!process.argv.slice(2).length) {
  program.outputHelp(make_red);
}

function make_red(txt) {
  return chalk.red(txt); //display the help text in red on the console
}
