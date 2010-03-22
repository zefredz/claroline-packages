<?php
	$participantCount = count($this->survey->getParticipationList());
	$surveyLineList = $this->survey->getSurveyLineList();

        $questionLineList = array_filter($surveyLineList, create_function('$surveyLine', 'return is_a($surveyLine, "QuestionLine");'));
	

	$surveyResults = SurveyResults::loadResults($this->survey->id);
	$cmd_menu = array();
	if($this->editMode)
    {	
        $cmd_menu[] = '<a class="claroCmd" href="show_results.php?surveyId='.$this->survey->id.'&amp;cmd=reset">'.get_lang('Delete all results').'</a>';
	    $cmd_menu[] = '<a class="claroCmd" href="show_participation.php?surveyId='.$this->survey->id.'">'.get_lang('Show participations').'</a>';
	}
    $cmd_menu[] = '<a class="claroCmd" href="show_results.php?surveyId='.$this->survey->id.'&amp;format=SyntheticCSV">'.get_lang('Export Synthetic results').'</a>';
    $cmd_menu[] = '<a class="claroCmd" href="show_results.php?surveyId='.$this->survey->id.'&amp;format=RawCSV">'.get_lang('Export Raw results').'</a>';
	echo '<p>' . claro_html_menu_horizontal($cmd_menu) . '</p>'; 
    
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
	<?php foreach ($questionLineList as $surveyLine) : ?>
		<?php 			
			$question = $surveyLine->question;
			$choiceList = $question->getChoiceList();
			$lineResultList = new LineResults();
       		if( isset($surveyResults->lineResultList[$surveyLine->id]))
       		{
       			$lineResultList = $surveyResults->lineResultList[$surveyLine->id];
       		}       					
		?>
		<div class="LVSURVEYQuestion">
        	<input type="hidden" name="questionType" value="<?php echo $question->type; ?>" />
        	<div class="LVSURVEYQuestionTitle"><?php  echo htmlspecialchars($question->text); ?></div>        
        	<div class="LVSURVEYQuestionContent">
        	<?php if($question->type != 'OPEN' ): ?>
        		<div class="LVSURVEYQuestionResultChart"></div>
        	<?php endif;?>        	
       			<?php if (empty($choiceList)) :?>
       				<div class="answer">
       					<?php echo get_lang('No Choices'); ?>
       				</div>       			
       			<?php  else :?>
       				<div class="answer">
       					<table>
       					<?php foreach($choiceList as $choice) : ?>
       						<?php 
       							$choiceResultList = new ChoiceResults();
       							if( isset($lineResultList->choiceResultList[$choice->id]))
       							{
       								$choiceResultList = $lineResultList->choiceResultList[$choice->id];
       							}
       							$resultList = $choiceResultList->resultList;
       							$resultCount = count($resultList); 
       							//bad hack but we do not not want to show choices made in other surveys for open questions
       							if($question->type == 'OPEN' && $resultCount == 0 ) continue;      							
       						?>
       						<tr class="answerTR">
       							<td>
       								<span class="answerLabel" >
       									<?php echo $choice->text; ?> :
									</span>
								</td>
								<?php  if (0 == $resultCount) :?>
       								<td colspan="2" >
										<?php echo get_lang('No Results'); ?>
									</td>
									<td>										
        	    						 &#040; <!--  left parenthese -->
        	    						 <span class="answerPercentage" >
        	    						 	0
        	    						 </span>  &#37; <!--  percentage  -->
        	    						 &#041; <!--  right parenthese  -->
        	    					</td>
       							<?php else :?>
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
	       						<tr>
	        	    				<td>
	          	    					<a href="#" class="deployDetailedList">
	          	    						<?php  echo get_lang("Display Details"); ?>
	          	   						</a>
	          	    					<ul class="detailedList" >
	          	    					<?php  foreach($resultList as $result) : ?>
	          	    						<?php if (!$this->survey->is_anonymous) : ?>
	          	    							<li>
	          	    								<?php echo $result->firstName.' '.$result->lastName; ?> 
	          	    								<?php if(!empty($result->comment)) : ?>
	          	    									: <?php echo $result->comment; ?>
	          	    								<?php endif; ?> 
	          	    							</li>
	          	    						<?php else : ?>
	          	    							<?php if(!empty($result->comment)) : ?>
	          	    								<li>
	          	    									<?php echo $result->comment; ?>
	          	    								</li>
	          	    							<?php endif;?>
	          	    						<?php endif; ?>
	          	    					<?php  endforeach; ?>
	          	    					</ul>
	          	    					<a href="#" class="hideDetailedList">
	          	    						<?php  echo get_lang("Hide Details"); ?>
	          	   						</a>
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