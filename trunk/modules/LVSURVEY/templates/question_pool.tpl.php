<?php 
	$editIcon 		= claro_html_icon('edit', 		get_lang('Modify'), 		get_lang('Modify'));
	$deleteIcon		= claro_html_icon('delete');
	$selectIcon		= claro_html_icon('select',		get_lang('Select'), 		get_lang('Select'));
	
	$surveySuffix = isset($this->surveyId)?'&amp;surveyId='.$this->surveyId:'';
    $mine_filter = '&amp;author_filter='. claro_get_current_user_id().'';
    $current_course_filter = '&amp;course_filter='. claro_get_current_course_id().'';

	echo claro_html_tool_title(get_lang('List of questions'));

	$cmd_menu = array();
	$cmd_menu[] = '<a class="claroCmd" href="edit_question.php">'.get_lang('New question').'</a>';
    echo '<p>' . claro_html_menu_horizontal($cmd_menu) . '</p>';
    
    $disp_menu = array();	
    $disp_menu[] = '<a id="filter_all_link" class="claroCmd" href="question_pool.php?'.$surveySuffix.'">'.get_lang('Display all questions').'</a>';
    $disp_menu[] = '<a id="filter_mine_link" class="claroCmd" href="question_pool.php?'.$surveySuffix.$mine_filter.'">'.get_lang('Display only my questions').'</a>';
    $disp_menu[] = '<a id="filter_current_course_link" class="claroCmd" href="question_pool.php?'.$surveySuffix.$current_course_filter.'">'.get_lang('Display only questions appearing in this course').'</a>';
    echo '<p>' . claro_html_menu_horizontal($disp_menu) . '</p>';
    

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
					<?php  echo get_lang('Author of question'); ?>
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
        	<td colspan="6">
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
                	<?php  echo "{$question->getAuthor()->firstName} {$question->getAuthor()->lastName}"; ?>
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
                		$urlDelete = 'question_pool.php?questionId='.$question->id.'&amp;cmd=questionDelete';     		 
                		echo claro_html_link($urlDelete, $deleteIcon);
                	?>
                </td>
                <?php endif; ?>                
			</tr>
    	<?php endforeach;?>
    <?php endif; ?>    
	</tbody>
</table>