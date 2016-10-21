var chalk = require('chalk');
var emoji = require('node-emoji');


process.stdout.write(chalk.bgGreen.black(emoji.emojify('[:heavy_check_mark: ] Css compiled!'))+ "\n");
