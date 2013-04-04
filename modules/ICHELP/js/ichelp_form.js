$(document).ready(function(){
    if( $('#isManager').attr('checked') )
    {
        $(".issueType3").show();
    }
    else
    {
        $(".issueType3").hide();
    }
    $("#isManager").click(function(){
        $(".issueType3").show();
    });
    $("#notManager").click(function(){
        $(".issueType3").hide();
    });
    
    if( $('#isMember').attr('checked') )
    {
        $(".issueType1").show();
    }
    else
    {
        $(".issueType1").hide();
    }
    $("#isMember").click(function(){
        $(".issueType1").show();
    });
    $("#notMember").click(function(){
        $(".issueType1").hide();
    });
});
