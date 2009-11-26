<?php // $Id$

/**
 * Claroline Platform Stats
 *
 * @version     CLL10N 1.0-alpha $Revision$ - Claroline 1.9
 * @copyright   2001-2009 Universite catholique de Louvain (UCL)
 * @license     http://www.gnu.org/copyleft/gpl.html (GPL) GENERAL PUBLIC LICENSE
 * @package     CLSTATS
 * @author      Dimitri Rambout <dim@claroline.net>
 */

class ClaroStatsRenderer{
    
    public static function view()
    {
        $tpl = new PhpTemplate( dirname(__FILE__) . '/../templates/index.tpl.php' );
        
        return $tpl->render();
    }
    
    public static function generateStats( $reset )
    {
        $tpl = new PhpTemplate( dirname( __FILE__ ) . '/../templates/generateStats.tpl.php' );
        
        $tpl->assign( 'reset' , $reset ? 1 : 0 );
        
        return $tpl->render();
    }
    
    public static function pendingCourses( $pendingCourses )
    {
        $tpl = new PhpTemplate( dirname( __FILE__ ) . '/../templates/pendingCourses.tpl.php' );
        
        $tpl->assign( 'pendingCourses', $pendingCourses );
        
        return $tpl->render();
    }
}

?>