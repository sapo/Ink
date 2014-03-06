module.exports = function (grunt) {
    var path = require('path');

    require('jit-grunt')(grunt, {
        'bower': 'grunt-bower-task'
    });

    var jshintFile = './src/js/.jshintrc';

    // Folder containing the Ink source files, Ink/*
    // (Relative to your "js" folder containing ink-all.js)
    // for source maps.
    // If you're serving the whole Ink tree on your server, the below works.
    var sourceMapPathToInkSource = '../../src/js';

    // Project configuration.
    grunt.initConfig({
        pkg: grunt.file.readJSON('package.json'),

        ink: {
            folders: {
                bower: './bower_components/',
                js: {
                    srcBase: './src/js/',
                    src: './src/js/Ink/',
                    tests: './src/js/tests/',
                    dist: './dist/js/'
                },
                css: {
                    src: './src/less/',
                    dist: './dist/css/'
                }
            }
        },
        // handle 3rd party dependencies
        bower: {
            install: {
                options: {
                    copy: false,
                    targetDir: '<%= ink.folders.bower %>',
                    layout: 'byType',
                    install: true,
                    verbose: false,
                    cleanTargetDir: false,
                    bowerOptions: {
                        forceLatest: true
                    }
                }
            }
        },
        //
        copy: {
            /*
            [3.0.0]: uncomment this
            fontAwesome: {
                files: [{
                    cwd: '<%= ink.folders.bower %>font-awesome/less/',
                    src: [
                        '*.less',
                        '!variables.less',
                    ],
                    dest: '<%= ink.folders.css.src %>modules/icons/',
                    expand: true
                }]
            },
            */
            animate: {
                files: [{
                    cwd: '<%= ink.folders.bower %>animate.css',
                    src: ['animate.css'],
                    dest: '<%= ink.folders.css.src %>modules/animations/',
                    expand: true,
                    rename: function (dirname, filename) { return dirname + (filename.replace(/\.css$/, '.less')); }
                }]
            },
            modernizr: {
                files: [{
                    cwd: '<%= ink.folders.bower %>modernizr',
                    src: 'modernizr.js',
                    dest: '<%= ink.folders.js.dist %>',
                    expand: true
                }]
            },
            html5shiv: {
                files: [{
                    cwd: '<%= ink.folders.bower %>html5shiv/dist',
                    src: '*',
                    dest: '<%= ink.folders.js.dist %>',
                    expand: true,
                }]
            }
        },


        // builds the javascript bundles
        concat: {

            // ink.js
            ink: {
                files: [{
                    expand: true,
                    flatten: true,
                    cwd: '<%= ink.folders.js.src %>',
                    src: [
                        '1/**/lib.js',
                        'Net/**/lib.js',
                        'Dom/**/lib.js',
                        'Util/**/lib.js',
                    ],
                    dest: '<%= ink.folders.js.dist %>',
                    rename: function (dest, src) {
                        return dest + 'ink.js';
                    }
                }]
            },

            // ink-all.js
            ink_all: {
                files: [{
                    expand: true,
                    flatten: true,
                    cwd: '<%= ink.folders.js.src %>',
                    src: [
                        '1/**/lib.js',
                        // Don't include autoload
                        'Net/**/lib.js',
                        'Dom/**/lib.js',
                        'Util/**/lib.js',
                        'UI/**/lib.js'
                    ],
                    dest: '<%= ink.folders.js.dist %>',
                    rename: function (dest, src) {
                        return dest + 'ink-all.js';
                    }
                }]
            },

            // ink-ui.js
            ink_ui: {
                files: [{
                    expand: true,
                    flatten: true,
                    cwd: '<%= ink.folders.js.src %>UI/',
                    src: [
                        '**/lib.js'
                    ],
                    dest: '<%= ink.folders.js.dist %>',
                    rename: function (dest, src) {
                        return dest + 'ink-ui.js';
                    }
                }]
            },

            // ui components as single files
            ui: {
                files: [{
                    expand: true,
                    cwd: '<%= ink.folders.js.src %>UI/',
                    src: ['**/[0-9]/lib.js'],
                    dest: '<%= ink.folders.js.dist %>',
                    rename: function (dest, src) {
                        // check if this is v1
                        // Here data is in the form of [UIComponent]/[ver]/lib.js
                        var split = src.split(/\//);
                        var modName = split[0].toLowerCase();
                        var version = split[1];

                        if (version === '1') {
                            // and it it is discard the version number
                            return dest + 'ink.' + modName + '.js';
                        } else {
                            // or replace the slash by an underscore and version number and prepend to dest file name
                            return dest + 'ink.' + modName + '-' + version + '.js';
                        }
                    }
                }
                ]
            },

            autoload: {
                files: [{
                    expand: true,
                    cwd: '<%= ink.folders.js.src %>Autoload',
                    src: ['**/lib.js'],
                    dest: '<%= ink.folders.js.dist %>',
                    rename: function (dest, src) {
                        var version = src.split('/')[0];
                        if (version === '1') {
                            return dest + 'autoload.js';
                        } else {
                            return dest + 'autoload-' + version + '.js';
                        }
                    }
                }]
            }
        },

        clean: {
            js: {
                src: [
                    '<%= ink.folders.js.dist %>/ink*.js',
                    '<%= ink.folders.js.dist %>/ink*.js.map',
                ]
            },
            css: {
                src: ['<%= ink.folders.css.dist %>/ink*.css']
            },
            /*
            [3.0.0]: uncomment this
            fontAwesome: {
                src: [
                    '<%= ink.folders.css.src %>modules/icons/*',
                    '!<%= ink.folders.css.src %>modules/icons/variables.less'
                ]
            },
            */
        },

        // [TODO] check if this works okay
        qunit: {
            options: {
                // inject: 'js/tests/assets/phantom.js',
                urls: ['http://localhost:8000/js/tests/index.html']
            },
            files: ['<%= ink.folders.js.src %>/tests/unit/**/*.html']
        },

        connect: {
            server: {
                options: {
                    port: 8000,
                    debug: true,
                    base: '.',
                    keepalive: true
                }
            }
        },

        // CONCATENATE JS
        uglify: {
            options: {
                report: 'min',
                sourceMapRoot: sourceMapPathToInkSource,
                sourceMapPrefix: 3,
                compress: {
                    sequences: true,
                    properties: true,
                    dead_code: false,
                    drop_debugger: false,
                    unsafe: false,
                    conditionals: true,
                    comparisons: true,
                    evaluate: true,
                    booleans: true,
                    loops: true,
                    unused: false,
                    hoist_funs: false,
                    hoist_vars: false,
                    if_return: true,
                    join_vars: true,
                    cascade: true
                }
            },
            ink: {
                src: [
                    '<%= ink.folders.js.src %>1/**/lib.js',
                    '<%= ink.folders.js.src %>Net/**/lib.js',
                    '<%= ink.folders.js.src %>Dom/**/lib.js',
                    '<%= ink.folders.js.src %>Util/**/lib.js',
                ],
                options: {
                    sourceMap: '<%= ink.folders.js.dist %><%= pkg.name %>.js.map',
                    sourceMappingURL: '<%= pkg.name %>.js.map'
                },
                dest: '<%= ink.folders.js.dist %><%= pkg.name %>.min.js'
            },
            ink_all: {
                src: [
                    '<%= ink.folders.js.src %>1/**/lib.js',
                    // Do not include autoload
                    '<%= ink.folders.js.src %>Net/**/lib.js',
                    '<%= ink.folders.js.src %>Dom/**/lib.js',
                    '<%= ink.folders.js.src %>UI/**/lib.js',
                    '<%= ink.folders.js.src %>Util/**/lib.js',
                ],
                options: {
                    sourceMap: '<%= ink.folders.js.dist %><%= pkg.name %>-all.js.map',
                    sourceMappingURL: '<%= pkg.name %>-all.js.map'
                },
                dest: '<%= ink.folders.js.dist %><%= pkg.name %>-all.min.js'
            },
            ink_ui: {
                src: ['<%= ink.folders.js.src %>UI/**/lib.js'],
                options: {
                    sourceMap: '<%= ink.folders.js.dist %><%= pkg.name %>-ui.js.map',
                    sourceMappingURL: '<%= pkg.name %>-ui.js.map'
                },
                dest: '<%= ink.folders.js.dist %><%= pkg.name %>-ui.min.js'
            }
        },

        // COMPILES THE CSS
        less: {
            distMin: {
                files: {
                    '<%= ink.folders.css.dist %><%= pkg.name %>-min.css': ['<%= ink.folders.css.src %><%= pkg.name %>.less'],
                },
                // COMPILES THE MINIFIED CSS
                options: {
                    compress: true,
                    // LESS source maps
                    // To enable, set sourceMap to true and update sourceMapRootpath based on your install
                    sourceMap: true,
                    sourceMapFilename: '<%= ink.folders.css.dist %><%= pkg.name %>-min.css.map',
                    sourceMapRootpath: '../../'
                }
            },
            IEMin: {
                files: {
                    '<%= ink.folders.css.dist %><%= pkg.name %>-ie7-min.css': ['<%= ink.folders.css.src %><%= pkg.name %>-ie7.less']
                }
            },
            dist: {
                files: {
                    '<%= ink.folders.css.dist %><%= pkg.name %>.css': ['<%= ink.folders.css.src %><%= pkg.name %>.less'],
                },
                // COMPILES THE MINIFIED CSS
                options: {
                    compress: false,
                    // LESS source maps
                    // To enable, set sourceMap to true and update sourceMapRootpath based on your install
                    sourceMap: true,
                    sourceMapFilename: '<%= ink.folders.css.dist %><%= pkg.name %>.css.map',
                    sourceMapRootpath: '../../'
                }
            },
            IE: {
                files: {
                    '<%= ink.folders.css.dist %><%= pkg.name %>-ie7.css': ['<%= ink.folders.css.src %><%= pkg.name %>-ie7.less']
                }
            }
        },

        // Runs JSHint on the Ink.js JavaScript source
        jshint: {
            inkjs: {
                options: {
                    jshintrc: '<%= ink.folders.js.srcBase %>.jshintrc'
                },
                files: {
                    src: '<%= ink.folders.js.src %>**/lib.js'
                }
            }
        },

        // Creates a plato report
        plato: {
            inkjs: {
                options: {
                    jshint: grunt.file.readJSON(jshintFile, {
                        encoding: 'utf-8'
                    })
                },
                files: {
                    '<%= ink.folders.js.srcBase %>report': '<%= ink.folders.js.src %>**/lib.js'
                }
            }
        }
    });

    grunt.registerTask('default', ['bower', 'copy', 'clean:css', 'less', 'clean:js', 'concat', 'uglify']);
    grunt.registerTask('test', ['connect', 'qunit']);
    grunt.registerTask('custom_bundle', 'Create your custom bundle from a json file', function (fileName) {
        if (arguments.length === 0) {
            grunt.log.error('You need to specify a file name');
        }

        var bundle = grunt.file.readJSON(fileName);
        var dependencies = bundle.dependencies || [];
        grunt.config.set('concat.ink_custom', {
            files: [{
                expand: true,
                flatten: true,
                cwd: '<%= ink.folders.js.src %>',
                dest: '<%= ink.folders.js.dist %>' + bundle.name + '/',
                src: dependencies.map(function (depName) {
                    return depName.replace('Ink/', '') + '/lib.js';
                }),
                rename: function (dest, src) {
                    return dest + 'ink-custom.js';
                }
            }]
        });
        grunt.config.set('uglify.ink_custom', {
            src: '<%= ink.folders.js.dist %>' + bundle.name + '/' + 'ink-custom.js',
            dest: '<%= ink.folders.js.dist %>' + bundle.name + '/' + 'ink-custom.min.js'
        });
        grunt.task.run(['concat:ink_custom', 'uglify:ink_custom']);
    });
};
