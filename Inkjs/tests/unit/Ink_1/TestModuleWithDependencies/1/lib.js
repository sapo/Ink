
Ink.createModule('TestModuleWithDependencies', 1, ['TestModule_1'], function (TestModule) {
    return {
        'hello': 'dependencies',
        TestModule: TestModule
    };
});
