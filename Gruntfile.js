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
        src: ["<%= ink.folders.css.dist %>/*.css","<%= ink.folders.css.dist %>/contrib" ]
      },
      csscontrib: [ '<%= ink.folders.css.dist %>/contrib' ]
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
          command: 'git remote update; git checkout origin/develop -- src && git add src'
        },
        inkdoc: {
            options: {
                stdout: true,
                stderr: true,
                failOnError: true
            },
            command: './node_modules/inkdoc/bin/inkdoc'
        }
    },

    text_grab: {
      glossary: {
        options: {
          pattern: '\\.[a-zA-Z][a-zA-Z0-9-_]+',
          templateStart: '<table class="props css">\n<tr><th class="large-30">Class</th><th>Description</th></tr>\n<caption>Glossary</caption>\n',
          templateRow: '<tr>\n<td><code>%s</code></td>\n<td></td>\n</tr>\n',
          templateEnd: '</table>\n',
          exceptions: ['\\.fa','ttf','otf','svg','eot','woff','jpg','jpeg','png','Microsoft','Alpha'],
        },
       files: {
         'glossary/g.html': ['assets/css/ink-flex.css']
       }
      },
    },

    copy: {
      facss: {
        src: 'assets/css/contrib/font-awesome/font-awesome.css',
        dest: 'assets/css/font-awesome.css'
      }
    },

    jekyll: {
      dev: {
        options: {
          config: '_config.yml,_config.dev.yml',
          serve: true,
          watch: true,
          host: 'localhost',
          port: 4000,
        }
      },
      devNoServe: {
        options: {
          config: '_config.yml,_config.dev.yml',
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
  grunt.loadNpmTasks('grunt-shell');
  grunt.loadNpmTasks('grunt-text-grab');
  grunt.loadNpmTasks('grunt-contrib-clean');
  grunt.loadNpmTasks('grunt-contrib-compass');
  grunt.loadNpmTasks('grunt-contrib-concat');
  grunt.loadNpmTasks('grunt-contrib-copy');
  grunt.loadNpmTasks('grunt-contrib-cssmin');
  grunt.loadNpmTasks('grunt-contrib-uglify');
  grunt.loadNpmTasks('grunt-contrib-watch');

  // Default task(s).
  grunt.registerTask('default', ['update','css','js']);
  grunt.registerTask('update', ['shell:src']);
  grunt.registerTask('docs', ['inkdoc','jekyll']);
  grunt.registerTask('dev', ['watch']);
  grunt.registerTask('inkdoc', ['shell:inkdoc']);
  grunt.registerTask('js', ['clean:js','concat','uglify']);
  grunt.registerTask('css', ['clean:css','compass','copy:facss','clean:csscontrib','cssmin']);
};
