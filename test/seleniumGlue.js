
if (/\?selenium/.test(window.location+'')) {
    (function () {
        window.logsForSelenium = [];
        qunitTap(QUnit, function printer(text) {
            if (window.console && window.console.log) {
                console.log(text)
            }
            window.logsForSelenium.push([].slice.call(arguments)+'');
        });
    }())
}

