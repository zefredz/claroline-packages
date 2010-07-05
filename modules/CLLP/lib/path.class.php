<?php // $Id$
if ( count( get_included_files() ) == 1 ) die( '---' );
/**
 * CLAROLINE
 *
 * $Revision$
 *
 * @copyright (c) 2001-2007 Universite catholique de Louvain (UCL)
 *
 * @license http://www.gnu.org/copyleft/gpl.html (GPL) GENERAL PUBLIC LICENSE
 *
 * @package CLLP
 *
 * @author Sebastien Piraux
 *
 */

class path
{
    /**
     * @var $id id of path, -1 if path doesn't exist already
     */
    private $id;

    /**
     * @var $title name of the path
     */
    private $title;

    /**
     * @var $description statement of the path
     */
    private $description;

    /**
     * @var $visibility visibility of the path (default is invisible)
     */
    private $visibility;

    /**
     * @var $rank order of the path in the path list
     */
    private $rank;

    /**
     * @var $version
     */
    private $version;

    /**
     * @var $lock
     */
    private $lock;

    /**
     * @var $identifier SCORM manifest ressource identifier
     */
    private $identifier;

    /**
     * @var $allowReinit allow to start path items again (default is false)
     */
    private $allowReinit;

    /**
     * @var $viewMode embedded or in full screen (default is embedded)
     */
    private $viewMode;

    /**
     * @var $encoding encoding of the path (default is utf-8)
     */
    private $encoding;

    /**
     * @var $tblPath
     */
    private $tblPath;

    const VERSION_12 = 'scorm12';
    const VERSION_13 = 'scorm13';

    /**
     * Constructor
     *
     * @author Sebastien Piraux <pir@cerdecam.be>
     */
    public function __construct()
    {
        $this->id = (int) -1;
        $this->title = '';
        $this->description = '';
        $this->visibility = 'INVISIBLE';
        $this->rank = 0;
        $this->version = self::VERSION_13;
        $this->lock = 'OPEN';
        $this->identifier = '';
        $this->allowReinit = false;
        $this->viewMode = 'EMBEDDED'; // or 'FULLSCREEN'
        $this->encoding = 'UTF-8'; // or 'ISO-8859-1', ...

        // define module table names
        $tblNameList = array(
            'lp_path',
            'lp_item',
            'lp_attempt',
            'lp_item_attempt'
        );

        // convert to Claroline course table names
        $tbl_lp_names = get_module_course_tbl( $tblNameList, claro_get_current_course_id() );
        $this->tblPath = $tbl_lp_names['lp_path'];
    }

    /**
     * load a path from DB
     *
     * @author Sebastien Piraux <pir@cerdecam.be>
     * @param integer $id id of path
     * @return boolean load successfull ?
     */
    public function load($id)
    {
        $sql = "SELECT
                    `id`,
                    `title`,
                    `description`,
                    `visibility`,
                    `rank`,
                    `version`,
                    `lock`,
                    `identifier`,
                    `allow_reinit`,
                    `view_mode`,
                    `encoding`
            FROM `".$this->tblPath."`
            WHERE `id` = ".(int) $id;

        $data = claro_sql_query_get_single_row($sql);

        if( !empty($data) )
        {
            // from query
            $this->id = (int) $data['id'];
            $this->title = $data['title'];
            $this->description = $data['description'];
            $this->visibility = $data['visibility'];
            $this->rank = (int) $data['rank'];
            $this->version = $data['version'];
            $this->lock = $data['lock'];
            $this->identifier = $data['identifier'];
            $this->allowReinit = $data['allow_reinit'];
            $this->viewMode = $data['view_mode'];
            $this->encoding = $data['encoding'];

            return true;
        }
        else
        {
            return false;
        }
    }

    /**
     * save path to DB
     *
     * @author Sebastien Piraux <pir@cerdecam.be>
     * @return mixed false or id of the record
     */
    public function save()
    {
        if( $this->id == -1 )
        {
            // set correct value for rank on creation
            $this->rank = $this->getHigherRank() + 1 ;

            // insert
            $sql = "INSERT INTO `".$this->tblPath."`
                    SET `title` = '".addslashes($this->title)."',
                        `description` = '".addslashes($this->description)."',
                        `visibility` = '".addslashes($this->visibility)."',
                        `rank` = '".(int) $this->rank."',
                        `version` = '".addslashes($this->version)."',
                        `lock` = '".addslashes($this->lock)."',
                        `identifier` = '".addslashes($this->identifier)."',
                        `allow_reinit` = ".(int) $this->allowReinit.",
                        `view_mode` = '".addslashes($this->viewMode)."',
                        `encoding` = '".addslashes($this->encoding)."'";

            // execute the creation query and get id of inserted assignment
            $insertedId = claro_sql_query_insert_id($sql);

            if( $insertedId )
            {
                $this->id = (int) $insertedId;
                
                return $this->id;
            }
            else
            {
                return false;
            }
        }
        else
        {
            // update, main query
            $sql = "UPDATE `".$this->tblPath."`
                    SET `title` = '".addslashes($this->title)."',
                        `description` = '".addslashes($this->description)."',
    					`visibility` = '".addslashes($this->visibility)."',
                        `rank` = '".(int) $this->rank."',
                        `version` = '".addslashes($this->version)."',
                        `lock` = '".addslashes($this->lock)."',
                        `identifier` = '".addslashes($this->identifier)."',
                        `allow_reinit` = ".(int) $this->allowReinit.",
                        `view_mode` = '".addslashes($this->viewMode)."',
                        `encoding` = '".addslashes($this->encoding)."'
                    WHERE `id` = '".$this->id."'";

            // execute and return main query
            if( claro_sql_query($sql) )
            {
                return $this->id;
            }
            else
            {
                return false;
            }
        }
    }

