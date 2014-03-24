## Note about this branch

The default branch of this repository is `develop`. This is a development branch, which is usable and has the latest features, but is not so stable as `master`, our release branch. Bugfixes are to be pushed to `develop`, where development takes place.

This branch contains built files in the `dist/` folder.


# Welcome to [Ink](http://ink.sapo.pt)

Ink is an interface kit for quick development of web interfaces, simple to use and expand on. It uses a combination of HTML, CSS and JavaScript to offer modern solutions for building layouts, display common interface elements and implement interactive features that are content-centric and user friendly for both your audience and your designers & developers.

Read the full documentation here: [http://ink.sapo.pt](http://ink.sapo.pt). The Ink.js documentation is in [http://js.ink.sapo.pt/docs/](http://js.ink.sapo.pt/docs/) (note: this documentation is for the most recent release, but the development version may have additions).


## Current Version: 2.3.1

*This version is the end of the line for the 2.x.x series*

We've improved things all over Ink, fixed a lot of bugs, added new functions, and the Carousel module. Check out the [changelog](http://ink.sapo.pt/changelog) to know more.

We're moving the CSS generation over to SASS + COMPASS and working on some new exciting stuff.
Stay tuned.

## Repository branches
    
We have three branches on our github repository:

* The **[master](https://github.com/sapo/Ink/tree/master)** branch contains the latest release as its HEAD and all previous releases as tags.
* The **[staging](https://github.com/sapo/Ink/tree/staging)** branch is a semi-stable branch containing code from the develop branch which is going to go into the next release.
* The **[develop](https://github.com/sapo/Ink/tree/develop)** branch contains our latest code that will eventually lead to a new release and tag on **[master](https://github.com/sapo/Ink/tree/master). This branch is likely to contain code that is not fully functional or documented. Use at your own risk.**

To get a specific release clone the repository and use, e.g. `git checkout 1.0.0`.


## Getting started

Let's get you started with Ink right away. Here's what you need to know and do:

1. Download latest build.
2. Open `my-page.html` if you want to serve Ink from your host or `my-cdn-page.html` if you want to serve Ink from our servers.
3. Check the template we provide and remove whatever you don't feel necessary for your project.
4. Make sure you have `ink.css`, `ink-ie.css` and `ink.js` included somewhere in the `<head>`.
5. Add your own stylesheets and scripts to carry all your project-specific changes.
6. Keep coming back to the documentation to help you along the way.
7. That's it! You'll see how easy it is once you pick it up.


# Installation instructions for hacking on Ink

Do this process if you'd like to build your own Ink and hack on the Ink source.

Make sure you have `node` installed. Then, on the root Ink folder of this repo:

    [sudo] npm install -g bower grunt-cli
    bower install
    npm install
    grunt


## JavaScript modules

The Ink JavaScript modules used to be in a separate repository. Now they are in the `src/js` folder. Check out the `README.md` file inside with further information regarding those modules.


## Building from source

If you wish to compile our source code you'll need a couple of things:

+ [Node.js](http://nodejs.org/)
+ [Compass](http://compass-style.org/install/)
+ [Grunt](http://gruntjs.com/getting-started)

After installing these dependencies simple run the `npm install` and `grunt` from your terminal.


## Kudos

Ink is built with help from these wonderfull projects:

**CSS Generation**
+ [Sass](http://sass-lang.com/)
+ [Compass](http://compass-style.org/)

**Typography and Icons**
+ [FontAwesome](http://fortawesome.github.io/Font-Awesome/)
+ [Ubuntu Font](http://font.ubuntu.com/)

**Browser feature detection**
+ [Modernizr](http://modernizr.com/)

**Build system**
+ [Node.js](http://nodejs.org/)
+ [Grunt](http://gruntjs.com/)
+ [Bower](http://bower.io/)

## Versions
* [Ink v2.3.1](https://github.com/sapo/Ink/archive/2.3.1.zip) (current)
* [Ink v2.3.0](https://github.com/sapo/Ink/archive/2.3.0.zip)
* [Ink v2.2.1](https://github.com/sapo/Ink/archive/2.2.1.zip)
* [Ink v2.2.0](https://github.com/sapo/Ink/archive/2.2.0.zip)
* [Ink v2.1.1](https://github.com/sapo/Ink/archive/2.1.1.zip)
* [Ink v2.1.0](https://github.com/sapo/Ink/archive/2.1.0.zip)
* [Ink v2.0.0](https://github.com/sapo/Ink/archive/2.0.0.zip)
* [Ink v1.1.0](https://github.com/sapo/Ink/archive/1.1.0.zip)

## Projects using Ink

You can check out a list of projects we find around the web that are using Ink in some form [here](https://github.com/sapo/Ink/wiki/Projects-using-Ink).

