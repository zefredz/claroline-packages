 <?php 
  /**
     * This is a tool to create surveys. It's the new version better than older CLSURVEY
     * @copyright (c) Haute Ecole Léonard de Vinci
     * @version     0.1 $Revision$
     * @author      BAUDET Gregory <gregory.baudet@gmail.com>
     * @license     http://www.gnu.org/copyleft/gpl.html
     *              GNU GENERAL PUBLIC LICENSE version 2 or later
     * @package     LVSURVEY
     */
     
require_once dirname(__FILE__) . '/SurveyConstants.php';

//class to manage list of surveys
class SurveyList {
    
    //unique id of the course
    protected $courseId;
    
    //if user is allowed to edit survey
    protected $editMode;
    
    //list of surveys
    protected $surveyList;
    
    public function __construct($courseId, $editMode)
    {
        $this->editMode = $editMode;
        $this->courseId = mysql_real_escape_string($courseId);
    }
    
    //load the survey list
    public function load()
    {
        /*
                     * get rows of table
                     */
        if($this->editMode == true)
        {
             $sql = "SELECT
                        `id`,
                        `title`,
                        `description`,
                        `visibility`,
                        `resultsVisibility`,
                        UNIX_TIMESTAMP(`startDate`) AS `unix_start_date`,
                        UNIX_TIMESTAMP(`endDate`) AS `unix_end_date`
                FROM `".SurveyConstants::$SURVEY_TBL."`
                WHERE `courseId` = '".$this->courseId."'
                ORDER BY `rank` DESC";
        }
        else{
                $sql = "SELECT
                        `id`,
                        `title`,
                        `description`,
                        `resultsVisibility`,
                        UNIX_TIMESTAMP(`startDate`) AS `unix_start_date`,
                        UNIX_TIMESTAMP(`endDate`) AS `unix_end_date`
                FROM `".SurveyConstants::$SURVEY_TBL."`
                WHERE `courseId` = '".$this->courseId."' AND `visibility` = 'VISIBLE'
                ORDER BY `rank` DESC";
        }

        $this->surveyList = claro_sql_query_fetch_all($sql);
    }
    
    //move survey up in the list
    public function moveSurveyUp($surveyId)
    {
    	//exchange rank with 
        $sql = "SELECT
                	`id`,
                	`courseId`,
					`rank`
        		FROM `".SurveyConstants::$SURVEY_TBL."`
        		WHERE `id` = ".(int) $surveyId;
        $firstSurvey = claro_sql_query_get_single_row($sql);
        
        $sql = "SELECT
                	`id`,
                	`courseId`,
                	`rank`
                FROM `".SurveyConstants::$SURVEY_TBL."`
                WHERE `courseId` = '".$firstSurvey['courseId']."'
                AND `rank` > ".(int) $firstSurvey['rank']
                ." ORDER BY `rank` ASC LIMIT 1";
        $result = claro_sql_query($sql);
        if(mysql_num_rows($result)==1)
        {
            $nextSurvey = mysql_fetch_array($result, MYSQL_ASSOC);
            mysql_free_result($result);
        }
        else
            return false; //survey already first
            
        //exchange ranks
        $sql = "UPDATE `".SurveyConstants::$SURVEY_TBL."`
                	SET `rank` = ".(int) $nextSurvey['rank']."
                	WHERE `id` = ".(int) $firstSurvey['id'];
        claro_sql_query($sql);
        $sql = "UPDATE `".SurveyConstants::$SURVEY_TBL."`
                	SET `rank` = ".(int) $firstSurvey['rank']."
                	WHERE `id` = ".(int) $nextSurvey['id'];
        
   		
        if( claro_sql_query($sql) )
        {
            return true;
        }
        else
        {
            return false;
        }
    }
    