    /**
     * delete path
     *
     * @author Sebastien Piraux <pir@cerdecam.be>
     * @return boolean
     */
    public function delete()
    {
        if( $this->id == -1 ) return true;

        // delete all items related to this path
        $itemList = new PathItemList($this->id);
        $thisPathItemList = $itemList->load();

        if( !empty($thisPathItemList) )
        {
            foreach( $thisPathItemList as $item )
            {
                $itemObj = new item();
                $itemObj->load($item['id']);

                $itemObj->delete();
            }

        }

        // delete the path
        $sql = "DELETE FROM `" . $this->tblPath . "`
                WHERE `id` = " . (int) $this->id ;

        if( claro_sql_query($sql) == false ) return false;

        // delete path repository
        claro_delete_file(get_path('coursesRepositorySys') . claro_get_course_path() . '/scormPackages/path_' . $this->id );
        
        $this->id = -1;
        return true;
    }

    /**
     * check if data are valide
     *
     * @author Sebastien Piraux <pir@cerdecam.be>
     * @return boolean
     */
    public function validate()
    {
        // title is a mandatory element
        $title = strip_tags($this->title);

        if( empty($title) )
        {
            claro_failure::set_failure('path_no_title');
            return false;
        }

        return true; // no errors, form is valide
    }
    
    /**
     * Clear LearningPath progression (if both param are null, the entire progression for every users will be cleared)
     *
     * @author Dimitri Rambout <dim@claroline.net>
     *
     * @param int $userId Id of the user (can be null)
     * @param int $itemId Id of the item (can be null)
     * 
     * @return boolean
     */
    
    public function clearProgression( $userId = null, $itemId = null )
    {
        if( $this->id == -1 )
        {
            return false;
        }
        // define module table names
        $tblNameList = array(
            'lp_attempt',
            'lp_item_attempt'
        );

        // convert to Claroline course table names
        $tbl_lp_names = get_module_course_tbl( $tblNameList, claro_get_current_course_id() );
        
        $query = "SELECT `id` FROM `" . $tbl_lp_names['lp_attempt'] . "` WHERE `path_id` = '" . $this->id . "'";
        if( ! is_null( $userId ) )
        {
            $query .= " AND `user_id` = " . (int) $userId;
        }
        
        $result = Claroline::getDatabase()->query( $query );
        
        $attemptIds = array();
        
        while( $attemptId = $result->fetch() )
        {
            $attemptIds[] = $attemptId;
        }
        
        foreach( $attemptIds as $attemptId )
        {
            if( is_null( $itemId ) )
            {
                $query = "DELETE FROM `" . $tbl_lp_names['lp_attempt'] . "` WHERE `id` = " . (int) $attemptId['id'];
                Claroline::getDatabase()->exec( $query );
            }
            
            $query = "DELETE FROM `" . $tbl_lp_names['lp_item_attempt'] . "` WHERE `attempt_id` = " . (int) $attemptId['id'];
            if( ! is_null( $itemId ) )
            {
                $query .= " AND `item_id` = " . (int) $itemId;
            }
            Claroline::getDatabase()->exec( $query );
        }
        
        return true;    
    }

    //-- Getter & Setter

    /**
     * get id
     *
     * @author Sebastien Piraux <pir@cerdecam.be>
     * @return int
     */
    public function getId()
    {
        return (int) $this->id;
    }

    /**
     * get title
     *
     * @author Sebastien Piraux <pir@cerdecam.be>
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * set title
     *
     * @author Sebastien Piraux <pir@cerdecam.be>
     * @param string $value
     */
    public function setTitle($value)
    {
        $this->title = trim($value);
    }

    /**
     * get description
     *
     * @author Sebastien Piraux <pir@cerdecam.be>
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * set description
     *
     * @author Sebastien Piraux <pir@cerdecam.be>
     * @param string $value
     */
    public function setDescription($value)
    {
        $this->description = trim($value);
    }

    /**
     * set visible
     *
     * @author Sebastien Piraux <pir@cerdecam.be>
     */
    public function setVisible()
    {
        $this->visibility = 'VISIBLE';
    }

    /**
     * set invisible
     *
     * @author Sebastien Piraux <pir@cerdecam.be>
     */
    public function setInvisible()
    {
        $this->visibility = 'INVISIBLE';
    }

    /**
     * is the path visible
     *
     * @author Sebastien Piraux <pir@cerdecam.be>
     * @return boolean
     */
    public function isVisible()
    {
        if( $this->visibility == 'VISIBLE' )    return true;
        else                                    return false;
    }

    /**
     * is the path invisible
     *
     * @author Sebastien Piraux <pir@cerdecam.be>
     * @return boolean
     */
    public function isInvisible()
    {
        return !$this->isVisible();
    }

    /**
     * get rank
     *
     * @author Sebastien Piraux <pir@cerdecam.be>
     * @return string
     */
    public function getRank()
    {
        return (int) $this->rank;
    }

    /**
     * set rank
     *
     * @author Sebastien Piraux <pir@cerdecam.be>
     * @param string $value
     */
    public function setRank($value)
    {
        $this->rank = trim($value);
    }

    /**
     * get version
     *
     * @author Sebastien Piraux <pir@cerdecam.be>
     * @return string
     */
    public function getVersion()
    {
        return $this->version;
    }

    /**
     * set version
     *
     * @author Sebastien Piraux <pir@cerdecam.be>
     * @param string $value
     */
    public function setVersion($value)
    {
        $acceptedValues = array(self::VERSION_12, self::VERSION_13);

        if( in_array($value, $acceptedValues) )
        {
            $this->version = $value;
            return true;
        }
        return false;
    }

    /**
     * get lock
     *
     * @author Sebastien Piraux <pir@cerdecam.be>
     * @return string
     */
    public function getLock()
    {
        return $this->lock;
    }

