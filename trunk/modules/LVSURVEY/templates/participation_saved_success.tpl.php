<ul>
	<?php if( $this->showResultsLink ) : ?>
    <li>
		<a href="show_results.php?surveyId=<?php echo $this->surveyId; ?>">
        	<?php echo get_lang('View results of this survey'); ?>
        </a>
    </li>
    <?php endif; ?>
    <li>
		<a href="show_survey.php?surveyId=<?php echo $this->surveyId ?>">
        	<?php echo get_lang('Return to the survey'); ?>
        </a>
    </li>
    <li>
    	<a href="survey_list.php">
        	<?php echo get_lang('Get back to the survey list'); ?>
        </a>
    </li>
</ul>
