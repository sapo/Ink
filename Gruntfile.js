module.exports = function(grunt) {

  // Project configuration.
  grunt.initConfig({
    pkg: grunt.file.readJSON('package.json'),
    // Compile Inks CSS
    less: {
      dist: {
        files: {
          "css/<%= pkg.name %>.css":"less/<%= pkg.name %>.less",
          "css/<%= pkg.name %>-ie7.css":"less/<%= pkg.name %>-ie7.less"
        }
      },
      // Compile Inks minified CSS
      min: {
        options: {
          yuicompress: true
        },
        files: {
          "css/<%= pkg.name %>-min.css":"less/<%= pkg.name %>.less",
          "css/<%= pkg.name %>-ie7-min.css":"less/<%= pkg.name %>-ie7.less"            
        }
      }
    }

  });

  // Load the plugin that provides the "less" task.
  grunt.loadNpmTasks('grunt-contrib-less');
  // grunt.loadNpmTasks('grunt-git-dist'); 

  // Default task(s).
  grunt.registerTask('default', ['less']);
};
