'use strict';

/*jshint browser:false, node:true */

var fs          = require('fs'),
    assert      = require('assert'),
    Mocha       = require('mocha'),
    XUnit2      = Mocha.reporters.XUnit2 = require('./xunit2'),
    myUtils     = require('./utils'),
    selGridTest = require('./selGridTest');



var n = function() {};
var p = function(r) { console.log( Object.keys(r) ); };
var s = function(r) { console.log( JSON.stringify(r, null, '\t') ); };
var v = function(r) { console.log( r.value ); };
var e = function() { console.log('ERR!'); };
var save = function(fn, data) { fs.writeFile(fn, data); };



var prefix = 'http://127.0.0.1:8181/';
//START: nohup python -m SimpleHTTPServer 8181 &
//END:   fg ctrl+C



// https://github.com/Camme/webdriverjs/tree/master/node_modules/webdriverjs/lib/commands



var runUnitTest = function(testName, browsers, local, nextCb) {
    console.log('** runUnitTest ' + testName + '... **\n');

    var testFn = function(br, brName) {
        var testNameWithBr = [testName, '@', brName].join('');
        var htmlPath = [prefix, 'tests/unit/', testName, '.html?brname=', brName].join('');
        var xmlPath  = ['tests/unit/', testNameWithBr, '.xml'].join('');
        console.log('* unit test: ' + testNameWithBr);

        br.url(htmlPath);
        br.waitFor('#qunitReportIsReady', 10000, n);
        br.execute('return QUnit.report;', function(r) { save(xmlPath, r.value); });
        br.end();
    };

    selGridTest(testFn, browsers, local, nextCb);
};



// QUnit global functions
// http://api.qunitjs.com/category/assert/
// http://nodejs.org/api/assert.html

global.deepEqual      = function() { assert.deepEqual.apply(     assert, arguments); };
global.equal          = function() { assert.equal.apply(         assert, arguments); };
global.notDeepEqual   = function() { assert.notDeepEqual.apply(  assert, arguments); };
global.notEqual       = function() { assert.notEqual.apply(      assert, arguments); };
global.notStrictEqual = function() { assert.notStrictEqual.apply(assert, arguments); };
global.ok             = function() { assert.ok.apply(            assert, arguments); };
global.throws         = function() { assert.throws.apply(        assert, arguments); };



var runFlowTest = function(testName, browsers, local, nextCb) {
    console.log('** runFlowTest ' + testName + '... **\n');
    var testPath = ['tests/flow/', testName, '.js'].join('');

    var testFn = function(br, brName, innerCb) {
        global.br = br;
        global.brName = brName;
        global.modul = function(suiteName) { return suite('flow.' + suiteName + '.' + brName); };
        var testNameWithBr = [testName, '@', brName].join('');
        var xmlPath  = ['tests/flow/', testNameWithBr, '.xml'].join('');
        console.log('* flow test: ' + testNameWithBr);

        var mocha = new Mocha({
            ui:       'qunit',
            reporter: XUnit2.bind(this, fs.createWriteStream(xmlPath, {encoding:'utf8'}), testNameWithBr)
        });
        mocha.addFile(testPath);
        mocha.run(innerCb);
    };

    selGridTest(testFn, browsers, local, nextCb);
};



//runUnitTest('Ink_Dom_Css_1', browsers, local);
//runFlowTest('yahoo',         browsers, local);



// configuration
var config = myUtils.loadJSON('serverUtils/config.json');
var browsers = config.tests.browsers;
var local    = config.tests.runLocalGrid;

var tests = []; // grouping all tests into a single array for serial processing
var files = myUtils.loadJSON('serverUtils/testFiles.json');
files.unit.forEach(function(f) { tests.push([f, 'u']); });
files.flow.forEach(function(f) { tests.push([f, 'f']); });



// treat one test at a time...

(function() {
    var n = tests.length;
    var i = 0;

    console.log(['\nRUNNING ', n, ' TESTS ', (local ? 'LOCALLY' : 'USING GRID'), ' ON BROWSERS: ', browsers, '\n'].join(''));

    var processNextTest = function() {
        console.log('processNextTest');
        var test = tests.shift();
        if (!test) { return console.log('DONE'); }

        console.log( ['RUNNING TEST ', ++i, ' / ', n, ' (', (i/n * 100).toFixed(1), '%)\n'].join('') );

        if (test[1] === 'u') {
            runUnitTest(test[0], browsers, local, processNextTest);
        }
        else {
            runFlowTest(test[0], browsers, local, processNextTest);
        }
    };
    processNextTest();
})();
