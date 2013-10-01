'use strict';

/*jshint browser:false, node:true */

var webdriverjs = require('webdriverjs');



// defaults
var HOST = 'vmsel-hub.selenium.bk.sapo.pt';
var PORT = 4444;
var BROWSER_PROFILES = {
    ff:   ['firefox'],
    ch:   ['chrome'],
    ie10: ['internet explorer', '10'],
    ie9:  ['internet explorer', '9'],
    ie8:  ['internet explorer', '8'],
    ie7:  ['internet explorer', '7']
};
var DEFAULT_BROWSERS = 'ff ch ie10 ie9 ie8 ie7';



// run fn on desired browsers...
var selGridTest = function(fn, browsers, inLocalHost, qUnit) {
    if (!browsers) {
        browsers = DEFAULT_BROWSERS;
    }
    if (typeof browsers === 'string') {
        browsers = browsers.split(' ');
    }
    if (inLocalHost) {
        HOST = 'localhost';
    }

    /*console.log([
        'browsers: ', browsers.join(' '), '\n',
        'host:     ', HOST, '\n',
        'port:     ', PORT
    ].join(''));*/

    browsers.forEach(function(brName/*, index*/) {
        var prof = BROWSER_PROFILES[brName];
        if (!prof) {
            throw new Error('Do not have a profile for the browser "' + brName + '"!');
        }
        var cfg = {
            logLevel: 'silent',
            host: HOST,
            port: PORT,
            platform: 'ANY',
            desiredCapabilities: {
                browserName: prof[0]
            }
        };
        if (prof[1]) {
            cfg.desiredCapabilities.version = prof[1];
        }

        var br = webdriverjs.remote(cfg);

        //console.log('TESTING ' + brName + '...');

        br.testMode();
        br.init();

        fn(br, brName, qUnit);
    });
};



module.exports = selGridTest;
