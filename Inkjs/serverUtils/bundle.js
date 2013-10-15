'use strict';

/*jshint browser:false, node:true */



var fs       = require('fs'),
    async    = require('async'),
    spawn     = require('child_process').spawn,
    myUtils  = require('./utils');


var cfg = myUtils.loadJSON('./serverUtils/config.json');
var files = myUtils.loadJSON('./serverUtils/moduleFiles.json');
var filesToBundle;

var bundleFile = process.argv[2];
var regexp;
var minify = false;
var negates = false;

/*var op = process.argv[2];
var minify = (op && op === 'min');
var bundleFile = (minify ? cfg.bundleMinFile : cfg.bundleFile);*/


// process arguments
var arg;
for (var i = 3, f = process.argv.length; i < f; ++i) {
    arg = process.argv[i];
    switch (arg) {
        case 'minify':  minify  = true; break;
        case 'negates': negates = true; break;
        default:        regexp  = arg;
    }
}


/*console.log('BUNDLE FILE?', bundleFile);
console.log('MINIFY?',  minify ? 'Y' : 'N');
console.log('REGEXP?',  regexp || '');
console.log('NEGATES?', negates ? 'Y' : 'N');*/


if (regexp) {
    regexp = new RegExp(regexp);
}

/*console.log('\nFILES:');
console.log('* ' + files.join('\n* '));*/


// elect files to concatenate
filesToBundle = [];
var file, i, f, l, useIt;
for (i = 0, f = files.length; i < f; ++i) {
    file = files[i];

    useIt = !negates;
    if (regexp) {
        useIt = regexp.test(file);
        if (negates) {
            useIt = !useIt;
        }
    }

    if (!useIt) { continue; }

    //console.log(useIt ? 'Y' : 'N', file);

    filesToBundle.push(file);
}


/*console.log('\nFILES 2:');
console.log('* ' + filesToBundle.join('\n* '));*/


// options
var options = ['-b'],
    command = [],
    cmd;

// If we have the minified flag, minify the bundling instead of beautifying it
if(minify){
    options = ['-e', 'document,window:document,window', '-c', '-m', 'eval=true'];
}


options.push('-o', bundleFile);

cmd = spawn('uglifyjs', filesToBundle.concat(options));
cmd.stdout.on('data', function (data) {
      console.log('stdout: ' + data);
});
cmd.stderr.on('data', function (data) {
    console.log('stderr: ' + data);
});
/*
async.forEachSeries(
    filesToBundle,
    function(f, innerCb) { // for each
        //ws.write('\n// ' + f + '\n'); // uncomment if you want to prefix each file with its comment
        ws.write('\n'); // one file per line
        --left;
        var rs = fs.createReadStream(f, {encoding: 'utf-8'});
        rs.on('end', innerCb);
        rs.pipe(ws, {end: left === 0});
    },
    function(err) { // on all done or error...
        console.log(err ? err : 'Created bundle on ' + bundleFile);
        if(minify){
        spawn('uglifyjs '+bundleFile+' -e document,window:document,window -c -m toplevel=true,eval=true -o '+bundleFile, function(error, stdout, stderr){
            console.log('File minified: ' + bundleFile);
        });
        }
    }
);
*/