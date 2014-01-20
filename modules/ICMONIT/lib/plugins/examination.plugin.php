<?php // $Id$
/**
 * Student Monitoring Tool
 *
 * @version     ICMONIT 1.0.0 $Revision$ - Claroline 1.11.5
 * @copyright   2001-2013 Universite catholique de Louvain (UCL)
 * @license     http://www.gnu.org/copyleft/gpl.html (GPL) GENERAL PUBLIC LICENSE
 * @package     ICMONIT
 * @author      Frederic Fervaille <frederic.fervaille@uclouvain.be>
 */

/**
 * Report plugins for "Examination" tool
 * These plugins allow to send datas from Claroline tools to the Report object
 * @const TOOL_NAME
 * @const TOOL_LABEL
 */
class ExaminationPlugin extends ReportPlugin
{
    const TOOL_NAME  = 'Examination Report';
    const TOOL_LABEL = 'ICMONIT';
    
    /**
     * contructor
     */
    public function __construct()
    {
        $this->toolName  = self::TOOL_NAME;
        $this->toolLabel = self::TOOL_LABEL;
        
        $this->tbl = get_module_course_tbl ( array ( 'ICMONIT_examination_session' , 'ICMONIT_examination_score' ) );
    }
    
    /**
     * loads datas needed by Report
     */
    public function load()
    {
        $this->itemQueryResult = Claroline::getDatabase()->query( "
            SELECT
                id, title, visibility
            FROM
                `{$this->tbl['ICMONIT_examination_session']}`" );
        
        $this->dataQueryResult = array( Claroline::getDatabase()->query( "
            SELECT
                M.user_id,
                M.session_id AS item_id,
                ROUND( " . self::DEFAULT_MAX_SCORE . " * ( M.score / S.max_score ) ) AS score
            FROM
                `{$this->tbl['ICMONIT_examination_score']}` AS M,
                `{$this->tbl['ICMONIT_examination_session']}` AS S
            WHERE
                M.session_id = S.id" ) );
    }
}
