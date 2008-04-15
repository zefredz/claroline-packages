	var openedEditors = new Array();

	$(document).ready( function ()
	{
		// bind events
		//	to cmd of each component (use livequery so that binding is automatically added on new DOM elements
		$('a.toggleEditorCmd').livequery('click', rqToggleEditor);
		$('a.deleteComponentCmd').livequery('click', deleteComponent);
		$('a.mkVisibleCmd').livequery('click', mkVisible);
		$('a.mkInvisibleCmd').livequery('click', mkInvisible);
		$('a.mkUpCmd').livequery('click', mkUp);
		$('a.mkDownCmd').livequery('click', mkDown);

		updateMoveCmdVisibility();
	});

	/*
		Add a component to the list
	*/
	function addComponent( type )
	{
	    $.ajax({
	    	url: moduleUrl + "ajaxHandler.php",
	    	data: "cmd=addComponent&cidReq="+ cidReq + "&pageId=" + pageId + "&itemType=" + type,
	    	success: function(response){
		    	var addedHtmlId = getComponentIdFromHtml(response);
				// append block at the end list
		    	$("#componentsContainer").append(response);

		    	$("#" + addedHtmlId).toggleEditor();
		    	updateMoveCmdVisibility();

		    },
	    	dataType: 'html'
	    });

	    return false;
	}


	function deleteComponent()
	{
		if( confirm("Are you sure to delete ?") )
		{
			// this.parentNode.parentNode.parentNode is the component
			var componentDiv = $(this.parentNode.parentNode.parentNode);

			var id = getIdFromComponent( componentDiv );
			var type = getTypeFromComponent( componentDiv );

			$.ajax({
		    	url: moduleUrl + "ajaxHandler.php",
		    	data: "cmd=deleteComponent&cidReq="+ cidReq + "&pageId=" + pageId + "&itemType=" + type + "&itemId=" + id,
		    	success: function(response){
		    		if( response = 'true' )
		    		{
		    			// delete was successfull so we can remove component from the DOM
			    		$("#component_" + id).remove();
			    		updateMoveCmdVisibility();
					}
			    },
		    	dataType: 'html'
		    });
		}
		return false;
	}

	function rqToggleEditor()
	{
		// this event is binded on a cmd link so we have to find its parent
		// and to toggle editor on this parent
		$(this.parentNode.parentNode.parentNode).toggleEditor();
		return false;
	}

	/*
	 * Toggle a component editor
	 */
	$.fn.toggleEditor = function()
	{
		return this.each(function(){
			// from here this is the object representing the DOM element so this.id is "component_X"

			var id = getIdFromComponent($(this));

			if( openedEditors[id] )
			{
				// remove tinyMCE if required
				// close editor
				// remove editor from the DOM as it seems to be already opened
				$("#component_" + id + " .componentEditor").remove();

				// mark editor as closed
				openedEditors[id] = false;
			}
			else
			{
				// open editor
				var type = getTypeFromComponent($(this));

				// get editor content
				// AND configure the submission to be made using ajax

				$.ajax({
			    	url: moduleUrl + "ajaxHandler.php",
			    	data: "cmd=getEditor&cidReq="+ cidReq + "&pageId=" + pageId + "&itemType=" + type + "&itemId=" + id,
			    	success: function(response){
			    		if( response != '' )
			    		{
			    			// append response
				    		$("#component_" + id + " .componentHeader").after(response);
				    		// add tinymce on all textarea
				    		$("#component_" + id + " textarea").tinymce();

							//
						    $("#component_" + id + " form").submit(function() {

						    	// force push content of editors in their respective textarea BEFORE submission
							    $("#component_" + id + " textarea").tinymceTriggerSave();
							    // submit the form
							    $(this).ajaxSubmit({
							        success: function(response){
							        	if( response == 'true' )
							        	{
							        		$("#component_" + id).refreshComponent();
										}
							        },  // post-submit callback
							        error : showErrorMessage
							    });

							    // return false to prevent normal browser submit and page navigation
							    return false;
							});

							// focus title
							$("#component_" + id + " #title_" + id).focus();

				    		// mark editor as opened
						    openedEditors[id] = true;
						}
				    },
			    	dataType: 'html'
			    });
			}

			return false;
		});

	}

	$.fn.refreshComponent =	function()
	{
		return this.each(function(){

			var id = getIdFromComponent($(this));
			var type = getTypeFromComponent($(this));

			// mark editor as closed
			openedEditors[id] = 0;

			// refresh content
			$.ajax({
		    	url: moduleUrl + "ajaxHandler.php",
		    	data: "cmd=getComponent&cidReq="+ cidReq + "&pageId=" + pageId + "&itemId=" + id + "&itemType=" + type,
		    	success: function(response){
					// replace current component by its new content
			    	$("#component_" + id)
			    		.after(response)
			    		.remove();

			    	updateMoveCmdVisibility();
			    },
			    error: showErrorMessage,
		    	dataType: 'html'
		    });
		});
	}

	function showErrorMessage()
	{
	    alert('Cannot send form');
	}

	function mkVisible()
	{
		// this.parentNode.parentNode.parentNode is the component
		var componentDiv = $(this.parentNode.parentNode.parentNode);

		var id = getIdFromComponent( componentDiv );
		var type = getTypeFromComponent( componentDiv );

	    $.ajax({
	    	url: moduleUrl + "ajaxHandler.php",
	    	data: "cmd=mkVisible&cidReq="+ cidReq + "&pageId=" + pageId + "&itemType=" + type + "&itemId=" + id,
	    	success: function(response){
					// switch eye
					if( response == "true" )
					{
						componentDiv.removeClass('invisible');
						$('.mkVisibleCmd', componentDiv).hide();
						$('.mkInvisibleCmd', componentDiv).show();

					}
		        },
	    	dataType: 'html'
	    });

	    return false;
	}

	function mkInvisible()
	{
		// this.parentNode.parentNode.parentNode is the component
		var componentDiv = $(this.parentNode.parentNode.parentNode);

		var id = getIdFromComponent( componentDiv );
		var type = getTypeFromComponent( componentDiv );

	    $.ajax({
	    	url: moduleUrl + "ajaxHandler.php",
	    	data: "cmd=mkInvisible&cidReq="+ cidReq + "&pageId=" + pageId + "&itemType=" + type + "&itemId=" + id,
	    	success: function(response){
					// switch eye
					if( response == "true" )
					{
						componentDiv.addClass('invisible');
						$('.mkInvisibleCmd', componentDiv).hide();
						$('.mkVisibleCmd', componentDiv).show();
					}

		        },
	    	dataType: 'html'
	    });

	    return false;
	}

	function mkUp()
	{
		// this.parentNode.parentNode.parentNode is the component
		var componentDiv = $(this.parentNode.parentNode.parentNode);

		var id = getIdFromComponent( componentDiv );
		var type = getTypeFromComponent( componentDiv );

	    $.ajax({
	    	url: moduleUrl + "ajaxHandler.php",
	    	data: "cmd=mkUp&cidReq="+ cidReq + "&pageId=" + pageId + "&itemType=" + type + "&itemId=" + id,
	    	success: function(response){
					// switch eye
					if( response == "true" )
					{
						// move component up : insert component to move before the one before him
						$("#component_" + id )
							.prev()
							.before( $("#component_" + id) );

						updateMoveCmdVisibility();
					}
		        },
	    	dataType: 'html'
	    });

	    return false;
	}

	function mkDown()
	{
		// this.parentNode.parentNode.parentNode is the component
		var componentDiv = $(this.parentNode.parentNode.parentNode);

		var id = getIdFromComponent( componentDiv );
		var type = getTypeFromComponent( componentDiv );

	    $.ajax({
	    	url: moduleUrl + "ajaxHandler.php",
	    	data: "cmd=mkDown&cidReq="+ cidReq + "&pageId=" + pageId + "&itemType=" + type + "&itemId=" + id,
	    	success: function(response){
					// switch eye
					if( response == "true" )
					{
						// move component down : insert component_id after the component after him
						$("#component_" + id )
							.next()
							.after( $("#component_" + id) );

						updateMoveCmdVisibility();
					}
		        },
	    	dataType: 'html'
	    });

	    return false;
	}

	function getIdFromComponent( componentDiv )
	{
		var componentId = componentDiv.attr('id');
		// get what is after 'component_'
		return componentId.substring(10, componentId.length);
	}

	function getTypeFromComponent( componentDiv )
	{
		var componentClass = componentDiv.attr('class');

		// find 'type_' position
		var typeClassPos = componentClass.indexOf('type_');

		if( typeClassPos > -1 )
		{
			// find first space after finded 'type_'
			var spacePos = componentClass.indexOf(' ', typeClassPos);

			if( spacePos == -1 )
			{
				// if no space 'type_X' is probably at the end of string
				spacePos = componentClass.length;
			}

			// return string between (end of) 'type_' and the space
			return componentClass.substring(typeClassPos + 5, spacePos);
		}
		else
		{
			return '';
		}
	}

	function getComponentIdFromHtml( html )
	{
		var componentStringStart = html.indexOf('component_');

		if( componentStringStart > -1 )
		{
			// find first space after finded 'component_'
			var endOfId = html.indexOf('"', componentStringStart);

			if( endOfId == -1 )
			{
				return false;
			}

			// return string between (end of) 'component_' and the closing "
			return html.substring(componentStringStart, endOfId);
		}
		else
		{
			return '';
		}
	}

	function updateMoveCmdVisibility()
	{
		// show all
		$('#componentsContainer .sortableComponent a.mkUpCmd').show();
		$('#componentsContainer .sortableComponent a.mkDownCmd').show();

		// hide up command for first component, and down command for the last
		$('#componentsContainer .sortableComponent:first-child a.mkUpCmd').hide();
		$('#componentsContainer .sortableComponent:last-child a.mkDownCmd').hide();
	}

	$.fn.tinymce = function(options)
	{
	    return this.each(function(){
	    	try {
	    		
	    		tinyMCE.execCommand('mceAddControl', false, this.id);
	    	}
	    	catch(e)
	    	{
	    		alert(e.message);
	    	}
			return '';
	    });
	}

	$.fn.tinymceTriggerSave = function(options)
	{
	    return this.each(function(){
	    	try {

				for ( var n in tinyMCE.instances) {
					var inst = tinyMCE.getInstanceById(n);

					if( inst.formTargetElementId == this.id )
					{
						inst.triggerSave(false,false);
					}
				}

				// force remove of this tinymce instance
				tinyMCE.execCommand('mceRemoveControl', false, this.id);

	    	}
	    	catch(e)
	    	{
	    		alert(e.message);
	    	}
			return '';
	    });
	}
