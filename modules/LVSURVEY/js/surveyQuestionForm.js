function showMultipleChoices(nbChoice)
{
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
	
	$(":input[id^='questionCh']").focus( function()
	{
		divIndex = $("div").index($(this).parent("div:first"));
		$("div:eq(" + (divIndex ) + ")").show();
	});
	
	
		
	$("#addChoice").click(function () {
		nbChoice = parseInt($("#questionNbCh").val()) +1;
		$("#questionNbCh").val(nbChoice);
		showMultipleChoices(nbChoice);
	});
	
	$("#removeChoice").click(function () {
		nbChoice = parseInt($("#questionNbCh").val()) -1;
		$("#questionNbCh").val(nbChoice);
		showMultipleChoices(nbChoice);
	});
	
});