    //move survey down in the list
    public function moveSurveyDown($surveyId)
    {
        $sql = "SELECT
                	`id`,
                	`courseId`,
					`rank`
        		FROM `".SurveyConstants::$SURVEY_TBL."`
        		WHERE `id` = ".(int) $surveyId;
        $firstSurvey = claro_sql_query_get_single_row($sql);
        
        $sql = "SELECT
                	`id`,
                	`courseId`,
                	`rank`
                FROM `".SurveyConstants::$SURVEY_TBL."`
                WHERE `courseId` = '".$firstSurvey['courseId']."' 
                AND `rank` < ".(int) $firstSurvey['rank']
                ." ORDER BY `rank` DESC LIMIT 1";
        $result = claro_sql_query($sql);
        if(mysql_num_rows($result)==1)
        {
            $nextSurvey = mysql_fetch_array($result, MYSQL_ASSOC);
            mysql_free_result($result);
        }
        else
            return false; //survey already last
            
        //exchange ranks
        $sql = "UPDATE `".SurveyConstants::$SURVEY_TBL."`
                	SET `rank` = ".(int) $nextSurvey['rank']."
                	WHERE `id` = ".(int) $firstSurvey['id'];
        claro_sql_query($sql) ;
        
        $sql = "UPDATE `".SurveyConstants::$SURVEY_TBL."`
                	SET `rank` = ".(int) $firstSurvey['rank']."
                	WHERE `id` = ".(int) $nextSurvey['id'];
        
        if( claro_sql_query($sql) )
        {
            return true;
        }
        else
        {
            return false;
        }
    }
    
