var Image = function(item){

	if(typeof item === 'undefined') return;

	var defaultImage = item.find('.superpower-image-size-default');


	var data = defaultImage.data('size').split('x');

	var size = {
		width	: data[0],
		height 	: data[1]
	}

	var newSize = {
		width: 0,
		height : 0
	}

	var dialog = wp.media({
		title: "Select Image",
		multiple: false,
		library: { type: 'image' },
		button : { text : 'Add image' }
	});

	var property = {
		boot : function(){

			newSize = property.scaleImage(defaultImage);

			item.find('.superpower-image:not(.superpower-image-size-default)').each(function(){
				property.scaleImage($(this));
			});

			var attachment = item.find('.superpower-property-attachment').val();
			if(attachment.length){
				attachment = JSON.parse(attachment);
				property.attachImage(attachment);
			}
		},
		size : function(width, height) {
			return {width: width, height: height};
		},
		getImageSizeProperty : function(image) {
			var size = image.data('size').split('x')
			return property.size(size[0], size[1]);
		},
		getImageSizeScaled : function(image) {
			return image.data('scaled');
		},
		removeImage : function() {
			item.find('.superpower-property-value').val('');
			item.removeClass('has-image');
		},
		scaleImage : function(image){

			var size = property.getImageSizeProperty(image);

			var scale = 1.0;
			var maxWidth = 300;
			var maxHeight = 300;

			if(size.width > size.height && size.width > maxWidth){

				var width = Math.min(maxWidth, size.width);
				scale = width/size.width;
			} else if(size.height > maxHeight) {

				var height = Math.min(maxHeight, size.height);
				scale = height/size.height;
			}

			var width  = Math.round(size.width * scale);
			var height = Math.round(size.height * scale);

			image.css(property.size(width, height));

			image.data('scaled', property.size(width, height));

			return property.size(width, height);
		},
		openDialog : function(files){
			dialog.open();

			var uploadView = dialog.uploader;
			if ( uploadView.uploader && uploadView.uploader.ready){
				uploadView.uploader.uploader.addFile( _.toArray( files ) );
			} else {
				dialog.on('uploader:ready', function(){
					uploadView.uploader.uploader.addFile( _.toArray( files ) );
				})
			}

		},
		attachImage : function(attachment){

			item.addClass('has-image');
			item.find('.superpower-property-value').val(JSON.stringify({ id: attachment.id }));

			item.find('.superpower-image').each(function(){
				property.setImage($(this), attachment);
			})

		},
		setImage : function(image, attachment){

			image.find('.image-view').attr('src', attachment.url);
			var max = property.getImageSizeScaled(image);

			var size = fitBoundsAndPreserveAspectCSS(attachment.width, attachment.height, max.width, max.height);
			image.find('.image-view').css(size.size);
		}
	};

	item.on('.image-remove-button').on('click', function(e){
		e.preventDefault();
		property.removeImage();
	})

	defaultImage.on('drop', function(e){
		e.preventDefault();

		var files = event.dataTransfer.files;
		$(this).removeClass('file-hover');
		property.openDialog(files);
	});

	defaultImage.on('click', function(e){
		e.preventDefault();
		property.openDialog();
	})

	dialog.on('select', function(e)
	{
		var attachment = dialog.state().get('selection').first().toJSON();

		property.attachImage(attachment);
	});

	defaultImage.on('dragover',function(e){
		e.preventDefault();
		$(this).addClass('file-hover');
	})

	property.boot();

	function fitBoundsAndPreserveAspectCSS(width, height, targetWidth, targetHeight, fitInside)
	{
		var sourceRatio = width/height;
		var targetRatio = targetWidth/targetHeight;

		var rect = {};

		if (!fitInside && targetRatio > sourceRatio || fitInside && targetRatio < sourceRatio)
		{
			var ratioH = height/width;
			var newHeight = targetWidth*ratioH;
			//rect = [0, -Math.round((newHeight-targetHeight)/2), targetWidth, Math.round(newHeight)];
			rect.size =
			{
				top: -Math.round((newHeight-targetHeight)/2),
				left: 0,
				width: targetWidth,
				height: Math.round(newHeight)
			}
			rect.ratio = ratioH;
		}
		else
		{
			var ratioW = width/height;
			var newWidth = targetHeight*ratioW;

			//rect = [-Math.round((newWidth-targetWidth)/2), 0, Math.round(newWidth), targetHeight];
			rect.size =
			{
				top: 0,
				left: -Math.round((newWidth-targetWidth)/2),
				width: Math.round(newWidth),
				height: targetHeight
			}
			rect.ratio = ratioW;
		}
		//rect.push(targetRatio, sourceRatio);

		return rect;
	}

	return property;

}