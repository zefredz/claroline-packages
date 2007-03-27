$(document).ready(init);

function init()
{
    // attach events on elements
    $("#cl_jchat_form").submit(submitMsg);  
	
	
	
	
	// hide some interface elements
	$("#cl_jchat_loading").hide();
	$("#cl_jchat_messageBox").hide();
	$("#cl_jchat_archives").hide();	
	
	// set event for ajax call start and stop
	$("#cl_jchat_loading").ajaxStart(function(){
		$(this).show();
	});
		
	$("#cl_jchat_loading").ajaxStop(function(){
		$(this).hide();
	});
		
	// set interval does not execute function directly but wait for the first interval.
	// so call refresh one time at launch before setting up the interval
	rqRefresh();
	setInterval(rqRefresh, refreshRate);
	
    // give focus to form
	$("#cl_jchat_msg").focus();	
}

function rqRefresh()
{
    $.ajax({url: "index.php?cmd=rqRefresh", ifModified : true, success: function(response){exRefresh(response)}, dataType: "html"});
}

function exRefresh(response)
{
	$("#cl_jchat_chatarea").empty();
	$("#cl_jchat_chatarea").append(response);
	// Add a display effect for all lines that are added since last refresh
	$(".newLine").fadeIn("slow");
	
	// scroll to bottom of cl_jchat_chatarea
	document.getElementById("cl_jchat_chatarea").scrollTop = document.getElementById("cl_jchat_chatarea").scrollHeight;
}

function displayLogs(response)
{
    $("#cl_jchat_archives_content").empty();
    $("#cl_jchat_archives_content").append(response);
    $("#cl_jchat_archives").show("slow");
}	

function hideLogs()
{
    $("#cl_jchat_archives").hide("slow");
}

function showMsg(msg)
{
    $("#cl_jchat_messageBox").empty();
    $("#cl_jchat_messageBox").append(msg);
    $("#cl_jchat_messageBox").show("slow");
    
    setTimeout(hideMsg,refreshRate);
}

function hideMsg()
{
    $("#cl_jchat_messageBox").hide("slow");
}

// -- events

function submitMsg() 
{ 
    if( $("#cl_jchat_msg").val().length > 0 )
    {
        $.ajax({
            url: "index.php?cmd=rqAdd", 
            data: $(this.elements).serialize(), 
            success: function(response){
                exRefresh(response);
                $("#cl_jchat_msg").val("");
                $("#cl_jchat_msg").focus();
                }, 
            dataType: "html"});
        return false;
    }
    else
    {
        // do nothing
        return false;
    }
}

