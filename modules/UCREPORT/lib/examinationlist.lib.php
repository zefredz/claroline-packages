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

class ExaminationList extends AssetList
{
    public function __construct()
    {
        $tbl = get_module_course_tbl( array( 'examination_session' ) );
        
        parent::__construct( $tbl[ 'examination_session' ] , 'max_score' );
    }
}