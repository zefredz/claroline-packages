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
class Catalogue extends ResourceSet
{
    /**
     *
     */
    public function validate()
    {
        $library_tbl = get_module_main_tbl( array( 'library_library' ) );
        
        return Claroline::getDatabase()->query( "
            SELECT
                id
            FROM
                `{$library_tbl['library_library']}`
            WHERE
                id = " . Claroline::getDatabase()->escape( $this->refId )
        )->numRows();
    }
}