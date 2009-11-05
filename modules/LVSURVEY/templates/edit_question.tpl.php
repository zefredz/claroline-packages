
<?php 
	JavascriptLoader::getInstance()->load('jquery');
	JavascriptLoader::getInstance()->load('surveyQuestionForm');
	$dialogBox = new DialogBox();
	if($this->question->isAnswered()){
    	$dialogBox->warning(get_lang('Some users have already answered to this question.'));
    }
    if($this->question->getUsed() >= 2){
    	$dialogBox->warning(get_lang('This question is used in several surveys. Changes will take effect on all these surveys.'));
    }
    echo $dialogBox->render();
	
?>
       
<form method="post" action="./edit_question.php" >
	<input type="hidden" name="questionId" value="<?php  echo $this->question->id; ?>" />
	<?php if (isset($this->surveyId)):?>
		<input type="hidden" name="surveyId" value="<?php echo $this->surveyId; ?>" />
	<?php endif;?>
	<input type="hidden" name="claroFormId" value="<?php  echo uniqid(''); ?>">
     
     <table border="0" cellpadding="5" id="formTable">


	<?php if ($this->question->getUsed() >= 2 ):?>  
    	<tr>
			<td valign="top">
				
			</td>
			<td>
            	<?php  echo get_lang('This question is used in several surveys.'); ?> <br />
            	<input  type="radio" name="questionDuplicate" id="questionDuplicate" size="60" maxlength="200" value="0" checked />
             	&nbsp;<?php  echo get_lang('Change for all surveys'); ?> 
            	<input  type="radio" name="questionDuplicate" id="questionDuplicate" size="60" maxlength="200" value="1" />
            	&nbsp;<?php echo get_lang('Create a new question'); ?>
           	</td>
		</tr>
    <?php endif; ?>
        
    <!--  QUESTION TEXT -->
    <tr>
   		<td valign="top">
        	<label for="questionTitle">
        		<?php echo get_lang('Title'); ?>&nbsp;
        		<span class="required">*</span>&nbsp;:
       		</label>
      	</td>
        <td>
        	<input  type="text" name="questionText" id="questionText" size="60" maxlength="200" 
        		value="<?php echo htmlspecialchars($this->question->text); ?>" />
		</td>
	</tr>
	
	<!-- QUESTION TYPE -->
	<tr>
		<td>
			<?php  echo get_lang('Type of question'); ?> : 
		</td>
		<td>
    		<input type="radio" id="questionType" name="questionType" value="OPEN" <?php echo $this->question->type=="OPEN"?"checked":""; ?> />
    		<?php echo get_lang('Text'); ?>
    		<input type="radio" id="questionType" name="questionType" value="MCSA" <?php echo $this->question->type=="MCSA"?"checked":""; ?> />
    		<?php  echo get_lang('Multiple choice, single answer'); ?>
    		<input type="radio" id="questionType" name="questionType" value="MCMA" <?php echo $this->question->type=="MCMA"?"checked":""; ?> />
    		<?php echo get_lang('Multiple choice, multiple answers'); ?>
    	</td>
    </tr>
	<tr>
		<td>
			&nbsp;
		</td>
		<td>
			<div id="divquestionCh">
				<?php echo get_lang('Choices'); ?>&nbsp;: 
				<input name="questionNbCh" id="questionNbCh" type="hidden" value="<?php echo count($this->question->getChoiceList())+2; ?>" />
			</div>
			<?php 
			$choiceCount = 0;
			foreach($this->question->getChoiceList() as $choice) : 
				$choiceCount++
			?>
				<div id="divquestionCh<?php echo $choiceCount; ?>">
					<?php echo $choiceCount; ?> : 
					<input name="questionCh<?php echo $choiceCount; ?>" id="questionCh<?php echo $choiceCount; ?>" type="text" value="<?php echo htmlspecialchars($choice->text); ?>" />
					<input name="questionChId<?php echo $choiceCount; ?>" id="questionChId<?php echo $choiceCount; ?>" type="hidden" value="<?php echo $choice->id; ?>" />
				</div>
			<?php endforeach; ?>
			<?php for($i = 0 ; $i<=10; $i++) :
					$choiceCount++;
			?>
				<div id="divquestionCh<?php echo $choiceCount; ?>">
					<?php echo $choiceCount; ?> : 
					<input name="questionCh<?php echo $choiceCount; ?>" id="questionCh<?php echo $choiceCount; ?>" type="text" value="" />
					<input name="questionChId<?php echo $choiceCount; ?>" id="questionChId<?php echo $choiceCount; ?>" type="hidden" value="-1" />
					
				</div>
			<?php endfor;?>                
		
		</td>
	</tr>
	<!--  ALIGNMENT -->
	<tr>
		<td>
			&nbsp;
		</td>
		<td>
			<div id="divquestionAlign">
				<input type="radio" name="questionAlignment" id="questionAlignment" value="VERTI"
					<?php echo $this->question->choiceAlignment=="VERTI"?"checked":""; ?> />
				<?php  echo get_lang('Vertical alignment'); ?>
				<input type="radio" name="questionAlignment" id="questionAlignment" value="HORIZ"
					<?php echo $this->question->choiceAlignment=="HORIZ"?"checked":""; ?> />
				<?php  echo get_lang('Horizontal alignment'); ?>
			</div>
			<div id="menuaddrem">
				<a href="#" id="addChoice">
					<?php  echo get_lang('Add a choice'); ?>
				</a> 
				&nbsp;-&nbsp;
				<a href="#" id="removeChoice">
					<?php echo get_lang('Remove a choice'); ?>
				</a>
			</div>
		</td>
	</tr>
	<!-- SUBMIT -->
	<tr>
		<td>
			&nbsp;
		</td>
		<td>
			<input type="submit" value="<?php echo get_lang('Finish'); ?>" />
		</td>
	</tr>
</table>
</form>