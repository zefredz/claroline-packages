<?php

// $Id$

/**
 * CLAROLINE
 *
 * $Revision$
 * @copyright (c) 2001-2011 Universite catholique de Louvain (UCL)
 * @license http://www.gnu.org/copyleft/gpl.html (GPL) GENERAL PUBLIC LICENSE
 * @package CLPAGES
 * @author Claroline team <info@claroline.net>
 */
// vim: expandtab sw=4 ts=4 sts=4 foldmethod=marker:

/**
 * Abstract component class
 */
abstract class Component
{

    protected $id = 0;
    protected $pageId = 0;
    protected $page = null;
    protected $title = '';
    protected $type = '';
    protected $visibility = 'VISIBLE';
    protected $titleVisibility = 'VISIBLE';
    protected $rank = 0;

    // data fields is handled by extending classes
    // Abstract methods    
    
    /**
     * @return string
     */
    abstract public function render();
    
    /**
     * Get the component editor
     */
    abstract public function editor();

    /**
     * Get dat from the editor
     */
    abstract public function getEditorData();

    /**
     * set the component data
     * @param $data array data
     * @return boolean success of operation
     */
    abstract public function setData($data);

    /**
     * get the component data
     * @return array component data
     */
    abstract function getData();

    // title
    public function getTitle()
    {
        return $this->title;
    }

    public function setTitle($title)
    {
        $this->title = $title;
    }

    // id
    public function getId()
    {
        return (int) $this->id;
    }

    public function setId($id)
    {
        $this->id = (int) $id;
    }

    // rank
    public function getRank()
    {
        return (int) $this->rank;
    }

    public function setRank($rank)
    {
        $this->rank = (int) $rank;
    }

    // page id
    public function getPageId()
    {
        return (int) $this->pageId;
    }

    public function setPageId($pageId)
    {
        $this->pageId = (int) $pageId;
    }
    
    public function getPage()
    {
        if ( is_null( $this->page ) )
        {
            $page = new Page();
            $page->load($this->getPageId());
            $this->setPage( $page );
        }
        
        return $this->page;
    }
    
    public function setPage( Page $page )
    {
        $this->page = $page;
    }

    // type
    public function getType()
    {
        return $this->type;
    }

    public function setType($type)
    {
        $this->type = $type;
    }

    // visibility
    public function getVisibility()
    {
        return $this->visibility;
    }

    public function setVisibility($visibility)
    {
        $this->visibility = ( $visibility === 'INVISIBLE' ) ? 'INVISIBLE' : 'VISIBLE';
    }

    public function setVisible()
    {
        $this->setVisibility('VISIBLE');
    }

    public function setInvisible()
    {
        $this->setVisibility('INVISIBLE');
    }

    public function isVisible()
    {
        return ( $this->getVisibility() === 'VISIBLE' );
    }

    // title visibility
    public function getTitleVisibility()
    {
        return $this->titleVisibility;
    }

    public function setTitleVisibility($visibility)
    {
        $this->titleVisibility = ( $visibility === 'INVISIBLE' ) ? 'INVISIBLE' : 'VISIBLE';
    }

    public function setTitleVisible()
    {
        $this->setTitleVisibility('VISIBLE');
    }

    public function setTitleInvisible()
    {
        $this->setTitleVisibility('INVISIBLE');
    }

    public function isTitleVisible()
    {
        return ( $this->getTitleVisibility() === 'VISIBLE' );
    }

    public function validate() //must be surcharged by other children component
    {
        return true;
    }

    // load
    public function load($id)
    {
        $tblList = get_module_course_tbl(array ('clpages_contents'), claro_get_current_course_id());
        $sql = "SELECT `id`,
                    `title`,
                    `pageId`,
                    `type`,
                    `data`,
                    `visibility`,
                    `titleVisibility`,
                    `rank`
                FROM `" . $tblList['clpages_contents'] . "`
                WHERE id = '" . (int) $id . "'";

        $data = claro_sql_query_get_single_row($sql);

        if (!empty($data))
        {
            // from query
            $this->id = (int) $data['id'];
            $this->pageId = (int) $data['pageId'];
            $this->title = $data['title'];
            $this->type = $data['type'];
            $this->visibility = $data['visibility'];
            $this->titleVisibility = $data['titleVisibility'];
            $this->rank = (int) $data['rank'];

            $this->setData(unserialize($data['data']));

            return true;
        }
        else
        {
            return false;
        }
    }

