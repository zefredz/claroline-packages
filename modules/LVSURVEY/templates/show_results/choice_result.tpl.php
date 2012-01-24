<?php 
$participantCount = $this->participantCount;
$choice = $this->choice;
$choiceResults = $this->choiceResults;
$anonymous = $this->anonymous;

$choice = $this->choice;
$choiceText = $choice->getText();
$resultList = $choiceResults->resultList;
$resultCount = count($resultList);
?>
<tr class="answerTR">
    <td>
        <span class="answerLabel" >
        <?php echo $choice->text; ?>
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
<?php if (! $anonymous) : ?>
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