# Welcome to [Ink](http://ink.sapo.pt)

Ink is an interface kit for quick development of web interfaces, simple to use and expand on. It uses a combination of HTML, CSS and JavaScript to offer modern solutions for building layouts, display common interface elements and implement interactive features that are content-centric and user friendly for both your audience and your designers & developers.

Read the full documentation here: [http://ink.sapo.pt](http://ink.sapo.pt)

## This branch is used for Inks documentation only
If you find any inaccuracies or problems in our documentation this is the place to get them fixed. We'll only accept pull request related to the documentation on this branch. 

## Installing Jekyll and required build tools

### On Windows

- [Running Jekyll on windows](https://github.com/juthilo/run-jekyll-on-windows/). Read and follow this how-to thoroughly and you'll save yourself hours of frustration.
- [Install node.js](http://nodejs.org/) This is required to run our build scripts.
- Install the [Compass](http://compass-style.org/) ruby gem by running following command on the command line ``gem install compass``
- Install the [grunt-cli](https://github.com/gruntjs/grunt-cli) by running the following command on the command line ``npm install -g grunt-cli``
- Install all other dependencies by running the ``npm install`` command inside Inks folder.

------


### On OS X / Linux

- Install [Jekyll](http://jekyllrb.com/) by running this command on a terminal: ``gem install jekyll``
- Install the [Compass](http://compass-style.org/) ruby gem by running following command a terminal ``gem install compass``
- Install the [grunt-cli](https://github.com/gruntjs/grunt-cli) by running the following command a terminal ``npm install -g grunt-cli``
- Install all other dependencies by running the ``npm install`` command inside Inks folder.

------

## Getting stuff running

Inside Inks folder (this would be wherever you cloned the project to), run ``jekyll serve`` and you'll get your own local instance of our documentation site running on [http://localhost:4000](http://localhost:4000)
You can get Jekyll to watch for changes on the sites files and update the local instance by running ``jekyll serve --watch``. 