(function() {

    'use strict';

    /*jshint browser:false, node:true */



    /* dependency modules */
    var ls      = require('./ls'),
        myUtils = require('./utils'),
        fs      = require('fs'),
        util    = require('util');



    var files = {
        unit: [],
        flow: []
    };

    ls({
        path: './tests/',
        onFile: function(o) {
            var name = o.name;
            var i = name.lastIndexOf('.');
            var ext = name.substring(i+1);
            name = name.substring(0, i);
            //console.log(name, ext);
            if (ext === 'html') {
                files.unit.push(name);
            }
            else if (ext === 'js' && name !== 'qunitExtras') {
                files.flow.push(name);
            }
        },
        onComplete: function(err, o) {
            myUtils.saveJSON('serverUtils/testFiles.json', files);
        }
    });

})();