    /**
     * set lock
     *
     * @author Sebastien Piraux <pir@cerdecam.be>
     * @param string $value
     */
    public function setLock($value)
    {
        $this->lock = trim($value);
    }
    /**
     * set lock
     *
     * @author Sebastien Piraux <pir@cerdecam.be>
     */
    public function lock()
    {
        $this->lock = 'CLOSE';
    }

    /**
     * set unlock
     *
     * @author Sebastien Piraux <pir@cerdecam.be>
     */
    public function unlock()
    {
        $this->lock = 'OPEN';
    }

    /**
     * is the path locked ?
     *
     * @author Sebastien Piraux <pir@cerdecam.be>
     * @return boolean
     */
    public function isLocked()
    {
        if( $this->lock == 'CLOSE' )    return true;
        else                            return false;
    }

    /**
     * is the path unlocked ?
     *
     * @author Sebastien Piraux <pir@cerdecam.be>
     * @return boolean
     */
    public function isUnlocked()
    {
        return !$this->isLocked();
    }

    /**
     * set viewMode
     *
     * @author Sebastien Piraux <pir@cerdecam.be>
     */
    public function setViewMode($value)
    {
        $acceptedValues = array('FULLSCREEN', 'EMBEDDED');

        if( in_array($value, $acceptedValues) )
        {
            $this->viewMode = $value;
            return true;
        }
        return false;
    }

    /**
     * get viewMode
     *
     * @author Sebastien Piraux <pir@cerdecam.be>
     */
    public function getViewMode()
    {
        return $this->viewMode;
    }

    /**
     * show the path fullscreen ?
     *
     * @author Sebastien Piraux <pir@cerdecam.be>
     * @return boolean
     */
    public function isFullscreen()
    {
        if( $this->viewMode == 'FULLSCREEN' )    return true;
        else                                     return false; // EMBEDDED
    }

    /**
     * get the higher rank of available learning path
     *
     * @author Sebastien Piraux <pir@cerdecam.be>
     * @return int higher path rank
     */
    public function getHigherRank()
    {
        $sql = "SELECT max(`rank`)
                FROM `" . $this->tblPath . "`";

        $rankMax = claro_sql_query_get_single_value($sql);

        if( !is_null($rankMax) ) return (int) $rankMax;
        else                     return 0;
    }
}


/**
 * path scorm export enable to export a learning path to a zip archive in SCORM 2004 formart
 *
 * @author Dimitri Rambout <dim@claroline.net>
 *
 **/

class PathScormExport
{
    /**
     * @var $path object a valid learning path
     */     
    private $path;
    /**
     * @var $pathItemList object the list of items from the $path
     */     
    private $pathItemList;
    /**
     * @var $destDir string path when the files are saved before zip
     */
    private $destDir;
    /**
     * @var $srcDirDocument string path of the old scorm content
     */
    private $srcDirScorm;
    /**
     * @var $fromScorm boolean true = need to copy old scorm content
     */
    private $fromScorm;
    /**
     * @var $error string error during export
     */
    private $error;
    
    /**
     * Constructor
     *
     * @author Dimitri Rambout <dim@claroline.net>
     */
    public function __construct( &$path )
    {
        $this->path = $path;
        $this->fromScorm = false;
    }
    
    /**
     * Export the content of a learning path
     *
     * @author Dimitri Rambout <dim@claroline.net>
     * @return boolean
     */
    public function export()
    {
        if( ! $this->fetch() ) return false;
        if( ! $this->prepare() ) return false;
        if( ! $this->createManifest() ) return false;
        if( ! $this->zip() ) return false;
        if( ! $this->send() ) return false;
        
        return true;
    }
    /**
     * Fetch - load path item list and set directories names
     *
     * @author Dimtiri Rambout <dim@claroline.net>
     * @return boolean
     */
    private function fetch()
    {
        $this->pathItemList = new PathItemList( $this->path->getId());
        
        if( ! $this->pathItemList->load())
        {
            $this->error = get_lang('Learning Path is empty');
            return false;
        }
        
        /* Build various directories' names */

        // Replace ',' too, because pclzip doesn't support it.
        $this->destDir = get_path('coursesRepositorySys') . claro_get_course_path() . '/temp/'
            . str_replace(',', '_', replace_dangerous_char($this->path->getTitle()));
        $this->srcDirScorm    = get_path('coursesRepositorySys') . claro_get_course_path() . '/scormPackages/path_'.$this->path->getId();
        
        return true;
    }
    
