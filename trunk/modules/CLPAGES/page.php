<?php // $Id$
/**
 * CLAROLINE
 *
 * $Revision$
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

    // load Claroline kernel
    $tlabelReq = 'CLPAGES';
    require_once dirname(__FILE__) . '/../../claroline/inc/claro_init_global.inc.php';

	require_once dirname( __FILE__ ) . '/lib/clpages.lib.php';
	require_once dirname( __FILE__ ) . '/lib/pluginRegistry.lib.php';
	// load and register all plugins
	$pluginRegistry = pluginRegistry::getInstance();


	/*
	 * init request vars
	 */
	if( isset($_REQUEST['pageId']) && is_numeric($_REQUEST['pageId']) ) $pageId = (int) $_REQUEST['pageId'];
	else                                                                $pageId = null;


	/*
	 * init other vars
	 */
	claro_set_display_mode_available(true);
	$is_allowedToEdit = claro_is_allowed_to_edit();

	if( is_null($pageId) )
	{
		header("Location: ./index.php");
		exit();
	}
	else
	{
	    $page = new Page();

	    if( !$page->load($pageId) )
	    {
	        // required
	        header("Location: ../index.php");
	    	exit();
	    }
	}

	/*
	 * Output
	 */
	$cssLoader = CssLoader::getInstance();
    $cssLoader->load( 'clpages', 'screen');

	if( $is_allowedToEdit )
	{
		// we should not need any javascript for normal user
		// output stuff
	    $jsloader = JavascriptLoader::getInstance();
	    $jsloader->load('jquery');
	    $jsloader->load('jquery.interface');
	    $jsloader->load('jquery.livequery');
	    $jsloader->load('jquery.json');
	    $jsloader->load('jquery.form');
	    $jsloader->load('clpages');

		$cssLoader->load( 'clpages_admin', 'screen');

		$htmlHeaders = "\n"
		.    '<script type="text/javascript">' . "\n"
		.	 '  var cidReq = "'.claro_get_current_course_id().'";' . "\n"
		.	 '  var pageId = "'.$page->getId().'";' . "\n"
		.	 '  var moduleUrl = "'.get_module_url('CLPAGES').'/";' . "\n"
		.    '</script>' . "\n\n";

		$htmlHeaders .= "\n"
		.	 '<script type="text/javascript" src="'.get_path('url').'/claroline/editor/tiny_mce/tiny_mce.js" ></script>' . "\n"
		.	 '<script type="text/javascript" src="'.get_path('url').'/claroline/editor/tiny_mce/tiny_mce_init.js" ></script>' . "\n";


		$claroline->display->header->addHtmlHeader($htmlHeaders);
	}


   	$out = '';

	$interbredcrump[]= array ('url' => './index.php' . claro_url_relay_context('?'), 'name' => get_lang('Pages'));

   	$nameTools = get_lang('Edit page');

	$out .= claro_html_tool_title($nameTools)
   	.	 '<div id="pageContainer">' . "\n";

   	if( $is_allowedToEdit )
   	{
   		$pluginRegistry = pluginRegistry::getInstance();
   		$availablePlugins = $pluginRegistry->getList();

   		$out .= '<div id="pageSidebar">' . "\n"
   		.	 '<img src="'.get_module_url('CLPAGES').'/img/loading.gif" alt="'.get_lang('Loading...').'" id="loading" width="16" height="16" />' . "\n"
   		.	 '<strong>'.get_lang('Add a composant').'</strong>' . "\n"
   		.	 '<ul>' . "\n";

   		foreach( $availablePlugins as $pluginType => $pluginDetails )
		{
			$out .= '<li>'
			.	 '<a href="#" onclick="javascript:addItem(\''.$pluginType.'\'); return false;">'.$pluginDetails['displayName'].'</a>'
			.	 '</li>';
		}
   		$out .= '</ul>' . "\n"
   		.	 '</div>' . "\n";
   	}


	$out .= '<div id="componentsContainer" class="componentWrapper">' . "\n\n";

	$componentList = $page->getComponentList();

	foreach( $componentList as $component )
	{
		if( $component->isVisible() || $is_allowedToEdit )
		{
			$out .= $component->renderBlock();
		}
	}

	// componentsContainer
	$out .= '</div>' . "\n\n"
	.	 '<div class="spacer"></div>' . "\n"
	// pageContainer
   	.	 '</div>' . "\n";





	$claroline->display->body->appendContent($out);

    echo $claroline->display->render();
?>