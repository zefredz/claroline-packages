<?php // $Id$
/**
 * Student Report for Claroline
 *
 * @version     UCREPORT 2.4.3 $Revision$ - Claroline 1.11.5
 * @copyright   2001-2013 Universite catholique de Louvain (UCL)
 * @license     http://www.gnu.org/copyleft/gpl.html (GPL) GENERAL PUBLIC LICENSE
 * @package     UCREPORT
 * @author      Frederic Fervaille <frederic.fervaille@uclouvain.be>
 */

/**
 * Report plugins for "Exercice" tool
 * These plugins allow to send datas from Claroline tools to the Report object
 * @const TOOL_NAME
 * @const TOOL_LABEL
 */
class ExercisePlugin extends ReportPlugin
{
    const TOOL_NAME  = 'Exercises';
    const TOOL_LABEL = 'CLQWZ';
    
    /**
     * contructor
     */
    public function __construct()
    {
        $this->toolName  = self::TOOL_NAME;
        $this->toolLabel = self::TOOL_LABEL;
        
        $this->tbl = get_module_course_tbl ( array ( 'qwz_exercise' , 'qwz_tracking' ) );
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
                `{$this->tbl['qwz_exercise']}`" );
        
        $this->dataQueryResult = array( Claroline::getDatabase()->query( "
            SELECT
                T.user_id,
                T.exo_id AS item_id,
                ROUND( " . self::DEFAULT_MAX_SCORE . " * ( T.result/T.weighting ) ) AS score
            FROM
                `{$this->tbl['qwz_tracking']}` AS T,
                `{$this->tbl['qwz_exercise']}` AS E
            WHERE
                T.exo_id = E.id" ) );
    }
}