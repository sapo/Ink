(function() {

    'use strict';

    /*jshint browser:false, node:true */

    return; // TODO DISABLES SYMLINK-RELATED TASKS


    var fs      = require('fs'),
        myUtils = require('./utils');



    var updateLatestSymLink = function(dir, deleteIt) {
        fs.readdir(dir, function(err, files) {
            if (err) { throw err; }

            var from = [dir, 'lib.js'].join('/');

            if (files.indexOf('lib.js') !== -1) {
                fs.unlinkSync([dir, 'lib.js'].join('/'));

                if (deleteIt) {
                    return;// console.log('X ' + from);
                }
            }

            var versions = [];
            for (var i = 0, f = files.length; i < f; ++i) {
                var v = files[i];
                v = parseInt(v, 10);
                if (isNaN(v)) { continue; }
                versions.push(v);
            }
            versions.sort();
            var highest = versions.pop();

            var to   = [highest, 'lib.js'].join('/');
            //console.log(from, '->', to);

            fs.symlink(to, from, 'file', function(err) {
                if (err) { throw err; }
            });
        });
    };



    var op = process.argv[2];

    var supportedOps = ['update', 'delete'];

    if (supportedOps.indexOf(op) === -1) {
        console.error('Syntax is: node manageSymLinks.js [update|delete]');
        process.exit(1);
    }

    var rootDir = './';
    var deleteThem = op === 'delete';

    var dirs = myUtils.loadJSON('serverUtils/moduleDirs.json');
    var dir;
    for (var i = 0, f = dirs.length; i < f; ++i) {
        dir = rootDir + dirs[i];
        updateLatestSymLink(dir, deleteThem);
    }

})();
