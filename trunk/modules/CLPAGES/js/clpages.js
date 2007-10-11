	var openedEditors = new Array();

	$(document).ready( function ()
	{

		$("#loading").hide();

		$("#loading").ajaxStart(function(){
			$(this).show();
		});

		$("#loading").ajaxStop(function(){
			$(this).hide();
		});

		$('a.toggleEditorCmd').livequery('click', toggleEditor);
		$('a.deleteItemCmd').livequery('click', deleteItem);
		$('a.mkVisibleCmd').livequery('click', mkVisible);
		$('a.mkInvisibleCmd').livequery('click', mkInvisible);



		// sortable list of components
		$('div.componentWrapper').Sortable(
			{
				accept: 'sortableComponent',
				helperclass: 'sortHelper',
				activeclass : 'sortableactive',
				hoverclass : 'sortablehover',
				handle: 'div.componentHeader',
				tolerance: 'pointer',
				axis : 'vertically',
				onChange : function(serializedList)
				{
					var hash = serializedList[0].hash;

					$.ajax({
				    	type: "POST",
				        url: moduleUrl + "ajaxHandler.php",
				        data: "cmd=exOrder&cidReq="+ cidReq + "&docId=" + docId + "&" + hash,
				        dataType: 'html'
				    });
				},
				onStart : function()
				{
					$.iAutoscroller.start(this, document.getElementsByTagName('body'));
				},
				onStop : function()
				{
					$.iAutoscroller.stop();
				}
			}
		);
	});

	function addItem( itemType )
	{
	    $.ajax({
	    	url: moduleUrl + "ajaxHandler.php",
	    	data: "cmd=addItem&cidReq="+ cidReq + "&pageId=" + pageId + "&itemType=" + itemType,
	    	success: function(response){
	    		// we need to update sortable with id of last add html block, so find id of this block (component_*)
				var addedHtmlId = getComponentIdFromHtml(response);
				// append block at the end list and update sortable object
		    	$("#componentsContainer").append(response).SortableAddItem(document.getElementById(addedHtmlId));
		    	// scroll to the new item
		    	$("#componentsContainer :last-child").ScrollTo(800);
		    	// newComponent.toggleEditor();
		        },
	    	dataType: 'html'
	    });

	    return false;
	}


	function deleteItem()
	{
		if( confirm("Are you sure to delete ?") )
		{
			// this.parentNode.parentNode.parentNode is the component
			var componentDiv = $(this.parentNode.parentNode.parentNode);

			var id = getIdFromComponent( componentDiv );
			var type = getTypeFromComponent( componentDiv );

			$.ajax({
		    	url: moduleUrl + "ajaxHandler.php",
		    	data: "cmd=deleteItem&cidReq="+ cidReq + "&pageId=" + pageId + "&itemType=" + type + "&itemId=" + id,
		    	success: function(response){
		    		if( response = 'true' )
		    		{
			    		$("#component_" + id).remove();
					}
			    },
		    	dataType: 'html'
		    });
		}
		return false;
	}

	function toggleEditor()
	{
		// this.parentNode.parentNode.parentNode is the component
		var componentDiv = $(this.parentNode.parentNode.parentNode);

		var id = getIdFromComponent( componentDiv );
		var type = getTypeFromComponent( componentDiv );

		if( openedEditors[id] == 1 )
		{
			// remove editor from the DOM
			// TODO check if something has to be saved ?
			$("#component_" + id + " .componentEditor").remove();
			// mark editor as closed
			openedEditors[id] = 0;
		}
		else
		{
			$.ajax({
		    	url: moduleUrl + "ajaxHandler.php",
		    	data: "cmd=getEditor&cidReq="+ cidReq + "&pageId=" + pageId + "&itemType=" + type + "&itemId=" + id,
		    	success: function(response){
		    		if( response != '' )
		    		{
			    		$("#component_" + id + " .componentHeader").after(response);
			    		$("#component_" + id + " textarea").tinymce();

					    $("#component_" + id + " form").submit(function() {

					    	// force push content of editors in their respective textarea BEFORE submission
						    $("#component_" + id + " textarea").tinymceTriggerSave();
						    // submit the form
						    $(this).ajaxSubmit({
						        success: function(response){
						        	if( response == 'true' )
						        	{
										refreshComponent(id, type);
									}
						        },  // post-submit callback
						        error : showErrorMessage
						    });

						    // return false to prevent normal browser submit and page navigation
						    return false;
						});
			    		// mark editor as opened
					    openedEditors[id] = 1;
					}
			    },
		    	dataType: 'html'
		    });
		}

		return false;

	}

	function refreshComponent(id, type)
	{
		// mark editor as closed
		openedEditors[id] = 0;

		// refresh content
		$.ajax({
	    	url: moduleUrl + "ajaxHandler.php",
	    	data: "cmd=getComponent&cidReq="+ cidReq + "&pageId=" + pageId + "&itemId=" + id + "&itemType=" + type,
	    	success: function(response){
	    		// we need to update sortable with id of last add html block, so find id of this block (component_*)
				var addedHtmlId = getComponentIdFromHtml(response);

				// replace current component by its new content and update sortable object
		    	$("#component_" + id)
		    		.after(response)
		    		.remove();
		    	$("#componentsContainer").SortableAddItem(document.getElementById(addedHtmlId));
		        },
		    error: showErrorMessage,
	    	dataType: 'html'
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


	$.fn.tinymce = function(options)
	{
	    return this.each(function(){
	    	try {
	    		tinyMCE.addMCEControl(document.getElementById(this.id), this.id);
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
				tinyMCE.removeMCEControl(this.id);

	    	}
	    	catch(e)
	    	{
	    		alert(e.message);
	    	}
			return '';
	    });
	}

	function dump(arr,level) {
		var dumped_text = "";
		if(!level) level = 0;

		//The padding given at the beginning of the line.
		var level_padding = "";
		for(var j=0;j<level+1;j++) level_padding += "    ";

		if(typeof(arr) == 'object') { //Array/Hashes/Objects
		 for(var item in arr) {
		  var value = arr[item];

		  if(typeof(value) == 'object') { //If it is an array,
		   dumped_text += level_padding + "'" + item + "' ...\n";
		   dumped_text += dump(value,level+1);
		  } else {
		   dumped_text += level_padding + "'" + item + "' => \"" + value + "\"\n";
		  }
		 }
		} else { //Stings/Chars/Numbers etc.
		 dumped_text = "===>"+arr+"<===("+typeof(arr)+")";
		}
		return dumped_text;
	}
