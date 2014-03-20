(function () {
    qunitTap(QUnit, function printer(text) {
        if (window.console && window.console.log) {
            console.log(text)
        }
    });
}());
