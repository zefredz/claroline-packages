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

class ReportList extends AssetList
{
    public function __construct()
    {
        $tbl = get_module_course_tbl( array( 'ICMONIT_report' ) );
        
        parent::__construct( $tbl[ 'ICMONIT_report' ] , 'datas' );
    }
}
