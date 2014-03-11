/*jshint node:true*/
'use strict';

var fsExtra = require('fs-extra');
var async = require('async');
var path = require('path');
var fs = require('fs');

if (process.argv.length === 4) {
    var uiFolder = process.argv[2] // Ink/Inkjs/Ink/UI
    var jsFolder = process.argv[3] // Ink/js
} else {
    console.log('Usage: node serverUtils/updateUi.js <uiFolder> <jsFolder>');
    process.exit(1);
}

// { from: ..., to: ...} objects for describing the copies we are going to do
var copyOps = [];

// iterate UI module folders
fs.readdirSync(uiFolder).forEach(function (module) {
    // iterate versions
    fs.readdirSync(path.join(uiFolder, module)).forEach(function (version) {
        if (+version === NaN) { return; }  // "sample", "test" folders

        // now we have module and version, generate the filename
        var copyOp = {};
        copyOp.from = path.join(uiFolder, module, version, 'lib.js');
        if (version !== '1') {
            copyOp.to = 'ink.' + module.toLowerCase() + '-' + version + '.js';
        } else {    // When version is '1', no need to add the version to the fname.
            copyOp.to = 'ink.' + module.toLowerCase() + '.js';
        }

        copyOp.to = path.join(jsFolder, copyOp.to);

        copyOps.push(copyOp);
    });
});

// returns true iif copy was successful
var cpSync = function(from, to) {
    return sh.exec(['cp ', from, ' ', to].join('')).code === 0;
};

// Copy all the things!
async.series(copyOps, function (next) {
    fsExtra.copy(copyOp.from, copyOp.to, function (err) {
        if (err) {
            console.log(['error copying ', copyOp.from, ' to ', copyOp.to, '!'].join(''));
            console.log(err);
        }
        next();
    });
});

