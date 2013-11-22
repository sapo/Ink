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

  grunt.loadNpmTasks('grunt-contrib-less');
  grunt.loadNpmTasks('grunt-jekyll');
  grunt.loadNpmTasks('grunt-contrib-watch');

  // Default task(s).
  grunt.registerTask('default', ['watch']);
  grunt.registerTask('all', ['jekyll','less']);
};
