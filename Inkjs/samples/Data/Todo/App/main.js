/*
 * Application bootstrap
 * 
 * - Setup Ink lib paths
 * - Require app module 
 * 
 */
Ink.setPath('App', 'App/');
Ink.setPath('Ink', 'libs/Ink/');

Ink.requireModules(['App.Tasks'], function(app) {
    app.run();
});