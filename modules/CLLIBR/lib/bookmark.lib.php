<?php // $Id$
/**
 * Online library for Claroline
 *
 * @version     CLLIBR 0.2.8 $Revision$ - Claroline 1.9
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
        $this->validateTbl = get_module_main_tbl( array( 'user' ) );
        
        return $this->database->query( "
            SELECT
                user_id
            FROM
                `{$this->validateTbl['user']}`
            WHERE
                user_id = " . $this->database->quote( $this->refId )
        )->numRows();
    }
}