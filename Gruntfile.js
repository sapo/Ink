module.exports = function(grunt) {

  // Project configuration.
  grunt.initConfig({
    pkg: grunt.file.readJSON('package.json'),
    less: {
      css: {
        files: {
          "css/<%= pkg.name %>.css":"less/<%= pkg.name %>.less"
        }
      }
      ,legacy_css: {
        files: {
          "css/<%= pkg.name %>-ie7.css":"less/<%= pkg.name %>-ie7.less"
        }
      }
      ,minified_css: {
        options: {
          yuicompress: true
        }
        ,files: {
          "css/<%= pkg.name %>-min.css":"less/<%= pkg.name %>.less"
        }
      }
      ,minified_legacy_css: {
        options: {
          yuicompress: true
        }
        ,files: {
          "css/<%= pkg.name %>-ie7-min.css":"less/<%= pkg.name %>-ie7.less"
        }
      }
    }    
  });

  // Load the plugin that provides the "less" task.
  grunt.loadNpmTasks('grunt-contrib-less');

  // Default task(s).
  grunt.registerTask('default', ['less']);


};