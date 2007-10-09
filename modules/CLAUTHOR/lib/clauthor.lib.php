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
 * @package CLAUTHOR
 *
 * @author Claroline team <info@claroline.net>
 *
 */
    // vim: expandtab sw=4 ts=4 sts=4 foldmethod=marker:

    abstract class Component
    {
    	private $id = 0;
    	private $docId = 0;
    	private $title = '';
    	private $type = '';
    	private $visibility = 'VISIBLE';
    	private $titleVisibility = 'VISIBLE';
    	private $rank = 0;

    	// data fields is handled by extending classes

		// Abstract methods
    	abstract function render();

    	abstract function editor();

    	abstract function getEditorData();

		/**
		 * set the component data
		 * @param $data array data
		 * @return boolean success of operation
		 */
    	abstract function setData( $data );

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

    	public function setTitle( $title )
    	{
    		$this->title = $title;
    	}

		// id
    	public function getId()
    	{
    		return (int) $this->id;
    	}

    	public function setId( $id )
    	{
    		$this->id = (int) $id;
    	}

		// rank
    	public function getRank()
    	{
    		return (int) $this->rank;
    	}

    	public function setRank( $rank )
    	{
    		$this->rank = (int) $rank;
    	}

    	/* Move this to document class
    	public function move(){}
    	public function moveUp(){}
    	public function moveDown(){}
    	*/

		// document id
    	public function getDocId()
    	{
    		return (int) $this->docId;
    	}

		public function setDocId( $docId )
		{
			$this->docId = (int) $docId;
		}

		// type
	   	public function getType()
	   	{
	   		return $this->type;
	   	}

    	public function setType( $type )
    	{
    		$this->type = $type;
    	}

		// visibility
    	public function getVisibility()
    	{
    		return $this->visibility;
    	}

    	public function setVisibility( $visibility )
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

    	public function setTitleVisibility( $visibility )
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


    	public function renderBlock()
    	{

			$out = '<div id="component_'.$this->getId().'" class="type_'.$this->getType().' sortableComponent'.($this->isVisible()?'':' invisible').'">' . "\n";

			if( $this->isTitleVisible() || claro_is_allowed_to_edit() )
			{
	    		// display bar ( title, link to edit, link to delete)
				$out .= ' <div class="componentHeader">' . "\n";

				if( claro_is_allowed_to_edit() )
				{
					// commands
					$out .= '  <span class="componentHeaderCmd">'
					.	 '<a href="#" class="mkInvisibleCmd" '.(!$this->isVisible()? 'style="display:none"':'').'>' . claro_html_icon('visible') . '</a>'
					.	 '<a href="#" class="mkVisibleCmd" '.($this->isVisible()? 'style="display:none"':'').'>' . claro_html_icon('invisible') . '</a>'
					.	 '&nbsp;'
					.	 '<a href="#" class="toggleEditorCmd">' . claro_html_icon('edit') . '</a>'
					. 	 '&nbsp;'
					.	 '<a href="#" class="deleteItemCmd">' . claro_html_icon('delete') . '</a>'
					. 	 '  </span>' . "\n";
				}

				$out .= '  <span class="componentHeaderTitle">&nbsp;'  . $this->getTitle() . '</span>' . "\n"
				.	 ' </div>' . "\n";
			}

    		$out .= ' <div class="componentContent">' . "\n"
			.	 $this->render() . "\n"
			.	 ' </div>' . "\n"
			.	 '</div>' . "\n\n"
			;

			return $out;
    	}
    	public function renderEditor()
    	{
			$out = "\n\n" . ' <div class="componentEditor">' . "\n"
			.	 '<form id="form_'.$this->id.'" action="ajaxHandler.php" method="post">' . "\n"
			// hidden
			.    claro_form_relay_context()
    		.	 '<input type="hidden" name="claroFormId" value="'.uniqid('').'" />' . "\n"
   			.	 '<input type="hidden" name="cmd" value="exEdit" />' . "\n"
   			.	 '<input type="hidden" name="docId" value="'.$this->getDocId().'" />' . "\n"
   			.	 '<input type="hidden" name="itemId" value="'.$this->getId().'" />' . "\n"
   			.	 '<input type="hidden" name="itemType" value="'.$this->getType().'" />' . "\n"
   			// title
    		.	 '<label for="title_'.$this->getId().'">' . get_lang('Title') . '</label><br />' . "\n"
    		.	 '<input type="text" name="title_'.$this->getId().'" id="title_'.$this->getId().'" maxlength="255" value="'.htmlspecialchars($this->getTitle()).'" />' . "\n"
    		// title visibility
    		.	 '<input type="checkbox" name="titleVisibility_'.$this->getId().'" id="titleVisibility_'.$this->getId().'" value="VISIBLE" '.($this->isTitleVisible() ? ' checked="checked"' : '').'/>'
    		.	 ' <label for="titleVisibility_'.$this->getId().'">'.get_lang('Display title').'</label>' . "\n"
    		.	 '<br /><br />'
    		// component specific edition
			.	 $this->editor() . "\n"
			.	 '<br /><br />'
			// submit
			.    '<input type="submit" value="' . get_lang('Ok') . '" />&nbsp;' . "\n"
    		.    '</form>' . "\n"
			.	 ' </div>' . "\n"
			;

			return $out;
    	}

		// load
    	public function load( $id )
    	{
    		$tblList = get_module_course_tbl( array( 'clauthor_contents' ), claro_get_current_course_id() );
			$sql = "SELECT `id`,
	                    `title`,
	                    `docId`,
	                    `type`,
	                    `data`,
	                    `visibility`,
	                    `titleVisibility`,
	                    `rank`
					FROM `".$tblList['clauthor_contents']."`
					WHERE id = '".(int) $id . "'";

			$data = claro_sql_query_get_single_row($sql);

	        if( !empty($data) )
	        {
	            // from query
	            $this->id = (int) $data['id'];
	            $this->docId = (int) $data['docId'];
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
    		$tblList = get_module_course_tbl( array( 'clauthor_contents' ), claro_get_current_course_id() );

	        if( ! $this->getId() )
	        {
	            $this->setRank( $this->getHigherRank() + 1 );

	            // insert
	            $sql = "INSERT INTO `".$tblList['clauthor_contents']."`
	                    SET `docId` = '".$this->getDocId()."',
	                    	`title` = '".addslashes($this->getTitle())."',
	                        `type` = '".addslashes($this->getType())."',
	                        `visibility` = '".addslashes($this->getVisibility())."',
	                        `titleVisibility` = '".addslashes($this->getTitleVisibility())."',
	                        `rank` = '".$this->getRank()."',
	                        `data` = '".addslashes(serialize($this->getData()))."'";

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
	            $sql = "UPDATE `".$tblList['clauthor_contents']."`
	                    SET `docId` = '".$this->getDocId()."',
	                    	`title` = '".addslashes($this->getTitle())."',
	                        `type` = '".addslashes($this->getType())."',
	                        `visibility` = '".addslashes($this->getVisibility())."',
	                        `titleVisibility` = '".addslashes($this->getTitleVisibility())."',
	                        `rank` = '".$this->getRank()."',
	                        `data` = '".addslashes(serialize($this->getData()))."'
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

    	public function delete()
    	{
	        if( ! $this->getId() ) return true;

			$tblList = get_module_course_tbl( array( 'clauthor_contents' ), claro_get_current_course_id() );

	        $sql = "DELETE FROM `" . $tblList['clauthor_contents'] . "`
	                WHERE `id` = " . $this->getId() ;

	        if( claro_sql_query($sql) == false ) return false;

	        $this->setId(0);
	        return true;
    	}

    	function getHigherRank()
	    {
	    	$tblList = get_module_course_tbl( array( 'clauthor_contents' ), claro_get_current_course_id() );

	    	// use max instead of count to handle suppressed attempts
	    	$sql = "SELECT MAX(`rank`)
	    			FROM ".$tblList['clauthor_contents']."
	    			WHERE `docId` = ".(int) $this->docId;

	    	$higherRank = claro_sql_query_get_single_value($sql);

	    	if( is_null($higherRank)  || !$higherRank )
	    	{
	    		return 1;
	    	}
	    	else
	    	{
	    		// value is at least 1
	    		return max(1,$higherRank);
	    	}
	    }

	    function getFromRequest( $key )
	    {
	    	if( !empty($_REQUEST[$key]) )
	    	{
	    		return claro_utf8_decode($_REQUEST[$key]);
	    	}
	    	else
	    	{
	    		return '';
	    	}
	    }
    }

    class Document
    {
    	private $id = 0;
    	private $title = '';
    	private $description = '';
    	private $authorId = 0;
    	private $editorId = 0;
    	private $creationTime = 0;
    	private $lastModificationTime = 0;
    	private $visibility = 'VISIBLE';

    	private $componentList;

    	public function __construct()
    	{
    		$this->componentList = array();
    	}


		// load
    	public function load( $id )
    	{
    		$tblList = get_module_course_tbl( array( 'clauthor_docs' ), claro_get_current_course_id() );
			$sql = "SELECT
						`id`,
	                    `title`,
	                    `description`,
	                    `authorId`,
	                    `editorId`,
	                    `creationTime`,
	                    `lastModificationTime`,
	                    `visibility`
					FROM `".$tblList['clauthor_docs']."`
					WHERE id = '".(int) $id . "'";

			$data = claro_sql_query_get_single_row($sql);

	        if( !empty($data) )
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
    		$tblList = get_module_course_tbl( array( 'clauthor_docs' ), claro_get_current_course_id() );

	        if( ! $this->getId() )
	        {
	        	// insert
	            $sql = "INSERT INTO `".$tblList['clauthor_docs']."`
	                    SET `title` = '".addslashes($this->getTitle())."',
	                    	`description` = '".addslashes($this->getDescription())."',
	                    	`authorId` = '".$this->getAuthorId()."',
	                    	`editorId` = '".$this->getEditorId()."',
	                        `creationTime` = FROM_UNIXTIME('".$this->getCreationTime()."'),
	                        `lastModificationTime` = FROM_UNIXTIME('".$this->getLastModificationTime()."'),
	                        `visibility` = '".addslashes($this->getVisibility())."'";

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
	            // do not update creation time and author id on update
	            $sql = "UPDATE `".$tblList['clauthor_docs']."`
	                    SET `title` = '".addslashes($this->getTitle())."',
	                    	`description` = '".addslashes($this->getDescription())."',
	                    	`editorId` = '".$this->getEditorId()."',
	                        `lastModificationTime` = FROM_UNIXTIME('".$this->getLastModificationTime()."'),
	                        `visibility` = '".addslashes($this->getVisibility())."'
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

		// delete
    	public function delete()
    	{
	        if( ! $this->getId() ) return true;

			$tblList = get_module_course_tbl( array( 'clauthor_docs', 'clauthor_contents' ), claro_get_current_course_id() );

	        $sql = "DELETE FROM `" . $tblList['clauthor_docs'] . "`
	                WHERE `id` = " . $this->getId() ;

	        if( claro_sql_query($sql) == false ) return false;

			$sql = "DELETE FROM `" . $tblList['clauthor_contents'] . "`
	                WHERE `docId` = " . $this->getId() ;

	        if( claro_sql_query($sql) == false ) return false;

	        $this->setId(0);
	        return true;
    	}

		private function createComponents()
		{
			$tbl_lp_names = get_module_course_tbl( array('clauthor_contents'), claro_get_current_course_id() );
	        $tblContents = $tbl_lp_names['clauthor_contents'];

	        $sql = "SELECT
	                    `id`,
	                    `title`,
	                    `docId`,
	                    `type`,
	                    `data`,
	                    `visibility`,
	                    `titleVisibility`,
	                    `rank`
	            FROM `".$tblContents."`
	            WHERE `docId` = ". $this->getId() ."
	            ORDER BY `rank` ASC";

	        if ( false === ( $data = claro_sql_query_fetch_all_rows($sql) ) )
	        {
	            return false;
	        }
	        else
	        {
	        	$this->componentList = array();
	        	$factory = new ComponentFactory();

	        	foreach( $data as $componentData )
	        	{
	        		$component = $factory->createComponent( $componentData['type'] );

					if( $component )
					{
		        		$component->setId($componentData['id']);
		        		$component->setDocId($componentData['docId']);
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

	        if( empty($title) )
	        {
	            claro_failure::set_failure('document_no_title');
	            return false;
	        }

	        return true; // no errors, form is valide
	    }

		public function getComponentById( $id )
		{
			return $this->componentList[$id];
		}

		//-- Getter & Setter

		// document id
	    public function getId()
	    {
	        return (int) $this->id;
	    }

	    public function setId( $id )
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

		public function setAuthorId( $authorId )
		{
			$this->authorId = (int) $authorId;
		}

		// editor id
    	public function getEditorId()
    	{
    		return (int) $this->editorId;
    	}

		public function setEditorId( $editorId )
		{
			$this->editorId = (int) $editorId;
		}

		// creation time
		public function getCreationTime()
    	{
    		return (int) $this->creationTime;
    	}

		public function setCreationTime( $creationTime )
		{
			$this->creationTime = (int) $creationTime;
		}


		// last modification time
		public function getLastModificationTime()
    	{
    		return (int) $this->lastModificationTime;
    	}

		public function setLastModificationTime( $lastModificationTime )
		{
			$this->lastModificationTime = (int) $lastModificationTime;
		}


		// visibility
    	protected function getVisibility()
    	{
    		return $this->visibility;
    	}

    	protected function setVisibility( $visibility )
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

    	public function getComponentList()
    	{
    		return $this->componentList;
    	}

    }


    class DocList
	{
	    /**
	     * @var $tblDocs name of the docs table
	     */
	    private $tblDocs;


	    /**
	     * Constructor
	     *
	     * @author Sebastien Piraux <pir@cerdecam.be>
	     */
	    function __construct()
	    {
	        $tblNameList = array(
	            'clauthor_docs'
	        );

	        // convert to Claroline course table names
	        $tbl_lp_names = get_module_course_tbl( $tblNameList, claro_get_current_course_id() );
	        $this->tblDocs = $tbl_lp_names['clauthor_docs'];
	    }

		/**
	     * Load the correct list
	     */
	    function load( $loadAll = false )
	    {
	        if( $loadAll )
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
	            FROM `".$this->tblDocs."`
	            ORDER BY `creationTime`";

	        if ( false === ( $data = claro_sql_query_fetch_all_rows($sql) ) )
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
	            FROM `".$this->tblDocs."`
	            WHERE `visibility` = 'VISIBLE'
	            ORDER BY `creationTime`";

	        if ( false === ( $data = claro_sql_query_fetch_all_rows($sql) ) )
	        {
	            return array();
	        }
	        else
	        {
	            return $data;
	        }
	    }

	}


	class ComponentFactory
	{
		private $pluginRegistry;

		public function __construct()
		{
			$this->pluginRegistry = pluginRegistry::getInstance();
		}


		public function createComponent( $type )
		{
			$className = $this->pluginRegistry->getPluginClass( strtolower($type) );

			if( $className != '' && class_exists($className) )
			{
				return new $className();
			}
			else
			{
				return new DefaultComponent();
			}

			return false;
		}
	}

?>