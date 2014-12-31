(function () {
    var rModName = /# module: (.*?)$/;
    var lastModule;
    qunitTap(QUnit, function printer(text) {
        if (window.console && window.console.log) {
            if (rModName.test(text)) {
                var modName = text.match(rModName)[1];
                if (modName === 'undefined' || modName === lastModule) {
                    return;
                } else {
                    lastModule = modName
                }
            }
            console.log(text)
        }
    });
}());
