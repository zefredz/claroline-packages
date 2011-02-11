<?php // $Id$
/**
 * Online library for Claroline
 *
 * @version     CLLIBR 0.2.2 $Revision$ - Claroline 1.9
 * @copyright   2001-2010 Universite catholique de Louvain (UCL)
 * @license     http://www.gnu.org/copyleft/gpl.html (GPL) GENERAL PUBLIC LICENSE
 * @package     CLLIBR
 * @author      Frederic Fervaille <frederic.fervaille@uclouvain.be>
 */

/**
 *
 */
class Bibliography extends ResourceSet
{
    public function validate()
    {
        $course_tbl = get_module_main_tbl( array( 'cours' ) );
        
        return Claroline::getDatabase()->query( "
            SELECT
                code
            FROM
                `{$course_tbl['cours']}`
            WHERE
                code = " . Claroline::getDatabase()->quote( $this->refId )
        )->numRows();
    }
}