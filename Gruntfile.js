module.exports = function(grunt) {

  var jshintFile = './src/js/.jshintrc';

  // Project configuration.
  grunt.initConfig({
    pkg: grunt.file.readJSON('package.json'),


    ink: {
      folders: {
        js: {
          srcBase: './src/js/',
          src: './src/js/Ink/',
          tests: './src/js/tests/',
          dist: './dist/js/'
        },
        css: {
          src: './src/sass/',
          distBase: './dist/',
          dist: './dist/css/'
        },
      },
    },
    
    // gets 3rd party dependencies
    bower: {
      update: {
        options: {
          targetDir: 'tmp',
          layout: 'byType',
          install: true,
          verbose: true,
          cleanTargetDir: false,
          cleanBowerDir: true,
          bowerOptions: {
            forceLatest: true
          }
        }
      }
    },

    // copies 3rd party dependencies to the propper places 
    copy: {
      compass: {
        files: [
          {
            cwd: 'tmp/bower-compass-core/compass/stylesheets/',
            src: '**/*.scss', 
            dest: 'src/sass/contrib/',
            expand: true,
          }
        ]
      },
      fontAwesome: {
        files: [
          {
            cwd: 'tmp/font-awesome/scss/',
            src: '*.scss', 
            dest: 'src/sass/contrib/font-awesome/',
            expand: true,
          },

          {
            cwd: 'tmp/font-awesome/less/',
            src: '*.less', 
            dest: 'src/less/contrib/font-awesome/',
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
        },
       ],
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
          },
        },
       ],
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
              if (src.substring(src.lastIndexOf('/'),-1).match(/[0-9]/) && src.substring(src.lastIndexOf('/'),-1).match(/[0-9]/) == 1) {
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
        src: ["<%= ink.folders.js.dist %>/ink*.js"]
      },
      css: {
        src: ["<%= ink.folders.css.dist %>/*.css"]
      },
      fontAwesome: {
        src: ["<%= ink.folders.css.src %>/less/modules/icons/*.less"]
      },
      tmp: {
        src: ["tmp"]
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
        report: "min",
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
        src: '<%= ink.folders.js.dist %>ink.js', 
        dest: '<%= ink.folders.js.dist %>ink.min.js'
      },
      ink_all: {
        src: '<%= ink.folders.js.dist %>ink-all.js', 
        dest: '<%= ink.folders.js.dist %>ink-all.min.js'
      },
      ink_ui: {
        src: '<%= ink.folders.js.dist %>ink-ui.js', 
        dest: '<%= ink.folders.js.dist %>ink-ui.min.js'
      },
    },

    // COMPILES THE CSS
    less: {
      dist: {
        files: {
          '<%= ink.folders.css.dist %><%= pkg.name %>.css':['<%= ink.folders.css.src %><%= pkg.name %>.less'],
          '<%= ink.folders.css.dist %><%= pkg.name %>-ie7.css':['<%= ink.folders.css.src %><%= pkg.name %>-ie7.less']
        }
      },

      // COMPILES THE MINIFIED CSS
      min: {
        options: {
          yuicompress: true
        },
        files: {
          '<%= ink.folders.css.dist %><%= pkg.name %>-min.css':'<%= ink.folders.css.src %><%= pkg.name %>.less',
          '<%= ink.folders.css.dist %><%= pkg.name %>-ie7-min.css':'<%= ink.folders.css.src %><%= pkg.name %>-ie7.less'
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

    compass: {                  
      css: {                   
        options: {   
          config: "config.rb"
        }
      },
    },

    cssmin: {
      minify: {
        expand: true,
        cwd: '<%= ink.folders.css.dist %>',
        src: ['*.css', '!quick-start.css'],
        dest: '<%= ink.folders.css.dist %>',
        ext: '.min.css',
        options: {
          report: 'min'
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
    },

    watch: {
      css: {
        files: ['<%= ink.folders.css.src %>**/*.scss'],
        tasks: ['css'],
        options: {
          spawn: false,
          // interrupt: true,
        }
      },
      js: {
        files: ['<%= ink.folders.js.src %>/**/*.js'],
        tasks: ['js'],
        options: {
          spawn: false,
          // interrupt: true,
        }
      },
    },

  });

  // grunt.loadNpmTasks('grunt-contrib-less');
  // grunt.loadNpmTasks('grunt-contrib-connect');
  // grunt.loadNpmTasks('grunt-contrib-qunit');
  
  grunt.loadNpmTasks('grunt-bower-task');
  grunt.loadNpmTasks('grunt-contrib-uglify');
  grunt.loadNpmTasks('grunt-contrib-concat');
  grunt.loadNpmTasks('grunt-contrib-clean');
  grunt.loadNpmTasks('grunt-contrib-jshint');
  grunt.loadNpmTasks('grunt-contrib-copy');
  grunt.loadNpmTasks('grunt-contrib-compass');
  grunt.loadNpmTasks('grunt-contrib-watch');
  grunt.loadNpmTasks('grunt-contrib-cssmin');
  grunt.loadNpmTasks('grunt-plato');

  grunt.registerTask('js', ['clean:js', 'concat', 'uglify']);
  grunt.registerTask('css', ['clean:css', 'compass', 'cssmin']);
  grunt.registerTask('dependencies', ['bower', 'copy', 'clean:tmp']);
  grunt.registerTask('default', ['dependencies','css','js']);

  // grunt.registerTask('test', ['connect', 'qunit']);
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
            dest: '<%= ink.folders.js.dist %>' + bundle['name'] + '/',
            src: dependencies.map(function(depName){
                return depName.replace('Ink/', '') + '/lib.js';
            }),
            rename: function(dest, src) {
              return dest + 'ink-custom.js';
            },
          },
        ],       
    });
    grunt.config.set('uglify.ink_custom', {
        src: '<%= ink.folders.js.dist %>' + bundle['name'] + '/' + 'ink-custom.js',
        dest: '<%= ink.folders.js.dist %>' + bundle['name'] + '/' + 'ink-custom.min.js'
    });
    grunt.task.run(['concat:ink_custom', 'uglify:ink_custom']);
  });
};
