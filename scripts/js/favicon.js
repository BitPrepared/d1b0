var path = require('path');
var fs = require('fs');
var dest = path.resolve('./applications/assets/icons/');

// var tidy = require("tidy-html5").tidy_html5
// var tidyOptions = {
//         'add-xml-decl': false,
//         'input-xml': true,
//         'output-xml': true,
//         'output-html': false,
//         'indent': true,
//         'indent-spaces': 2,
//         'wrap': 0,
//         'vertical-space': true,
//         'quiet': true,
//         'show-info': false,
//         'show-warnings': false,
//     };

var favicons = require('favicons'),
    source = path.resolve('./assets/images/logo.png'),  // Source image(s). `string`, `buffer` or array of `string`
    configuration = {
        appName: "Impresa Luna",
        appDescription: "Gestione imprese",
        developerURL: "https://github.com/BitPrepared/d1b0#readme",
        background: "#fff",             // Background colour for flattened icons. `string`
        path: "/assets/icons/",                      // Path for overriding default icons path. `string`
        display: "standalone",          // Android display: "browser" or "standalone". `string`
        orientation: "portrait",        // Android orientation: "portrait" or "landscape". `string`
        version: "1.0",                 // Your application's version number. `number`
        logging: false,                 // Print logs to console? `boolean`
        online: false,                  // Use RealFaviconGenerator to create favicons? `boolean`
        preferOnline: false,            // Use offline generation, if online generation has failed. `boolean`
        icons: {
            android: true,              // Create Android homescreen icon. `boolean`
            appleIcon: true,            // Create Apple touch icons. `boolean` or `{ offset: offsetInPercentage }`
            appleStartup: true,         // Create Apple startup images. `boolean`
            coast: { offset: 25 },      // Create Opera Coast icon with offset 25%. `boolean` or `{ offset: offsetInPercentage }`
            favicons: true,             // Create regular favicons. `boolean`
            firefox: true,              // Create Firefox OS icons. `boolean` or `{ offset: offsetInPercentage }`
            windows: true,              // Create Windows 8 tile icons. `boolean`
            yandex: true                // Create Yandex browser icon. `boolean`
        }
    },
    callback = function (error, response) {
        if (error) {
            console.log(error.status);  // HTTP error code (e.g. `200`) or `null`
            console.log(error.name);    // Error name e.g. "API Error"
            console.log(error.message); // Error description e.g. "An unknown error has occurred"
        }
        //console.log(response.images);   // Array of { name: string, contents: <buffer> }
        //console.log(response.files);    // Array of { name: string, contents: <string> }
        //console.log(response.html);     // Array of strings (html elements)
        error = function (err) {
          if (err) {
            console.log(err);
          }
        };

        var len = response.images.length;
        for (var i = 0; i < len; i++) {
          fs.writeFile(path.join(dest,response.images[i].name), response.images[i].contents, error);
        }
        var logStream = fs.createWriteStream(path.join(dest,'icons.html'), {flags:'a'});
        var lenHtml = response.html.length;
        var html = "";
        for (var j = 0; j < lenHtml; j++) {
          html += response.html[j]+"\n";
        }
        logStream.write(html);
    };

favicons(source, configuration, callback);
