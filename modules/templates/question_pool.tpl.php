<?php 
	$editIcon 		= claro_html_icon('edit', 		get_lang('Modify'), 		get_lang('Modify'));
	$deleteIcon		= claro_html_icon('delete');
	$selectIcon		= claro_html_icon('select',		get_lang('Select'), 		get_lang('Select'));
	
	$surveySuffix = isset($this->surveyId)?'&amp;surveyId='.$this->surveyId:'';


	echo claro_html_tool_title(get_lang('List of questions'));

	$cmd_menu = array();
	$cmd_menu[] = '<a class="claroCmd" href="edit_question.php?'.$surveySuffix.'">'.get_lang('New question').'</a>';
    echo '<p>' . claro_html_menu_horizontal($cmd_menu) . '</p>';

?>

<table class="claroTable emphaseLine" border="0" align="center" cellpadding="2" cellspacing="2" width="100%">
	<thead>
    	<tr class="headerX">
        	<th>
        		<a href="question_pool.php?orderby=text<?php echo $surveySuffix . (($this->orderby=='text') && ($this->ascDesc=='ASC')?'&amp;ascDesc=DESC':''); ?>" > 
					<?php  echo get_lang('Question title'); ?> 
        		</a>
        	</th>
			<th>
				<a href="question_pool.php?orderby=type<?php echo $surveySuffix . (($this->orderby=='type') && ($this->ascDesc=='ASC')?'&amp;ascDesc=DESC':''); ?>" > 
					<?php  echo get_lang('Type of question'); ?>
        		</a>
        	</th>
        	<th>
        		<a href="question_pool.php?orderby=used<?php echo $surveySuffix . (($this->orderby=='used') && ($this->ascDesc=='ASC')?'&amp;ascDesc=DESC':''); ?>" > 
        			<?php  echo get_lang('Number of surveys using the question'); ?> 
				</a>
			</th>
			<?php  if (isset($this->surveyId)) : ?>
        		<th>
        		<?php  echo get_lang('Add'); ?>
        		</th>
        	<?php endif; ?>
        	<?php if (!isset($this->surveyId)) : ?>  
        	<th>
        		<?php echo get_lang('Modify');  ?>
        	</th>
        	<th>
        		<?php  echo get_lang('Delete'); ?>
        	</th>        
        	<?php endif; ?>	
        </tr>
	</thead>
    <tbody>
    <?php if (empty($this->questionList)) : ?>  
    	<tr>
        	<td colspan="5">
        		<?php echo get_lang('Empty'); ?>
        	</td>
        </tr>    	
    <?php  else : ?>
    	<?php foreach($this->questionList as $question) :?>
    		<tr>
            	<td>
                	<a href="show_question.php?questionId=<?php echo $question->id . $surveySuffix; ?>" class="item">
						<?php  echo $question->text; ?>
                	</a>                	 
           		</td>
        		<td>
                	<?php  echo $question->type; ?>
                </td>
                <td>
                	<?php  echo $question->getUsed(); ?>
                </td>
                <?php  if (isset($this->surveyId)) : ?>
        			<td>
        				<?php 
							$urlChoose = 'add_question.php?questionId='.$question->id.'&amp;surveyId='.$this->surveyId;     		 
                			echo claro_html_link($urlChoose, $selectIcon);
        				?>
        			</td>
        		<?php endif; ?>
        		<?php if (!isset($this->surveyId)) : ?>  
                <td align="center">
                	<?php 
                		$urlEdit = 'edit_question.php?questionId='.$question->id;    		 
                		echo claro_html_link($urlEdit, $editIcon);
                	?>
                </td>                
                <td>
                	<?php                 		
                		$urlDelete = 'question_pool.php?questionId='.$question->id.'&amp;cmd=questionDel';     		 
                		echo claro_html_link($urlDelete, $deleteIcon);
                	?>
                </td>
                <?php endif; ?>                
			</tr>
    	<?php endforeach;?>
    <?php endif; ?>    
	</tbody>
</table>