	var displayedSlide;
	var lastSlide;
	
	$(document).ready(function() {
		// hide all slides
		$('.sortableComponent').hide();

		// show the first slide
		$('.sortableComponent:first').show();
		$('.sortableComponent:first').addClass('currentSlide');
	
		// navigation commands
		$('.prevSlide a').click(previous);
		$('.nextSlide a').click(next);
		
		displayedSlide = 1;
		lastSlide = $('.sortableComponent').size();
		
		updateNav();
		
		// banner visibility
		$('.bannerToggle').click( function(){
		  $('#topBanner').toggle();
		  $('#campusFooter').toggle();
		});
	});	
	
	function next(){
		// check that this is not the last slide
		if( displayedSlide == lastSlide ) return false;
		
		// select current slide
		$('.currentSlide')
			// unmark it as current 
			.removeClass('currentSlide')
			// hide it
			.hide()
			// select next slide
			.next('.sortableComponent')
			// mark it as current
			.addClass('currentSlide')
			// display it
			.show()
			;
		
		displayedSlide++;
		updateNav();
		
		return true;
	}
	
	function previous(){
		// check that this is not the first slide
		if( displayedSlide == 1 ) return false;
		
		// select current slide
		$('.currentSlide')
			// unmark it as current 
			.removeClass('currentSlide')
			// hide it
			.hide()
			// select next slide
			.prev('.sortableComponent')
			// mark it as current
			.addClass('currentSlide')
			// display it
			.show()
			;
		
		displayedSlide--;
		updateNav();
		
		return true;
	}
	
	function updateNav()
	{
		$('.displayedSlide').text(displayedSlide);
		$('.lastSlide').text(lastSlide);
		
		// is this first slide ?
		if( displayedSlide == 1 )
		{
			// disable previous nav link
			$('.prevSlide').addClass('navDisabled');
		}
		else
		{
			$('.prevSlide').removeClass('navDisabled');
		}
		
		// is this last slide ?
		if( displayedSlide == lastSlide )
		{
			// disable next nav link
			$('.nextSlide').addClass('navDisabled');		
		}
		else
		{
			$('.nextSlide').removeClass('navDisabled');
		}		
	}