'use strict';

/*jshint browser:false, node:true */



var fs = require('fs');



var mdl = {};



mdl.loadJSON = function(file) {
    var tree;
    try {
        tree = JSON.parse( fs.readFileSync(file).toString() );
    } catch (ex) {
        console.log(ex);
        throw('Error loading JSON file: ' + file);
    }

    return tree;
};



mdl.saveJSON = function(file, data) {
    fs.writeFileSync(file, JSON.stringify(data, null, '\t'));
};



module.exports = mdl;
