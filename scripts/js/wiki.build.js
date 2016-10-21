var chalk = require('chalk');
var emoji = require('node-emoji');
var shell = require('shelljs');
var fs = require('fs');

if (!fs.existsSync('./wiki')) {
  process.stdout.write(chalk.gray(emoji.emojify('[  ] Download github wiki')) + "\n");
  gitClone = shell.exec("git clone https://github.com/BitPrepared/d1b0.wiki.git wiki", {silent:true});
  if ( gitClone.code !== 0 ){
    process.stderr.write(chalk.bgRed.white(emoji.emojify("[:heavy_multiplication_x: ] Errore clone wiki")) + "\n" );
    process.stderr.write(chalk.gray(gitClone.stdout+"\n"));
    process.stderr.write(chalk.red(gitClone.stderr+"\n"));
    process.exit(1);
  }
}

if (fs.existsSync('./wiki/README.adoc')) {

  process.stdout.write(chalk.gray(emoji.emojify('[  ] Build wiki')) + "\n");
  gitbook = shell.exec("gitbook build", {silent:true});
  if ( gitbook.code !== 0 ){
    process.stderr.write(chalk.bgRed.white(emoji.emojify("[:heavy_multiplication_x: ] Errore build wiki con gitbook")) + "\n" );
    process.stderr.write(chalk.gray(gitbook.stdout+"\n"));
    process.stderr.write(chalk.red(gitbook.stderr+"\n"));
    process.exit(1);
  }

  process.stdout.write(chalk.bgGreen.black( emoji.emojify('[:heavy_check_mark: ] Wiki compiled!')) + "\n" );

} else {

  process.stdout.write(chalk.yellow(emoji.emojify("[:raised_hand: ] Missing index on wiki! ")) + "\n");
  process.stdout.write(chalk.gray(emoji.emojify('[  ] Skip generation wiki')) + "\n");

}
