$(document).ready(function(){
    if(!$("#hasStartDate").attr("checked")){
        $("#startDate").hide();
    }
    if(!$("#hasEndDate").attr("checked")){
        $("#endDate").hide();
    }
    $("#hasStartDate").click(function(){
        if($("#hasStartDate").attr("checked")){
            $("#startDate").val($("#dateInfo").attr("startdate"));
            $("#startDate").show();
        }else{
            $("#startDate").hide();
            $("#startDate").val('');
        }
    });
    $("#hasEndDate").click(function(){
        if($("#hasEndDate").attr("checked")){
            $("#endDate").val($("#dateInfo").attr("enddate"));
            $("#endDate").show();
        }else{
            $("#endDate").hide();
            $("#endDate").val('');
        }
    });
});