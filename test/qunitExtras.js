(function() {

    var o = [];
    var logged;

    var currentModule,   currentTest,
        startTime,       endTime,
        testStartTime,   testEndTime,
        moduleStartTime, moduleEndTime;

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

    var cdata = function(str) {
        return ['<![CDATA[', str, ']]>'].join('');
    };

    QUnit.begin = function() {
        startTime = new Date();
    },

    QUnit.moduleStart = function(module) {
        moduleStartTime = new Date();
        currentModule = module.name;
    };

    QUnit.testStart = function(test) {
        logged = [];
        testStartTime = new Date();
        currentTest = test.name;
    };

    QUnit.log = function(m) {
        if (!m.result) {
            var failureContents = [];

            failureContents = failureContents.concat(['Message: ', m.message, '\n']);

            if (m.expected) {
                failureContents = failureContents.concat(['Expected: ', m.expected, '\n']);
            }
            if (m.actual) {
                failureContents = failureContents.concat(['Result: ', m.actual, '\n']);
            }
            if (m.expected && m.actual) {
                failureContents = failureContents.concat(['Diff: ', QUnit.diff(QUnit.jsDump.parse(m.expected), QUnit.jsDump.parse(m.actual)), '\n']);
            }
            if (m.source) {
                failureContents = failureContents.concat(['Source: ', m.source, '\n']);
            }

            logged = logged.concat(['\t\t<failure message="', currentTest, '" type="error">\n']);
            logged.push( cdata( failureContents.join('') ) );
            logged.push('\t\t</failure>\n');
        }
    };

    QUnit.testDone = function(result) {
        testEndTime = new Date();

        o = o.concat([
            '\t<testcase',
                ' name="', currentTest, '"',
                ' classname="', currentModule, '"',
                ' time="', Number((testEndTime-testStartTime)/1000).toPrecision(), '"'
        ]);

        if (result.failed > 0) {
            o = o.concat([
                '>\n',
                logged.join(''),
                '\t</testcase>\n'
            ]);
        }
        else {
            o.push('/>\n');
        }
    };

    QUnit.moduleDone = function() {
        moduleEndTime = new Date();
    };

    QUnit.done = function(result) {
        endTime = new Date();

        var tests = o.join('');

        o = [
            '<?xml version="1.0"?>\n',
            '<testsuite name="unit tests"',
            '  timestamp="', getTS(startTime), '"',
            //'  hostname=""',
            '  tests="', result.total, '"',
            '  failures="', result.failed, '"',
            '  errors="0"',
            '  time="', Number((endTime - startTime)/1000).toPrecision(), '"',
            '>\n',
            tests,
            '\n</testsuite>\n'
        ];

        // to make the report globally accessible
        QUnit.report = o.join('');

        // so selenium can wait on this element's presence
        var el = document.createElement('div');
        el.id = 'qunitReportIsReady';
        document.body.appendChild(el);
    };

    QUnit._test = QUnit.test;
    QUnit.test = function() {
        var args = Array.prototype.slice.call(arguments);
        args[0] = 'unit.' + args[0] + '.br';
        QUnit._test.apply(QUnit, args);
    };

    var href = location.href;
    var i = href.lastIndexOf('brname=');
    window.brName = href.substring(i+7);

    window.modul = function(nm) { return module('unit.' + nm + '.' + brName); };

})();