    /**
     * Prepare - copy files (statics & from modules) needed to export the LP
     *
     * @author Dimitri Rambout <dim@claroline.net>
     * @return boolean
     */
    private function prepare()
    {
        // (re)create fresh directory
        claro_delete_file($this->destDir);
        if ( !claro_mkdir($this->destDir, CLARO_FILE_PERMISSIONS , true) )
        {
            $this->error = get_lang('Unable to create directory : ') . $this->destDir;
            return false;
        }
        // JS
        if ( !claro_mkdir($this->destDir.'/js', CLARO_FILE_PERMISSIONS , true) )
        {
            $this->error = get_lang('Unable to create directory : ') . $this->destDir . '/js';
            return false;
        }
        //CSS
        /*if ( !claro_mkdir($this->destDir.'/' . get_conf('claro_stylesheet'), CLARO_FILE_PERMISSIONS , true) )
        {
            $this->error = get_lang('Unable to create directory : ') . $this->destDir . '/' . get_conf('claro_stylesheet');
            return false;
        }*/
        
        // Copy usual files (.css, .js, .xsd, etc)
        // Check css to use
        if( file_exists( get_path( 'clarolineRepositorySys' ) . '../platform/css/' . get_conf('claro_stylesheet') ) )
        {
            $claro_stylesheet_path = get_path( 'clarolineRepositorySys' ) . '../platform/css/' . get_conf('claro_stylesheet');
        }
        elseif( file_exists( get_path( 'clarolineRepositorySys' ) . '../web/css/' . get_conf('claro_stylesheet') ) )
        {
            $claro_stylesheet_path = get_path( 'clarolineRepositorySys' ) . '../web/css/' . get_conf('claro_stylesheet');
        }
        else
        {
            return false;
        }
        
        if( !claro_copy_file( $claro_stylesheet_path, $this->destDir . '/' ) )
        {
            $this->error = get_lang('Error when copying needed SCORM files') . ' ' . get_lang( '%fileName', array( '%fileName' => 'main.css' ) );
            return false;
        }
        
        if( !claro_copy_file( get_module_path('CLLP') . '/export/APIWrapper.js', $this->destDir.'/js') )
        {
            $this->error = get_lang('Error when copying needed SCORM files') . ' ' . get_lang( '%fileName', array( '%fileName' => 'APIWrapper.js' ) );
            return false;
        }
        
        if( !claro_copy_file( get_module_path('CLLP') . '/export/scores.js', $this->destDir.'/js') )
        {
            $this->error = get_lang('Error when copying needed SCORM files') . ' ' . get_lang( '%fileName', array( '%fileName' => 'scores.js' ) );
            return false;
        }
        
        if( !claro_copy_file( get_module_path('CLLP') . '/export/ims_xml.xsd', $this->destDir) )
        {
            $this->error = get_lang('Error when copying needed SCORM files') . ' ' . get_lang( '%fileName', array( '%fileName' => 'ims_xml.xsd' ) );
            return false;
        }
        
        if( !claro_copy_file( get_module_path('CLLP') . '/export/imscp_rootv1p1p2.xsd', $this->destDir) )
        {
            $this->error = get_lang('Error when copying needed SCORM files') . ' ' . get_lang( '%fileName', array( '%fileName' => 'imscp_rootv1p1p2.xsd' ) );
            return false;
        }
        
        if( !claro_copy_file( get_module_path('CLLP') . '/export/imsmd_rootv1p2p1.xsd', $this->destDir) )
        {
            $this->error = get_lang('Error when copying needed SCORM files') . ' ' . get_lang( '%fileName', array( '%fileName' => 'imsmd_rootv1p2p1.xsd' ) );
            return false;
        }
        
        if( !claro_copy_file( get_module_path('CLLP') . '/export/adlcp_rootv1p2.xsd', $this->destDir) )
        {
            $this->error = get_lang('Error when copying needed SCORM files') . ' ' . get_lang( '%fileName', array( '%fileName' => 'adlcp_rootv1p2.xsd' ) );
            return false;
        }
        
        if( !claro_copy_file( get_path('clarolineRepositorySys') . '/../web/js/jquery.js', $this->destDir.'/js') )
        {
            $this->error = get_lang('Error when copying needed SCORM files') . ' ' . get_lang( '%fileName', array( '%fileName' => 'jquery.js' ) );
            return false;
        }
        
        if( !claro_copy_file( get_path('clarolineRepositorySys') . '/../web/js/claroline.js', $this->destDir.'/js') )
        {
            $this->error = get_lang('Error when copying needed SCORM files') . ' ' . get_lang( '%fileName', array( '%fileName' => 'claroline.js' ) );
            return false;
        }
        
        if( !claro_copy_file( get_path('clarolineRepositorySys') . '/../web/js/claroline.ui.js', $this->destDir.'/js') )
        {
            $this->error = get_lang('Error when copying needed SCORM files') . ' ' . get_lang( '%fileName', array( '%fileName' => 'claroline.ui.js' ) );
            return false;
        }
        
        if( !claro_copy_file( get_module_path('CLLP') . '/js/scormtime.js', $this->destDir.'/js') )
        {
            $this->error = get_lang('Error when copying needed SCORM files') . ' ' . get_lang( '%fileName', array( '%fileName' => 'scormtome.js' ) );
            return false;
        }
        
        if( !claro_copy_file( get_module_path('CLLP') . '/js/connector13.js', $this->destDir.'/js') )
        {
            $this->error = get_lang('Error when copying needed SCORM files') . ' ' . get_lang( '%fileName', array( '%fileName' => 'connector13.js' ) );
            return false;
        }
        
        if( !claro_copy_file( get_module_path('CLDOC') . '/js/cllp.cnr.js', $this->destDir.'/js') )
        {
            $this->error = get_lang('Error when copying needed SCORM files') . ' ' . get_lang( '%fileName', array( '%fileName' => 'cllp.cnr.js' ) );
            return false;
        }
        
        /*if (
               !claro_copy_file( get_path('clarolineRepositorySys') . '/../web/css/'.get_conf('claro_stylesheet').'/main.css', $this->destDir . '/' . get_conf('claro_stylesheet'))
            || !claro_copy_file( get_path('clarolineRepositorySys') . '/../web/css/'.get_conf('claro_stylesheet').'/rtl.css', $this->destDir. '/' . get_conf('claro_stylesheet'))
            || !claro_copy_file( get_module_path('CLLP') . '/export/APIWrapper.js', $this->destDir.'/js')
            || !claro_copy_file( get_module_path('CLLP') . '/export/scores.js', $this->destDir.'/js')
            || !claro_copy_file( get_module_path('CLLP') . '/export/ims_xml.xsd', $this->destDir)
            || !claro_copy_file( get_module_path('CLLP') . '/export/imscp_rootv1p1p2.xsd', $this->destDir)
            || !claro_copy_file( get_module_path('CLLP') . '/export/imsmd_rootv1p2p1.xsd', $this->destDir)
            || !claro_copy_file( get_module_path('CLLP') . '/export/adlcp_rootv1p2.xsd', $this->destDir)
            || !claro_copy_file( get_path('clarolineRepositorySys') . '/../web/js/jquery.js', $this->destDir.'/js')
            || !claro_copy_file( get_path('clarolineRepositorySys') . '/../web/js/claroline.js', $this->destDir.'/js')
            || !claro_copy_file( get_path('clarolineRepositorySys') . '/../web/js/claroline.ui.js', $this->destDir.'/js')
            || !claro_copy_file( get_module_path('CLLP') . '/js/connector13.js', $this->destDir.'/js')
            || !claro_copy_file( get_module_path('CLLP') . '/js/scormtime.js', $this->destDir.'/js')
            || !claro_copy_file( get_module_path('CLDOC') . '/js/cllp.cnr.js', $this->destDir.'/js')
           )
        {
            $this->error = get_lang('Error when copying needed SCORM files');
            return false;
        }*/
        
        if (! $this->createProgressFrame( $this->destDir ) )
        {
            return false;
        }
        
        // Create direcotries & files structure
        $itemTree = $this->pathItemList->getItemTree();
        foreach( $itemTree as $item )
        {
            if( ! $this->prepareItem( $item, $this->destDir ) )
            {
                return false;
            }
        }
        
        if( $this->fromScorm )
        {
            // Copy the scorm directory as OrigScorm/
            if (
                   !claro_copy_file($this->srcDirScorm,  $this->destDir)
                || !claro_rename_file($this->destDir.'/path_'.$this->path->getId(), $this->destDir.'/OrigScorm')  )
            {
                $this->error = get_lang('Error copying existing SCORM content');
                return false;
            }
            
            // Remove imsmanifest.xml from OrigScorm
            $directory = new RecursiveIteratorIterator(new RecursiveDirectoryIterator( $this->destDir.'/OrigScorm' ), RecursiveIteratorIterator::SELF_FIRST);
            foreach($directory as $file => $fileInfo)
            {
                if( strpos( strtolower($file), 'imsmanifest.xml') !== false  )
                {
                    claro_delete_file( $file );
                }
            }
            
        }
        
        return true;
        
    }
    
