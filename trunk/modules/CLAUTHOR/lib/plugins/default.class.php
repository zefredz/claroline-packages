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

    class DefaultComponent extends Component
    {
		/**
		 * @see Component
		 */
    	function render()
    	{
			return ( claro_is_allowed_to_edit() ? '<p>'.get_lang('Deprecated plugin, contact administrator').'</p>': '');
    	}

		/**
		 * @see Component
		 */
    	function editor()
    	{
    		return '';
    	}

		/**
		 * @see Component
		 */
    	function getEditorData()
    	{
    		// do nothing
    	}

		/**
		 * @see Component
		 */
    	function setData( $data )
    	{
  			// do nothing
    	}

		/**
		 * @see Component
		 */
    	function getData()
    	{
    		// do nothing
    	}
    }

	// do not register this default plugin
    //PluginRegistry::register('default',get_lang('Text'),'TextComponent');
?>