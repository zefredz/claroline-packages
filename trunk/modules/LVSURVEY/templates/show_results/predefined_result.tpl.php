<?php 
$participantCount = $this->participantCount;
//$predefinedAnswer = $this->predefinedAnswer;
$resultList = $this->predefinedResults;
$anonymous = $this->anonymous;

//$choice = $this->choice;
//$choiceText = $choice->getText();
//$resultList = $predefinedResults->resultList;
$resultCount = count($resultList);
?>
<tr class="answerTR">
	<td>
   		<span class="answerLabel" >
        <?php echo get_lang( $resultList[0]->predefinedValue ) ?>
	</span> :
	</td>
       <td colspan="2" >
		<?php echo (0 == $resultCount)?get_lang('No Results'):$resultCount; ?>
	</td>
	<td>										
    	&#040; <!--  left parenthese -->
        <span class="answerPercentage" >
        	<?php  echo (0 == $resultCount)?0:($resultCount*100/$participantCount); ?>
        </span>  &#37; <!--  percentage  -->
        &#041; <!--  right parenthese  -->
    </td>      
</tr>
<?php if ($anonymous) : ?>
	<tr>
		<td colspan="3">
			<div class="LVSURVEYQuestionDetails">
				<a href="#" class="deployDetailedList">
					<?php  echo get_lang("Display details"); ?>
				</a>
				<ul class="detailedList" >
					<?php  foreach($resultList as $result) : ?>						
			              	<li>
			              		<?php echo $result->firstName.' '.$result->lastName; ?>  
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