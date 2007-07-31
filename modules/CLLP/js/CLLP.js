//$(document).ready(init);

function init()
{
    /*
    // use frameReady to access the inner frames
    $.frameReady(function(){
		$('#topBanner').hide();
    	$('#breadcrumbLine').hide();
    	$('#campusFooter').hide;
	},"top.lp_content" );

	$.frameReady(refreshToc, "top.lp_toc");
	*/

	//setTimeout("refreshToc()", 1000);
}

/**
 * Send all values to server to store them
 *
 */
function doCommit()
{
	var scormdata = $.toJSON(elementList);

    $.ajax({
    	type: "POST",
        url: moduleUrl + "viewer/scormServer.php",
        data: "cmd=doCommit&cidReq="+ cidReq + "&itemId=" + itemId + "&scormdata=" + scormdata,
        success: function(response){
            refreshToc();
            },
        dataType: 'html'});

    return false;
}

/**
 * Ask a refresh of viewer (toc, nav, api, content)
 *
 */
function refreshViewer()
{
    refreshToc();
    refreshNav();
    refreshApi();
    refreshContent();
}

function refreshToc()
{
    $("#table_of_content", top.frames["lp_toc"].document).empty();

    getToc(pathId);
}

function refreshNav()
{

}

function refreshContent()
{

}

function refreshApi()
{

}

/**
 * Ajax methods
 *
 */

function getToc(pathId)
{
    var tocHtml = "";
    // return all the toc that will fill the div in the toc frame
    $.ajax({
    url: moduleUrl + "viewer/scormServer.php?cmd=rqToc&pathId=" + pathId + "&cidReq=" + cidReq,
    success: function(response){
        $("#table_of_content", top.frames["lp_toc"].document).append(response);
        },
    dataType: 'html'});

    return true;
}

function rqOpenItem(itemId)
{
	// get url and set frame location to this url
    $.ajax({
    url: moduleUrl + "viewer/scormServer.php?cmd=rqContentUrl&pathId=" + pathId + "&itemId=" + itemId + "&cidReq=" + cidReq,
    success: mkOpenItem,
    dataType: 'html'});
}

function mkOpenItem(response)
{
	if( response != '' )
	{
		parent.lp_content.location = response;
	}
	else
	{
		parent.lp_content.location = 'blank.htm';
	}
	//refreshApi();
	//refreshContent();
}

/**
 * Some utilities functions
 *
 */

function debug(msg, level)
{
    if( debug_mode > level )
    {
        $("#lp_debug", top.frames["lp_nav"].document).append(msg + "<br />\n\n");
    }
}

function isolateContent()
{
	$('#topBanner').hide();
	$('#breadcrumbLine').hide();
	$('#campusFooter').hide();
}

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