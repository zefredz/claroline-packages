<?php  // $Id$ ?>
<h3 class="claroToolTitle"><?php  echo get_lang('Surveys duplication'); ?></h3>

<?php 
if( !empty( $this->selectedSurvey ) ) :
    echo '<p>';
    echo get_lang('You\'ve selected the survey <b>%survey</b>.', array('%survey' => $this->selectedSurvey['title']));
    echo '<br/>';
    echo get_lang('Choose the course(s) in which you want to duplicate this survey.'); 
    echo '</p>';
    
    if( !empty( $this->courseList ) ) :
        echo '<form method="post" action="' . $_SERVER['PHP_SELF'] . '" >';
        echo '<fieldset>';
        echo '<input type="hidden" name="surveyId" value="'.$this->selectedSurvey['surveyId'].'" />';
        echo '<input type="hidden" name="cmd" value="editProperties" />';
        
        foreach( $this->courseList as $course ) :
        
            echo '<input type="checkbox" name="selectCourse'.$course['courseId'].'" id="select_course_'.$course['courseId'].'" /> ';
            echo '<label for="select_course_'.$course['courseId'].'">'.$course['officialCode'].' - '.$course['intitule'];
            
            if (!empty($course['sourceCourseId'])) : 
                echo ' ['.get_lang('Session').']';
            endif;
            
            echo '</label><br/>';
            
        endforeach;
        
        echo '</fieldset>';
        echo '<input type="submit" value="' . get_lang('Select') . '" />';
        echo '</form>';
    else : 
        echo '<p>' . get_lang('No course') . '</p>';
    endif;
    
else : 
    echo '<p>';
    echo get_lang('No survey selected.  Please <a href="%link_to_survey_list">select one</a>.', 
    array('%link_to_survey_list' => htmlspecialchars(Url::Contextualize($_SERVER['PHP_SELF'] . '?cmd=selectSurvey' ))));
    echo '</p>';
endif;
?>