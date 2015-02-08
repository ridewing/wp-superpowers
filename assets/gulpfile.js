var gulp = require('gulp');
var ProjectHelper = require('project-helpers');

ProjectHelper.setup({
	debug : false,
	sourcePath : 'source/',
	componentsPath : 'source/components/',
	mainLessFile : 'main.less'
});

ProjectHelper.registerComponent('knockout');
ProjectHelper.registerComponent('jquery');
ProjectHelper.registerComponent('jquery-ui', 'jquery-ui.js');
ProjectHelper.registerComponent('spin.js');
ProjectHelper.registerComponent('ladda', 'dist/ladda.min.js');
ProjectHelper.registerComponent('sweetalert', 'lib/sweet-alert.js');

gulp.task("default", function () {
	ProjectHelper.default();
});
