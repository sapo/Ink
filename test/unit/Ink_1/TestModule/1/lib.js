
Ink.createModule('TestModule', 1, [], function () {
    ok(true, 'TestModule loaded');
    return {
        'hello': 'world'
    }
});

