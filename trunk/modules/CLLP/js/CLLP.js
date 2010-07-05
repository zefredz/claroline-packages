// $Id$
/**
 * Librairy to manage a learning path in Ajax
 * 
 * @version 0.1 $Revision$
 * @copyright (c) 2001-2007 Universite catholique de Louvain (UCL)
 * @license http://www.gnu.org/copyleft/gpl.html (GPL) GENERAL PUBLIC LICENSE
 * @package CLLP
 * @author Sebastien Piraux
 * @author Dimitri Rambout <dim@claroline.net>
 */
//$(document).ready(init);

/**
 * Function to initialize variables/functions in object
 *
 * @param int pathId Id of a learning path
 * @param string cidReq Id of a course
 * @param string moduleUrl Url of a module
 * @param boolean debugMode Active or not the debug mode
 */
function lpHandler(pathId, cidReq, moduleUrl, debugMode)
{
    this.pathId = pathId;
    this.cidReq = cidReq;
    this.moduleUrl = moduleUrl;
    this.debugMode = debugMode;


    /**
     * Commit
      * Send all values to server to store them
      *
      */
    this.commit = commit;

    /**
     * Set Content
      * Reinitialize API then open content in content frame
      *
      */
    this.setContent = setContent;

    /**
     * Refresh the table of content
     * TODO : does it depends on user id ?
     */
    this.refreshToc = refreshToc;

    /**
     * Change the content of content frame according to itemId received
     * Should be call as callback for the load of the api
     *
     */
    this.rqOpenItem = rqOpenItem;

    /**
     * Change the content of content frame according to url received
     * Should be called by rqOpenItem
     *
     */
    this.mkOpenItem = mkOpenItem;

    // Presentation function :
    
    /**
     * jump to the previous module if any
     *
     */
    this.goPrevious = goPrevious;
    
    /**
     * jump to the next module if any
     *
     */
    this.goNext = goNext;
    
    /**
     * Resize frameset to have a fullscreen
     *
     */
    this.setFullscreen = setFullscreen;

    /**
     * Resize frameset to have a embedded mode
     *
     */
    this.setEmbedded = setEmbedded;

    /**
     * Append a debug msg to navigation frame
     * TODO use a debug frame ?
     *
     */
    this.debug = debug;
}


/**
 * Functions mapped in object
 */

/**
 * Commit
 * Send all values to server to store them
 *
 * @param string datamodel the data model of a path
 * @return HTML
 */
function commit(datamodel) {
    debug("Commit",1);

    var datamodelValues = {};
    // avoid sending complete datamodel so get only key and values
    for( var key in datamodel )
    {
        var item = datamodel[key];

        if(typeof(item) == 'object')
        {
            datamodelValues[ key ] = item['value'];
        }
        else
        {
            // do nothing, this should be an array with value, mod and format as keys
        }
    }
        var jsonDatamodelValues = $.toJSON(datamodelValues);
        $.ajax({
        type: "POST",
        url: lpHandler.moduleUrl + "viewer/scormServer.php",
        data: "cmd=doCommit&cidReq="+ lpHandler.cidReq + "&pathId=" + lpHandler.pathId + "&itemId=" + lpHandler.itemId + "&scormdata=" + jsonDatamodelValues,
        success: function( response ){
                    refreshToc();
                    //branching conditions
                    $.ajax({
                        type: "POST",
                        url: lpHandler.moduleUrl + "viewer/scormServer.php",
                        data: "cmd=rqBranchConditions&cidReq=" + lpHandler.cidReq + "&pathId=" + lpHandler.pathId + "&itemId=" + lpHandler.itemId,
                        success: function(response)
                        {
                            if( !isNaN(parseInt(response, 10)) )
                            {                                
                                lpHandler.setContent(response);    
                            }
                            else
                            {
                                if( response )
                                {
                                    lpHandler.itemId = 0;
                                    this.itemId = 0;
                                    mkOpenItem( response );
                                }                                
                            }
                        },
                        dataType: 'html'
                    });    
                },
        dataType: 'html'
    });

    return false;
}


/**
 * Set Content
 * Reinitialize API then open content in content frame
 *
 */
function setContent(itemId) {
    debug("setContent("+itemId+")",1);
    // set item id
    this.itemId = itemId;
        lpHandler.itemId = this.itemId;
        // refresh api then refresh content
    $.getScript("apiData.php?cidReq="+ lpHandler.cidReq + "&pathId=" + lpHandler.pathId + "&itemId=" + itemId, rqOpenItem );
}

