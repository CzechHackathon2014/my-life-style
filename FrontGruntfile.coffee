
module.exports = (grunt) ->

  grunt.initConfig

    useminPrepare:
      html: ['app/FrontModule/templates/@layout.latte']
      options:
        dest: '.'

    netteBasePath:
      basePath: 'www'
      options:
        removeFromPath: ['app/FrontModule/templates/']

    stylus:
      compile:
        options:
          paths: ['www/style/site']
          compress: false
          linenos: true
        files:
          'www/style/site/main.css': 'www/style/site/main.styl'#  1:1 compile
          # 'www/style/main.css': ['www/style/*.styl'] # compile and concat into single file
    sass:
      dist:
        options:
          includePaths: ['www/style/foundation']
          sourceMap: true

        files:
          'www/style/foundation/foundation.css': ['www/style/foundation/foundation.scss']
          'www/style/foundation/normalize.css': 'www/style/foundation/normalize.scss'

    watch:
      styles:
        files: 'www/style/**/*.styl'
        tasks: ['stylus']
        options:
          interrupt: true

      sass:
        files: ['www/style/foundation/**/*.scss']
        tasks: ['sass']

    autoprefixer:
      no_dest:
        src: 'www/style/site/app.min.css'

  # These plugins provide necessary tasks.
  grunt.loadNpmTasks 'grunt-contrib-watch'
  grunt.loadNpmTasks 'grunt-contrib-stylus'
  grunt.loadNpmTasks 'grunt-contrib-concat'
  grunt.loadNpmTasks 'grunt-contrib-uglify'
  grunt.loadNpmTasks 'grunt-contrib-cssmin'
  grunt.loadNpmTasks 'grunt-usemin'
  grunt.loadNpmTasks 'grunt-nette-basepath'
  grunt.loadNpmTasks 'grunt-autoprefixer'

  # Default task.
  grunt.registerTask 'default', [
    'stylus'
    'sass'
    'useminPrepare'
    'netteBasePath'
    'concat'
    'uglify'
    'cssmin'
    'autoprefixer'
  ]
