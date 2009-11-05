<?php
	$participantCount = count($this->survey->getParticipationList());
	 
	if($this->editMode)
    {
    	$cmd_menu = array();
        $cmd_menu[] = '<a class="claroCmd" href="show_results.php?surveyId='.$this->survey->id.'&amp;cmd=resultsDel">'.get_lang('Delete all results').'</a>';
    	 echo '<p>' . claro_html_menu_horizontal($cmd_menu) . '</p>'; 
    }
	claro_html_tool_title(get_lang('Results'));
	CssLoader::getInstance()->load('LVSURVEY');
    JavascriptLoader::getInstance()->load('jquery');
    JavascriptLoader::getInstance()->load('excanvas.min');
    JavascriptLoader::getInstance()->load('jquery.flot');
    JavascriptLoader::getInstance()->load('jquery.flot.pie');
    JavascriptLoader::getInstance()->load('surveyResult');
?>

<div><?php  echo $this->survey->description; ?></div>
<div class="LVSURVEYQuestionList">
	<?php foreach ($this->survey->getQuestionList() as $question) : ?>
		<?php 
			$choiceList = $question->getChoiceList();
		?>
		<div class="LVSURVEYQuestion">
        	<input type="hidden" name="questionType" value="<?php echo $question->type; ?>" />
        	<div class="LVSURVEYQuestionTitle"><?php  echo htmlspecialchars($question->text); ?></div>        
        	<div class="LVSURVEYQuestionContent">
       			<div class="LVSURVEYQuestionResultChart"></div>
       			<?php if (empty($choiceList)) :?>
       				<div class="answer">
       					<?php echo get_lang('No result'); ?>
       				</div>
       			<?php  else :?>
       				<div class="answer">
       					<table>
       					<?php foreach($choiceList as $choice) : ?>
       						<?php 
       							$resultList = Result::loadResults($this->survey->id, $question->id, $choice->id);
       							$resultCount = count($resultList);
       						?>
       						<tr class="answerTR">
       							<td>
       								<span class="answerLabel" >
       									<?php echo $choice->text; ?> :
									</span>
								</td>
								<td>
									<?php echo $resultCount; ?>
								</td>
								<td>
									
        	    					 &#040; <!--  left parenthese -->
        	    					 <span class="answerPercentage" >
        	    					 	<?php  echo ($resultCount*100/$participantCount)?>
        	    					 </span>  &#37; <!--  percentage  -->
        	    					 &#041; <!--  right parenthese  -->
        	    				</td>								
       						</tr>
       						<?php  if($resultCount > 0) :?>       						
	       						<tr>
	        	    				<td>
	          	    					<a href="#" class="deployDetailedList">
	          	    						<?php  echo get_lang("See Details"); ?>
	          	   						</a>
	          	    					<ul class="detailedList" >
	          	    					<?php  foreach($resultList as $result) : ?>
	          	    						<li>
	          	    							<?php if (!$this->survey->is_anonymous) : ?>
	          	    								<?php echo $result->firstName.' '.$result->lastName; ?> :
	          	    							<?php endif;?>
	          	    								<?php echo $result->comment; ?>
	          	    						</li>
	          	    					<?php  endforeach; ?>
	          	    					</ul>
	          	    				</td>
	          	    			</tr>
	          	    		<?php endif;?>
       					<?php endforeach;?>
       					</table>
       				</div>
       			<?php  endif;?>
       		</div>
       	</div>
	<?php endforeach;?>
</div>