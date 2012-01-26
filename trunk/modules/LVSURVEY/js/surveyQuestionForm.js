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

function enableOptions()
{
	$("span[id^='optionBlocForChoice']").show();
}
function disableOptions()
{
	$("span[id^='optionBlocForChoice']").hide();
}

function hasValidAnswer(surveyLine)
{
   //return always true for non mandatory questions
   if( !$(surveyLine).find('span').hasClass('required') ) return true;
   
   //test for open question: textarea not empty
   if( $(surveyLine).find('textarea').length ) 
   {
       if( $(surveyLine).find('textarea').val() != '' ) return true;
   }
   
   //test for MCUA, MCMA and LIKERT questions : at least one input checked in choice list
   if( $(surveyLine).find('li > input:checked').length ) return true;
      
   //test for Array questions : one input checked per array line
   if( $(surveyLine.find('table')).length ) 
   {
       if( $(surveyLine).find('tr').length == $(surveyLine).find('input:checked').length ) return true;
   }
   return false;
}

$(document).ready(function(){
	
	$('#surveyForm').submit( function()
    {
        var itemCount = 0;
        var valid = 0;
        $('.LVSURVEYLine').each( function()
        {
            itemCount++;
            if( hasValidAnswer($(this)) ) 
            {
                $(this).removeClass('invalid');
                valid++;
            }
            else
            {
                $(this).addClass('invalid');
            }
        });
        if( valid != itemCount )
        {
            alert(Claroline.getLang('__INCOMPLETE_SURVEY_ALERT__'));
            $('html, body').animate({ scrollTop: $('.invalid:first').offset().top }, 'slow');
        }
        
        return valid == itemCount;
    });
    
	
	$("input[name='questionType']").click(	function()
	{
		nbChoice = parseInt($("#questionNbCh").val());
		switch($("input[name='questionType']:checked").val())
		{			
			default : 
			case 'MCMA' :
			case 'MCSA' :
				showMultipleChoices(nbChoice);
				disableOptions();
				break;
			case 'ARRAY' : 
				showMultipleChoices(nbChoice);
				enableOptions();
				break;
			case 'OPEN' :
            case 'LIKERT' :
				hideMultipleChoice();
				break;
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