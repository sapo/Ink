#Welcome to [Ink](http://ink.sapo.pt) [![Build Status](https://travis-ci.org/sapo/Ink.svg?branch=develop)](https://travis-ci.org/sapo/Ink)

Ink is an interface kit for quick development of web interfaces, simple to use and expand on. It uses a combination of HTML, CSS and JavaScript to offer modern solutions for building layouts, display common interface elements and implement interactive features that are content-centric and user friendly for both your audience and your designers & developers.

Ink is part of [SAPOs Open Source Software initiative](http://oss.sapo.pt).

## Current Version: 3.0.3

You can read our full documentation, examples and recipes in http://ink.sapo.pt

## Getting started

Let's get you started with Ink right away. Here's what you need to know and do:

1. Download the [latest](https://github.com/sapo/Ink/archive/3.0.3.zip) release.
2. Check the recipes we provide in the ```dist/cookbook``` folder and choose one to start from or use ```quick-start.html``` as a blank slate.
3. Make sure you have `ink.css`, `ink-ie.css` and `ink.js` included somewhere in the `<head>`.
4. Add your own css and scripts to carry all your project-specific changes. You can use ```quick-start.css``` from the ```dist/css``` folder. It allready contains the same media queries as Ink.
5. Keep coming back to the documentation to help you along the way.
6. That's it! You'll see how easy it is once you pick it up.

##Repository

###Branches

* **[master](https://github.com/sapo/Ink/tree/master)** -  The master branch contains the latest release as its HEAD and all previous releases as tags named as the version numbers.
* **[staging](https://github.com/sapo/Ink/tree/staging)** - The staging branch is a semi-stable branch containing code from the develop branch which is under testing and will, eventually, go into the next release.
* **[develop](https://github.com/sapo/Ink/tree/develop)*** - The develop branch contains our latest code that will eventually lead to a new release and tag on [master](https://github.com/sapo/Ink/tree/master).

**\* This branch is very likely to contain code that is not fully functional or documented. Support requests for problems with this branch will have the lowest priority so, use at your own risk.**

___

###Structure

Since version 2.3.0 we've moved things around in order keep source code from distibution code separated:

* **JS**
  ```
  dist/js
  ```
* **CSS**
  ```
  dist/css
  ```
* **Sass source**
  ```
  src/sass
  ```
* **Js source**
  ```
  src/js
  ```

We've also unified our build system using [Grunt](http://gruntjs.com/) and [Bower](http://bower.io/). So, building from source is now a lot easier.

---

##Building from source

###Required tools
If you want to build from our source code, you'll need to install a few things:
* [Node.js](http://nodejs.org/)
* [Ruby](https://www.ruby-lang.org/en/downloads/)
* [Compass](http://compass-style.org/)
* [Grunt](http://gruntjs.com/)
* [Bower](http://bower.io/)



* #### OS X
  * **Install Homebrew:**
  ```
  ruby -e "$(curl -fsSL https://raw.github.com/Homebrew/homebrew/go/install)"
  ```
  * **Install Node.js:**
  ```
  brew install node
  ```
  * **Install Grunt:**
  ```
  npm install -g grunt-cli
  ```
  * **Install Bower:**
  ```
  npm install -g bower
  ```
  * **Install Compass:**
  ```
  sudo gem update --system && sudo gem install compass
  ```
  * **Move into Inks folder and install remaining build tools:**
  ```
  npm install
  ```

* #### Ubuntu
  * **Install Node.js**
  ```
  sudo apt-get install python-software-properties python g++ make
  sudo add-apt-repository ppa:chris-lea/node.js
  sudo apt-get update
  sudo apt-get install nodejs
  ```
  * **Install Grunt:**
  ```
  sudo npm install -g grunt-cli
  ```
  * **Install Bower:**
  ```
  sudo npm install -g bower
  ```
  * **Install Ruby:**
  ```
  sudo apt-get install ruby rubygems
  ```
  * **Install Compass:**
  ```
  sudo gem install compass
  ```
  * **Move into Inks folder and install remaining build tools:**
  ```
  npm install
  ```


* #### Windows

If you don't need to rebuild Javascript code you can just use [Scout](http://mhs.github.io/scout-app/) which is a nice, free and cross platform Sass/Compass compiler.
  
Building on Windows is a lot tricker so we won't get into details on how to install all the required tools. Instead here's a list of usefull pages you'll need to read while trying this endeavour:

  * [Getting started with Sass and Compass](http://thesassway.com/beginner/getting-started-with-sass-and-compass)
  * [Grunt](http://gruntjs.com/frequently-asked-questions)


### Building
Grunt exposes these build tasks:
* ```grunt``` - Gets third party dependencies, deletes previously built js and css, recompiles and minifies the css, rebuilds and minifies the js bundle files.
* ```grunt test``` - Runs Inks js test suite.
* ```grunt css``` - Deletes previously built css, recompiles and minifies the css.
* ```grunt js``` - Deletes previously built js, builds and minifies the js bundle files.
* ```grunt watch``` - Watches for changes in either css or js files and calls ```grunt js``` and ```grunt css```
* ```grunt watch:css``` - Watches for changes in Sass files and calls ```grunt css```
* ```grunt watch:js``` - Watches for changes in JS files and calls ```grunt js```

---

## Documentation
The documentation is no longer distributed with Inks releases. We've completely rewritten or documentation and are now running our site on Github pages using Jekyll.

If you wish to contribute to the documentation you can find it in the ```gh-pages``` branch of this repository:
```
git checkout -b origin/gh-pages gh-pages
```

## Ink is built with help from these wonderfull projects:

**CSS Generation**
+ [Sass](http://sass-lang.com/)
+ [Compass](http://compass-style.org/)

**Typography and Icons**
+ [FontAwesome](http://fortawesome.github.io/Font-Awesome/)
+ [Roboto Font](https://www.google.com/fonts/specimen/Roboto)

**Browser feature detection**
+ [Modernizr](http://modernizr.com/)

**Build system**
+ [Node.js](http://nodejs.org/)
+ [Grunt](http://gruntjs.com/)
+ [Bower](http://bower.io/)

## Versions
* [Ink 3.0.3](https://github.com/sapo/Ink/archive/3.0.3.zip)
* [Ink 3.0.2](https://github.com/sapo/Ink/archive/3.0.2.zip)
* [Ink 3.0.1](https://github.com/sapo/Ink/archive/3.0.1.zip)
* [Ink 3.0.0](https://github.com/sapo/Ink/archive/3.0.0.zip)
* [Ink 2.3.1](https://github.com/sapo/Ink/archive/2.3.1.zip)
* [Ink 2.3.0](https://github.com/sapo/Ink/archive/2.3.0.zip)
* [Ink 2.2.1](https://github.com/sapo/Ink/archive/2.2.1.zip)
* [Ink 2.2.0](https://github.com/sapo/Ink/archive/2.2.0.zip)
* [Ink 2.1.1](https://github.com/sapo/Ink/archive/2.1.1.zip)
* [Ink 2.1.0](https://github.com/sapo/Ink/archive/2.1.0.zip)
* [Ink 2.0.0](https://github.com/sapo/Ink/archive/2.0.0.zip)
* [Ink 1.1.0](https://github.com/sapo/Ink/archive/1.1.0.zip)

## Projects using Ink

You can check out a list of projects we find around the web that are using Ink in some form [here](https://github.com/sapo/Ink/wiki/Projects-using-Ink).
