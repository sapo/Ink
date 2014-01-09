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

    clean: {
      css: {
        src: ["assets/css/*.css"]
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
        files: ['src/**/*.scss'],
        tasks: ['css'],
        options: {
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
          command: 'git fetch && git checkout origin/3.0.0-wip -- src/*'
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

  // Default task(s).
  grunt.registerTask('css', ['clean','compass','cssmin']);
  grunt.registerTask('update', ['shell:src']);
  grunt.registerTask('dev', ['watch']);
  grunt.registerTask('default', ['update','css']);
};
