<?php 
/**
    * This is a tool to create surveys. It's the new version better than older CLSURVEY
    * @copyright (c) Haute Ecole Léonard de Vinci
    * @version     0.1 $Revision$
    * @author      Van Eerdenbrugghe Philippe <philippe.vaneerdenbrugghe@vinci.be>
    * @license     http://www.gnu.org/copyleft/gpl.html
    *              GNU GENERAL PUBLIC LICENSE version 2 or later
    * @package     LVSURVEY
    * 
    * TODO 
    *  OPEN QUESTION : do not show choices made by users of other  surveys ! (change hack in show_survey.tpl)
    *  prevent people to change their answer after a survey is closed
    *  prevent users from deleteing used questions or questions belonging to someone else
    *  propose array question : http://docs.limesurvey.org/tiki-index.php?page=Question+types&structure=English+Instructions+for+LimeSurvey#A_Array_5_Point_choice_
    *  
*/
header('Location:./survey_list.php');
exit();
?>