module.exports = function(grunt) {

  var jshintFile = './src/js/.jshintrc';

  // Project configuration.
  grunt.initConfig({
    pkg: grunt.file.readJSON('package.json'),

    // handle 3rd party dependencies
    bower: {
      install: {
        options: {
          targetDir: 'tmp',
          layout: 'byType',
          install: false,
          verbose: false,
          cleanTargetDir: false,
          cleanBowerDir: true,
          bowerOptions: {
            forceLatest: true
          }
        }
      }
    },

    // 
    copy: {
      fontAwesome: {
        files: [
        {
          cwd: 'tmp/font-awesome/less/',
          src: '*.less', 
          dest: 'src/less/modules/icons/',
          expand: true,
        }
        ]
      },
      modernizr: {
        files: [
        {
          cwd: 'tmp/modernizr',
          src: 'modernizr.js', 
          dest: '<%= ink.folders.js.dist %>',
          expand: true,
        }
        ]
      }
    },

    ink: {
      folders: {
        js: {
          srcBase: './src/js/',
          src: './src/js/Ink/',
          tests: './src/js/tests/',
          dist: './dist/js/'
        },
        css: {
          src: './src/less/',
          dist: './dist/css/'
        },
      },
    },

    // builds the javascript bundles
    concat: {

      // ink.js
      ink: {
        files: [
        {
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
          rename: function(dest, src) {
            return dest + 'ink.js';
          },
        }
        ]
      },

      // ink-all.js
      ink_all: {
        files: [
        {
          expand: true,
          flatten: true,
          cwd: '<%= ink.folders.js.src %>',
          src: [
          '1/**/lib.js',
          'Net/**/lib.js',
          'Dom/**/lib.js',
          'Util/**/lib.js',
          'UI/**/lib.js'
          ],
          dest: '<%= ink.folders.js.dist %>',
          rename: function(dest, src) {
            return dest + 'ink-all.js';
          }
        }
        ]
      },

      // ink-ui.js
      ink_ui: {
        files: [
        {
          expand: true,
          flatten: true,
          cwd: '<%= ink.folders.js.src %>UI/',
          src: [
          '**/lib.js'
          ],
          dest: '<%= ink.folders.js.dist %>',
          rename: function(dest, src) {
            return dest + 'ink-ui.js';
          },
        },
        ],
      },

      // ui components as single files
      ui: {
        files: [
        {
          expand: true,
          cwd: '<%= ink.folders.js.src %>UI/',
          src: ['**/[0-9]/lib.js'],
          dest: '<%= ink.folders.js.dist %>',
          rename: function(dest, src) {
              // [TODO] refactor
              // check if this is v1
              if (src.substring(src.lastIndexOf('/'),-1).match(/[0-9]/) && src.substring(src.lastIndexOf('/'),-1).match(/[0-9]/) === 1) {
                // and it it is discard the version number               
                return dest + 'ink.' + src.substring(0, src.indexOf('/')).toLowerCase() + '.js';
              } else {
                // or replace the slash by an underscore and version number and prepend to dest file name 
                return dest + 'ink.' + src.substring(0, src.lastIndexOf('/')).toLowerCase().replace('/','-') + '.js';                
              }
            },
          },
          ],
        },
      },

      clean: {
        js: {
          src: ['<%= ink.folders.js.dist %>/ink*.js']
        },
        css: {
          src: ['<%= ink.folders.css.dist %>/ink*.css']
        },
        fontAwesome: {
          src: ['<%= ink.folders.css.src %>/less/modules/icons/*.less']
        },
        tmp: {
          src: ['tmp']
        }
      },

    // [TODO] check if this works okay
    qunit: {
      options: {
          // inject: 'js/tests/assets/phantom.js',
          urls: ['http://localhost:8000/js/tests/index.html']
        },
        files: ['<%= ink.folders.js.src %>/tests/unit/*.html']
      },

      connect: {
        server: {
          options: {
            port: 8000,
            debug: true,
            base: '.'
          }
        }
      },

    // CONCATENATE JS
    uglify: {
      options: {
        report: 'min',
        sourceMapRoot: '../..',
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
        src: '<%= ink.folders.js.dist %><%= pkg.name %>.js',
        options: {
          sourceMap: '<%= ink.folders.js.dist %><%= pkg.name %>.js.map',
          sourceMappingURL: '<%= pkg.name %>.js.map'
        },
        dest: '<%= ink.folders.js.dist %><%= pkg.name %>.min.js'
      },
      ink_all: {
        src: '<%= ink.folders.js.dist %><%= pkg.name %>-all.js',
        options: {
          sourceMap: '<%= ink.folders.js.dist %><%= pkg.name %>-all.js.map',
          sourceMappingURL: '<%= pkg.name %>-all.js.map'
        }, 
        dest: '<%= ink.folders.js.dist %><%= pkg.name %>-all.min.js'
      },
      ink_ui: {
        src: '<%= ink.folders.js.dist %><%= pkg.name %>-ui.js',
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
          '<%= ink.folders.css.dist %><%= pkg.name %>-min.css':['<%= ink.folders.css.src %><%= pkg.name %>.less'],
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
            '<%= ink.folders.css.dist %><%= pkg.name %>-ie7-min.css':['<%= ink.folders.css.src %><%= pkg.name %>-ie7.less']
          }
        },
        dist: {
          files: {
            '<%= ink.folders.css.dist %><%= pkg.name %>.css':['<%= ink.folders.css.src %><%= pkg.name %>.less'],
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
          '<%= ink.folders.css.dist %><%= pkg.name %>-ie7.css':['<%= ink.folders.css.src %><%= pkg.name %>-ie7.less']
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
          jshint: eval('(' +
            grunt.file.read(jshintFile, { encoding: 'utf-8'}) +
            ')')
        },
        files: {
          '<%= ink.folders.js.srcBase %>report': '<%= ink.folders.js.src %>**/lib.js'
        }
      }
    }
  });

