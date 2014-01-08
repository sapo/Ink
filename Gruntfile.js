module.exports = function(grunt) {

  // Project configuration.
  grunt.initConfig({
    pkg: grunt.file.readJSON('package.json'),

    less: {
      // Compile Inks minified CSS
      docs: {
        options: {
          yuicompress: false
        },
        files: {
          "./assets/css/docs.css":"./less/docs.less",
          "./assets/css/ink-ie7-min.css":"./less/ink-ie7.less"
        }
      }
    },

    jekyll: {
      server: {
        options: {
          serve: true,
          watch: true,
          port: 4000
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
        cwd: 'assets/css/',
        src: ['*.css'],
        dest: 'assets/css/',
        ext: '.min.css',
        options: {
          report: 'min'
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
      }
    },

    watch: {
      scripts: {
        files: ['less/**/*.less'],
        tasks: ['less'],
        options: {
          spawn: false,
        },
      },
    }
  });

  grunt.loadNpmTasks('grunt-jekyll');
  grunt.loadNpmTasks('grunt-contrib-watch');
  grunt.loadNpmTasks('grunt-contrib-copy');
  grunt.loadNpmTasks('grunt-contrib-compass');
  grunt.loadNpmTasks('grunt-contrib-cssmin');

  // Default task(s).
  grunt.registerTask('default', ['compass','cssmin']);
  grunt.registerTask('dev', ['watch']);
};
