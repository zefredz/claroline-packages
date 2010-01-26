$(document).ready(function (){
	//hide detailed list
	$(".detailedList").hide();
	$(".hideDetailedList").hide();
	$(".deployDetailedList").click(function(e){
		$(this).hide("slow");
		$(this).parents("td").children("ul.detailedList").slideDown("normal");
		$(this).parents("td").children("a.hideDetailedList").show("slow");
		e.preventDefault();
	});
	$(".hideDetailedList").click(function(e){
		$(this).hide("slow");
		$(this).parents("td").children("ul.detailedList").slideUp("normal");
		$(this).parents("td").children("a.deployDetailedList").show("slow");
		e.preventDefault();
	});

	
	// paint charts
	$(".LVSURVEYQuestion").each(function(){
			var questionType = $(this).find("input[name='questionType']").val();
			var data = [];
			var ticksArray = [];
			var i = 0;
			$(this).find(".answerTR").each(function(){
				var answerLabel = $(this).find(".answerLabel:first").html();
				var answerPercentage = parseInt($(this).find(".answerPercentage:first").html());
				if('TEXT' == questionType)
				{
					return true;
				}
				if('MCMA' == questionType)
				{
					var answerData = [[i,answerPercentage]];
				}
				if('MCSA' == questionType)
				{
					var answerData = answerPercentage/100.0;
				}
				
				var serie = {label:answerLabel,data : answerData};
				data.push(serie)
				ticksArray.push([i,answerLabel]);
				++i;
			});
			
			if('MCMA' == questionType)
			{				
				$.plot($(this).find(".LVSURVEYQuestionResultChart:first"), data,
						{
							bars: {
								show: true,
								barWidth : 0.7,
								align : "center",
							},							 
							legend: {
						        show: false
						    },						    
						    yaxis :{
						    	min : 0,
						    	max : 100
						    },
						    xaxis : {
						    	ticks : ticksArray
						    }
						}
				);					
				
			}
			if('MCSA' == questionType)
			{
				$.plot($(this).find(".LVSURVEYQuestionResultChart:first"), data,
						{
						        series: {
						            pie: {
						                show: true,
						                label: {
							            	show : false
							            }
						            }		            
						        },
						        legend: {
						            show: true
						        }
						}
				);					
				
			}	
			
	});


});

/* if type == mcsa => pie
mcma => bars
text => nothing
*/