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
          "./css/docs.css":"./less/docs.less",
          "./css/ink-ie7-min.css":"./less/ink-ie7.less"
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

  // Load the plugin that provides the "less" task.
  grunt.loadNpmTasks('grunt-contrib-less');
  // Load the plugin that provides the "jekyll" task.
  grunt.loadNpmTasks('grunt-jekyll');
  grunt.loadNpmTasks('grunt-contrib-watch');

  // Default task(s).
  grunt.registerTask('default', ['less']);
};