/**
 * Change the content of content frame according to itemId received
 * Should be call as callback for the load of the api
 *
 */
function rqOpenItem() {
    debug("rqOpenItem()",1);
    // get url and set frame location to this url
    $.ajax({
            url: lpHandler.moduleUrl + "viewer/scormServer.php?cmd=rqContentUrl&cidReq=" + lpHandler.cidReq + "&pathId=" + lpHandler.pathId + "&itemId=" + lpHandler.itemId,
            success: mkOpenItem,
            dataType: 'html'
    });
}


/**
 * Change the content of content frame according to url received
 * Should be called by rqOpenItem
 *
 */
function mkOpenItem(itemUrl) {
    debug("mkOpenItem()",1);
        if( itemUrl != '' )
    {
      //get newWindow
            $.ajax({
                type: "GET",
                url: lpHandler.moduleUrl + "viewer/scormServer.php?cmd=getNewWindow&cidReq=" + lpHandler.cidReq + "&pathId=" + lpHandler.pathId + "&itemId=" + lpHandler.itemId,
                success: function( response ){
                    if( response != 0 )
                    {
                        var newWindow = window.open( itemUrl, 'newWindow');                        
                    }
                    else
                    {
                        var frame = lp_top.frames['lp_content'].document;
                        $(frame.getElementById('description')).children().remove();
                        $.ajax({
                            type: "GET",
                            url: lpHandler.moduleUrl + "viewer/scormServer.php?cmd=getItemDescription&cidReq=" + lpHandler.cidReq + "&pathId=" + lpHandler.pathId + "&itemId=" + lpHandler.itemId,
                            success: function(response){
                                if( response )
                                {
                                    $(frame.getElementById('description')).append(response);
                                }
                            },
                            dataType: 'html'
                        });
                        //lp_top.lp_content.content.location = itemUrl;
                        $(frame.getElementById('content')).attr('src', itemUrl);
                    }                    
                }
            });
    }
    else
    {
        lp_top.lp_content.location = 'blank.php?pathId=' + lpHandler.pathId;
    }

    makeItemActive(lpHandler.itemId);
}

/**
 * Refresh the table of content
 * TODO : does it depends on user id ?
 */
function refreshToc() {
    debug("refreshToc()",1);

    // return all the toc that will fill the div in the toc frame
    $.ajax({
        url: lpHandler.moduleUrl + "viewer/scormServer.php?cmd=rqToc&cidReq=" + lpHandler.cidReq + "&pathId=" + lpHandler.pathId,
        success: function(response){
            $("#table_of_content", lp_top.frames["lp_toc"].document).empty();
            $("#table_of_content", lp_top.frames["lp_toc"].document).append(response);
            makeItemActive(lpHandler.itemId);
            },
        dataType: 'html'
    });

    return true;
}

// Presentation

/**
 * jump to the previous module if any
 *
 */
function goPrevious() {
    debug("goPrevious()",1);
    
    $.ajax({
        type: "POST",
        url: lpHandler.moduleUrl + "viewer/scormServer.php",
        data: "cmd=getPreviousId&cidReq="+ lpHandler.cidReq + "&pathId=" + lpHandler.pathId + "&itemId=" + lpHandler.itemId,
        success: function(response){
            if( isInteger(response) )
            {
                lpHandler.setContent(response);
            }
        },
        dataType: 'html'
    });
    
}

/**
 * jump to the next module if any
 *
 */
function goNext() {
    debug("goNext()",1);
    $.ajax({
    type: "POST",
    url: lpHandler.moduleUrl + "viewer/scormServer.php",
    data: "cmd=getNextId&cidReq="+ lpHandler.cidReq + "&pathId=" + lpHandler.pathId + "&itemId=" + lpHandler.itemId,
    success: function(response){
        if( isInteger(response) )
        {    
        lpHandler.setContent(response);
        }
    },
    dataType: 'html'
    }); 

    /*if( $(".active", lp_top.frames["lp_toc"].document).size() == 1 )
    {
        var nextItemId = $(".active", lp_top.frames["lp_toc"].document).next().attr("id");
    }
    else
    {
        // take the first
        var nextItemId = $(".item:first", lp_top.frames["lp_toc"].document).attr("id");
    }    
    
    if( isDefined(nextItemId) )
    {
        var id = nextItemId.substring( nextItemId.indexOf('_') + 1);
    if(id.indexOf('_'))
    {
        id = id.substring( 0, id.indexOf('_'));
    }
        debug(id, 1);
        lpHandler.setContent(id);
    }
    else
    {
        return false;
    }*/

}

