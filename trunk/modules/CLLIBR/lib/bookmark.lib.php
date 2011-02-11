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
class Bookmark extends ResourceSet
{
    public function validate()
    {
        $user_tbl = get_module_main_tbl( array( 'user' ) );
        
        return Claroline::getDatabase()->query( "
            SELECT
                user_id
            FROM
                `{$user_tbl['user']}`
            WHERE
                user_id = " . Claroline::getDatabase()->quote( $this->refId )
        )->numRows();
    }
}