    // save
    public function save()
    {
        $tblList = get_module_course_tbl(array ('clpages_contents'), claro_get_current_course_id());

        if (!$this->getId())
        {
            $this->setRank($this->getHigherRank() + 1);

            // insert
            $sql = "INSERT INTO `" . $tblList['clpages_contents'] . "`
                    SET `pageId` = '" . $this->getPageId() . "',
                        `title` = '" . addslashes($this->getTitle()) . "',
                        `type` = '" . addslashes($this->getType()) . "',
                        `visibility` = '" . addslashes($this->getVisibility()) . "',
                        `titleVisibility` = '" . addslashes($this->getTitleVisibility()) . "',
                        `rank` = '" . $this->getRank() . "',
                        `data` = '" . addslashes(serialize($this->getData())) . "'";

            // execute the creation query and get id of inserted assignment
            $insertedId = claro_sql_query_insert_id($sql);

            if ($insertedId)
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
            $sql = "UPDATE `" . $tblList['clpages_contents'] . "`
                    SET `pageId` = '" . $this->getPageId() . "',
                        `title` = '" . addslashes($this->getTitle()) . "',
                        `type` = '" . addslashes($this->getType()) . "',
                        `visibility` = '" . addslashes($this->getVisibility()) . "',
                        `titleVisibility` = '" . addslashes($this->getTitleVisibility()) . "',
                        `rank` = '" . $this->getRank() . "',
                        `data` = '" . addslashes(serialize($this->getData())) . "'
                    WHERE `id` = '" . $this->id . "'";

            // execute and return main query
            if (claro_sql_query($sql))
            {
                return $this->id;
            }
            else
            {
                return false;
            }
        }
    }

    public function delete()
    {
        if (!$this->getId())
            return true;

        $tblList = get_module_course_tbl(array ('clpages_contents'), claro_get_current_course_id());

        $sql = "DELETE FROM `" . $tblList['clpages_contents'] . "`
                WHERE `id` = " . $this->getId();

        if (claro_sql_query($sql) == false)
            return false;

        $this->setId(0);
        return true;
    }

    function getHigherRank()
    {
        $tblList = get_module_course_tbl(array ('clpages_contents'), claro_get_current_course_id());

        // use max instead of count to handle suppressed attempts
        $sql = "SELECT MAX(`rank`)
                FROM " . $tblList['clpages_contents'] . "
                WHERE `pageId` = " . (int) $this->pageId;

        $higherRank = claro_sql_query_get_single_value($sql);

        if (is_null($higherRank) || !$higherRank)
        {
            return 1;
        }
        else
        {
            // value is at least 1
            return max(1, $higherRank);
        }
    }

    function getFromRequest($key)
    {
        if (!empty($_REQUEST[$key]))
        {
            return claro_utf8_decode($_REQUEST[$key]);
        }
        else
        {
            return '';
        }
    }

}

//PAGE CLASS DEFINITION
/**
 * A page
 */
class Page
{

    protected $id = 0;
    protected $title = '';
    protected $description = '';
    protected $authorId = 0;
    protected $editorId = 0;
    protected $creationTime = 0;
    protected $lastModificationTime = 0;
    protected $visibility = 'VISIBLE';
    protected $displayMode = 'PAGE'; // SLIDE or PAGE
    protected $componentList;

    public function __construct()
    {
        $this->componentList = array ();
    }

    // load
    public function load($id)
    {
        $tblList = get_module_course_tbl(array ('clpages_pages'), claro_get_current_course_id());
        $sql = "SELECT
                    `id`,
                    `title`,
                    `description`,
                    `authorId`,
                    `editorId`,
                    `creationTime`,
                    `lastModificationTime`,
                    `visibility`,
                    `displayMode`
                FROM `" . $tblList['clpages_pages'] . "`
                WHERE id = '" . (int) $id . "'";

        $data = claro_sql_query_get_single_row($sql);

        if (!empty($data))
        {
            // from query
            $this->id = (int) $data['id'];
            $this->title = $data['title'];
            $this->description = $data['description'];
            $this->authorId = (int) $data['authorId'];
            $this->editorId = (int) $data['editorId'];
            $this->creationTime = (int) $data['creationTime'];
            $this->lastModificationTime = (int) $data['lastModificationTime'];
            $this->visibility = $data['visibility'];
            $this->displayMode = $data['displayMode'];

            $this->createComponents();

            return true;
        }
        else
        {
            return false;
        }
    }

