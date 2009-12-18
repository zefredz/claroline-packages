<table>
	<tr>
   		<td>
			<form method="post" action="survey_list.php">
        		<input type="submit" name="submit" value="<?php echo get_lang('Cancel'); ?>" />
            </form>
		</td>
		<td>
			<form method="post" action="survey_list.php">
            	<input type="hidden" name="conf" value="1" />
                <input type="hidden" name="surveyId" value="<?php echo $this->survey->id; ?>" />
                <input type="hidden" name="cmd" value="surveyDel" />
                <input type="submit" name="submit" value="<?php echo get_lang('Confirm'); ?>" />
            </form>
    	</td>
	</tr>
</table>