    /**
     * Create a progress frame needed for Documents & co
     *
     * @author Dimitri Rambout <dim@claroline.net>
     * @param string $destDir Destination directory when the frame is saved
     * @return boolean
     */
    private function createProgressFrame( $destDir )
    {
        $pageContent = '<html>
        <head>
        <title></title>
        <meta http-equiv="expires" content="Tue, 05 DEC 2000 07:00:00 GMT">
        <meta http-equiv="Pragma" content="no-cache">
        <meta http-equiv="Content-Type" content="text/HTML; charset='.get_locale('charset').'"  />
    
        <link rel="stylesheet" type="text/css" href="' . get_conf('claro_stylesheet') . '/main.css" media="screen, projection, tv" />
        <script language="javascript" type="text/javascript" src="js/jquery.js"></script>
        <script language="javascript" type="text/javascript" src="js/claroline.js"></script>
        <script language="javascript" type="text/javascript" src="js/claroline.ui.js"></script>
        <script src="js/connector13.js" type="text/javascript"></script> 
        <script src="js/scormtime.js" type="text/javascript"></script> 
        <script src="js/cllp.cnr.js" type="text/javascript"></script>
        </head>
        <body>
        <div>
        <form method="get" action="#" id="progressForm">'
        .    get_lang('Progress') . ' : ' . "\n"
        .    '<input type="radio" name="progress" id="none" class="progressRadio" value="0" checked="checked" />' . "\n"
        .    '<label for="none">0%</label>' . "\n"
        
        .    '<input type="radio" name="progress" id="low" class="progressRadio" value="25" />' . "\n"
        .    '<label for="low">25%</label>' . "\n"
        
        .    '<input type="radio" name="progress" id="medium" class="progressRadio" value="50" />' . "\n"
        .    '<label for="medium">50%</label>' . "\n"
        
        .    '<input type="radio" name="progress" id="high" class="progressRadio" value="75" />' . "\n"
        .    '<label for="high">75%</label>' . "\n"
        
        .    '<input type="radio" name="progress" id="full" class="progressRadio" value="100" />' . "\n"
        .    '<label for="full">100%</label>' . "\n"
        
        //.    '<input type="button" value="'.get_lang('Done').'" id="progressDone" />' . "\n"
        .    '</form>
        </div>
        </body>
        </html>
        ';
        
        if (! $f = fopen($destDir . '/progressFrame.html', 'w') )
        {
            $this->error = get_lang('Error when creating progress frame.');            
            return false;
        }
        fwrite($f, $pageContent);
        fclose($f);
        
        return true;
        
    }
    
