'use strict';

/*jshint browser:false, node:true */



var fs       = require('fs'),
    myUtils  = require('./utils');



var files = myUtils.loadJSON('./serverUtils/moduleFiles.json');

var file, minFile, i, f, l;
for (i = 0, f = files.length; i < f; ++i) {
    file = files[i];
    l = file.length;
    minFile = [file.substring(0, l - 3), '.min.js'].join('');
    try {
        fs.unlinkSync(minFile);
    } catch (ex) {}
}