/**
 * Resize frameset to have a fullscreen
 *
 */
function setFullscreen() {
    debug("setFullScreen()",1);

    lp_top.document.body.rows = "0,*";
}

/**
 * Resize frameset to have a embedded mode
 *
 */
function setEmbedded() {
    debug("setEmbedded()",1);

    lp_top.document.body.rows = "150,*";
}

/**
 * Append a debug msg to js console
 *
 */
function debug(msg, level) {

    if( lpHandler.debugMode > level )
    {
        if( $.browser.msie || !top.window.console || !top.window.console.log )
        {
            $("#lp_debug", lp_top.frames["lp_toc"].document).append(msg + "<br />\n\n");
        }
        else
        {
            top.window.console.log(msg);
        }
    }
}


/**
 * Some utilities functions
 *
 */

/**
 *    Make active an item in the table of content
 *
 */
function makeItemActive( itemId )
{
    debug("makeItemActive("+itemId+")",1);
    if( itemId > 0 )
    {
        // remove header and footer of content
        //isolateContent();

        // remove all currently active class
        $(".active", lp_top.frames["lp_toc"].document).removeClass("active");

        // find correct item and add it the active class
        $("#item_" + lpHandler.itemId, lp_top.frames["lp_toc"].document).addClass("active");

        // blink the new active item
        $(".active a", lp_top.frames["lp_toc"].document).fadeOut("fast").fadeIn("slow");

    }

    return true;
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

function isInteger(s) {
    return (s.toString().search(/^-?[0-9]+$/) == 0);
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

function addBlockingCondition( pathId ) {
    var div = $("<div>").attr("id", "");
    
    if( $("select[name='item[]']").size() > 0)
    {
      var sConditions = $("<select>").attr("name","condition[]");            
      $.ajax({
        type: "POST",
        url: "../viewer/scormServer.php",
        data: "cmd=getConditions",
        success : function(response){
            $(sConditions).append(response);
        },
        dataType: 'html'
      });
      $(div).append(sConditions);
      
      var bRemove = $("<button>")
                    .append(langRemove)
                    .click(function(){
                        $(div).remove();
                    });                    
      $(div).append(bRemove);
      $(div).append("<br />");
    }
    
    var sItems = $("<select>").attr("name","item[]");
    $(div).append(sItems);
    $.ajax({
        type: "POST",
        url: "../viewer/scormServer.php",
        data: "cmd=getItems&pathId=" + pathId,
        success: function(response){
            $(sItems).append(response);
        },
        dataType: 'html'
    });
    
    var sOperators = $("<select>").attr("name","operator[]");
    $(sOperators).append('<option value="=">=</option>');
    $(div).append(sOperators);
    
    var sStatus = $("<select>").attr("name","status[]");
        $(sStatus).change( function(){
                
                $(div).find('span').remove();
                var iPct = $('<input>').attr('name','raw_to_pass[]').css('width','50px').css('text-align','right');
                var sSpan = $('<span>');
                if( $(sStatus).attr("value") == 'COMPLETED' )
                {
                    $(iPct).attr('type','text');
                    sSpan.append(iPct);
                    sSpan.append('%');
                }
                else
                {
                    $(iPct).attr('type','hidden');
                    sSpan.append(iPct);
                }
                $(div).append(sSpan);
            });
    $(div).append(sStatus);
        var iPct = $('<input>').attr('name','raw_to_pass[]').css('width','50px').css('text-align','right').attr('type','text');        
        var sSpan = $('<span>');
        sSpan.append(iPct);
        sSpan.append('%');
        $(div).append(sSpan);
        
    $.ajax({
        type: "POST",
        url: "../viewer/scormServer.php",
        data: "cmd=getStatus",
        success : function(response){
            $(sStatus).append(response);
       },
       dataType: 'html'
    });
    
    
    
    $(div).insertBefore("#block_condition_button");
    
}

function addBranchCondition( pathId )
{
    $.ajax({
        type: "POST",
        url: "../viewer/scormServer.php",
        data: "cmd=createBranchCondition&pathId=" + pathId,
        success:  function(response){
            $(response).insertBefore("#branch_condition_button");
        }
    }
    );
}