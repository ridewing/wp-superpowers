var SuperPowers;

var App = function(){

	var powers = {};

	powers.ajax = Ajax();
	powers.group = Group();
	powers.property = {
		image	: Image,
		slider 	: Slider
	};

	return powers;
}

$(document).ready(function(){
	SuperPowers = App();
})
