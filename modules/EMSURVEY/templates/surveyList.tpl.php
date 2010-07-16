<?php  // $Id$ ?>
<h3 class="claroToolTitle"><?php  echo get_lang('Surveys duplication'); ?></h3>

<p>
    <?php  echo get_lang('Choose the survey you want to duplicate by clicking it.'); ?>
</p>

<?php
if( !empty( $this->surveyList ) ) :

foreach( $this->surveyList as $survey ) :
    echo '<h4>' . $survey['course']['officialCode'] . ' - ' . $survey['course']['intitule'] . '</h4>';
    echo '<ul>';
    
    foreach ( $survey['surveys'] as $courseSurvey ) : 
        echo '<li><a href="'.htmlspecialchars( Url::Contextualize($_SERVER['PHP_SELF'] . '?cmd=selectCourse&surveyId='.$courseSurvey['surveyId']) ).'">'.$courseSurvey['title'].'</a></li>';
    endforeach;
    
    echo '</ul>';
endforeach;

else : 
    echo '<p>' . get_lang('No survey') . '</p>';
endif;
?>