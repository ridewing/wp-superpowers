var Group = function(){

	var group = {
		add : function (id, callback) {
			SuperPowers.ajax.controller(_superData.controller, 'addGroup', { groupId : id }, function (success, response) {

				if (success) {
					$('#' + id).find('.inside .super-group-content').last().after(response.data);
				}

				callback();
			})
		},
		remove : function (groupId, index, callback) {
			var item = $('#' + groupId).find('.super-group-content').eq(index);
			item.addClass('removed');
			window.setTimeout(function () {
				item.remove();
				group.updateGroupIndexes(groupId);
			}, 500)
		},
		updateGroupIndexes : function (groupId) {

			// Find all instances of this group
			var items = $('#' + groupId).find('.super-group-content');

			items.each(function (key, item) {

				// Update controll number
				$(item).find('.controlls .number').text(key + 1);

				// Find all property inputs in this group
				var properties = $(item).find('.superpower-property-value');

				properties.each(function (index, prop) {

					// Fetch the name model so that we can create a new name
					var nameModel = $(prop).data('name');

					// Update input name so that on save we have the correct input name
					$(prop).attr('name', nameModel.replace('%index%', key));
					$(prop).attr('id', nameModel.replace('%index%', key));
				})
			})
		},
		moveItemUp : function (item) {
			var prev = item.prev();
			if (prev.length) {
				item.addClass('move-up');
				prev.addClass('move-down');
				window.setTimeout(function () {
					item.removeClass('move-up');
					prev.removeClass('move-down');

					var hold = item.detach();
					prev.before(hold);
					group.updateGroupIndexes(item.data('id'));
				}, 350);
			}
		},
		moveItemDown : function (item) {
			var next = item.next();
			if (next.length) {
				item.addClass('move-down');
				next.addClass('move-up');
				window.setTimeout(function () {
					item.removeClass('move-down');
					next.removeClass('move-up');

					var hold = item.detach();
					next.after(hold);
					group.updateGroupIndexes(item.data('id'));
				}, 350)
			}
		}

	};

	$('.super-group-add').on('click', function(e){
		e.preventDefault();

		var loader = Ladda.create($(this)[0]);
		loader.start();
		group.add($(this).attr('id'), function(){
			loader.stop();
		});
	});

	$('.super-group-wrapper').on('click', '.super-group-content .button-remove', function(e){
		e.preventDefault();
		group.remove($(this).data('id'), $(this).parents('.super-group-content:first').index(), function(){

		})
	});

	$('.super-group-wrapper').on('click', '.super-group-content .controlls .controll', function(e) {
		e.preventDefault();
		var item = $(this).parents('.super-group-content:first');
		if($(this).hasClass('controll-up')) {
			group.moveItemUp(item);
		} else if($(this).hasClass('controll-down')) {
			group.moveItemDown(item);
		}
	})
	return group;
}