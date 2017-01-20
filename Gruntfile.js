module.exports = function(grunt) {
	grunt.initConfig({
		pkg: grunt.file.readJSON('package.json'),

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

		/** Sass decompiling */
		sass: {
			dist: {
				options: {
					style : 'compact',
					//sourcemap: 'none'
				},
				files:  [
					{
						expand: true,
						src: ['**/sass/*.scss'],
						ext: '.css',
						rename: function( dest, src ) {
							return src.replace( 'sass/', '' );
						},
					},
					{
						expand: true,
						src: ['**/scss/*.scss'],
						ext: '.css',
						rename: function( dest, src ) {
							return src.replace( 'scss/', '' );
						},
					},
				],
			}
		},

		/** CoffeeScript decompiling */
		coffee: {
			coffee_to_js: {
				options: {
					bare: true
				},
				expand: true,
				flatten: false,
				files: [
					{
						expand: true,
						src: ['**/*.coffee'],
						ext: '.js',
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
				files: [
					{
						expand: true,
						src: ['**/js/*.js', '!**/*.min.js'],
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
	grunt.registerTask('default',['watch']);
};