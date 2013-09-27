'use strict';

/*jshint browser:false, node:true */

/* dependency modules */
var	fs = require('fs');



/* auxiliary function to return n characters/strings - used in nodeToString */
var indent = function(ch, n) {
	var	r = new Array(n),
		i = 0;
	while (i < n) {	r[i++] = ch;	}
	return r.join('');
};



/* custom toString function no o prints an indented, pretty tree */
var nodeToString = function(lvl) {
	var res = [];

	if (lvl === undefined) {	lvl = 0;							}
	else {						res.push(	indent(' ', lvl)	);	}

	res.push(this.name);

	if (this.isDir) {
		res.push(':\n');
		var lvlPlus = lvl + 2;
		var i, f;

		for (i in this.dirs) {
			if (!this.dirs.hasOwnProperty(i)) {	continue;	}
			res.push(	this.dirs[i].toString(lvlPlus)	);
			res.push('\n');
		}

		for (i = 0, f = this.files.length; i < f; ++i) {
			res.push(	this.files[i].toString(lvlPlus)	);
			res.push('\n');
		}
		res.pop();
	}

	return res.join('');
};



/* recursive function used to traverse node (dir/file) */
var parseNode = function(o, cfg) {
	cfg.remaining[o.path] = true;

	var onNT = function() {
		if (Object.keys(cfg.remaining).length === 0) {
			while (o.parent) {	o = o.parent;	}
			if (cfg.onComplete) {	cfg.onComplete(null, o);	}
		}
	};

	var t;
	fs.stat(o.path, function(err, stats) {
		if (err) {
			if (cfg.onComplete) {	cfg.onComplete(err);	}
			else {					throw err;				}
			return;
		}

		o.isDir = stats.isDirectory();

		if ('filterFn' in cfg && !(cfg.filterFn(o))) {
			delete cfg.remaining[o.path];
			onNT();
			return;
		}

		if (o.parent) {
			if (!o.isDir) {	o.parent.files.push(o);		}
			else {			o.parent.dirs[o.name] = o;	}
		}

		if (!o.isDir) {
			if (cfg.onFile) {	cfg.onFile(o);	}
			delete cfg.remaining[o.path];
			onNT();
			return;
		}

		o.files = [];
		o.dirs = {};

		fs.readdir(o.path, function(err, files) {
			if (err) {
				if (cfg.onComplete) {	cfg.onComplete(err);	}
				else {					throw err;				}
				return;
			}

			var file, newO;
			for (var i = 0, f = files.length; i < f; ++i) {
				file = files[i];

				newO = {
					parent:		o,
					path:		o.path + '/' + file,
					name:		file,
					toString:	nodeToString
				};

				parseNode(newO, cfg);
			}

			if (cfg.onDir) {	cfg.onDir(o);	}
			delete cfg.remaining[o.path];
			onNT();
		});
	});
};



/**
 * @method ls
 * @param	{Object}						cfg
 *     @param {String}				[cfg.path]		- root path to start traversing
 *     @param {Function(err, o)}	[cfg.onComplete]- called when no directory is left to traverse
 *     @param {Function(o)}			[cfg.onDir]		- if defined, this method is on every directory found
 *     @param {Function(o)}			[cfg.onFile]	- if defined, this method is on every file found
 *     @param {Function(o)}			[cfg.filterFn]	- if defined, every element which returns a falsy value isn't traversed
 *
 * o object contains:
 *   {			String}		path	- the current path
 *   {			String}		name	- the last part of the path (file/directory name)
 *   {			Boolean}	isDir	- true iif path is a directory
 *   {optional	String[}}	files	- files in the given directory (if one)
 *   {optional	Object}		dirs	- sub-directories in the given directory (if one)
 */
var ls = function(cfg) {
	// remove last / if there
	var idx = cfg.path.length - 1;
	if (cfg.path.charAt(idx) === '/') {	cfg.path = cfg.path.substring(0, idx);	}

	// name = last part
	var names = cfg.path.split('/');
	var name = names[names.length - 1];

	cfg.remaining = [];
	if (!cfg.onComplete) {	cfg.onComplete = function(){};	}

	var where = {
		path:		cfg.path,
		name:		name,
		toString:	nodeToString
	};
	parseNode(where, cfg);
};



module.exports = ls;