    // save
    public function save()
    {
        $tblList = get_module_course_tbl(array ('clpages_pages'), claro_get_current_course_id());

        if (!$this->getId())
        {
            // insert
            $sql = "INSERT INTO `" . $tblList['clpages_pages'] . "`
                    SET `title` = '" . claro_sql_escape($this->getTitle()) . "',
                        `description` = '" . claro_sql_escape($this->getDescription()) . "',
                        `authorId` = '" . $this->getAuthorId() . "',
                        `editorId` = '" . $this->getEditorId() . "',
                        `creationTime` = FROM_UNIXTIME('" . $this->getCreationTime() . "'),
                        `lastModificationTime` = FROM_UNIXTIME('" . $this->getLastModificationTime() . "'),
                        `visibility` = '" . claro_sql_escape($this->getVisibility()) . "',
                        `displayMode` = '" . claro_sql_escape($this->getDisplayMode()) . "'";

            // execute the creation query and get id of inserted assignment
            $insertedId = claro_sql_query_insert_id($sql);

            if ($insertedId)
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
            // do not update creation time and author id on update
            $sql = "UPDATE `" . $tblList['clpages_pages'] . "`
                    SET `title` = '" . claro_sql_escape($this->getTitle()) . "',
                        `description` = '" . claro_sql_escape($this->getDescription()) . "',
                        `editorId` = '" . $this->getEditorId() . "',
                        `lastModificationTime` = FROM_UNIXTIME('" . $this->getLastModificationTime() . "'),
                        `visibility` = '" . claro_sql_escape($this->getVisibility()) . "',
                        `displayMode` = '" . claro_sql_escape($this->getDisplayMode()) . "'
                    WHERE `id` = '" . $this->id . "'";

            // execute and return main query
            if (claro_sql_query($sql))
            {
                return $this->id;
            }
            else
            {
                return false;
            }
        }
    }

    // delete
    public function delete()
    {
        if (!$this->getId())
            return true;

        $tblList = get_module_course_tbl(array ('clpages_pages', 'clpages_contents'), claro_get_current_course_id());

        $sql = "DELETE FROM `" . $tblList['clpages_pages'] . "`
                WHERE `id` = " . $this->getId();

        if (claro_sql_query($sql) == false)
            return false;

        $sql = "DELETE FROM `" . $tblList['clpages_contents'] . "`
                WHERE `pageId` = " . $this->getId();

        if (claro_sql_query($sql) == false)
            return false;

        $this->setId(0);
        return true;
    }

    protected function createComponents()
    {
        $tbl_lp_names = get_module_course_tbl(array ('clpages_contents'), claro_get_current_course_id());
        $tblContents = $tbl_lp_names['clpages_contents'];

        $sql = "SELECT
                    `id`,
                    `title`,
                    `pageId`,
                    `type`,
                    `data`,
                    `visibility`,
                    `titleVisibility`,
                    `rank`
            FROM `" . $tblContents . "`
            WHERE `pageId` = " . $this->getId() . "
            ORDER BY `rank` ASC";

        if (false === ( $data = claro_sql_query_fetch_all_rows($sql) ))
        {
            return false;
        }
        else
        {
            $this->componentList = array ();
            $factory = new ComponentFactory();

            foreach ($data as $componentData)
            {
                $component = $factory->createComponent($componentData['type']);

                if ($component)
                {
                    $component->setId($componentData['id']);
                    $component->setPageId($this->getId());
                    $component->setPage( $this );
                    $component->setTitle($componentData['title']);
                    $component->setType($componentData['type']);
                    $component->setVisibility($componentData['visibility']);
                    $component->setTitleVisibility($componentData['titleVisibility']);
                    $component->setRank($componentData['rank']);
                    $component->setData(unserialize($componentData['data']));

                    $this->componentList[$componentData['id']] = $component;
                }
                else
                {
                    return false;
                }
            }

            return true;
        }
    }

    /**
     * check if data are valide
     *
     * @author Sebastien Piraux <pir@cerdecam.be>
     * @return boolean
     */
    function validate()
    {
        // title is a mandatory element
        $title = strip_tags($this->title);

        if (empty($title))
        {
            claro_failure::set_failure('page_no_title');
            return false;
        }

        return true; // no errors, form is valide
    }

    public function getComponentById($id)
    {
        return $this->componentList[$id];
    }

