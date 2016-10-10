var path = require('path');
var fs = require('fs');
var JSZip = require("jszip");
var zip = new JSZip();

zip.file("Hello.txt", "Hello World\n");

//var img = zip.folder("images");
//img.file("smile.gif", imgData, {base64: true});

zip.generateNodeStream({type:'nodebuffer',streamFiles:true})
    .pipe(fs.createWriteStream('example.zip'))
    .on('finish', function () {
        // JSZip generates a readable stream with a "end" event,
        // but is piped here in a writable stream which emits a "finish" event.
        console.log("example.zip written.");
    });
