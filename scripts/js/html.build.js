var chalk = require('chalk');
var emoji = require('node-emoji');
var shell = require('shelljs');

process.stdout.write(chalk.bgGreen.black( emoji.emojify('[:heavy_check_mark: ] Html compiled!')) + "\n" );
