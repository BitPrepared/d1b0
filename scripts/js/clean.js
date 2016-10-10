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

      // "clean:key": "rimraf server/plays/ssh/*.key && rimraf server/plays/ssh/*.pub",
      // "clean:ssl": "rimraf server/plays/ssl/*.key && rimraf server/plays/ssl/*.crt && rimraf server/plays/ssl/*.pem",

      // npm run clean:key && npm run clean:ssl && rimraf server/roles/external/*
      // &&
      //cd server && vagrant destroy -f

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

//console.log(shell.pwd());
