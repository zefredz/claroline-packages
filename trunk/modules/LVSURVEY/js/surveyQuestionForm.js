function showMultipleChoices()
{
	nbChoice = parseInt($("#questionNbCh").val());
	$("div[id^='divquestionCh']").hide();
	$("div[id^='divquestionCh']:lt("+(nbChoice+1)+")").show();
	$("#menuaddrem").show();
    $("#divquestionAlign").show();
}

function hideMultipleChoice()
{
	$("div[id^='divquestionCh']").hide();
	$("#menuaddrem").hide();
	$("#divquestionAlign").hide();
}

$(document).ready(function(){
	
	
	$("input[name='questionType']").click(	function()
	{
		if ($("input[name='questionType']:checked").val() == "OPEN" )
		{
			hideMultipleChoice();
		}
		else
		{
			nbChoice = parseInt($("#questionNbCh").val());
			showMultipleChoices(nbChoice);
		}
	});	
	
	
	$("input[name='questionType']:checked").click();
	
	$("input[id^='questionCh']").focus( function()
	{
		nbChoice = parseInt($("#questionNbCh").val())
		var choiceIndex = parseInt($(this).attr('id').substr(10));
		if(choiceIndex >= nbChoice)
		{
			$("#questionNbCh").val(nbChoice+1);
			showMultipleChoices();
		}
	});
	
	
		
	$("#addChoice").click(function () {
		nbChoice = parseInt($("#questionNbCh").val());
		$("#questionNbCh").val(nbChoice+1);
		showMultipleChoices();
	});
	
	$("#removeChoice").click(function () {		
		nbChoice = parseInt($("#questionNbCh").val());
		$("#questionCh" + nbChoice).val("");
		$("#questionNbCh").val(nbChoice-1);
		showMultipleChoices();
	});
	
});