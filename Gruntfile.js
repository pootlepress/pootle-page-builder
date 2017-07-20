module.exports = function(grunt) {
	grunt.initConfig({
		pkg: grunt.file.readJSON('package.json'),

		/** Sass decompiling */
		sass: {
			dist: {
				options: {
					style : 'compressed',
					sourcemap: 'none'
				},
				files:  [
					{ // /**/*.scss files
						expand: true,
						cwd: 'css',
						dest: 'css',
						src: ['**/*.scss'],
						ext: '.css',
						rename: function( dest, src ) {
							return dest + '/' + src.replace( 'scss/', '' ).replace( 'sass/', '' );
						},
					},
					{ // /**/*.scss files
						expand: true,
						cwd: 'inc',
						dest: 'inc',
						src: ['**/*.scss'],
						ext: '.css',
						rename: function( dest, src ) {
							return dest + '/' + src.replace( 'scss/', '' ).replace( 'sass/', '' );
						},
					},
				],
			}
		},

		/** Autoprefixing */
		autoprefixer: {
			options : {
				browsers : ['last 5 versions'],
			},
			multiple_files : {
				expand : true,
				flatten : true,
				src : '*.css',
				dest : ''
			}
		},

		/** CoffeeScript decompiling */
		coffee: {
			coffee_to_js: {
				options: {
					sourceMap: true,
					bare: true
				},
				expand: true,
				flatten: false,
				files: [
					{
						expand: true,
						src: ['**/*.coffee'],
						ext: '.dev.js',
						rename: function( dest, src ) {
							return src.replace( 'coffee/', '' );
						},
					},
				],
			}
		},

		/** Minify JS */
		uglify : {
			minify: {
				options: {
					sourceMap: true,
					sourceMapIn: function( file ){return file + '.map';}
				},
				files: [
					{
						expand: true,
						src: ['**/*.dev.js', '!**/*.min.js'],
						ext: '.min.js',
						extDot: 'first',
					},
				],
			},
		},

		/** Watcher */
		watch : {
			css: {
				files: '**/*.scss',
				tasks: [ 'sass', 'newer:autoprefixer' ]
			},
			js: {
				files: '**/*.coffee',
				tasks: [ 'coffee', 'newer:uglify' ]
			}
		}
	});

	grunt.loadNpmTasks('grunt-newer');
	grunt.loadNpmTasks('grunt-autoprefixer');
	grunt.loadNpmTasks('grunt-contrib-sass');
	grunt.loadNpmTasks('grunt-contrib-coffee');
	grunt.loadNpmTasks('grunt-contrib-uglify');
	grunt.loadNpmTasks('grunt-contrib-watch');
	grunt.registerTask( 'default', ['sass', 'autoprefixer', 'coffee', 'uglify', 'watch'] );
//	grunt.registerTask( 'default', ['coffee', 'uglify'] );
};