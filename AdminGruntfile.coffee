
module.exports = (grunt) ->

  grunt.initConfig

    useminPrepare:
      html: ['app/AdminModule/templates/@layout.latte']
      options:
        dest: './'

    netteBasePath:
      basePath: 'www'
      options:
        removeFromPath: ['app/AdminModule/templates/'] # unix
        # removeFromPath: ['app\\'] // win

    stylus:
      compile:
        options:
          paths: ['www/style/admin']
          compress: false
          linenos: true
        files:
          'www/style/admin/main.css': 'www/style/admin/main.styl'#  1:1 compile
          # 'www/style/main.css': ['www/style/*.styl'] # compile and concat into single file

    sass:
      dist:
        options:
          includePaths: ['www/style/foundation-admin']
          sourceMap: true

        files:
          'www/style/foundation-admin/foundation.css': ['www/style/foundation-admin/foundation.scss']
          'www/style/foundation-admin/normalize.css': 'www/style/foundation-admin/normalize.scss'

    watch:
      styles:
        files: 'www/style/**/*.styl'
        tasks: ['stylus']
        options:
          interrupt: true

      sass:
        files: ['www/style/foundation-admin/**/*.scss']
        tasks: ['sass']

    autoprefixer:
      no_dest:
        src: 'www/style/admin/admin.min.css'


  # These plugins provide necessary tasks.
  grunt.loadNpmTasks 'grunt-contrib-watch'
  grunt.loadNpmTasks 'grunt-contrib-stylus'
  grunt.loadNpmTasks 'grunt-contrib-concat'
  grunt.loadNpmTasks 'grunt-contrib-uglify'
  grunt.loadNpmTasks 'grunt-contrib-cssmin'
  grunt.loadNpmTasks 'grunt-usemin'
  grunt.loadNpmTasks 'grunt-nette-basepath'
  grunt.loadNpmTasks 'grunt-sass'
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
  #  'autoprefixer'
  ]

