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
            FROM `{$this->tbl['cllktool_links']}`"
        );
        
        if ( ! $collection )
        {
            $collection = new ArrayIterator( array() );
        }
        
        return $collection;
    }
    
    public function update( $id, $url, $title, $type, $options = array(), $visible = 'visible' )
    {
        return Claroline::getDatabase()->exec(
            "UPDATE `{$this->tbl['cllktool_links']}`
            SET
                url = " . Claroline::getDatabase()->quote( $url ) . ",
                title = " . Claroline::getDatabase()->quote( $title ) . ",
                type = " . Claroline::getDatabase()->quote( $type ) . ",
                options = " . Claroline::getDatabase()->quote( serialize( $options ) ) . ",
                visibility = " . Claroline::getDatabase()->quote( $visible == 'visible' ? 'visible' : 'invisible' ) . "
            WHERE
                id = " . Claroline::getDatabase()->escape((int) $id)
        );
    }
    
    public function add( $url, $title, $type, $options = array(), $visible = 'visible' )
    {
        Claroline::getDatabase()->exec(
            "INSERT
            INTO `{$this->tbl['cllktool_links']}`
            SET
                url = " . Claroline::getDatabase()->quote( $url ) . ",
                title = " . Claroline::getDatabase()->quote( $title ) . ",
                type = " . Claroline::getDatabase()->quote( $type ) . ",
                options = " . Claroline::getDatabase()->quote( serialize( $options ) ) . ",
                visibility = " . Claroline::getDatabase()->quote( $visible == 'visible' ? 'visible' : 'invisible' )
        );
        
        return Claroline::getDatabase()->insertId();
    }
    
    public function delete( $id )
    {
        return Claroline::getDatabase()->exec(
            "DELETE FROM `{$this->tbl['cllktool_links']}`
            WHERE id = " . Claroline::getDatabase()->escape($id)
        );
    }
    
    public function get( $id )
    {
        return Claroline::getDatabase()->query(
            "SELECT id, url, title, type, options, visibility
            FROM `{$this->tbl['cllktool_links']}`
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
        $sql = "UPDATE `{$this->tbl['cllktool_links']}`
            SET
                visibility = " . Claroline::getDatabase()->quote( $visibility ) . "
            WHERE
                id = " . Claroline::getDatabase()->escape((int) $linkId);
        
        return Claroline::getDatabase()->exec( $sql );
    }
    
    
    public function checkOptionExist( $option )
    {
        $internOptionsList = $this->loadOptionsList();
        foreach( $internOptionsList as $options )
        {            
            if( isset( $options[$option] ) )
            {
                return true;
            }
        }
        
        return false;
    }
    
    public function loadOptionsList()
    {
        
        $internOptionsList = array();
        //User informations - claro_get_current_user_data();
        $userInformationLabel = get_lang( 'User informations' );
        $internOptionsList[ $userInformationLabel ][ 'userId' ] = get_lang( 'User id' );
        $internOptionsList[ $userInformationLabel ][ 'userName' ] = get_lang( 'Username' );
        $internOptionsList[ $userInformationLabel ][ 'firstName' ] = get_lang( 'Firstname' );
        $internOptionsList[ $userInformationLabel ][ 'lastName' ] = get_lang( 'Lastname' );
        //$internOptionsList[ $userInformationLabel ][ 'mail' ] = get_lang( 'Mail' );
        //$internOptionsList[ $userInformationLabel ][ 'officialEmail' ] = get_lang( 'Official email' );
        $internOptionsList[ $userInformationLabel ][ 'officialCode' ] = get_lang( 'Official code' );
        $internOptionsList[ $userInformationLabel ][ 'language' ] = get_lang( 'Language' );
        //Course informations
        $courseInformationLabel = get_lang( 'Course informations' );
        $internOptionsList[ $courseInformationLabel ][ 'sysCode' ] = get_lang( 'Course id' );
        $internOptionsList[ $courseInformationLabel ][ 'courseName' ] = get_lang( 'Name' );
        $internOptionsList[ $courseInformationLabel ][ 'courseOfficialCode' ] = get_lang( 'Official code' );
        $internOptionsList[ $courseInformationLabel ][ 'courseLanguage' ] = get_lang( 'Language' );
        $internOptionsList[ $courseInformationLabel ][ 'categoryCode' ] = get_lang( 'Category code' );
        //Plateform informations
        $plateformInformationLabel = get_lang( 'Platform informations ');
        $internOptionsList[ $plateformInformationLabel ][ 'platformId' ] = get_lang( 'Platform id' );
        
        return $internOptionsList;
    }
    
    
    public function loadInternalOptionValue( $option )
    {
        
        if( ! $this->checkOptionExist( $option ) )
        {
            return false;
        }
        
        switch( $option )
        {
            // User informations
            case 'userId' :
                return claro_get_current_user_id();
                break;
            case 'userName' :
                $userProperties = user_get_properties( claro_get_current_user_id() );
                return $userProperties['username'];
                break;
            case 'firstName' :
                $userProperties = user_get_properties( claro_get_current_user_id() );
                return $userProperties['firstname'];
                break;
            case 'lastName' :
                $userProperties = user_get_properties( claro_get_current_user_id() );
                return $userProperties['lastname'];
                break;
            case 'officialCode' :
                $userProperties = user_get_properties( claro_get_current_user_id() );
                return $userProperties['officialCode'];
                break;
            case 'language' :
                $userProperties = user_get_properties( claro_get_current_user_id() );
                return $userProperties['language'];
                break;
            // Course informations
            case 'sysCode' :
                $courseData = claro_get_current_course_data();
                return $courseData['sysCode'];
                break;
            case 'courseName' :
                $courseData = claro_get_current_course_data();
                return $courseData['name'];
                break;
            case 'courseOfficialCode' :
                $courseData = claro_get_current_course_data();
                return $courseData['officialCode'];
                break;
            case 'courseLanguage' :
                $courseData = claro_get_current_course_data();
                return $courseData['language'];
                break;
            case 'categoryCode' :
                $courseData = claro_get_current_course_data();
                return $courseData['categoryCode'];
                break;
            // Plateform informations
            case 'platformId' :
                return get_conf('platform_id');
                break;
        }
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
