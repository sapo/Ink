module.exports = function(grunt) {

  // Project configuration.
  grunt.initConfig({
    pkg: grunt.file.readJSON('package.json'),

    ink: {
      folders: {
        js: {
          srcBase: './src/js/',
          src: './src/js/Ink/',
          tests: './src/js/tests/',
          dist: './assets/js/'
        },
        css: {
          src: './src/sass/',
          distBase: './dist/',
          dist: './assets/css/'
        },
      },
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

      ink_all: {
        src: '<%= ink.folders.js.dist %>ink-all.js', 
        dest: '<%= ink.folders.js.dist %>ink-all.min.js'
      }
    },

    concat: {
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
    },

    clean: {
      js: {
        src: ["<%= ink.folders.js.dist %>/ink*.js"]
      },
      css: {
        src: ["<%= ink.folders.css.dist %>/*.css"]
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
        cwd: 'assets/css/',
        src: ['*.css', "!quick-start.css"],
        dest: 'assets/css/',
        ext: '.min.css',
        options: {
          keepSpecialComments: 0,
          report: 'min'
        }
      }
    },

    watch: {
      css: {
        files: ['src/**/*.scss'],
        tasks: ['css'],
        options: {
          atBegin: true,
          spawn: false,
          // interrupt: true,
        }
      },
      js: {
        files: ['src/js/**/*.js'],
        tasks: ['js'],
        options: {
          atBegin: true,
          spawn: false,
          // interrupt: true,
        }
      }
    },

    shell: {
        src: {
          options: {
            stdout: true,
            stderr: true,
            failOnError: true
          },
          command: 'git checkout 3.0.0-wip -- src && git add src && git commit -m "Updates src from the 3.0.0-wip branch"'
        }
    },

    text_grab: {
      glossary: {
        options: {
          pattern: '\\.[a-zA-Z][a-zA-Z0-9-]+',
          templateStart: '<table class="ink-table bordered alternating content-left">\n<tr><th class="large-30">Class</th><th>Description</th></tr>\n',
          templateRow: '<tr><td>%s</td><td></td><td></td></tr>\n',
          templateEnd: '</table>\n',
          exceptions: ['\\.fa','ttf','otf','svg','eot','woff','jpg','jpeg','png'],
        },
       files: {
         'glossary/g.html': ['assets/css/ink.css']
       }
      },
    },

    jekyll: {
      dev: {
        options: {
          config: '_config.dev.yml',
          serve: true,
          watch: true,
          host: 'localhost',
          port: 4000,
        }
      },
      prod: {
        options: {
          config: '_config.yml'
        }
      }
    }

  });

  grunt.loadNpmTasks('grunt-jekyll');
  grunt.loadNpmTasks('grunt-contrib-watch');
  grunt.loadNpmTasks('grunt-contrib-clean');
  grunt.loadNpmTasks('grunt-contrib-copy');
  grunt.loadNpmTasks('grunt-contrib-compass');
  grunt.loadNpmTasks('grunt-contrib-cssmin');
  grunt.loadNpmTasks('grunt-shell');
  grunt.loadNpmTasks('grunt-contrib-uglify');
  grunt.loadNpmTasks('grunt-contrib-concat');
  grunt.loadNpmTasks('grunt-text-grab');

  // Default task(s).
  grunt.registerTask('js', ['clean:js','concat','uglify']);
  grunt.registerTask('css', ['clean:css','compass','cssmin']);
  grunt.registerTask('update', ['shell:src']);
  grunt.registerTask('dev', ['watch']);
  grunt.registerTask('default', ['update','css','js']);
};
