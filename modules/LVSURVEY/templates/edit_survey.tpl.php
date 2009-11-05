<?php 
        JavascriptLoader::getInstance()->load('jquery');
        JavascriptLoader::getInstance()->load('ui.datepicker');
        CssLoader::getInstance()->load('ui.datepicker');

if($this->survey->isAnswered()){
		$dialogBox = new DialogBox();
        $dialogBox->warning( get_lang('Some users have already answered to this survey.'));
        echo $dialogBox->render();
}
?>

<!-- COMMAND MENU -->
<?php if($this->survey->id != -1) :?>
	<p>
	<?php 
		$editQuestionIcon = claro_html_icon('edit', get_lang('Modify'), get_lang('Modify'));
		$editQuestionURL = 'show_survey.php?surveyId='.$this->survey->id;	
		$editQuestionsLink = claro_html_link($editQuestionURL, $editQuestionIcon.' '.get_lang('Edit questions of this survey'),array('class' => 'claroCmd'));		
		echo claro_html_menu_horizontal(array($editQuestionsLink));
	?>    	
	</p>
<?php endif; ?>

<!-- EDIT FORM -->

<form method="post" action="edit_survey.php" >
	<input type="hidden" name="surveyId" value="<?php echo $this->survey->id ?>" />
    <input type="hidden" name="claroFormId" value="<?php echo uniqid(''); ?>" />
    <table border="0" cellpadding="5">
    <tbody>
	<!--  ANONYMOUS  -->
    <tr>
    	<td valign="top">
        	<label for="surveyAnonymous"><?php echo get_lang('Anonymous survey'); ?> 
        		&nbsp; <span class="required">*</span>&nbsp;:
        	</label>
         </td>
         <td>
        <?php if($this->survey->id == -1): ?>
        	<input type="radio" name="surveyAnonymous" id="surveyAnonymous" value="true"
                <?php echo ($this->survey->is_anonymous?'checked="checked" ':''); ?>
            /><?php echo get_lang('Yes'); ?>
            <input type="radio" name="surveyAnonymous" id="surveyAnonymous" value="false" 
                <?php echo (!$this->survey->is_anonymous?'checked="checked" ':''); ?>
            /><?php echo get_lang('No'); ?>
         <?php else : ?>
         	<?php echo ($this->survey->is_anonymous?get_lang('Yes'):get_lang('No')); ?>
         	<input type="hidden" name="surveyAnonymous" id="surveyAnonymous" 
                value="<?php echo ($this->survey->is_anonymous?'true" ':'false'); ?>" />
         <?php endif; ?>
         </td>
     </tr>
     <!--  TITLE  -->   
     <tr>
         <td valign="top">
         	<label for="surveyTitle"><?php echo get_lang('Title'); ?> &nbsp;
            	<span class="required">*</span>&nbsp;:
            </label>
     	 </td>
         <td>
            <input  type="text" name="surveyTitle" id="surveyTitle" size="60" maxlength="200" value="<?php echo htmlspecialchars( $this->survey->title); ?>" />
         </td>
	 </tr>
     <!--  DESCRIPTION  --> 
     <tr>
         <td valign="top">
             <label for="surveyDescription"><?php echo get_lang('Description'); ?> &nbsp;:</label>
         </td>
         <td>
             <?php echo claro_html_textarea_editor('surveyDescription', $this->survey->description); ?>
         </td>
     </tr>
     <!--  START DATE  --> 
     <tr>
         <td valign="top">
             <label for="surveyStartDate"><?php echo get_lang('Start date'); ?>&nbsp;:</label>
         </td>
         <td>
             <input  type="text" name="surveyStartDate" id="surveyStartDate" size="20" maxlength="20"  
               value="<?php echo claro_html_localised_date("%d/%m/%y", $this->survey->startDate ); ?>" />
         </td>
     </tr>
     <!--  END DATE  --> 
     <tr>
         <td valign="top">
             <label for="surveyEndDate"><?php echo get_lang('End date'); ?>&nbsp;:</label>
         </td>
         <td>
             <input  type="text" name="surveyEndDate" id="surveyEndDate" size="20" maxlength="20"  
               value="<?php echo claro_html_localised_date("%d/%m/%y", $this->survey->endDate ); ?>" />
             
         </td>
     </tr>
     <!--  RESULTS VISIBILITY  --> 
     <tr>
         <td valign="top">
             <label for="surveyAnonymous"><?php echo get_lang('Results visibility for users'); ?>&nbsp;
                 <span class="required">*</span>&nbsp;:
             </label>
         </td>
         <td>
             <input type="radio" name="surveyResultsVisibility" value="VISIBLE" 
                 <?php echo ($this->survey->resultsVisibility == 'VISIBLE'?'checked ':''); ?>
             /><?php echo get_lang('Always visible'); ?>
             <input type="radio" name="surveyResultsVisibility" value="VISIBLE_AT_END" 
                 <?php echo ($this->survey->resultsVisibility == 'VISIBLE_AT_END'?'checked ':''); ?>
             /><?php echo get_lang('Only visible at the end of the survey'); ?>
             <input type="radio" name="surveyResultsVisibility" value="INVISIBLE" 
                 <?php echo ($this->survey->resultsVisibility == 'INVISIBLE'?'checked ':''); ?>
             /><?php echo get_lang('Never visible'); ?>
         </td>
     </tr>  
     
     <!--  SUBMIT -->
     <tr>
         <td colspan="3">
             <input type="submit" value="<?php echo get_lang('Finish'); ?>" />
         </td>
	 </tr>
	</tbody>
</table>
</form>
<script>
     	$.datepicker.setDefaults({dateFormat: 'dd/mm/y'});
        $('#surveyStartDate').datepicker({showOn: 'both'});
        $('#surveyEndDate').datepicker({showOn: 'both'});
</script>
