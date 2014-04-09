<?php // $Id$

/**
 * Claroline Podcast Reader Podcast Properties
 *
 * @version     ICPCRDR 1.0 $Revision$
 * @copyright   2001-2014 Universite catholique de Louvain (UCL)
 * @license     http://www.gnu.org/copyleft/gpl.html (GPL) GENERAL PUBLIC LICENSE
 * @package     ICPCRDR
 * @author      Frederic Minne <frederic.minne@uclouvain.be>
 */

class PodcastProperties
{
    protected 
        $courseId,
        $podcastId,
        $properties;
    
    public function __construct ( $podcastId, $courseId = null )
    {
        $this->courseId = $courseId ? $courseId : claro_get_current_course_id ();
        
        $this->podcastId = $podcastId;
    }
    
    public function load()
    {
        $category = "ICPCRDR.podcast.{$this->podcastId}";
        
        $tbl_cdb_names = get_module_course_tbl( array('course_properties'), $this->courseId );
        $tbl_course_properties = $tbl_cdb_names['course_properties'];
        
        $properties = Claroline::getDatabase()->query(
            "SELECT `name`, `value`
               FROM `" . $tbl_course_properties . "`
              WHERE `category` = " . Claroline::getDatabase()->quote( $category ) );
        
        foreach ( $properties AS $property )
        {
            $this->properties[$property['name']] = $property['value'];
        }
        
        return $this;
    }
    
    public function unsetAll()
    {
        $this->properties = array();
        
        $category = "ICPCRDR.podcast.{$this->podcastId}";
        
        $tbl_cdb_names = get_module_course_tbl( array('course_properties'), $this->courseId );
        $tbl_course_properties = $tbl_cdb_names['course_properties'];
        
        Claroline::getDatabase()->exec(
            "DELETE
               FROM `" . $tbl_course_properties . "`
              WHERE `category` = " . Claroline::getDatabase()->quote( $category ) );
        
        return $this;
    }
    
    protected function saveProperty( $propertyName, $propertyValue )
    {
        $category = "ICPCRDR.podcast.{$this->podcastId}";
        
        $tbl_cdb_names = get_module_course_tbl( array('course_properties'), $this->courseId );
        $tbl_course_properties = $tbl_cdb_names['course_properties'];

        if( Claroline::getDatabase()->query( "SELECT `id`
               FROM `" . $tbl_course_properties . "`
              WHERE `name` = " . Claroline::getDatabase()->quote( $propertyName ) . "
                AND `category` = " . Claroline::getDatabase()->quote( $category ) )->numRows() )
        {
            return Claroline::getDatabase()->exec(
                "UPDATE `" . $tbl_course_properties . "`
                    SET `value` = " . Claroline::getDatabase()->quote( $propertyValue ) . "
                  WHERE `name` = " . Claroline::getDatabase()->quote( $propertyName ) . "
                    AND `category` = " . Claroline::getDatabase()->quote( $category ) );
        }
        else
        {
            return Claroline::getDatabase()->exec(
                "INSERT INTO `" . $tbl_course_properties . "`
                         SET `name` = " . Claroline::getDatabase()->quote( $propertyName ) . ",
                             `value` = " . Claroline::getDatabase()->quote( $propertyValue ) . ",
                             `category` = " . Claroline::getDatabase()->quote( $category ) );
        }
    }
    
    public function setProperty( $name, $value )
    {
        $this->saveProperty( $name, $value );
        
        $this->properties[$name] = $value;
        
        return $this;
    }
    
    public function getProperty( $name, $default = null )
    {
        if ( isset( $this->properties[$name] ) )
        {
            return $this->properties[$name];
        }
        else
        {
            return $default;
        }
    }
    
    public function unsetProperty( $name )
    {
        if ( isset( $this->properties[$name] ) )
        {
            unset( $this->properties[$name] );
        }
        
        return $this;
    }
}
