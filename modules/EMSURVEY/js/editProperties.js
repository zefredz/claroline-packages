$(document).ready(function(){
	
	$("div.editProperties").hide();
	
	$("a.linkToEdit").click(function() {
		// Get the survey's id
		var surveyId = $(this).attr('id').split('linkToEdit');
		var surveyId = surveyId[1];
		
		$("div#editProperties"+surveyId).toggle("fast");
		return false;
	});
	
	$("input.newTitle").click(function() {
		// Get the survey's id
		var surveyId = $(this).attr('id').split('newTitle');
		var surveyId = surveyId[1];
		
		$("input#useNewTitle"+surveyId).attr(
				'checked', 
				true
		);
	});
});