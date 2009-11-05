<?php 
if($this->survey->isAnswered())
{
	$dialogBox = new DialogBox();
	$dialogBox->warning( get_lang('Some users have already answered to this survey.').' '
                        . get_lang('It\'s not a good idea to add a question.'));
    echo $dialogBox->render();
}
?>
<ul>
	<li>
		<a href="add_question.php?fromPool=1&surveyId=<?php echo $this->survey->id; ?>">
        	<?php echo get_lang("Select an existing question"); ?>
        </a>
    </li>
                        
    <li>
   		<a href="edit_question.php?surveyId=<?php echo $this->survey->id; ?>">
        	<?php echo get_lang("Create a new question"); ?>
        </a>
    </li>
</ul>