    //-- Getter & Setter
    // page id
    public function getId()
    {
        return (int) $this->id;
    }

    public function setId($id)
    {
        $this->id = (int) $id;
    }

    // title
    public function getTitle()
    {
        return $this->title;
    }

    public function setTitle($value)
    {
        $this->title = trim($value);
    }

    // description
    public function getDescription()
    {
        return $this->description;
    }

    public function setDescription($value)
    {
        $this->description = trim($value);
    }

    // author id
    public function getAuthorId()
    {
        return (int) $this->authorId;
    }

    public function setAuthorId($authorId)
    {
        $this->authorId = (int) $authorId;
    }

    // editor id
    public function getEditorId()
    {
        return (int) $this->editorId;
    }

    public function setEditorId($editorId)
    {
        $this->editorId = (int) $editorId;
    }

    // creation time
    public function getCreationTime()
    {
        return (int) $this->creationTime;
    }

    public function setCreationTime($creationTime)
    {
        $this->creationTime = (int) $creationTime;
    }

    // last modification time
    public function getLastModificationTime()
    {
        return (int) $this->lastModificationTime;
    }

    public function setLastModificationTime($lastModificationTime)
    {
        $this->lastModificationTime = (int) $lastModificationTime;
    }

    // visibility
    protected function getVisibility()
    {
        return $this->visibility;
    }

    protected function setVisibility($visibility)
    {
        $this->visibility = ( $visibility === 'INVISIBLE' ) ? 'INVISIBLE' : 'VISIBLE';
    }

    public function setVisible()
    {
        $this->setVisibility('VISIBLE');
    }

    public function setInvisible()
    {
        $this->setVisibility('INVISIBLE');
    }

    public function isVisible()
    {
        return ( $this->getVisibility() === 'VISIBLE' );
    }

    public function getDisplayMode()
    {
        return $this->displayMode;
    }

    public function setDisplayMode($displayMode)
    {
        $this->displayMode = ( $displayMode === 'SLIDE' ) ? 'SLIDE' : 'PAGE';
    }

    public function getComponentList()
    {
        return $this->componentList;
    }

}

/**
 * The list of pages
 */
class PageList
{

    /**
     * @var $tblPages name of the pages table
     */
    protected $tblPages;

    /**
     * Constructor
     *
     * @author Sebastien Piraux <pir@cerdecam.be>
     */
    function __construct()
    {
        $tblNameList = array (
            'clpages_pages'
        );

        // convert to Claroline course table names
        $tbl_lp_names = get_module_course_tbl($tblNameList, claro_get_current_course_id());
        $this->tblPages = $tbl_lp_names['clpages_pages'];
    }

    /**
     * Load the correct list
     */
    function load($loadAll = false)
    {
        if ($loadAll)
        {
            return $this->loadAll();
        }
        else
        {
            return $this->loadVisible();
        }
    }

    function loadAll()
    {
        $sql = "SELECT
                    `id`,
                    `title`,
                    `description`,
                    `authorId`,
                    `editorId`,
                    `creationTime`,
                    `lastModificationTime`,
                    `visibility`
            FROM `" . $this->tblPages . "`
            ORDER BY `creationTime`";

        if (false === ( $data = claro_sql_query_fetch_all_rows($sql) ))
        {
            return false;
        }
        else
        {
            return $data;
        }
    }

    function loadVisible()
    {
        $sql = "SELECT
                    `id`,
                    `title`,
                    `description`,
                    `authorId`,
                    `editorId`,
                    `creationTime`,
                    `lastModificationTime`,
                    `visibility`
            FROM `" . $this->tblPages . "`
            WHERE `visibility` = 'VISIBLE'
            ORDER BY `creationTime`";

        if (false === ( $data = claro_sql_query_fetch_all_rows($sql) ))
        {
            return array ();
        }
        else
        {
            return $data;
        }
    }

}

/**
 * A component factory
 * Oh no... is this another ugly thing built upon the ugly singleton from 
 * pluginRegistry.lib ?!?
 */
class ComponentFactory
{

    protected $pluginRegistry;

    public function __construct()
    {
        $this->pluginRegistry = pluginRegistry::getInstance();
    }

    public function createComponent($type)
    {
        $className = $this->pluginRegistry->getPluginClass(strtolower($type));

        if ($className != '' && class_exists($className))
        {
            return new $className();
        }
        else
        {
            return new DefaultComponent();
        }
    }

}