grunt.loadNpmTasks('grunt-contrib-less');
grunt.loadNpmTasks('grunt-contrib-uglify');
grunt.loadNpmTasks('grunt-contrib-concat');
grunt.loadNpmTasks('grunt-contrib-clean');
grunt.loadNpmTasks('grunt-contrib-connect');
grunt.loadNpmTasks('grunt-contrib-qunit');
grunt.loadNpmTasks('grunt-contrib-jshint');
grunt.loadNpmTasks('grunt-bower-task');
grunt.loadNpmTasks('grunt-contrib-copy');
grunt.loadNpmTasks('grunt-plato');

grunt.registerTask('default', ['bower', 'copy', 'clean:css', 'less','clean:js','concat','uglify', 'clean:tmp']);
grunt.registerTask('test', ['connect', 'qunit']);
grunt.registerTask('custom_bundle', 'Create your custom bundle from a json file', function(fileName){
  if (arguments.length === 0) {
    grunt.log.error('You need to specify a file name');
  }

  var bundle = grunt.file.readJSON(fileName);
  var dependencies = bundle.dependencies || [];
  grunt.config.set('concat.ink_custom', {
   files: [
   {
    expand: true,
    flatten: true,
    cwd: '<%= ink.folders.js.src %>',
    dest: '<%= ink.folders.js.dist %>' + bundle.name + '/',
    src: dependencies.map(function(depName){
      return depName.replace('Ink/', '') + '/lib.js';
    }),
    rename: function(dest, src) {
      return dest + 'ink-custom.js';
    }
  }
  ]
});
  grunt.config.set('uglify.ink_custom', {
    src: '<%= ink.folders.js.dist %>' + bundle.name + '/' + 'ink-custom.js',
    dest: '<%= ink.folders.js.dist %>' + bundle.name + '/' + 'ink-custom.min.js'
  });
  grunt.task.run(['concat:ink_custom', 'uglify:ink_custom']);
});
};