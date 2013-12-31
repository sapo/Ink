# Ink.js 

[Ink](http://ink.sapo.pt/) comes with a collection of [UI components](http://ink.sapo.pt/js/ui/) out of the box. 
These components depend on [Ink's JavaScript core](http://ink.sapo.pt/js/core/), which provides a set of methods and modules that give developers the ability to extend the features of the framework.

Documentation for Ink's JavaScript core comes in two flavours, a simpler version to get you up and running as quickly as possible, and a more technical in depth version for when you want to go beyond our examples, these can be found at http://ink.sapo.pt/js/core/ and http://js.ink.sapo.pt/docs/ respectively.
 
This repo provides you with information on how Ink's JavaScript core is organised.
 
It all starts with __Ink__, from there you will find our main module, __the core__, and all of the relevant namespaces.

## Organization

* Ink/ - It's where you can find all the source code 

## Ink Namespaces 
 * Dom - modules to work with the DOM and Events 
 * Net - provides communication modules to make AJAX and JSONP requests
 * Util - random utility modules 
 * UI - Where all [UI modules](http://ink.sapo.pt/js/ui) live
 * Ext - Your extensions created with `Ink.createExt()` go here

A global variable named __Ink__ provides the methods to create and load all of the modules. 
 
Namespaces and modules mirror the structure of the filesystem keeping everything coordinated and where you'd expect it to be.
 
The __lib.js__ file is in a numbered directory to prevent collisions, this way different versions of the same module in the same page will not break any existing code, the following sections show how to avoid collisions altogether.

Ex: 
* /Ink/1/ exposes `window.Ink` 
* /Ink/Dom/Element/1/ exposes `Ink.Dom.Element` and `Ink.Dom.Element_1` with methods to manipulate DOM 
* /Ink/Dom/Event/1/ exposes `Ink.Dom.Event` and `Ink.Dom.Event_1` with methods to manipulate Events
* /Ink/Net/Ajax/1/ exposes `Ink.Net.Ajax` and `Ink.Net.Ajax_1` to make AJAX requests 

## Using modules 
 
There may be times when you need to use an older version of a component in the same instance that a newer one has been loaded. Ink provides a couple of methods that mitigate problems involving namespace collisions when loading modules, namely:

`Ink.requireModule()` - request a module if it's not loaded yet.
`Ink.getModule()` - return a module that has already been loaded (not recommended)

Ex: 
```javascript
Ink.requireModules(['Ink.Namespace.ModuleName_version'], function(ModuleName) {
    ModuleName.moduleMethod('arg1', 'arg2');
});
```

or 

```javascript
var ModuleName = Ink.getModule('Ink.Namespace.ModuleName', version);
ModuleName.moduleMethod('arg1', 'arg2');
```


## Creating modules 

To create a module, use the `Ink.createModule` function. It takes the module name, its version and dependencies, and a function which takes your dependencies, and from which you return the module. You need this function because Ink may load your dependencies asynchronously.

Take a look at our samples on __/Ink/Namespace/ClassModule/__ and __/Ink/Namespace/StaticModule/__

To put it simply:

```javascript
Ink.createModule(
    'Ink.Namespace.ModuleName', 
    'version', 
    ['Ink.Namespace.Dependency1_version', 'Ink.Namespace.Dependency2_version'], 
    function(Dependency1, Dependency2) {
        var ModuleName = {
            // __...your code here...__
        };

        return ModuleName;
    }
);
```

Put your module in your server tree, in the `src/js/Ink/` folder (see below how to tell Ink where to find your module in other folders), in its namespace folder(s), inside a folder with the version number, in a file named `lib.js`.

Example: If your module is `Ink.Foo.Bar.Baz` version `1`, put it in `Ink/Foo/Bar/Baz/1/lib.js`.

### How to tell Ink where to find my custom modules?

__Ink__ can be instructed to find specific modules or namespaces in specific paths. Meet `Ink.setPath()`.

Example: you create a module named `Ink.Foo.Bar.Baz`, version 1, and store it in `/path/to/InkFooModules/Bar/Baz/1/lib.js` on your server. You can call `Ink.setPath('Ink.Foo', '/path/to/InkFooModules/')` to tell Ink where to find every module under the `Ink.Foo` namespace.

After calling `Ink.setPath`, you can use Ink.requireModules, and __Ink__ will go to the correct directory.

