$(document).ready(function(){
    if( $('#isManager').attr('checked') )
    {
        $(".issueType2").show();
    }
    else
    {
        $(".issueType2").hide();
    }
    $("#isManager").click(function(){
        $(".issueType2").show();
    });
    $("#notManager").click(function(){
        $(".issueType2").hide();
    });
    
    if( $('#isMember').attr('checked') )
    {
        $(".issueType0").show();
    }
    else
    {
        $(".issueType0").hide();
    }
    $("#isMember").click(function(){
        $(".issueType0").show();
    });
    $("#notMember").click(function(){
        $(".issueType0").hide();
    });
});
