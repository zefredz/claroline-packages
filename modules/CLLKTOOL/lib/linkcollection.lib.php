<?php // $Id$

/**
 * Claroline Advanced Link Tool
 *
 * @version     CLLKTOOL 1.0-alpha $Revision$ - Claroline 1.9
 * @copyright   2001-2009 Universite catholique de Louvain (UCL)
 * @license     http://www.gnu.org/copyleft/gpl.html (GPL) GENERAL PUBLIC LICENSE
 * @package     CLLKTOOL
 * @author      Frederic Minne <frederic.minne@uclouvain.be>
 */

class LinkCollection
{
    protected $tbl;
    
    private function __construct()
    {
        $this->tbl = get_module_course_tbl( array('cllktool_links') );
    }
    
    public function getAll()
    {
        $collection = Claroline::getDatabase()->query(
            "SELECT id, url, title, type, options, visibility
            FROM `{$tbl['cllktool_links']}`"
        );
        
        return $collection;
    }
    
    public function update( $id, $url, $title, $type, $options = array(), $visibile = true )
    {
        return Claroline::getDatabase()->exec(
            "UPDATE `{$tbl['cllktool_links']}`
            SET
                url = " . Claroline::getDatabase()->quote( $url ) . ",
                title = " . Claroline::getDatabase()->quote( $title ) . ",
                type = " . Claroline::getDatabase()->quote( $type ) . ",
                options = " . Claroline::getDatabase()->quote( serialize( $options ) ) . "
                visibility = " . Claroline::getDatabase()->quote( $visibile ? 'true' : 'false' ) . "
            WHERE
                id = " . Claroline::getDatabase()->escape((int) $id)
        );
    }
    
    public function add( $url, $title, $type, $options = array() )
    {
        Claroline::getDatabase()->exec(
            "INSERT
            INTO `{$tbl['cllktool_links']}`
            SET
                url = " . Claroline::getDatabase()->quote( $url ) . ",
                title = " . Claroline::getDatabase()->quote( $title ) . ",
                type = " . Claroline::getDatabase()->quote( $type ) . ",
                options = " . Claroline::getDatabase()->quote( serialize( $options ) ) . "
                visibility = " . Claroline::getDatabase()->quote( $visibile ? 'true' : 'false' )
        );
        
        return Claroline::getDatabase()->insertId();
    }
    
    public function delete( $id )
    {
        return Claroline::getDatabase()->exec(
            "DELETE FROM `{$tbl['cllktool_links']}`
            WHERE id = " . Claroline::getDatabase()->escape($id)
        );
    }
    
    public function get( $id )
    {
        return Claroline::getDatabase()->exec(
            "SELECT id, url, title, type, options, visibility
            FROM `{$tbl['cllktool_links']}`
            WHERE id = " . Claroline::getDatabase()->escape($id)
        );
    }
    
    // Singleton constructor
    
    private static $instance = false;
    
    public static function getInstance()
    {
        if ( !self::$instance )
        {
            self::$instance = new self();
        }
        
        return self::$instance;
    }
}
