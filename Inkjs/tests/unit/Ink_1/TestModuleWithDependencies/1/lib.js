
Ink.createModule('TestModuleWithDependencies', 1, ['TestModule_1'], function (TestModule) {
    ok(true, 'TestModuleWithDependencies loaded');
    return {
        'hello': 'dependencies',
        TestModule: TestModule
    };
});
