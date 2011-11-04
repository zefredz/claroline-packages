
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
	<?php if (isset($this->survey)):?>
		<input type="hidden" name="surveyId" value="<?php echo $this->survey->id; ?>" />
	<?php endif;?>
    <?php if (isset($this->questionLine)):?>
		<input type="hidden" name="questionLineId" value="<?php echo $this->questionLine->id; ?>" />
	<?php endif;?>
	<input type="hidden" name="claroFormId" value="<?php  echo uniqid(''); ?>">
	<input type="hidden" name="cmd" value="QuestionSave">
     
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
        	<label for="questionText">
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
			
    		<input type="radio" id="questionTypeOPEN" name="questionType" value="OPEN" <?php echo $this->question->type=="OPEN"?"checked":""; ?> />
    		<label for="questionTypeOPEN"><?php echo get_lang('Text'); ?></label>
    		<input type="radio" id="questionTypeMCSA" name="questionType" value="MCSA" <?php echo $this->question->type=="MCSA"?"checked":""; ?> />
	   		<label for="questionTypeMCSA"><?php  echo get_lang('Multiple choices, single answer'); ?></label>
    		<input type="radio" id="questionTypeMCMA" name="questionType" value="MCMA" <?php echo $this->question->type=="MCMA"?"checked":""; ?> />
   			<label for="questionTypeMCMA"><?php echo get_lang('Multiple choices, multiple answers'); ?></label>
    		<input type="radio" id="questionTypeARRAY" name="questionType" value="ARRAY" <?php echo $this->question->type=="ARRAY"?"checked":""; ?> />
    		<label for="questionTypeARRAY"><?php echo get_lang('Array choices'); ?></label>
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
			$editChoiceTpl = new PhpTemplate(dirname(__FILE__).'/edit_choice.tpl.php');
			foreach($this->question->getChoiceList() as $choice) { 
				$choiceCount++;				
				$editChoiceTpl->assign('choiceNum', $choiceCount);
				$editChoiceTpl->assign('choice', $choice); 
				echo   $editChoiceTpl->render();
			}	
			for($i = 0 ; $i<=100; $i++) {
				$choiceCount++;				
				$editChoiceTpl->assign('choiceNum', $choiceCount);
				$editChoiceTpl->assign('choice', new Choice($this->question->id));   
				echo   $editChoiceTpl->render();
			}
			?>
		</td>
	</tr>
	<!--  ADD / REMOVE CHOICE -->
	<tr>
		<td>
			&nbsp;
		</td>
		<td>			
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
    <?php if (isset($this->survey)): ?>
		<!-- REQUIRED OR OPTIONAL -->
        <tr>
            <td>
                <?php  echo get_lang('Answer required'); ?>
            </td>
            <td>
                <input type="radio" id="answerRequiredYes" name="answerRequired" value="1" <?php echo $this->answerRequired?"checked":""; ?> />
                <label for="answerRequiredYes"><?php echo get_lang('Yes'); ?></label>
                <input type="radio" id="answerRequiredNo" name="answerRequired" value="0" <?php echo $this->answerRequired?"":"checked"; ?> />
                <label for="answerRequiredNo"><?php  echo get_lang('No'); ?></label>
            </td>
        </tr>
        
    <?php endif;?>
    <!-- SHARED OR NOT -->
    <tr>
        <td>
            <?php  echo get_lang('Allow other surveys to use this question'); ?>
        </td>
        <td>
            <input type="radio" id="sharedYes" name="shared" value="1" <?php echo $this->question->shared?"checked":""; ?> />
            <label for="sharedYes"><?php echo get_lang('Yes'); ?></label>
            <input type="radio" id="sharedNo" name="shared" value="0" <?php echo $this->question->shared?"":"checked"; ?> />
            <label for="sharedNo"><?php  echo get_lang('No'); ?></label>
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