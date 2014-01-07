(function() {

    'use strict';

    /*jshint browser:false, node:true */



    /* dependency modules */
    var ls      = require('./ls'),
        fs      = require('fs');



    var paths = [];

    ls({
        path: './tests/',
        onFile: function(o) {
            var name = o.name;
            if (name.lastIndexOf('.xml') === -1) { return; }
            paths.push(o.path);
        },
        onComplete: function(err/*, o*/) {
            if (err) {
                return console.log(err);
            }

            //console.log(paths);
            paths.forEach(function(path) {
                fs.unlink(path);
                console.log('- ' + path);
            });
            console.log('DONE');
        }
    });

})();
