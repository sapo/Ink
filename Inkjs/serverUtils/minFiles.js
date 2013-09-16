(function() {

    'use strict';

    /*jshint browser:false, node:true */



    var UglifyJS = require('uglify-js'),
        fs       = require('fs'),
        myUtils  = require('./utils');



    var files = myUtils.loadJSON('./serverUtils/moduleFiles.json');

    var file, minFile, res, i, f, l;
    for (i = 0, f = files.length; i < f; ++i) {
        file = files[i];
        l = file.length;
        minFile = [file.substring(0, l - 3), '.min.js'].join('');
        res = UglifyJS.minify(file);
        fs.writeFileSync(minFile, res.code);
    }

})();
