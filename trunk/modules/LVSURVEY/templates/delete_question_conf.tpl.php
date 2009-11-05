<table>
	<tr>
   		<td>
			<form method="post" action="question_pool.php">
        		<input type="submit" name="submit" value="<?php echo get_lang('Cancel'); ?>" />
            </form>
		</td>
		<td>
			<form method="post" action="question_pool.php">
            	<input type="hidden" name="conf" value="1" />
                <input type="hidden" name="questionId" value="<?php echo $this->question->id; ?>" />
                <input type="hidden" name="cmd" value="questionDel" />
                <input type="submit" name="submit" value="<?php echo get_lang('Confirm'); ?>" />
            </form>
    	</td>
	</tr>
</table>