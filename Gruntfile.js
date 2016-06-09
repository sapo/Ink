module.exports = function (grunt) {
    var child_process = require('child_process');
    var path = require('path');
    var async = require('async');

    require('jit-grunt')(grunt, {
        'bower': 'grunt-bower-task',
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
                    src: './src/sass/',
                    dist: './dist/css/'
                }
            },
            test: {
                connect_port: process.env['INK_TEST_CONNECT_PORT'] || 8000,
                root: '.',
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
                    cleanTargetDir: false
                }
            }
        },
        copy: {
            fontAwesome: {
                files: [
                    {
                        cwd: '<%= ink.folders.bower %>font-awesome/scss/',
                        src: '*.scss',
                        dest: 'src/sass/contrib/font-awesome/',
                        expand: true,
                    }
                ]
            },
            animate: {
                files: [{
                    cwd: '<%= ink.folders.bower %>animate.css',
                    src: ['animate.css'],
                    dest: 'src/sass/contrib/animations/',
                    expand: true,
                    rename: function (dest, src) {
                        return dest + 'animate.css';
                    }
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

            compass: {
                files: [
                    {
                        cwd: '<%= ink.folders.bower %>bower-compass-core/compass/stylesheets/',
                        src: '**/*.scss',
                        dest: 'src/sass/contrib/',
                        expand: true,
                    }
                ]
            },
            facss: {
              src: 'dist/css/contrib/font-awesome/font-awesome.css',
              dest: 'dist/css/font-awesome.css'
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
                src: [
                  '<%= ink.folders.css.dist %>/*.css',
                  '<%= ink.folders.css.dist %>/*.css.map',
                  '!<%= ink.folders.css.dist %>/quick-start.css'
                ]
            },
            csscontrib: [ '<%= ink.folders.css.dist %>/contrib' ]
        },

        // CONCATENATE JS
        uglify: {
            options: {
                report: 'min',
                sourceMapIncludeSources: true,
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
                    hoist_funs: true,
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
                    sourceMap: '<%= ink.folders.js.dist %>ink.js.map',
                    sourceMappingURL: 'ink.js.map'
                },
                dest: '<%= ink.folders.js.dist %>ink.min.js'
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
                    sourceMap: '<%= ink.folders.js.dist %>ink-all.js.map',
                    sourceMappingURL: 'ink-all.js.map'
                },
                dest: '<%= ink.folders.js.dist %>ink-all.min.js'
            },
            ink_ui: {
                src: ['<%= ink.folders.js.src %>UI/**/lib.js'],
                options: {
                    sourceMap: '<%= ink.folders.js.dist %>ink-ui.js.map',
                    sourceMappingURL: 'ink-ui.js.map'
                },
                dest: '<%= ink.folders.js.dist %>ink-ui.min.js'
            },
            autoload: {
                src: ['<%= ink.folders.js.src %>Autoload/1/lib.js'],
                options: {
                    sourceMap: '<%= ink.folders.js.dist %>autoload.js.map',
                    sourceMappingURL: 'autoload.js.map'
                },
                dest: '<%= ink.folders.js.dist %>autoload.min.js'
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
            },
        },

        compass: {
            css: {
                options: {
                    outputStyle: 'expanded',
                    noLineComments: true,
                    relativeAssets: true,
                    sassDir: 'src/sass',
                    cssDir: "dist/css",
                    fontsDir: 'dist/fonts',
                    imagesDir: 'dist/img'
                }
            },
        },

        cssmin: {
            minify: {
                expand: true,
                cwd: '<%= ink.folders.css.dist %>',
                src: ['*.css', '!quick-start.css', '!*min*'],
                dest: '<%= ink.folders.css.dist %>',
                ext: '.min.css',
                options: {
                    keepSpecialComments: 0,
                    report: 'min',
                    sourceMap: true,
                    sourceMapFilename: '<%= ink.folders.css.dist %>ink-min.css.map',
                    sourceMapRootpath: '../../'
                }
            }
        },

        connect: {
            test: {
                options: {
                    port: '<%= ink.test.connect_port %>',
                    base: '<%= ink.test.root %>',
                    keepalive: false
                }
            },
        },

        watch: {
            css: {
                files: [
                    'src/**/*.scss'
                ],
                tasks: ['css'],
                options: {
                    spawn: false,
                    interrupt: true,
                }
            },
            js: {
                files: ['<%= ink.folders.js.src %>/**/*.js'],
                tasks: ['js'],
                options: {
                    spawn: false,
                    interrupt: true,
                }
            },
        },

        compress: {
          main: {
            options: {
              archive: 'ink-<%= pkg.version %>.zip',
              mode: "zip",
              level: 9,
              pretty: true
            },
            files: [
              {expand: true, cwd: "dist/", src: ['**'], dest: '/ink-<%= pkg.version %>'} // includes files in path and its subdirs
            ]
          }
        },

        bump : {
            options: {
                files: [
                    'bower.json',
                    'package.json',
                    'README.md',
                    '<%= ink.folders.js.src %>/1/lib.js',
                    './src/sass/ink-flex.scss',
                    './src/sass/ink-ie.scss',
                    './src/sass/ink-legacy.scss',
                    './src/sass/ink.scss',
                    './src/sass/quick-start.scss'
                ],
                push: false,
                commit: false,
                tagName: '%VERSION%',
                regExp: new RegExp(
                    '([\'|\"|@]?version[\'|\"]?[ ]*:?[ ]*[\'|\"]?)(\\d+\\.\\d+\\.\\d+(-' +
                    '\\.\\d+)?(-\\d+)?)[\\d||A-a|.|-]*([\'|\"]?)', 'i'),
                createTag: false,
                commitFiles: []
            }    
        }
    });

    grunt.registerTask('js', ['clean:js', 'concat', 'uglify']);
    grunt.registerTask('css', ['clean:css', 'compass', 'copy:facss', 'clean:csscontrib', 'cssmin']);
    grunt.registerTask('dist', ['css', 'js', 'compress']);
    grunt.registerTask('dependencies', ['bower', 'copy:fontAwesome', 'copy:compass']);
    grunt.registerTask('default', ['dependencies','css','js']);
    grunt.registerTask('lintdoc', function (module) {
        require('eslint/lib/cli').execute([
            'x',
            'x',
            path.join(__dirname, 'src/js/Ink')
        ]);
    });
    grunt.registerTask('_phantomjs', function (module) {
        this.requires('connect:test');

        var options = this.options({
            module: '**'
        });

        var failures = [];

        var testFiles = grunt.file.expand('test/unit/' + options.module + '/index.html');

        var done = this.async();

        async.eachSeries(testFiles, function (testFile, next) {
            var url = 'http://localhost:' + grunt.config.get('ink.test.connect_port') + '/' + testFile;
            var displayName = path.basename(path.dirname(testFile));

            console.log('\nrunning tests for ' + testFile);
            console.log('(visiting ' + url + ')');
            phantomjsProcess = child_process.spawn('./node_modules/phantomjs/bin/phantomjs', [
                    'test/phantomjs-qunit.js',
                    url
                ]);
            phantomjsProcess.on('close', function(exitCode) {
                if (exitCode) {
                    failures.push(displayName);
                }
                next(null);
            });
            phantomjsProcess.stdout.pipe(process.stdout);
            phantomjsProcess.stderr.pipe(process.stdout);
        }, function afterAllTests() {
            if (failures.length) {
                console.log('\n# %d suites (in %d) FAILED: %s',
                    failures.length, testFiles.length, failures.join(' ,'));
                process.exit(failures.length);
            } else {
                console.log('\n# %d suites passed', testFiles.length);
            }
            done(failures.length);
        });
    });
	grunt.registerTask('test', function (moduleName) {
        if (moduleName) {
            grunt.config.set('_phantomjs.options.module', moduleName);
        }
        grunt.task.run(['connect:test', '_phantomjs:test']);
    });
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
