$( document ).ready(function() {

	/**
	* Récupérer le formulaire par rapport
	* au type d'utilisateur selectionné.
	*/

	$('#role').change(function(){

		var route = '';

		switch ($(this).val()) {
			case 'student' :
				route = '../student/new';
	    	break;
	    	case 'teacher' :
	    		route = '../teacher/new';
	    	break
	    	case 'manager' :
	    		route = '../manager/new';
	    	break
		}

		$.ajax(route)
		  .done(function(layout) {
		    $('.panel-content').html(layout);
		  })
		  .fail(function() {
		    alert( "error" );
		  })

	})
	$("#role").trigger("change", [ 'student' ]);

 });