    /**
     * Prepare item for the zip, copy directories and files in the destination directory
     *
     * @author Dimitri Rambout <dim@claroline.net>
     * @param array $item item information from a path
     * @param string $destDir Destination directory when files are copied
     * @param int $deepness Deepness of the directory
     * @return boolean
     */
    private function prepareItem ( &$item, $destDir, $deepness = 0 )
    {
        $thisItem = new item();
        if( ! $thisItem->load( $item['id'] ) )
        {
            $this->error = get_lang('Unable to load item %item', array( '%item' => $item['title'] ));
            return false;
        }
        
        switch( $item['type'])
        {
            // Create the directory in $destDir and all prepareItem if children exist for the item
            case 'CONTAINER' :
            {
                $destDir.= '/' . str_replace(',','_',replace_dangerous_char($item['title']));
                if( ! claro_mkdir( $destDir,CLARO_FILE_PERMISSIONS, true ) )
                {
                    $this->error = get_lang('Unable to create directory %path in temporary directory.', $destDir);
                    return false;
                }
                if( isset($item['children']) && !empty( $item['children'] ) )
                {
                    $deepness++;
                    foreach( $item['children'] as $itemChild )
                    {
                        if( ! $return = $this->prepareItem( $itemChild, $destDir, $deepness) )
                        {
                            return false;
                        }   
                    }                    
                }                
            }
            break;
            // Set $fromScorm to true (used after prepareItem to copy old Scorm Content in a specific directory )
            case 'SCORM' :
            {
                $this->fromScorm = true;
            }
            break;
            // Call the connector from the concerned module (cllp.scormexport.cnr.php) for copy concerned files
            case 'MODULE' :
            {
                $locator = ClarolineResourceLocator::parse($item['sys_path']);
                
                $connectorPath = get_module_path( $locator->getModuleLabel() ) . '/connector/cllp.scormexport.cnr.php';
                if( file_exists( $connectorPath ) )
                {
                    include_once( $connectorPath );
                    $class = $locator->getModuleLabel().'_ScormExport';
                    $connector = new $class();
                    if( ! $connector->prepareFiles($locator->getResourceId(), $thisItem, $destDir, $deepness) )
                    {
                       $this->error = $connector->getError();
                       return false;
                    }
                    
                }                
            }
            break;
        }
        return true;
    }
    
    /**
     * Create a frame file to include item from the module
     *
     * @author Dimitri Rambout <dim@claroline.net>
     * @param string $frameName name used to create the file
     * @param string $docId title of the frame
     * @param object $item item of a path
     * @param string $destDir directory when the file is saved
     * @param int $deepness deepness of the $destDir
     *
     * @return boolean
     */
    protected function createFrameFile( $frameName, $docId, $item, $destDir, $deepness )
    {
        $deep = '';
        if( $deepness )
        {
            for($i = $deepness; $i > 0; $i--)
            {
                $deep .=' ../';
            }
        }        
        
        $subFrameName = 'sub_' . $frameName;
        
        
        // Generate standard page header
        $pageHeader = '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
        <html>
        <head>
        <title>' . $docId . '</title>
        <meta http-equiv="expires" content="Tue, 05 DEC 2000 07:00:00 GMT">
        <meta http-equiv="Pragma" content="no-cache">
        <meta http-equiv="Content-Type" content="text/HTML; charset='.get_locale('charset').'"  />
    
        <link rel="stylesheet" type="text/css" href="' . $deep . get_conf('claro_stylesheet') . '/main.css" media="screen, projection, tv" />
        <script language="javascript" type="text/javascript" src="' . $deep . 'js/jquery.js"></script>
        <script language="javascript" type="text/javascript" src="' . $deep . 'js/claroline.js"></script>
        <script language="javascript" type="text/javascript" src="' . $deep . 'js/claroline.ui.js"></script>
    
        <script language="javascript" type="text/javascript" src="' . $deep . 'js/APIWrapper.js"></script>
        <script language="javascript" type="text/javascript" src="' . $deep . 'js/scores.js"></script>
        </head>
        ' . "\n";
        
        $pageBody = '
        <frameset rows="*,50">
            <frame src="' . $subFrameName . '"/>
            <frame src="' . $deep . 'progressFrame.html" /> 
        </frameset>';
        
        $subPageBody = '
        <div id="description">' . claro_parse_user_text($item->getDescription()) . '</div>
        <iframe src ="' . $docId . '" width="100%" height="100%">
            <p>Your browser does not support iframes.</p>
        </iframe>
        ';
        
        $pageFooter = '</html>' . "\n";
        
        // Create sub Frame
        $subPageContent = $pageHeader . $subPageBody . $pageFooter;
        if (! $f = fopen($destDir . '/' . $subFrameName, 'w') )
        {
            $this->error = get_lang('Unable to create file : ') . $subFrameName;
            return false;
        }
        fwrite($f, $subPageContent);
        fclose($f);
        unset($f);
        //Create Fame
        $pageContent = $pageHeader . $pageBody . $pageFooter;
        if (! $f = fopen($destDir . '/' . $frameName, 'w') )
        {
            $this->error = get_lang('Unable to create file : ') . $frameName;
            return false;
        }
        fwrite($f, $pageContent);
        fclose($f);
        
        return true;
    }
    
    /**
     * Create the manifest needed to use the export
     *
     * @author Dimitri Rambout <dim@claroline.net>
     * @return boolean
     */
    private function createManifest()
    {
        $itemTree = $this->pathItemList->getItemTree();
        
        $manifest = '<?xml version="1.0" encoding="' . get_locale('charset') . '" ?>
        <manifest identifier="SingleCourseManifest" version="1.1"
                xmlns="http://www.imsproject.org/xsd/imscp_rootv1p1p2"
                xmlns:adlcp="http://www.adlnet.org/xsd/adlcp_rootv1p2"
                xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
                xmlns:imsmd="http://www.imsglobal.org/xsd/imsmd_rootv1p2p1"
                xsi:schemaLocation="http://www.imsproject.org/xsd/imscp_rootv1p1p2 imscp_rootv1p1p2.xsd
                http://www.imsglobal.org/xsd/imsmd_rootv1p2p1 imsmd_rootv1p2p1.xsd
                http://www.adlnet.org/xsd/adlcp_rootv1p2 adlcp_rootv1p2.xsd">
                ';
        $metaData = '<metadata>
            <schema>ADL SCORM</schema>
            <schemaversion>2004 3rd Edition</schemaversion>
            <title><![CDATA['. htmlspecialchars( $this->path->getTitle() ) .']]></title>
            <description><![CDATA['. htmlspecialchars( $this->path->getDescription() ) .']]></description>
        </metadata>
        ';
        
        $organizations = '<organizations default="A1">
            <organization identifier="A1">
                <title>' . htmlspecialchars( $this->path->getTitle() ) . '</title>
                <description>' . htmlspecialchars( $this->path->getDescription() ) . '</description>
                ';
        $resources = '<resources>
        ';
        foreach( $itemTree as $item )
        {
            $organizations .= $this->createManifestItems( $item );
            $resources .= $this->createManifestResources( $item, '.');
        }
        $organizations .= '</organization>
        </organizations>
        ';
        $resources .= '</resources>';
        
        $manifest .= $metaData
        .   $organizations
        .   $resources
        .   '</manifest>';
        
        $manifestPath = $this->destDir . '/imsmanifest.xml';
        if (! $f = fopen($manifestPath, 'w') )
        {
            $this->error = get_lang('Unable to create manifest');
            return false;
        }
        fwrite($f, $manifest);
        fclose($f);
        
        return true;
        
    }
    
