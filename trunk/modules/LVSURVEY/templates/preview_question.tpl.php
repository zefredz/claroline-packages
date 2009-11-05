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
		<textarea name="choiceText<?php  echo $question->id; ?>" id="choiceText<?php  echo $question->id; ?>" rows="3" cols="40">
		</textarea>
	<?php endif; ?>
	<?php if ('MCSA' == $question->type) : ?>
		<ul <?php echo ($question->choiceAlignment == 'HORIZ')?'class="horizChoiceList"':''; ?>>
			<?php foreach($question->getChoiceList() as $choice) : ?>
				<li>
					<input name="choiceId<?php  echo $question->id; ?>" type="radio" value="<?php  echo $choice->id; ?>" 
						id="choiceId<?php  echo $question->id; ?>_<?php  echo $choice->id; ?>" />
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
					<input name="choiceId<?php  echo $question->id; ?>[]" type="checkbox" value="<?php  echo $choice->id; ?>" 
						id="choiceId<?php  echo $question->id; ?>[]_<?php  echo $choice->id; ?>" />
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
        <input maxlength="200" type="text" size="70" name="answerComment<?php echo $question->id; ?>" />
        <span id="commentCharLeft<?php echo $question->id; ?>" class="commentCharLeft"></span>
        <?php echo get_lang('char(s) left'); ?>
    </div>
    <div>
    	<a href="question_pool.php?<?php echo isset($this->surveyId)?'surveyId='.$this->surveyId:''; ?>">
    		<?php echo get_lang('Go back to Question Pool')?>
    	</a>
    
    <?php  if(isset($this->surveyId)) :?>
    	 <?php echo get_lang('or')?> 
    	 <a href="add_question.php?questionId=<?php echo $question->id; ?>&amp;surveyId=<?php echo $this->surveyId; ?>">
    	 	<?php echo get_lang('Add this question to survey')?>
    	 </a>
    <?php endif;?>
    </div>
</div>