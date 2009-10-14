var nbchoice;

function showMultipleChoices(nbChoice)
{
	$("div[id^='divquestionCh']").hide();
	$("div[id^='divquestionCh']:lt("+(nbchoice+1)+")").show();
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
		if ($("input[name='questionType']:checked").val() == "TEXT" )
		{
			hideMultipleChoice();
		}
		else
		{
			nbChoice = $("#questionNbCh").val();
			showMultipleChoices(nbChoice);
		}
	});	
	
	nbchoice = 2;
	$("#questionNbCh").val(nbchoice);
	$("#questionType[value='MCSA']").click();
	showMultipleChoices();	
		
	$("#addChoice").click(function () {
		if(nbchoice<10)
		{
			nbchoice = nbchoice + 1;
			$("#questionNbCh").val(nbchoice);
			$("div[id^='divquestionCh']:hidden:first").show();
		}
	});
	
	$("#removeChoice").click(function () {
		if(nbchoice>2)
		{
			nbchoice = nbchoice -1;
			$("#questionNbCh").val(nbchoice);
			$("div[id^='divquestionCh']:visible:last").hide();
		}
	});
	
});