    /**
     * Create items for the manifest
     *
     * @author Dimitri Rambout <dim@claroline.net>
     * @param array $item item's data
     * @return boolean
     */
    private function createManifestItems( &$item )
    {
        $thisItem = new item();
        $thisItem->load( $item['id'] );        
            
        $_item = '<item identifier="I_'.$thisItem->getId().'" identifierref="R_'.$thisItem->getId().'"
            adlcp:completionThreshold="'.$thisItem->getCompletionThreshold().'" isvisible="'.($thisItem->isVisible() ? 'true' : 'false' ).'">
            <title><![CDATA['.htmlspecialchars($thisItem->getTitle()).']]></title>
            <description><![CDATA[' . htmlspecialchars($thisItem->getDescription()) . ']]></description>';
        if( $item['type'] == 'CONTAINER' && isset($item['children']) && !empty( $item['children']) )
        {
            foreach( $item['children'] as $itemChild )
            {
                $_item .= $this->createManifestItems( $itemChild );   
            }            
        }
        $_item .= '</item>';
        
        return $_item;
    }
    
    /**
     * Create resources for the manifest
     *
     * @author Dimtiri Rambout <dim@claroline.net>
     * @param array $item item's data
     * @param string $destDir destination directory
     * @return boolean
     */
    private function createManifestResources( &$item, $destDir )
    {
        $destDir .= '/';
        $resource = '';
        switch( $item['type'] )
        {
            case 'CONTAINER' :
            {
                $destDir.= str_replace(',','_',replace_dangerous_char($item['title']));
                if( isset($item['children']) && !empty( $item['children'] ) )
                {
                    $resource .= '<resource identifier="R_' . $item['id'] . '" type="webcontent" adlcp:scormType="sco" href="' . $destDir . '" ></resource>'
                    ;
                    foreach( $item['children'] as $itemChild )
                    {
                        if( ! $resource .= $this->createManifestResources( $itemChild, $destDir) )
                        {
                            return false;
                        }   
                    }                    
                }
            }
            break;
            case 'SCORM' :
            {
                $sysPath = $item['sys_path'];
                if( strpos( $sysPath, './') !== false && strpos( $sysPath, './') == 0 )
                {
                    $sysPath = substr( $sysPath, 2 );
                }
                $filePath = $this->destDir.'/OrigScorm/' . $sysPath;
                if( file_exists( $filePath  ) )
                {
                    $scormFilePath = '/OrigScorm/' . $sysPath;
                    $resource .= '<resource identifier="R_' . $item['id']. '" type="webcontent" adlcp:scormType="sco" href="'. $scormFilePath . '">
                        <file href="' . $scormFilePath . '" />
                    </resource>
                    ';
                }                
            }
            break;
            case 'MODULE' :
            {
                $locator = ClarolineResourceLocator::parse($item['sys_path']);
                $connectorPath = get_module_path( $locator->getModuleLabel() ) . '/connector/cllp.scormexport.cnr.php';
                if( file_exists( $connectorPath ) )
                {
                    include_once( $connectorPath );
                    $class = $locator->getModuleLabel().'_scormExport';
                    $connector = new $class();
                    $resource .= $connector->prepareManifestResources( $item, $destDir, $locator);
                }                
            }
            break;
        }
        return $resource;
    }
    
    /**
     * Create a zip file with all the files in $destDir
     *
     * @author Dimitri Rambout <dim@claroline.net>
     * @return boolean
     */
    private function zip()
    {
        include_once get_path('incRepositorySys') . "/lib/thirdparty/pclzip/pclzip.lib.php";
        $list = 1;
        $zipFile = new PclZip($this->destDir . '.zip');
        $list = $zipFile->create($this->destDir, PCLZIP_OPT_REMOVE_PATH, $this->destDir);

        if ( !$list )
        {
            $this->error = get_lang('Unable to create the SCORM archive');
            return false;
        }

        // Temporary directory can be deleted, now that the zip is made.
        claro_delete_file($this->destDir);

        return true;
    }
    
    /**
     * Send the zip in the header for direct download
     *
     * @author Dimitri Rambout <dim@claroline.net>
     */
    private function send()
    {
        $filename = $this->destDir . '.zip';
        header('Content-Description: File Transfer');
        header('Content-Type: application/force-download');
        header('Content-Length: ' . filesize($filename));
        header('Content-Disposition: attachment; filename=' . basename($filename));
        readfile($filename);
        
        claro_delete_file( $this->destDir . '.zip' );
        
        exit(0);
    }
    
    /**
     * Return the error stored in $error
     *
     * @author Dimitri Rambout <dim@claroline.net>
     * @return string $error
     */
    public function getError()
    {
        return $this->error;
    }
}

/**
 * path list is an class used to get a list of learning path.
 *
 * @author Sebastien Piraux <pir@cerdecam.be>
 * @return boolean
 */
class pathListIterator implements SeekableIterator, Countable
{
    /**
     * @var $tblPath name of the path table
     */
    private $tblPath;
    private $pathList;


