//$(document).ready(init);


function lpClient(pathId, cidReq, moduleUrl, debugMode)
{
	this.pathId = pathId;
	this.cidReq = cidReq;
	this.moduleUrl = moduleUrl;
	this.debugMode = debugMode;

	// refreshed by api data
	this.itemId;
	this.APIInitialized = false;
	this.APILastError = "301";
	this.elementList = {};

	/**
	 * Commit
 	 * Send all values to server to store them
 	 *
 	 */
	this.commit = function commit() {
		var scormdata = $.toJSON(this.elementList);

	    $.ajax({
	    	type: "POST",
	        url: this.moduleUrl + "viewer/scormServer.php",
	        data: "cmd=doCommit&cidReq="+ this.cidReq + "&itemId=" + this.itemId + "&scormdata=" + scormdata,
	        success: this.refreshToc(),
	        dataType: 'html'
	    });

	    return false;
	}

	/**
	 * Set Content
 	 * Reinitialize API then open content in content frame
 	 *
 	 */
	this.setContent = function setContent(itemId) {
		// set item id
		this.itemId = itemId;
		// refresh api then refresh content
		$.getScript("apiData.php?cidReq="+ this.cidReq + "&pathId=" + this.pathId + "&itemId=" + itemId, this.rqOpenItem() );
	}

	/**
	 * Refresh the table of content
	 * TODO : does it depends on user id ?
	 */
	this.refreshToc = function refreshToc() {

	    // return all the toc that will fill the div in the toc frame
	    $.ajax({
	    	url: this.moduleUrl + "viewer/scormServer.php?cmd=rqToc&cidReq=" + this.cidReq + "&pathId=" + this.pathId,
	    	success: function(response){
		    	$("#table_of_content", top.frames["lp_toc"].document).empty();
		        $("#table_of_content", top.frames["lp_toc"].document).append(response);
		        },
	    	dataType: 'html'
	    });

	    return true;
	}

	/**
	 * Change the content of content frame according to itemId received
	 * Should be call as callback for the load of the api
	 *
	 */
	this.rqOpenItem = function rqOpenItem() {

		// get url and set frame location to this url
	    $.ajax({
	    	url: this.moduleUrl + "viewer/scormServer.php?cmd=rqContentUrl&cidReq=" + this.cidReq + "&pathId=" + this.pathId + "&itemId=" + this.itemId,
	    	success: this.mkOpenItem,
	    	dataType: 'html'
	    });
	}

	/**
	 * Change the content of content frame according to url received, also refresh the API
	 * Should be called by rqOpenItem
	 *
	 */
	this.mkOpenItem = function mkOpenItem(itemUrl) {

		if( itemUrl != '' )
		{
			parent.lp_content.location = itemUrl;
		}
		else
		{
			parent.lp_content.location = 'blank.htm';
		}

		this.isolateContent();
	}

	/**
	 * Append a debug msg to navigation frame
	 * TODO use a debug frame ?
	 *
	 */
	this.debug = function debug(msg, level) {

	    if( this.debugMode > level )
	    {
	        $("#lp_debug", top.frames["lp_nav"].document).append(msg + "<br />\n\n");
	    }
	}

	/**
	 * Remove headers, footers,... from a claroline content inserted as a module
	 *
	 */
	this.isolateContent = function isolateContent() {

		$("#topBanner", top.frames["lp_content"].document).hide();
		$("#userBanner", top.frames["lp_content"].document).hide();
		$("#courseBanner", top.frames["lp_content"].document).hide();
		$("#breadcrumbLine", top.frames["lp_content"].document).hide();
		$("#campusFooter", top.frames["lp_content"].document).hide();
	}
}

/**
 * Some utilities functions
 *
 */


function array_indexOf(arr,val)
{
    for ( var i = 0; i < arr.length; i++ )
    {
        if ( arr[i] == val )
        {
            return i;
        }
    }
    return -1;
}

function isDefined(a)
{
	return typeof a != 'undefined';
}

function isNull(a)
{
    return typeof a == 'object' && !a;
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