var Slider = function(item, value) {

	var property = {

		boot : function () {
			var slider = $(item).slider({
				min: 0,
				max: 10,
				range: "min",
				value: value*2,
				slide: function( event, ui ) {
					var value = ui.value/2;
					item.find('.superpower-property-value').val(value);
					item.prev().find('span').text(value.toFixed(1));
				}
			});
		}
	};

	property.boot();

}