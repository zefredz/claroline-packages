<?php 
	JavascriptLoader::getInstance()->load('jquery');
    JavascriptLoader::getInstance()->load('jquery.limit-1.2');
    JavascriptLoader::getInstance()->load('fillSurvey');
	CssLoader::getInstance()->load('LVSURVEY');
	$question = $this->question;
?>

<div class="LVSURVEYQuestion">
	<div class="LVSURVEYQuestionTitle">
		<?php echo htmlspecialchars($question->text); ?>
    </div>
    <div class="LVSURVEYQuestionContent">
		<?php if ('OPEN' == $question->type) : ?>
			<textarea name="choiceText" id="choiceText" rows="3" cols="40">
			</textarea>
		<?php endif; ?>
		<?php if ('MCSA' == $question->type) : ?>
			<ul>
				<?php foreach($question->getChoiceList() as $choice) : ?>
					<li>
						<input 	name="choiceId" 
								type="radio" 
								value="" 
								id="choiceId_<?php  echo $choice->id; ?>" 
						/>
                        <label 	for="choiceId_<?php  echo $choice->id; ?>">
                        	<?php echo htmlspecialchars($choice->text); ?>
                        </label>
					</li>
				<?php endforeach;?>
             </ul>
		<?php endif; ?>
		<?php if ('MCMA' == $question->type) : ?>
			<ul>
				<?php foreach($question->getChoiceList() as $choice) : ?>
					<li>
						<input 	name="choiceId[]" 
								type="checkbox" 
								value="<?php  echo $choice->id; ?>" 
								id="choiceId[]_<?php  echo $choice->id; ?>"								
						/>
                        <label for="choiceId[]_<?php  echo $choice->id; ?>">
                        	<?php echo htmlspecialchars($choice->text); ?>
                        </label>
					</li>
				<?php endforeach;?>
            </ul>
		<?php endif; ?>
		<?php if ('ARRAY' == $question->type) : ?>
			<table>
				<?php foreach($question->getChoiceList() as $choice) : ?>
					<tr>						
                        <td><span>
                        	<?php echo htmlspecialchars($choice->text); ?> : 
                        </span></td>
                        <?php foreach($choice->getOptionList() as $option) : ?>
							<td><span>
								<input 	name="choiceId_<?php  echo $choice->id; ?>" 
										type="radio" 
										value="<?php  echo $option->getId(); ?>" 
										id="choiceId_<?php  echo $choice->id; ?>_optionId<?php  echo $option->getId(); ?>" 
								/>
		                        <label 	for="choiceId_<?php  echo $choice->id; ?>_optionId<?php  echo $option->getId(); ?>">
		                        	<?php echo htmlspecialchars($option->getText()); ?>
		                        </label>
							</span></td>
						<?php endforeach;?>                        
					</tr>
				<?php endforeach;?>
            </table>
		<?php endif; ?>
	</div>				
	<div class="answerCommentBlock" id="answerCommentBlock">				
    	<?php echo get_lang('Comment'); ?> :
        <input 	
        	maxlength="200" 
        	type="text" 
        	size="70" 
        	name="answerComment" 
        	id="answerComment"
        />
        <span id="commentCharLeft" class="commentCharLeft"></span>
        <?php echo get_lang('char(s) left'); ?>            			
 	</div>	
    <div>
    	<a href="question_pool.php?<?php echo isset($this->surveyId)?'surveyId='.$this->surveyId:''; ?>">
    		&gt;&gt; <?php echo get_lang('Go back to Question Pool')?>
    	</a>
    
    <?php  if(isset($this->surveyId)) :?>
    	 <?php echo get_lang('or')?> 
    	 <a href="add_question.php?questionId=<?php echo $question->id; ?>&amp;surveyId=<?php echo $this->surveyId; ?>">
    	 	&gt;&gt; <?php echo get_lang('Add this question to survey')?>
    	 </a>
    <?php endif;?>
    </div>
</div>