module.exports = function(grunt) {

  // Project configuration.
  grunt.initConfig({
    pkg: grunt.file.readJSON('package.json'),
    // We need to generate the includes folder so inkdoc doesn't go bad
    jsFolder: grunt.file.mkdir('_includes/js'),
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
        src: '<%= ink.folders.js.dist %>search.js',
        dest: '<%= ink.folders.js.dist %>search.min.js'
      }
    },

    clean: {
      css: {
        src: ["<%= ink.folders.css.dist %>/*.css","<%= ink.folders.css.dist %>/contrib" ]
      },
      inkdoc: ['_includes/js']
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
      inkdoc: {
        files: ['_includes/inkdoctemplates/ink/**/*.js','_includes/inkdoctemplates/ink/**/*.hbs'],
        tasks: ['shell:inkdoc', 'copy:inkdoc'],
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
        command: 'rm -rf develop.zip Ink-develop src/js;' +
          'wget https://github.com/sapo/Ink/archive/develop.zip;' +
          'unzip -q develop;' +
          'mv Ink-develop/src/js src/;' +
          'rm -rf Ink-develop develop.zip'
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

    copy: {
      inkdoc: {
        files: [
          {
            expand: true,
            cwd: '_includes/js/',
            src: ['index.html'],
            dest: 'javascript/',
            filter: 'isFile'
          },

          {
            expand: true,
            cwd: '_includes/js/',
            src: ['Ink_*.html'],
            dest: 'javascript/',
            filter: 'isFile',
            rename: function(dest,fileName){
               return dest+fileName.replace('.html','').replace('_1','').replace(/[_]+/g,'.')+'/index.html';
            }
          }
        ]
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
      build: {
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
  grunt.loadNpmTasks('grunt-contrib-clean');
  grunt.loadNpmTasks('grunt-contrib-compass');
  grunt.loadNpmTasks('grunt-contrib-copy');
  grunt.loadNpmTasks('grunt-contrib-cssmin');
  grunt.loadNpmTasks('grunt-contrib-uglify');
  grunt.loadNpmTasks('grunt-contrib-watch');

  // Default task(s).
  grunt.registerTask('default', ['css','update','inkdoc','jekyll:build']);
  grunt.registerTask('update', ['shell:src']);
  grunt.registerTask('docs', ['inkdoc','jekyll']);
  grunt.registerTask('dev', ['watch']);
  grunt.registerTask('inkdoc', ['shell:inkdoc', 'copy:inkdoc', 'clean:inkdoc']);
  grunt.registerTask('css', ['clean:css','compass','cssmin']);
};
