<?php // $Id$
/**
 * Student Report for Claroline
 *
 * @version     UCREPORT 1.3.0 $Revision$ - Claroline 1.9
 * @copyright   2001-2011 Universite catholique de Louvain (UCL)
 * @license     http://www.gnu.org/copyleft/gpl.html (GPL) GENERAL PUBLIC LICENSE
 * @package     UCREPORT
 * @author      Frederic Fervaille <frederic.fervaille@uclouvain.be>
 */

/**
 * Report plugins for "Assignments" tool
 * These plugins allow to send datas from Claroline tools to the Report object
 * @const TOOL_NAME
 */
class WorkPlugin extends ReportPlugin
{
    const TOOL_NAME  = 'Assignments';
    const TOOL_LABEL = 'CLWORK';
    
    /**
     * constructor
     */
    public function __construct()
    {
        $this->toolName  = self::TOOL_NAME;
        $this->toolLabel = self::TOOL_LABEL;
        
        $this->tbl = get_module_course_tbl ( array ( 'wrk_assignment'
                                                   , 'wrk_submission'
                                                   , 'group_team'
                                                   , 'group_rel_team_user' ) );
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
                    `{$this->tbl['wrk_assignment']}`" );
        
        
        $this->dataQueryResult = array( Claroline::getDatabase()->query( "
                SELECT
                    S1.id,
                    S1.user_id,
                    S1.assignment_id as item_id,
                    A.title,
                    S2.score,
                    S2.creation_date
                FROM
                    `{$this->tbl['wrk_assignment']}` AS A,
                    `{$this->tbl['wrk_submission']}` AS S2,
                    `{$this->tbl['wrk_submission']}` AS S1
                LEFT JOIN
                    `{$this->tbl['group_rel_team_user']}` AS R
                ON
                    R.team = S1.group_id
                AND
                    R.user = S1.user_id
                WHERE
                    A.assignment_type = 'INDIVIDUAL'
                AND
                    A.id = S1.assignment_id
                AND
                    S2.parent_id = S1.id
                AND
                    S2.parent_id IS NOT NULL
                AND
                    S2.score >= 0
                ORDER BY
                    S1.user_id, S2.creation_date DESC" ) ,
                                       
                                       Claroline::getDatabase()->query( "
                SELECT
                    S1.id,
                    S1.user_id,
                    S1.assignment_id as item_id,
                    A.title,
                    S2.score,
                    S2.creation_date
                FROM
                    `{$this->tbl['group_team']}` AS G,
                    `{$this->tbl['group_rel_team_user']}` AS R,
                    `{$this->tbl['wrk_assignment']}` AS A,
                    `{$this->tbl['wrk_submission']}` AS S2,
                    `{$this->tbl['wrk_submission']}` AS S1
                WHERE
                    G.id = R.team
                AND
                    A.assignment_type = 'GROUP'
                AND
                    A.id = S1.assignment_id
                AND
                    S2.parent_id = S1.id
                AND
                    S2.parent_id IS NOT NULL
                AND
                    S2.score >= 0
                ORDER BY
                    S1.user_id, S2.creation_date DESC" ) );
    }
}