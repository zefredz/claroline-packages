<?php // $Id$

/**
 * Claroline Podcast Reader Parser Collection
 *
 * @version     ICPCRDR 1.0 $Revision$ - Claroline 1.9
 * @copyright   2001-2011 Universite catholique de Louvain (UCL)
 * @license     http://www.gnu.org/copyleft/gpl.html (GPL) GENERAL PUBLIC LICENSE
 * @package     ICPCRDR
 * @author      Frederic Minne <frederic.minne@uclouvain.be>
 */

require_once __DIR__ . '/podcastproperties.lib.php';

class PodcastCollection
{
    protected $tbl;
    
    public function __construct()
    {
        $this->tbl = get_module_course_tbl( array('icpcrdr_podcasts') );
    }
    
    /**
     * Get all the podcast in the collection
     * @return ArrayIterator 
     */
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
    
    /**
     * Update a podcast in the collection
     * @param int $id int
     * @param string $url
     * @param string $title
     * @param string $visible 'visible' or 'invisible'
     * @return boolean 
     */
    public function update( $id, $url, $title, $visible = 'visible', $properties = null )
    {
        $result = Claroline::getDatabase()->exec(
            "UPDATE `{$this->tbl['icpcrdr_podcasts']}`
            SET
                url = " . Claroline::getDatabase()->quote( $url ) . ",
                title = " . Claroline::getDatabase()->quote( $title ) . ",
                visibility = " . Claroline::getDatabase()->quote( $visible == 'visible' ? 'visible' : 'invisible' ) . "
            WHERE
                id = " . Claroline::getDatabase()->escape((int) $id)
        );
            
        $this->saveProperties($id, $properties);

        return $result;
    }
    
    /**
     * Add a podcast to the collection
     * @param string $url
     * @param string $title
     * @param string $visible 'visible' or 'invisible'
     * @return boolean 
     */
    public function add( $url, $title, $visible = 'visible', $properties = null )
    {
        Claroline::getDatabase()->exec(
            "INSERT
            INTO `{$this->tbl['icpcrdr_podcasts']}`
            SET
                url = " . Claroline::getDatabase()->quote( $url ) . ",
                title = " . Claroline::getDatabase()->quote( $title ) . ",
                visibility = " . Claroline::getDatabase()->quote( $visible == 'visible' ? 'visible' : 'invisible' )
        );
        
        $id = Claroline::getDatabase()->insertId();
        
        $this->saveProperties($id, $properties);
        
        return $id;
    }
    
    protected  function saveProperties( $id, $properties = null )
    {
        
        if ( empty ( $properties ) )
        {
            return;
        }
        
        $propertiesObj = new PodcastProperties( $id );
        $propertiesObj->load();
        
        foreach ( $properties as $name => $value )
        {
            $propertyFromDB = $propertiesObj->getProperty ( $name, null );
            
            if ( !$propertyFromDB || $propertyFromDB != $value )
            {
                $propertiesObj->setProperty( $name, $value );
            }
        }
    }


    /**
     * Delete a podcast from the collection
     * @param type $id int
     * @return boolean
     */
    public function delete( $id )
    {
        $properties = new PodcastProperties( $id );
        $properties->unsetAll();
        
        return Claroline::getDatabase()->exec(
            "DELETE FROM `{$this->tbl['icpcrdr_podcasts']}`
            WHERE id = " . Claroline::getDatabase()->escape($id)
        );
    }
    
    /**
     * Get a podcast from the collection
     * @param int $id
     * @return array 
     */
    public function get( $id )
    {
        return Claroline::getDatabase()->query(
            "SELECT id, url, title, visibility
            FROM `{$this->tbl['icpcrdr_podcasts']}`
            WHERE id = " . Claroline::getDatabase()->escape($id)
        )->fetch();
    }
    
    /**
     * Change podcast visibility
     *
     * @author Dimitri Rambout <dimitri.rambout@uclouvain.be>
     * @author Frederic Minne <zefredz@claroline.net>
     * @param int $id
     * @param string $visibility 'visible' or 'invisible'
     * @return boolean
     */
    public function changeVisibility( $id, $visibility )
    {
        return Claroline::getDatabase()->exec( 
            "UPDATE `{$this->tbl['icpcrdr_podcasts']}`
            SET
                visibility = " . Claroline::getDatabase()->quote( $visibility ) . "
            WHERE
                id = " . Claroline::getDatabase()->escape((int) $id)
        );
    }
}
