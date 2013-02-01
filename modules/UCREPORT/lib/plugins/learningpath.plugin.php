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
 * Report plugins for "Learning Path" tool
 * These plugins allow to send datas from Claroline tools to the Report object
 * @const TOOL_NAME
 * @const TOOL_LABEL
 */
class LearningPathPlugin extends ReportPlugin
{
    const TOOL_NAME  = 'Learning path';
    const TOOL_LABEL = 'CLLNP';
    
    /**
     * contructor
     */
    public function __construct()
    {
        $this->toolName  = self::TOOL_NAME;
        $this->toolLabel = self::TOOL_LABEL;
        
        $this->tbl = get_module_course_tbl ( array ( 'lp_learnPath'
                                                   , 'lp_user_module_progress' ) );
    }
    
    /**
     * loads datas needed by Report
     */
    public function load()
    {
        $this->itemQueryResult = Claroline::getDatabase()->query( "
            SELECT
                learnPath_id as id,
                name as title,
                IF(visibility='SHOW','VISIBLE','INVISIBLE') as visibility
            FROM
                `{$this->tbl['lp_learnPath']}`" );
        
        $result = Claroline::getDatabase()->query( "
            SELECT
                user_id,
                learnPath_id AS item_id
            FROM
                `{$this->tbl['lp_user_module_progress']}`" );
        
        FromKernel::uses( 'learnPath.lib.inc' );
        $count = array();
        
        foreach( $result as $line )
        {
            $userId = $line[ 'user_id' ];
            $itemId = $line[ 'item_id' ];
            
            if ( ! isset( $count[ $userId ][ $itemId ] ) )
            {
                $count[ $userId ][ $itemId ] = true;
                $this->dataQueryResult[ 0 ][] = array( 'user_id' => $userId
                                                , 'item_id' => $itemId
                                                , 'score' => get_learnPath_progress( $itemId , $userId ) );
            }
        }
    }
}