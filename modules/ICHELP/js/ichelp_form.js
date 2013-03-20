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
});
