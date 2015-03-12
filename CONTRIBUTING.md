# Issue reporting

* Please be as descriptive as possible about your problem. "Stuff doesn't work" won't help us to help you.
* When reporting an issue, provide a [jsfiddle](http://www.jsfiddle.net) example (or something like it, such as codepen) which reproduces your problem.
* Always state the following key points:
  * What were you trying to accomplish? (Infinite scroll, subscribe to click events, ...)
  * What steps did you take? (Use Ink.Dom.Element.inViewport, try to capture the `domready` event, ...)
  * What results did you expect, instead of what you see now?
  * What version of Ink are you using?


We're a small team and have other projects in our hands so, we'll try to at least give you some feedback on issues, even if it's just a comment on a maximum of 3 business days. Please be patient.

# Contributing with code

We'd love to have your contribution so, here's a quick guide:

1. Fork our repository
2. Do your work on the ```develop``` branch

## JavaScript

2. To make sure your changes don't break anything run ``grunt test``. We'll only accept pull requests that pass unit tests.
3. Add a test for your changes. If you are adding functionality or fixing a bug, you have to write tests!
4. Make sure all tests are passing.
5. Push to your fork and submit a pull request.

### Style guide
1. No tabs. Use four spaces for indentation.
2. Use camel case for method names.
3. Private/internal methods must start with underscore, e.g. ``_onClick: function(e){...}``
4. Component names are camel cased and start with a capital letter, e.g. ``Ink.Ext.MyPhotoGallery``
5. All Ink code should pass `grunt jshint` to ensure code quality and maintainability. Use inline directives only if you have a good reason to.
6. (If you're adding a new component) Add your new components under the ``Ink.Ext`` namespace with [Ink.createExt()](http://ink.sapo.pt/javascript/ink/#Ink_1-Ink_1-createExt):
    ```js
    Ink.createExt('MyAwesomeModule', 1, ['Ink.Dom.Event_1'], function(InkEvent) {

      var MyAwesomeModule = function() {
          this._init();
      };

      MyAwesomeModule.prototype = {
          _init: function() {
              this._doStuff();
          },
          _doStuff: function() {
              alert('Doing stuff in MyAwesomeModule');
              /* ... */
          }
      };
      return MyAwesomeModule;
    });
    ```

## Sass/CSS

1. To Make sure your changes don't break compilation, run ``grunt css``.
5. Push to your fork and submit a pull request. Please be as descriptive as possible about the reason for your changes.

###Style guide

We're not really strict with Sass code but adhering to these guidelines will help us:

1. Two spaces, no tabs.
2. Try to declare each css property on a single line.
3. Use shorthand declarations whenever possible;
4. Check if compass already has a @mixin for what you need before writing new ones.

## Documentation

Our documentation is built with [Jekyll](http://jekyllrb.com/). Get familiar with it.

1. Changes to the documentation must me submitted to [this separate repository](https://github.com/sapo/Ink-doc/).
