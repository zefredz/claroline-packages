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

class PodcastCollection
{
    protected $tbl;
    
    private function __construct()
    {
        $this->tbl = get_module_course_tbl( array('icpcrdr_podcasts') );
    }
    
    public function getAll()
    {
        $collection = Claroline::getDatabase()->query(
            "SELECT id, url, title, visibility
            FROM `{$this->tbl['icpcrdr_podcasts']}`"
        );
        
        if ( ! $collection )
        {
            $collection = new ArrayIterator( array() );
        }
        
        return $collection;
    }
    
    public function update( $id, $url, $title, $visible = 'visible' )
    {
        return Claroline::getDatabase()->exec(
            "UPDATE `{$this->tbl['icpcrdr_podcasts']}`
            SET
                url = " . Claroline::getDatabase()->quote( $url ) . ",
                title = " . Claroline::getDatabase()->quote( $title ) . ",
                visibility = " . Claroline::getDatabase()->quote( $visible == 'visible' ? 'visible' : 'invisible' ) . "
            WHERE
                id = " . Claroline::getDatabase()->escape((int) $id)
        );
    }
    
    public function add( $url, $title, $visible = 'visible' )
    {
        Claroline::getDatabase()->exec(
            "INSERT
            INTO `{$this->tbl['icpcrdr_podcasts']}`
            SET
                url = " . Claroline::getDatabase()->quote( $url ) . ",
                title = " . Claroline::getDatabase()->quote( $title ) . ",
                visibility = " . Claroline::getDatabase()->quote( $visible == 'visible' ? 'visible' : 'invisible' )
        );
        
        return Claroline::getDatabase()->insertId();
    }
    
    public function delete( $id )
    {
        return Claroline::getDatabase()->exec(
            "DELETE FROM `{$this->tbl['icpcrdr_podcasts']}`
            WHERE id = " . Claroline::getDatabase()->escape($id)
        );
    }
    
    public function get( $id )
    {
        return Claroline::getDatabase()->query(
            "SELECT id, url, title, visibility
            FROM `{$this->tbl['icpcrdr_podcasts']}`
            WHERE id = " . Claroline::getDatabase()->escape($id)
        )->fetch();
    }
    
    /**
     * change link visibility
     *
     * @author Dimitri Rambout <dimitri.rambout@uclouvain.be>
     * @param $visibility visibility (visible or invisible)
     * @return boolean
     */
    
    public function changeVisibility( $linkId, $visibility )
    {
        $sql = "UPDATE `{$this->tbl['icpcrdr_podcasts']}`
            SET
                visibility = " . Claroline::getDatabase()->quote( $visibility ) . "
            WHERE
                id = " . Claroline::getDatabase()->escape((int) $linkId);
        
        return Claroline::getDatabase()->exec( $sql );
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
