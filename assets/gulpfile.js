var gulp = require('gulp');
var ph = require('project-helpers')({
	mainLessFile : 'main.less'
});

ph.registerComponent('knockout');
ph.registerComponent('jquery');
ph.registerComponent('jquery-ui', 'jquery-ui.js');
ph.registerComponent('spin.js');
ph.registerComponent('ladda', 'dist/ladda.min.js');
ph.registerComponent('sweetalert', 'lib/sweet-alert.js');

ph.ready();

gulp.task("default", function () {
	ph.default();
});
