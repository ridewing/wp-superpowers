var Ajax = function(){

	var ajax = {
		controller : function(controller, method, args, callback){

			args.postId = _superData.postId;

			$.ajax({
				url : _superData.api,
				data: {controller : controller, method : method, args: args},
				success : function(resp){

					if(typeof callback === 'function')
						callback(resp.success, resp);
				}
			})
		}
	};

	return ajax;
}