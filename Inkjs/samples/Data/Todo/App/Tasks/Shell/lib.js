Ink.createModule('App.Tasks.Shell', '1', ['App.Tasks'], function(app) {
    var Module = function() {
        var self=this;
        
        this.moduleName =  'App.Tasks.Shell';
        this.definedRoutes = app.definedRoutes;
        this.modalModule = app.modalModule;
        this.alertModule = app.alertModule;
        this.infoModule = app.infoModule;
        this.appTitle = app.appTitle;
    };

    Module.prototype.afterRender = function() {
        new Ink.UI.Toggle('#mainMenuTrigger');
        app.signals.shellRendered.dispatch();
    };

    return new Module();
});
