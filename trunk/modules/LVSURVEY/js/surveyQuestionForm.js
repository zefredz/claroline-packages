var nbchoice;

$(document).ready(function(){
	$("#questionNoJavascript").hide();
	
	if($("#questionType[@checked]").val()!="TEXT")
	{
		nbchoice = parseInt($("#questionNbCh").val());
		$("#questionNoJavascript").text(nbchoice);
		$("div[@id^='divquestionCh']").hide();
		$("div[@id^='divquestionCh']:lt("+(nbchoice+1)+")").show();
		$("#menuaddrem").show();
        $("#divquestionAlign").show();
	}
	else
	{
		nbchoice = 2;
		$("#questionNbCh").val(nbchoice);
		$("div[@id^='divquestionCh']").hide();
		$("#menuaddrem").hide();
        $("#divquestionAlign").hide();
	}
	
	$("#questionNbCh").val(nbchoice);
	
	$(":radio[@id='questionType']").click(function () {
		if($("#questionType[@checked]").val()!="TEXT")
		{
			$("div[@id^='divquestionCh']:lt("+(nbchoice+1)+")").show();
			$("#menuaddrem").show();
            $("#divquestionAlign").show();
		}
		else
		{
			$("div[@id^='divquestionCh']").hide();
			$("#menuaddrem").hide();
            $("#divquestionAlign").hide();
		}
	});
		
	$("#addChoice").click(function () {
		if(nbchoice<10)
		{
			nbchoice = nbchoice + 1;
			$("#questionNbCh").val(nbchoice);
			$("div[@id^='divquestionCh']:hidden:first").show();
		}
	});
	
	$("#removeChoice").click(function () {
		if(nbchoice>2)
		{
			nbchoice = nbchoice -1;
			$("#questionNbCh").val(nbchoice);
			$("div[@id^='divquestionCh']:visible:last").hide();
		}
	});
	
});