$(document).ready(init);

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
	
	setTimeout("refreshToc()", 1000);
}

/**
 * Send all values to server to store them 
 *
 */
function doCommit()
{
    $.ajax({
        url: moduleUrl + "viewer/scormServer.php?cmd=doCommit&cidReq=" + cidReq, 
        success: function(response){
            refreshViewer();
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

function getContentUrlById(pathId, itemId)
{
    var contentUrl = "";
    
    $.ajax({
    url: moduleUrl + "viewer/scormServer.php?cmd=rqContentUrl&pathId=" + pathId + " &itemId=" + itemId + "&cidReq=" + cidReq, 
    success: function(response){
        contentUrl = response;
        }, 
    dataType: 'html'});
         
    return contentUrl;
}


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
         
    return tocHtml;
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