    //generate html for list
    public function render()
    {
        $out = '';
        
        $out .= '<table class="claroTable emphaseLine" border="0" align="center" cellpadding="2" cellspacing="2" width="100%">' . "\n\n"
        .     '<thead>' . "\n"
        .     '<tr class="headerX">' . "\n"
        .     '<th>' . get_lang('Survey title') . '</th>' . "\n"
        .     '<th>' . get_lang('Access') . '</th>' . "\n";
        $colspan = 2;
        
        if( $this->editMode )
        {
            $out .=  '<th>' . get_lang('Modify') . '</th>' . "\n"
            .     '<th>' . get_lang('Delete') . '</th>' . "\n"
            .     '<th>' . get_lang('Move') . '</th>' . "\n"
            .     '<th>' . get_lang('Visibility') . '</th>' . "\n";
            $colspan = 6;
        
            /*
            if( get_conf('enableExerciseExportQTI') )
            {
                $out .= '<th>' . get_lang('Export') . '</th>' . "\n";
                $colspan++;
            }
        
            if( $is_allowedToTrack )
            {
                $out .= '<th>' . get_lang('Statistics') . '</th>' . "\n";
                $colspan++;
            }*/
        }
        
        $out .= '</tr>' . "\n"
        .     '</thead>' . "\n\n"
        .     '<tbody>' . "\n\n";  
        
        if( !empty($this->surveyList) )
        {
            $counter = 0;
            foreach( $this->surveyList as $aSurvey )
            {
                $counter++;
                if( $this->editMode && $aSurvey['visibility'] == 'INVISIBLE' )
                {
                    $invisibleClass = ' class="invisible"';
                }
                else
                {
                    $invisibleClass = '';
                }
        
                $out .= '<tr'.$invisibleClass.'>' . "\n"
                .     '<td>'
                .     '<a href="show_survey.php?surveyId='.$aSurvey['id'].'" class="item">'
                .     '<img src="' .get_icon_url('survey').'" alt="" />'
                .     $aSurvey['title']
                .     '</a>'
                .     '</td>' . "\n";
        		
                //determine if survey is accessible
                if(is_null($aSurvey['unix_start_date']))
                {
                	$surveystatus = get_lang("Closed");
                	$surveyaction = ' (<a href="survey_list.php?surveyId='.$aSurvey['id'].'&amp;cmd=surveyStart">' 
               			            . get_lang('Start now')
               			            .'</a>)';
                }
                else if($aSurvey['unix_start_date']>time())
                {
           			$surveystatus = get_lang("Accessible from %date", array( '%date' => claro_html_localised_date(get_locale('dateFormatLong'), $aSurvey['unix_start_date'])));
                    $surveyaction = ' (<a href="survey_list.php?surveyId='.$aSurvey['id'].'&amp;cmd=surveyStart">(' 
               			            . get_lang('Start now')
               			            .'</a>)';
                }
           		else{ 
               		if(is_null($aSurvey['unix_end_date']))
               		{
               			$surveystatus = get_lang("Accessible");
               			$surveyaction = ' (<a href="survey_list.php?surveyId='.$aSurvey['id'].'&amp;cmd=surveyStop">' 
               			            . get_lang('Close now')
               			            .'</a>)';
               		}
               		else if($aSurvey['unix_end_date']>time())
               		{
               			$surveystatus = get_lang("Accessible until %date", array( '%date' => claro_html_localised_date(get_locale('dateFormatLong'), $aSurvey['unix_end_date'])));
               		    $surveyaction = ' (<a href="survey_list.php?surveyId='.$aSurvey['id'].'&amp;cmd=surveyStop">' 
               			            . get_lang('Close now')
               			            .'</a>)';
               		}
               		else
               		{
               			$surveystatus = get_lang("Closed since %date", array( '%date' => claro_html_localised_date(get_locale('dateFormatLong'), $aSurvey['unix_end_date'])));
   		                $surveyaction = ' (<a href="survey_list.php?surveyId='.$aSurvey['id'].'&amp;cmd=surveyRestart">' 
   			                . get_lang('Reopen now')
   			                .'</a>)';
               		}
               	}
           		
                
                $out .= '<td> ' . $surveystatus .($this->editMode?$surveyaction:'').'</td>' . "\n";
                
                
                if( $this->editMode )
                {
                    //edit icon
                    $out .=  '<td align="center">'
                    .     '<a href="edit_survey.php?surveyId='.$aSurvey['id'].'">'
                    .     '<img src="'. get_icon_url('edit').'" border="0" alt="'.get_lang('Modify').'" />'
                    .     '</a>'
                    .     '</td>' . "\n";
                    
                    //delete icon
                    $out .=  '<td align="center">'
                    .     '<a href="survey_list.php?surveyId='.$aSurvey['id'].'&amp;cmd=surveyDel" >'
                    .     '<img src="' .get_icon_url('delete').'" border="0" alt="'.get_lang('Delete').'" />'
                    .     '</a>'
                    .     '</td>' . "\n";
                    
                    //add arrows to move survey
                    $updownlinks = '';
                    if($counter != 1)
                    	$updownlinks .= '<a href="survey_list.php?surveyId='.$aSurvey['id'].'&amp;cmd=surveyMoveUp">'
                        .     '<img src="' .get_icon_url('move_up').'" border="0" alt="'.get_lang('Move up').'" />'
                        .     '</a>';
                    if(count($this->surveyList)!=$counter)
                    	$updownlinks .= '<a href="survey_list.php?surveyId='.$aSurvey['id'].'&amp;cmd=surveyMoveDown">'
                        .     '<img src="' . get_icon_url('move_down').'" border="0" alt="'.get_lang('Move down').'" />'
                        .     '</a>';
                    if($updownlinks=='')
                        $updownlinks='&nbsp;';
                    $out.= '<td align="center">'.$updownlinks.'</td>' . "\n";
                    
                    //add eye of visibility
                    if( $aSurvey['visibility'] == 'VISIBLE' )
                    {
                        $out .=  '<td align="center">'
                        .     '<a href="survey_list.php?surveyId='.$aSurvey['id'].'&amp;cmd=surveyMkInvis">'
                        .     '<img src="' .get_icon_url('visible').'" border="0" alt="'.get_lang('Make invisible').'" />'
                        .     '</a>'
                        .     '</td>' . "\n";
                    }
                    else
                    {
                        $out .=  '<td align="center">'
                        .     '<a href="survey_list.php?surveyId='.$aSurvey['id'].'&amp;cmd=surveyMkVis">'
                        .     '<img src="' . get_icon_url('invisible').'" border="0" alt="'.get_lang('Make visible').'" />'
                        .     '</a>'
                        .     '</td>' . "\n";
                    }
                }
        
                $out .=  '</tr>' . "\n\n";
            }
        }
        else
        {
            $out .= '<tr>' . "\n"
            .     '<td colspan="'.$colspan.'">' . get_lang('Empty') . '</td>' . "\n"
            .     '</tr>' . "\n\n";
        }
        
        $out .= '</tbody>' . "\n\n"
            .'</table>' . "\n\n";
        
        return $out;
    }
}


?>