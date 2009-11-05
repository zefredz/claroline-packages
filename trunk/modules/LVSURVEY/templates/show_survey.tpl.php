<?php 
	JavascriptLoader::getInstance()->load('jquery');
    JavascriptLoader::getInstance()->load('jquery.limit-1.2');
    JavascriptLoader::getInstance()->load('fillSurvey');
	CssLoader::getInstance()->load('LVSURVEY');
	
	$editIcon 		= claro_html_icon('edit', 		get_lang('Modify'), 		get_lang('Modify'));
	$arrowUpIcon 	= claro_html_icon('move_up', 	get_lang('Move Up'), 		get_lang('Move Up'));
	$arrowDownIcon 	= claro_html_icon('move_down', 	get_lang('Move Down'), 		get_lang('Move Down'));
	$deleteIcon		= claro_html_icon('delete');
	
	$currentUserId = claro_get_current_user_id();
	$participation = $this->participation;
	$questionList = $this->survey->getQuestionList();
	
	echo claro_html_tool_title($this->survey->title);
	$cmd_menu = array();
	if($this->editMode)
	{		
		$cmd_menu[] = '<a class="claroCmd" href="edit_survey.php?surveyId='.$this->survey->id.'">'.$editIcon.' '.get_lang('Edit survey properties').'</a>';
    	$cmd_menu[] = '<a class="claroCmd" href="add_question.php?surveyId='.$this->survey->id.'">'.get_lang('Add question').'</a>';
	}
	if($this->editMode || $this->survey->areResultsVisibleNow())
	{
		$cmd_menu[] = '<a class="claroCmd" href="show_results.php?surveyId='.$this->survey->id.'">'.get_lang('View results of this survey').'</a>';
	}
	echo '<p>' . claro_html_menu_horizontal($cmd_menu) . '</p>';
	
	$infoBox = new DialogBox();
	if($this->survey->is_anonymous)
	{
		$infoBox->info( get_lang('This survey is anonymous. We won\'t display your identification .'));
	}
	else
	{
		$infoBox->info( get_lang('This survey is not anonymous. Your identification will be displayed.'));
	}
	if($this->survey->hasEnded())
	{
		$infoBox->info( get_lang('This survey has ended. You cannot change your answers anymore.'));
	}
	if('VISIBLE_AT_END' == $this->survey->resultsVisibility && !$this->survey->hasEnded())
    {
    	$infoBox->info(get_lang('Results will be visible only at the end of the survey on %date.', 
             array('%date'=>claro_html_localised_date(get_locale('dateFormatLong'), $this->survey->endDate))));
    }
    if('INVISIBLE' == $this->survey->resultsVisibility )
    {
    	$infoBox->info(get_lang('Results are not visible by the participants of this Survey.'));
    }
    
	echo $infoBox->render();

?>

<div><?php echo $this->survey->description; ?></div>

<?php  if(!$this->survey->hasEnded()) : ?>
	<form method="post" action="show_survey.php?surveyId=<?php echo $this->survey->id; ?>">
	<input type="hidden" name="claroFormId" value="<?php echo uniqid(''); ?>" />
    <input type="hidden" name="surveyGoToConf" value="" />
    <input type="hidden" name="surveyId" value="<?php echo $this->survey->id; ?>" />
    <input type="hidden" name="participationId" value="<?php echo $participation->id; ?>" />
