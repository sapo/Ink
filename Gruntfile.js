module.exports = function(grunt) {

  // Project configuration.
  grunt.initConfig({
    pkg: grunt.file.readJSON('package.json'),

    ink: {
      folders: {
        js: {
          src: './src/js/',
          output: './dist/js/'
        },
        css: {
          src: './src/less/',
          output: './dist/css/'
        },
      },
    },

    // builds the javascript bundles
    concat: {

      // ink.js
      ink: {
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
          ],
          dest: '<%= ink.folders.js.output %>',
          rename: function(dest, src) {
            return dest + 'ink.js';
          },
        },
        ],
      },

      // ink-all.js
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
          'UI/Common/lib.js',
          'UI/**/lib.js'
          ],
          dest: '<%= ink.folders.js.output %>',
          rename: function(dest, src) {
            return dest + 'ink-all.js';
          },
        },
        ],
      },

      // ink-ui.js
      ink_ui: {
        files: [
        {
          expand: true,
          flatten: true,
          cwd: '<%= ink.folders.js.src %>UI/',
          src: [
          'UI/Common/lib.js',
          '**/lib.js'
          ],
          dest: '<%= ink.folders.js.output %>',
          rename: function(dest, src) {
            return dest + 'ink-ui.js';
          },
        },
        ],
      },

      // ui components as single files
      ui: {
        files: [
        {
          expand: true,
          cwd: '<%= ink.folders.js.src %>UI/',
          src: ['**/[0-9]/lib.js'],
          dest: '<%= ink.folders.js.output %>',
          rename: function(dest, src) {
              // check if this is v1
              if (src.substring(src.lastIndexOf('/'),-1).match(/[0-9]/) && src.substring(src.lastIndexOf('/'),-1).match(/[0-9]/) == 1)
              {
                // and it it is discard the version number               
                return dest + 'ink.' + src.substring(0, src.indexOf('/')).toLowerCase() + '.js';
              } 
              else 
              {
                // or replace the slash by an underscore and version number and prepend to dest file name 
                return dest + 'ink.' + src.substring(0, src.lastIndexOf('/')).toLowerCase().replace('/','-') + '.js';                
              }
            },
          },
          ],
        },
      },

    // TODO: build on separate folder and move to dist
    clean: ["<%= ink.folders.js.output %>/ink*.js", "./docs"],


    qunit: {
      options: {
        // inject: 'js/tests/assets/phantom.js',
        urls: ['http://localhost:8000/js/tests/index.html']
      },
      files: ['js/tests/*.html']
    },
    yuidoc: {
      compile: {
        name:        'Ink.js',
        description: 'JavaScript Framework by SAPO',
        version:     'latest',
        url:         'http://ink.sapo.pt/',
        logo:        'http://js.ink.sapo.pt/docs/yuidoclogo.png',
        options: {
          paths: ['./src/js'],
          outdir: './docs'
        }
      }
    },
    connect: {
      server: {
        options: {
          port: 8000,
          debug: true,
          base: '.'
        }
      },
      docs: {
        options: {
          port: 4567,
          debug: true,
          base: 'docs',
          keepalive:true
        }
      }
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
      ink: {
        src: '<%= ink.folders.js.output %>ink.js', 
        dest: '<%= ink.folders.js.output %>ink.min.js'
      },
      ink_all: {
        src: '<%= ink.folders.js.output %>ink-all.js', 
        dest: '<%= ink.folders.js.output %>ink-all.min.js'
      },
      ink_ui: {
        src: '<%= ink.folders.js.output %>ink-ui.js', 
        dest: '<%= ink.folders.js.output %>ink-ui.min.js'
      },
    },

    // COMPILES THE CSS
    less: {
      dist: {
        files: {
          '<%= ink.folders.css.output %><%= pkg.name %>.css':['<%= ink.folders.css.src %><%= pkg.name %>.less'],
          '<%= ink.folders.css.output %><%= pkg.name %>-ie7.css':['<%= ink.folders.css.src %><%= pkg.name %>-ie7.less']
        }
      },

      // COMPILES THE MINIFIED CSS
      min: {
        options: {
          yuicompress: true
        },
        files: {
          '<%= ink.folders.css.output %><%= pkg.name %>-min.css':'<%= ink.folders.css.src %><%= pkg.name %>.less',
          '<%= ink.folders.css.output %><%= pkg.name %>-ie7-min.css':'<%= ink.folders.css.src %><%= pkg.name %>-ie7.less'
        }
      }
    }
  });

grunt.loadNpmTasks('grunt-contrib-less');
grunt.loadNpmTasks('grunt-contrib-uglify');
grunt.loadNpmTasks('grunt-contrib-concat');
grunt.loadNpmTasks('grunt-contrib-clean');
grunt.loadNpmTasks('grunt-contrib-connect');
grunt.loadNpmTasks('grunt-contrib-qunit');
grunt.loadNpmTasks('grunt-contrib-yuidoc');

grunt.registerTask('default', ['less','clean','concat','uglify']);
grunt.registerTask('test', ['connect:server', 'qunit']);
grunt.registerTask('liveDocs', ['yuidoc:compile', 'connect:docs'])
grunt.registerTask('custom_bundle', 'Create your custom bundle from a json file', function(fileName){
  if (arguments.length === 0) {
    grunt.log.error('You need to specify a file name');
  }

  var bundle = grunt.file.readJSON(fileName);
  var dependencies = bundle.dependencies || [];
  grunt.config.set('concat.ink_custom', {
   files: [
   {
    expand: true,
    flatten: true,
    cwd: '<%= ink.folders.js.src %>',
    dest: '<%= ink.folders.js.output %>' + bundle['name'] + '/',
    src: dependencies.map(function(depName){
      return depName.replace('Ink/', '') + '/lib.js';
    }),
    rename: function(dest, src) {
      return dest + 'ink-custom.js';
    },
  },
  ],       
});
  grunt.config.set('uglify.ink_custom', {
    src: '<%= ink.folders.js.output %>' + bundle['name'] + '/' + 'ink-custom.js',
    dest: '<%= ink.folders.js.output %>' + bundle['name'] + '/' + 'ink-custom.min.js'
  });
  grunt.task.run(['concat:ink_custom', 'uglify:ink_custom']);
});
};