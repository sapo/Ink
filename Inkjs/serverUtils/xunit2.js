/*jshint browser:false, node:true */

/**
 * Based on:
 * https://raw.github.com/visionmedia/mocha/master/lib/reporters/xunit.js
 *
 *
 *
 * Author: jose.pedro.dias@gmail.com
 *
 *
 *
 * Changes:
 * - instead of logging result XML to console, logs it to a stream
 * - allows setting the suite name
 *
 *
 *
 * How to use:
 *
 * var fs     = require('fs'),
 *     Mocha  = require('mocha'),
 *     XUnit2 = Mocha.reporters.XUnit2 = require('./xunit2');
 *
 * var testPath  = 'tests/abc.js';
 * var xmlPath   = 'tests/abc.xml';
 * var suiteName = 'My ABC Suite';
 * var mocha = new Mocha({
 *     ui:       'qunit',
 *     reporter: XUnit2.bind(this, fs.createWriteStream(xmlPath, {encoding:'utf8'}), suiteName)
 * });
 * mocha.addFile(testPath);
 * mocha.run();
 */



/**
 * Module dependencies.
 */

(function(global) {

var Base   = require('mocha').reporters.Base,
    utils  = require('mocha').utils,
    escape = utils.escape;



/**
 * Save timer references to avoid Sinon interfering (see GH-237).
 */

var Date          = global.Date,
    setTimeout    = global.setTimeout,
    setInterval   = global.setInterval,
    clearTimeout  = global.clearTimeout,
    clearInterval = global.clearInterval;



/**
 * Expose `XUnit2`.
 */

exports = module.exports = XUnit2;


var pad0 = function(v) { return v < 10 ? '0'+v : v; };

var getTS = function(t) {
    var diff = t.getTimezoneOffset() * -1 / 60;

    return [
        t.getUTCFullYear(),        '-',
        pad0(t.getUTCMonth() + 1), '-',
        pad0(t.getUTCDate()),      'T',
        pad0(t.getUTCHours()),     ':',
        pad0(t.getUTCMinutes()),   ':',
        pad0(t.getUTCSeconds()),
        ((diff < 0) ? '-' : '+'),
        pad0(Math.abs(diff)), ':00'
    ].join('');
};

var repStr = function(str, times) {
    return new Array(times+1).join(str);
};


/**
 * Initialize a new `XUnit2` reporter.
 *
 * @param {Stream} outStream
 * @param {String} suiteName
 * @param {Runner} runner
 * @api public
 */

function XUnit2(outStream, suiteName, runner) {
    Base.call(this, runner);
    var stats = this.stats,
        tests = [];

    var log = function(msg) {
        outStream.write(msg + '\n');
    };

    var testFn = function(t) {
        var attrs = {
            classname: t.parent.fullTitle(),
            name:      t.title,
            time:      t.duration / 1000
        };

        if ('failed' === t.state) {
            var err = t.err;
            attrs.message = escape(err.message);
            log(tag('testcase', 1, attrs, false, tag('failure', 2, attrs, false, cdata(err.stack))));
        }
        else if (t.pending) {
            log(tag('testcase', 1, attrs, false, tag('skipped', 2, {}, true)));
        }
        else {
            log(tag('testcase', 1, attrs, true) );
        }
    };

    runner.on('pass', function(test) {
        tests.push(test);
    });

    runner.on('fail', function(test) {
        tests.push(test);
    });

    runner.on('end', function() {
        log('<?xml version="1.0"?>');

        log(tag('testsuite', 0, {
            name:      'flow tests',
            tests:     stats.tests,
            failures:  stats.failures,
            errors:    0,//stats.failures,
            skip:      stats.tests - stats.failures - stats.passes,
            //timestamp: (new Date()).toUTCString(),
            timestamp: getTS(new Date()),
            time:      stats.duration / 1000
        }, false));

        tests.forEach(testFn);
        log('</testsuite>');
        //outStream.close();
    });
}



/**
 * Inherit from `Base.prototype`.
 */

XUnit2.prototype.__proto__ = Base.prototype;



/**
 * HTML tag helper.
 */

function tag(name, indent, attrs, close, content) {
    var end      = (close ? '/>' : '>'),
        numAttrs = Object.keys(attrs),
        s        = [repStr('\t', indent), '<', name];

    if (numAttrs > 0) {
        s.push(' ');
    }

    for (var k in attrs) {
        s = s.concat([' ', k, '="', escape(attrs[k]), '"']);
    }

    s.push(end);

    if (content) {
        s = s.concat([content, '</', name, end]);
    }

    return s.join('');
}



/**
 * Return cdata escaped CDATA `str`.
 */

function cdata(str) {
    return ['<![CDATA[', escape(str), ']]>'].join('');
}


})(this);