<?php endif; ?>

    
    <div class="LVSURVEYQuestionList">
	<?php  if(empty($questionList)) :?>
		<?php echo get_lang('No question in this survey'); ?>
	<?php else :?>
		<?php foreach($questionList as $question) :?>
			<?php
				$answer = $participation->getAnswerForQuestion($question->id);
				$selectedChoiceList = $answer->getSelectedChoiceList();
			?>
			<div class="LVSURVEYQuestion">
				<input type="hidden" name="questionId<?php echo $question->id; ?>" value="<?php echo $question->id; ?>" />
				<input type="hidden" name="answerId<?php echo $question->id; ?>" value="<?php echo $answer->id; ?>" />
				<div class="LVSURVEYQuestionTitle">
					<?php
						if($this->editMode)
						{ 
							$urlMoveUp = 'show_survey.php?surveyId='.$this->survey->id.'&amp;questionId='.$question->id.'&amp;cmd=questionMoveUp';     		 
                			echo claro_html_link($urlMoveUp, $arrowUpIcon);
                			$urlMoveDown = 'show_survey.php?surveyId='.$this->survey->id.'&amp;questionId='.$question->id.'&amp;cmd=questionMoveDown';     		 
                			echo claro_html_link($urlMoveDown, $arrowDownIcon);
                			$urlEdit = 'edit_question.php?surveyId='.$this->survey->id.'&amp;questionId='.$question->id;
                			echo claro_html_link($urlEdit, $editIcon);
                			$urlRemove = 'show_survey.php?surveyId='.$this->survey->id.'&amp;questionId='.$question->id.'&amp;cmd=questionRemove';
							echo claro_html_link($urlRemove, $deleteIcon);
						}
						echo htmlspecialchars($question->text);
                	?>
                </div>
				<div class="LVSURVEYQuestionContent">
					<?php if ('OPEN' == $question->type) : ?>
						<textarea name="choiceText<?php  echo $question->id; ?>" id="choiceText<?php  echo $question->id; ?>" rows="3" cols="40"><?php 
            					$answerText = empty($selectedChoiceList)?'':reset($selectedChoiceList)->text;
            					echo htmlspecialchars($answerText); 
            				?></textarea>
					<?php endif; ?>
					<?php if ('MCSA' == $question->type) : ?>
						<ul <?php echo ($question->choiceAlignment == 'HORIZ')?'class="horizChoiceList"':''; ?>>
							<?php foreach($question->getChoiceList() as $choice) : ?>
								<li>
									<input name="choiceId<?php  echo $question->id; ?>" type="radio" value="<?php  echo $choice->id; ?>" id="choiceId<?php  echo $question->id; ?>_<?php  echo $choice->id; ?>"
										<?php echo in_array($choice->id, array_keys($selectedChoiceList))?'checked="checked"':''; ?> />
                        			<label for="choiceId<?php  echo $question->id; ?>_<?php  echo $choice->id; ?>">
                        				<?php echo htmlspecialchars($choice->text); ?>
                        			</label>
								</li>
							<?php endforeach;?>
                    	</ul>
					<?php endif; ?>
					<?php if ('MCMA' == $question->type) : ?>
						<ul <?php echo ($question->choiceAlignment == 'HORIZ')?'class="horizChoiceList"':''; ?>>
							<?php foreach($question->getChoiceList() as $choice) : ?>
								<li>
									<input name="choiceId<?php  echo $question->id; ?>[]" type="checkbox" value="<?php  echo $choice->id; ?>" id="choiceId<?php  echo $question->id; ?>[]_<?php  echo $choice->id; ?>"
										<?php echo in_array($choice->id, array_keys($selectedChoiceList))?'checked="checked"':''; ?> />
                        			<label for="choiceId<?php  echo $question->id; ?>[]_<?php  echo $choice->id; ?>">
                        				<?php echo htmlspecialchars($choice->text); ?>
                        			</label>
								</li>
							<?php endforeach;?>
                    	</ul>
					<?php endif; ?>
				</div>
				<div class="answerCommentBlock" id="answerCommentBlock<?php echo $question->id; ?>">
        			<?php echo get_lang('Comment'); ?> : 
        			<input maxlength="200" type="text" size="70" name="answerComment<?php echo $question->id; ?>" 
        				value="<?php echo $answer->comment; ?>" />
        			<span id="commentCharLeft<?php echo $question->id; ?>" class="commentCharLeft"></span>
        			<?php echo get_lang('char(s) left'); ?>
        		</div>
			</div>
		<?php endforeach;?>
	<?php endif; ?>
	</div>
	<?php  if(!$this->survey->hasEnded()) : ?>
		<input type="submit" value="<?php echo get_lang('Submit'); ?>" />
		</form>
	<?php endif; ?>

	