    /**
     * Constructor
     *
     * @author Sebastien Piraux <pir@cerdecam.be>
     */
    public function __construct()
    {
        $tblNameList = array(
            'lp_path'
        );

        // convert to Claroline course table names
        $tbl_lp_names = get_module_course_tbl( $tblNameList, claro_get_current_course_id() );
        $this->tblPath = $tbl_lp_names['lp_path'];
        
        $this->pathList = array();
    }

	/**
     * Load the correct list depending on parameter
     *
     * @param userId integer id of the user we need to display the path progression, can be ommitted default is null
     * @return array 2d array containing list of all available learning paths
     * @author Sebastien Piraux <pir@cerdecam.be>
     */
    public function load( $userId = null )
    {
        if( !is_null($userId) )
        {
            $this->pathList = $this->loadUserProgress($userId);
        }
        else
        {
            $this->pathList = $this->loadAll();
        }
    }

    /**
     * load list of all learning paths
     *
     * @return array 2d array containing list of all available learning paths
     * @author Sebastien Piraux <pir@cerdecam.be>
     */
    public function loadAll()
    {
        $sql = "SELECT
                    `id`,
                    `title`,
                    `description`,
                    `visibility`,
                    `rank`,
                    `version`,
                    `lock`,
                    `identifier`,
                    `allow_reinit`,
                    `view_mode`,
                    `encoding`
            FROM `".$this->tblPath."`
            ORDER BY `rank`";

        if ( false === ( $data = claro_sql_query_fetch_all_rows($sql) ) )
        {
            return false;
        }
        else
        {
            return $data;
        }
    }

    /**
     * load list of learning path progression related to $userId
     *
     * @param userId integer id of the user we need to display the path progression
     * @return array 2d array containing list of visible learning paths and progression of userId in it
     * @author Sebastien Piraux <pir@cerdecam.be>
     */
    public function loadUserProgress( $userId )
    {
        // TODO ... write the correct code ?
        $sql = "SELECT
                    `id`,
                    `title`,
                    `description`
            FROM `".$this->tblPath."`
            WHERE `visibility` = 'VISIBLE'";
//            AND 'userId' = " . (int) $userId;

        if ( false === ( $data = claro_sql_query_fetch_all_rows($sql) ) )
        {
            return false;
        }
        else
        {
            return $data;
        }
    }

    /**
     * move path one position up in the list if possible (rank becomes lower than before)
     *
     * @author Sebastien Piraux <pir@cerdecam.be>
     * @param $path object path to move up
     * @return boolean result of operation
     */
    public function movePathUp($path)
    {
        // find where is the path in the list to get the id of the previous one
        $i = 0;
        while( $i < count($this->pathList) )
        {
            if( $this->pathList[$i]['id'] == $path->getId() )
            {
                break;
            }
            $i++;
        }

        // if the path is the first of the list
        if( $i == 0 )
        {
            return false;
        }

        $currentRank = $path->getRank();
        $otherPathId = $this->pathList[$i-1]['id'];


        // get the path that is at the new position
        $otherPath = new path();
        $otherPath->load($otherPathId);

        // invert ranks
        $newRank = $otherPath->getRank();

        $otherPath->setRank($currentRank);
        $path->setRank($newRank);

        // save the two paths
        if( $path->validate() && $otherPath->validate() )
        {
            $path->save();
            $otherPath->save();

            // then move paths in the list
            $tempPath = $this->pathList[$i-1];
            $this->pathList[$i-1] = $this->pathList[$i];
            $this->pathList[$i] = $tempPath;
            return true;
        }
        else
        {
            return false;
        }
    }

    /**
     * move path one position down in the list if possible
     *
     * @author Sebastien Piraux <pir@cerdecam.be>
     * @param $path object path to move down
     * @return boolean result of operation
     */
    public function movePathDown($path)
    {
        // find where is the path is the list to get the id of the next one
        $i = 0;
        while( $i < count($this->pathList) )
        {
            if( $this->pathList[$i]['id'] == $path->getId() )
            {
                break;
            }
            $i++;
        }

        // if the path is the first of the list
        if( $i == count($this->pathList) - 1 )
        {
            return false;
        }

        $currentRank = $path->getRank();
        $otherPathId = $this->pathList[$i+1]['id'];


        // get the path that is at the new position
        $otherPath = new path();
        $otherPath->load($otherPathId);

        // invert ranks
        $newRank = $otherPath->getRank();

        $otherPath->setRank($currentRank);
        $path->setRank($newRank);

        // save the two paths
        if( $path->validate() && $otherPath->validate() )
        {
            $path->save();
            $otherPath->save();
            
            // then move paths in the list
            $tempPath = $this->pathList[$i+1];
            $this->pathList[$i+1] = $this->pathList[$i];
            $this->pathList[$i] = $tempPath;
            return true;
        }
        else
        {
            return false;
        }
    }
    
    // iterator implementation
    public function first()
    {
        $this->seek(0);
        return $this->current();
    }
    
    public function last()
    {
        $this->seek($this->count() - 1);
        return $this->current();
    }
    
    // Countable
    
    public function count()
    {
        return count( $this->pathList );
    }
    
    // Iterator
    
    protected $idx = 0;
    
    public function valid()
    {
        return !empty($this->pathList)
            && $this->idx >= 0
            && $this->idx < $this->count();
    }
    
    public function rewind()
    {
        $this->idx = 0;
    }
    
    public function next()
    {
        $this->idx++;
    }
    
    public function current()
    {
        return $this->pathList[$this->idx];
    }
    
    public function key()
    {
        return $this->idx;
    }
    
    // SeekableIterator
    
    public function seek( $index )
    {
        $this->idx = $index;
        
        if ( !$this->valid() )
        {
            throw new OutOfBoundsException('Invalid seek position');
        }
    }
}

?>