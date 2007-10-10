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

	/*
	 * init request vars
	 */
	$acceptedCmdList = array(   'rqDelete', 'exDelete',
	                            'rqEdit', 'exEdit',
	                            'exVisible', 'exInvisible',
	                    );

	if( isset($_REQUEST['cmd']) && in_array($_REQUEST['cmd'], $acceptedCmdList) )   $cmd = $_REQUEST['cmd'];
	else                                                                            $cmd = null;

	if( isset($_REQUEST['pageId']) && is_numeric($_REQUEST['pageId']) )   	$pageId = (int) $_REQUEST['pageId'];
	else                                                                	$pageId = null;

	/*
	 * Init other vars
	 */
	claro_set_display_mode_available(true);

	$is_allowedToEdit = claro_is_allowed_to_edit();

	$dialogBox = new DialogBox();


	/*
	 * Admin only commands
	 */
	if( $is_allowedToEdit )
	{
		$page = new page();

		if( !is_null($pageId) )
		{
		    if( !$page->load($pageId) )
		    {
		        $cmd = null;
		        $pageId = null;
		    }
		}

		if( $cmd == 'exEdit' )
		{
			$page->setTitle($_REQUEST['title']);
			$page->setDescription($_REQUEST['description']);

			// set author id if creation
			if( is_null($pageId) )
			{
				$page->setAuthorId( claro_get_current_user_id() );
				$page->setCreationTime( time() );
			}

			// on creation editor is the same as author, and the last modification is creation
			$page->setEditorId( claro_get_current_user_id() );
			$page->setLastModificationTime( time() );

			if( $page->validate() )
		    {
		        if( $insertedId = $page->save() )
		        {
		        	if( is_null($pageId) )
		            {
		                $dialogBox->success( get_lang('Empty page successfully created') );
		                $pageId = $insertedId;
		            }
		            else
		            {
		            	$dialogBox->success( get_lang('Page successfully modified') );
		            }
		        }
		        else
		        {
		            // sql error in save() ?
		            $cmd = 'rqEdit';
		        }

		    }
		    else
		    {
		        if( claro_failure::get_last_failure() == 'page_no_title' )
		        {
		            $dialogBox->error( get_lang('Field \'%name\' is required', array('%name' => get_lang('Title'))) );
		        }
		        $cmd = 'rqEdit';
		    }
		}

		if( $cmd == 'rqEdit' )
		{
			// show form
		    $htmlEditForm = "\n\n";

		    if( !is_null($pageId) )
		    {
		    	$htmlEditForm .= '<strong>' . get_lang('Edit page settings') . '</strong>' . "\n";
		    }
		    else
		    {
		    	$htmlEditForm .= '<strong>' . get_lang('Create a new page') . '</strong>' . "\n";
		    }

		    $htmlEditForm .= '<form action="' . $_SERVER['PHP_SELF'] . '?pageId='.$pageId.'" method="post">' . "\n"
		    .    claro_form_relay_context()
		    .	 '<input type="hidden" name="claroFormId" value="'.uniqid('').'" />' . "\n"
		    .	 '<input type="hidden" name="cmd" value="exEdit" />' . "\n"

		    // title
		    .	 '<label for="title">' . get_lang('Title') . '</label>&nbsp;<span class="required">*</span><br />' . "\n"
		    .	 '<input type="text" name="title" id="title" maxlength="255" value="'.htmlspecialchars($page->getTitle()).'" /><br />' . "\n"
		    // description
		    .	 '<label for="title">' . get_lang('Description') . '</label><br />' . "\n"
		    .	 '<textarea name="description" id="description" cols="50" rows="5">'.htmlspecialchars($page->getDescription()).'</textarea><br />'

		    .	 '<span class="required">*</span>&nbsp;'.get_lang('Denotes required fields') . '<br />' . "\n"
		    .    '<input type="submit" value="' . get_lang('Ok') . '" />&nbsp;' . "\n"
		    .    claro_html_button($_SERVER['PHP_SELF'], get_lang('Cancel'))
		    .    '</form>' . "\n"
		    ;

		    $dialogBox->form($htmlEditForm);
		}

		if( $cmd == 'exDelete' )
	    {
	    	if( $page->delete() )
	    	{
	    		$dialogBox->success( get_lang('Page succesfully deleted') );
	    	}
	    	else
	    	{
	    		$dialogBox->error( get_lang('Fatal error : cannot delete') );
	    	}
	    }

	    if( $cmd == 'rqDelete' )
	    {
	        $htmlConfirmDelete = get_lang('Are you sure to delete page "%pageTitle" ?', array('%pageTitle' => htmlspecialchars($page->getTitle()) ))
			.	 '<br /><br />'
	        .    '<a href="' . $_SERVER['PHP_SELF'] . '?cmd=exDelete&amp;pageId='.$pageId.'">' . get_lang('Yes') . '</a>'
	        .    '&nbsp;|&nbsp;'
	        .    '<a href="' . $_SERVER['PHP_SELF'] . '">' . get_lang('No') . '</a>'
	        ;

	        $dialogBox->question( $htmlConfirmDelete );
	    }

		if( $cmd == 'exVisible' )
	    {
	    	$page->setVisible();

	    	$page->save();
	    }

	    if( $cmd == 'exInvisible' )
	    {
	    	$page->setInvisible();

	    	$page->save();
	    }
	}

	/*
	 * Output
	 */

	$cssLoader = CssLoader::getInstance();
    $cssLoader->load( 'clpages', 'screen');

	$out = '';
	$htmlHeaders = '';

	$claroline->display->header->addHtmlHeader($htmlHeaders);

	$nameTools = get_lang('Pages');

	$out .= claro_html_tool_title($nameTools);

	$out .= $dialogBox->render();

	$cmdMenu = array();
	if($is_allowedToEdit)
	{
	    $cmdMenu[] = claro_html_cmd_link('index.php?cmd=rqEdit'. claro_url_relay_context('&amp;'),get_lang('Create a new page'));
	}

	$out .= '<p>'
	.    claro_html_menu_horizontal( $cmdMenu )
	.    '</p>';

	$out .= '<table class="claroTable emphaseLine" width="100%" border="0" cellspacing="2">' . "\n"
	.    '<thead>' . "\n"
	.    '<tr class="headerX" align="center" valign="top">' . "\n"
	.	 '<th>' . get_lang('Page') . '</th>' . "\n"
	.	 '<th>' . get_lang('Creation date') . '</th>' . "\n";

	if( $is_allowedToEdit )
	{
	    $out .= '<th>' . get_lang('Modify') . '</th>' . "\n"
	    .    '<th>' . get_lang('Delete') . '</th>' . "\n"
	    .    '<th>' . get_lang('Visibility') . '</th>' . "\n";
	}

    $out .= '</tr>' . "\n"
    .    '</thead>' . "\n";


	$listLoader = new pageList();

	if( $is_allowedToEdit )
	{
		// all
		$pageListAray = $listLoader->load(true);
	}
	else
	{
		// only visible
		$pageListAray = $listLoader->load(false);
	}



	if( !empty($pageListAray) && is_array($pageListAray) )
    {
        $i = 0;
        $lpCount = count($pageListAray);
        $totalProgress = 0;

        $out .= '<tbody>' . "\n";

        foreach( $pageListAray as $aPage )
        {
            $i++;
            $out .= '<tr>' . "\n";

            // title
            $out .= '<td>' . "\n"
            .    '<a href="page.php?pageId='.$aPage['id'].'" title="'.htmlspecialchars(strip_tags($aPage['description'])).'">'
            .    claro_html_icon('learnpath') . '&nbsp;'
            .    htmlspecialchars($aPage['title'])
            .    '</a>' . "\n"
            .    '</td>' . "\n"

            .	 '<td align="center">' . "\n"
            .    $aPage['creationTime']
            .    '</td>' . "\n";

			if( $is_allowedToEdit )
			{
				$out .= '<td align="center">' . "\n"
	            .    '<a href="' . $_SERVER['PHP_SELF'] . '?cmd=rqEdit&amp;pageId='.$aPage['id'].'">'
	            .    claro_html_icon('edit')
	            .    '</a>' . "\n"
	            .    '</td>' . "\n"

	            .	 '<td align="center">' . "\n"
	            .    '<a href="' . $_SERVER['PHP_SELF'] . '?cmd=rqDelete&amp;pageId='.$aPage['id'].'">'
	            .    claro_html_icon('delete')
	            .    '</a>' . "\n"
	            .    '</td>' . "\n";

				if( $aPage['visibility'] == 'VISIBLE' )
				{
		            $out .= '<td align="center">' . "\n"
		            .    '<a href="' . $_SERVER['PHP_SELF'] . '?cmd=exInvisible&amp;pageId='.$aPage['id'].'">'
		            .    claro_html_icon('visible')
		            .    '</a>' . "\n"
		            .    '</td>' . "\n";
				}
				else
				{
					$out .= '<td align="center">' . "\n"
		            .    '<a href="' . $_SERVER['PHP_SELF'] . '?cmd=exVisible&amp;pageId='.$aPage['id'].'">'
		            .    claro_html_icon('invisible')
		            .    '</a>' . "\n"
		            .    '</td>' . "\n";
				}
			}

            $out .=  '</tr>' . "\n\n";
        }
    }
    else
    {
        $out .= '<tfoot>' . "\n"
        .    '<tr>' . "\n"
        .    '<td align="center" colspan="3">' . get_lang('No pages') . '</td>' . "\n"
        .    '</tr>' . "\n"
        .    '</tfoot>' . "\n";
    }

	$out .= '</table>' . "\n";



	$claroline->display->body->appendContent($out);

    echo $claroline->display->render();
?>