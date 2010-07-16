<?php  // $Id$ 
JavascriptLoader::getInstance()->load('jquery');
JavascriptLoader::getInstance()->load('editProperties');
?>

<h3 class="claroToolTitle"><?php  echo get_lang('Surveys duplication'); ?></h3>

<?php 
if( !empty( $this->selectedSurvey ) && !empty( $this->selectedCourseList ) ) : 
?>
    <p>
        <?php echo get_lang('You\'ve selected the survey (<b>%survey</b>) and 
        the courses in which you want to duplicate it', 
        array('%survey' => $this->selectedSurvey['title'])); ?>
        <br/>
        <?php echo get_lang('If you wish, before final duplication, you 
        can now edit the future surveys\' properties.<br/>You can also use the 
        "magic replace" function, by entering <i>%speaker%</i> in the survey\'s 
        title.  It will create a new survey for each course\'s manager.'); ?>
    </p>
    
    <form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>" >
    <fieldset>
    <input type="hidden" name="cmd" value="duplicate" />
    <input type="hidden" name="surveyId" value="<?php echo $this->selectedSurvey['surveyId']; ?>" />
    
    <ul>
    
    <?php 
    foreach ($this->selectedCourseList as $course) : 
        echo '<li><b>'.$course['intitule'].'</b>';
        
        if (!empty($course['sourceCourseId'])) : 
            echo ' ['.get_lang('Session').']';
        endif;
        
        echo '<input type="hidden" name="courseCode'.$course['courseCode'].'" 
            value="'.$course['courseCode'].'" />';
        
        echo ' <a class="linkToEdit" id="linkToEdit'.$course['courseCode'].'" 
            href="#"><img src="'.get_icon_url('edit').'" 
            alt="Modify survey\'s title" /> '.get_lang('Modify survey\'s title').'</a>';
        
        echo '<div class="editProperties", id="editProperties'.$course['courseCode'].'">';
        echo '<input type="checkbox" name="useNewTitle'.$course['courseCode'].'" 
            id="useNewTitle'.$course['courseCode'].'" /> ';
        echo '<label for="useNewTitle'.$course['courseCode'].'">'
            . get_lang('Use this new title for the survey');
        echo '</label>';
        echo '<br/>';
        echo '<input class="newTitle" name="newTitle'.$course['courseCode'].'"
            id="newTitle'.$course['courseCode'].'" value="'.$this->selectedSurvey['title'].'" />';
        echo '</div>';
        
        echo '</li>';
    endforeach;
    ?>
    
    </ul>
    
    </fieldset>
    <input type="submit" name="changeProperties" value="<?php echo get_lang('Next'); ?>" />
    </form>
    
<?php 
else : 
?>
    <p>
        <?php get_lang('No survey or courses selected.  Please <a href="%link_to_survey_list">select a survey to start</a>.', 
        array('%link_to_survey_list' => htmlspecialchars(Url::Contextualize($_SERVER['PHP_SELF'] . '?cmd=selectSurvey' )))); ?>
    </p>
<?php
endif;
?>