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
        $this->validateTbl = get_module_main_tbl( array( 'library_library' ) );
        
        return $this->database->query( "
            SELECT
                id
            FROM
                `{$this->validateTbl['library_library']}`
            WHERE
                id = " . $this->database->escape( $this->refId )
        )->numRows();
    }
}