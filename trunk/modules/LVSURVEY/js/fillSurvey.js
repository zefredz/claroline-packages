$(document).ready(function (){
	//show chars left
	$('.answerCommentBlock').each(function(){
		var infoSpanId = $(this).children('span.commentCharLeft').attr('id');
		$(this).children("input").limit('200', '#' + infoSpanId);
	});
});

