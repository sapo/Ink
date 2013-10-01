module.exports = function(grunt) {

  // Project configuration.
  grunt.initConfig({
    pkg: grunt.file.readJSON('package.json'),
    
    ink: {
      js: {
        paths: {
          src: './Inkjs/',
          output: './js/'
        }
      },
      css: {
        paths: {
          src: 'less/',
          output: 'css/'
        }
      }
    },

    concat: {
      dist: {
        src: ['<%= pkg.modules %>'],
        dest: "<%= ink.js.paths.output %><%= pkg.name %>.js"
      }
    },

    uglify: {
      dist: {
        files: {
          '<%= ink.js.paths.output %><%= pkg.name %>.min.js': ['<%= ink.js.paths.output %><%= pkg.name %>.js']
        }
      }
    },

    less: {
      dist: {
        files: {
          '<%= ink.css.paths.output %><%= pkg.name %>.css':['<%= ink.css.paths.src %><%= pkg.name %>.less'],
          '<%= ink.css.paths.output %><%= pkg.name %>-ie7.css':['<%= ink.css.paths.src %><%= pkg.name %>-ie7.less']
        }
      },
      // Compile Inks minified CSS
      min: {
        options: {
          yuicompress: true
        },
        files: {
          '<%= ink.css.paths.output %><%= pkg.name %>-min.css':'<%= ink.css.paths.src %><%= pkg.name %>.less',
          '<%= ink.css.paths.output %><%= pkg.name %>-ie7-min.css':'<%= ink.css.paths.src %><%= pkg.name %>-ie7.less'
        }
      }
    }

  });

  // Load the plugin that provides the "less" task.
  grunt.loadNpmTasks('grunt-contrib-less');
  grunt.loadNpmTasks('grunt-contrib-uglify');
  grunt.loadNpmTasks('grunt-contrib-concat');

  // Default task(s).
  grunt.registerTask('default', ['less', 'concat', 'uglify']);
};
