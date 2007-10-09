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

    class WebsiteComponent extends Component
    {
    	private $url = 'http://www.google.com';
    	private $size = 2;

		/**
		 * @see Component
		 */
    	function render()
    	{
			if( $this->size == 3 ) 		$height = "450px";
			elseif( $this->size == 2 )	$height = "300px";
			else						$height = "150px";

    		if( !empty($this->url) )
    		{
    			return '<iframe src="'.$this->url.'" width="100%" height="'.$height.'"></iframe>';
    		}
    		else
    		{
    			return '';
    		}
    	}

		/**
		 * @see Component
		 */
    	function editor()
    	{
    		return "\n"
    		// url
			.	 '<label for="url_'.$this->getId().'">' . get_lang('Url') . '</label>&nbsp;<span class="required">*</span><br />' . "\n"
    		.	 '<input type="text" name="url_'.$this->getId().'" id="url_'.$this->getId().'" maxlength="255" value="'.htmlspecialchars($this->url).'" /><br /><br />' . "\n"
    		// display size
    		.	 get_lang('Size') . '&nbsp;<span class="required">*</span><br />' . "\n"
    		.	 '<input type="radio" name="size_'.$this->getId().'" id="size_'.$this->getId().'_1" value="1" '.($this->size == 1 ? ' checked="checked"' : '').'/> <label for="size_'.$this->getId().'_1">'.get_lang('Small').'</label>' . "\n"
    		.	 '<input type="radio" name="size_'.$this->getId().'" id="size_'.$this->getId().'_2" value="2" '.($this->size == 2 ? ' checked="checked"' : '').'/> <label for="size_'.$this->getId().'_2">'.get_lang('Medium').'</label>' . "\n"
    		.	 '<input type="radio" name="size_'.$this->getId().'" id="size_'.$this->getId().'_3" value="3" '.($this->size == 3 ? ' checked="checked"' : '').'/> <label for="size_'.$this->getId().'_3">'.get_lang('Big').'</label>' . "\n"

    		;
    	}

		/**
		 * @see Component
		 */
    	function getEditorData()
    	{
    		$this->url = $this->getFromRequest('url_'.$this->getId());
    		$this->size = (int) $this->getFromRequest('size_'.$this->getId());
    	}

		/**
		 * @see Component
		 */
    	function setData( $data )
    	{
  			$this->url = !empty($data['url']) ? $data['url'] : '' ;
  			$this->size = !empty($data['size']) ? $data['size'] : '' ;
    	}

		/**
		 * @see Component
		 */
    	function getData()
    	{
    		return array(
					'url' => $this->url,
					'size' => $this->size
			);
    	}
    }

    PluginRegistry::register('website',get_lang('External website'),'WebsiteComponent');
?>