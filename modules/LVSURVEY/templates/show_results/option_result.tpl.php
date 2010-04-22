<?php 
$participantCount = $this->participantCount;
$choice = $this->choice;
$choiceResults = $this->choiceResults;
$anonymous = $this->anonymous;

$optionList = $choice->getOptionList();
$choiceText = $choice->getText(); 
?>
<tr class="answerTR">
       <td>
       		<span class="answerLabel" >
       			<?php echo $choice->text; ?> :
			</span>
		</td>
		<?php  foreach($optionList as $anOption) : ?>
			<?php 
				$optionResultList = new OptionResults();
       			if( isset($choiceResults->optionResultList[$anOption->getId()]))
       			{
       				$optionResultList = $choiceResults->optionResultList[$anOption->getId()];
       			}				       							
              	$resultList = $optionResultList->resultList;
              	$resultCount = count($resultList);
			?>
            	<td style="padding-left:10px;" class="OptionText" >
            		<?php echo $anOption->getText(); ?>
            	</td>
            	<td style="font-size: x-small;">
            		<span class="OptionCount" >
            			<?php echo $resultCount; ?>
            		</span>&#040; <!--  left parenthese -->
            		<span class="answerPercentage" >
		           	 	<?php  echo (0 == $resultCount)?0:($resultCount*100/$participantCount); ?>
		           	 </span> &#37;<!--  percentage  -->&#041; <!--  right parenthese  -->
            	</td>
        <?php endforeach ?>
</tr>
<?php if (! $anonymous) : ?>
	<tr>
		<td colspan="<?php echo 1+(count($optionList)); ?>">
			<div class="LVSURVEYQuestionDetails">
				<a href="#" class="deployDetailedList">
					<?php  echo get_lang("Display Details"); ?>
				</a>
				<ul class="detailedList" >
					<?php  foreach($choiceResults->resultList as $result) : ?>
						<?php 
							$optionChosenId = $result->optionId;
							$option = $choice->getOption($optionChosenId);
						?>						
			            <li>
			            	<?php echo "{$result->firstName} {$result->lastName} : {$option->getText()}"; ?>  
			            </li>
			          <?php  endforeach; ?>
			    </ul>
			    <a href="#" class="hideDetailedList">
			    	<?php  echo get_lang("Hide Details"); ?>
			    </a>
			</div>
		</td>
	</tr>
<?php endif; ?>