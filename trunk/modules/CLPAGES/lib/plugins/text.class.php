<?php // $Id$
if ( count( get_included_files() ) == 1 ) die( '---' );
/**
 * CLAROLINE
 *
 * $Revision: 324 $
 *
 * @copyright (c) 2001-2007 Universite catholique de Louvain (UCL)
 *
 * @license http://www.gnu.org/copyleft/gpl.html (GPL) GENERAL PUBLIC LICENSE
 *
 * @package CLPAGES
 *
 * @author Claroline team <info@claroline.net>
 *
 */
    // vim: expandtab sw=4 ts=4 sts=4 foldmethod=marker:

    class TextComponent extends Component
    {
    	private $content = '';

		/**
		 * @see Component
		 */
    	function render()
    	{
			return claro_parse_user_text($this->content);
    	}

		/**
		 * @see Component
		 */
    	function editor()
    	{
    		return '<textarea name="content_'.$this->getId().'" id="content_'.$this->getId().'" rows="20" cols="80" style="width: 100%;">'.htmlspecialchars($this->render()).'</textarea>';
    	}

		/**
		 * @see Component
		 */
    	function getEditorData()
    	{
    		$this->content = $this->getFromRequest('content_'.$this->getId());
    	}

		/**
		 * @see Component
		 */
    	function setData( $data )
    	{
  			$this->content = $data['content'];
    	}

		/**
		 * @see Component
		 */
    	function getData()
    	{
    		return array('content' => $this->content);
    	}
    }

    PluginRegistry::register('text',get_lang('Text'),'TextComponent', '', 'textIco');
?>