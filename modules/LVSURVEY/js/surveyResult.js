$(document).ready(function (){
	//hide detailed list
	$(".detailedList").hide();
	$(".hideDetailedList").hide();
	$(".deployDetailedList").click(function(e){
		$(this).hide("slow");
		$(this).parents().children(".detailedList").slideDown("normal");
		$(this).parents().children(".hideDetailedList").show("slow");
		e.preventDefault();
	});
	$(".hideDetailedList").click(function(e){
		$(this).hide("slow");
		$(this).parent().children(".detailedList").slideUp("normal");
		$(this).parent().children(".deployDetailedList").show("slow");
		e.preventDefault();
	});

	
	// paint pie charts
	$(".LVSURVEYQuestionResultChart.MCSA").each(function(){
		var data = [];
		var i = 0;
		$(this).siblings().find(".answerTR").each(function(){
			var answerLabel = $(this).find(".answerLabel:first").html();
			var answerPercentage = parseInt($(this).find(".answerPercentage:first").html());
			var answerData = answerPercentage/100.0;			
			var serie = {label:answerLabel,data : answerData};
			data.push(serie);
			++i;
		});		
		$.plot($(this), data,
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
		
	});
	
	// paint bar charts
	$(".LVSURVEYQuestionResultChart.MCMA").each(function(){
		var data = [];
		var ticksArray = [];
		var i = 0;
		$(this).siblings().find(".answerTR").each(function(){
			var answerLabel = $(this).find(".answerLabel:first").html();
			var answerPercentage = parseInt($(this).find(".answerPercentage:first").html());
			var answerData = [[i,answerPercentage]];			
			var serie = {label:answerLabel,data : answerData};
			data.push(serie);
			ticksArray.push([i,answerLabel]);
			++i;
		});
		
		$.plot($(this), data,
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
		
	});
	
			
			
	$(".LVSURVEYQuestionResultChart.ARRAY").each(function(){
		var ticksArray = [];
		var legendArray = [];
		
		
		var data = [];
		var i = 0;
		$(this).siblings().find(".answerTR").each(function(){
			var j = 0;
			var answerLabel = $(this).find(".answerLabel:first").html();
			ticksArray.push([i,answerLabel]);
			$(this).find(".OptionText").each(function(){			
				var answerPercentage = parseInt($(this).next().find(".answerPercentage:first").html());
				var legend = $(this).html();
				var answerData = [i,answerPercentage];
				
				if(data[j] == null)data[j] = [];
				if(legendArray[j] == null)legendArray[j] = [];
				data[j][i] = answerData;
				legendArray[j][i] = legend;
				++j;
			});			
			++i;
		});
		
		
		var series = [];		
		for (var i in data)
		{
			var serieData = data[i];
			var legend = legendArray[i].join(' - ');
			var serie = {label:legend ,data : serieData, stack : true};
			series.push(serie);
		}
		
		
		
		$.plot($(this), series, 
				{
					bars: {
						show: true,
						barWidth : 0.7,
						align : "center",
					},							 
					legend: {
				        show: true,
				        noColumns : series.length,
				        position : "sw"
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
		
	});
	
	

});

