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
 * @package CLAUTHOR
 *
 * @author Claroline team <info@claroline.net>
 *
 */
    // vim: expandtab sw=4 ts=4 sts=4 foldmethod=marker:

    // load Claroline kernel
    $tlabelReq = 'CLAUTHOR';
    require_once dirname(__FILE__) . '/../../claroline/inc/claro_init_global.inc.php';

	require_once dirname( __FILE__ ) . '/lib/clauthor.lib.php';
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

	if( isset($_REQUEST['docId']) && is_numeric($_REQUEST['docId']) )   $docId = (int) $_REQUEST['docId'];
	else                                                                $docId = null;

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
		$document = new document();

		if( !is_null($docId) )
		{
		    if( !$document->load($docId) )
		    {
		        $cmd = null;
		        $docId = null;
		    }
		}

		if( $cmd == 'exEdit' )
		{
			$document->setTitle($_REQUEST['title']);
			$document->setDescription($_REQUEST['description']);

			// set author id if creating doc
			if( is_null($docId) )
			{
				$document->setAuthorId( claro_get_current_user_id() );
				$document->setCreationTime( time() );
			}

			// on creation editor is the same as author, and the last modification is creation
			$document->setEditorId( claro_get_current_user_id() );
			$document->setLastModificationTime( time() );

			if( $document->validate() )
		    {
		        if( $insertedId = $document->save() )
		        {
		        	if( is_null($docId) )
		            {
		                $dialogBox->success( get_lang('Empty document successfully created') );
		                $docId = $insertedId;
		            }
		            else
		            {
		            	$dialogBox->success( get_lang('Document successfully modified') );
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
		        if( claro_failure::get_last_failure() == 'document_no_title' )
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

		    if( !is_null($docId) )
		    {
		    	$htmlEditForm .= '<strong>' . get_lang('Edit document settings') . '</strong>' . "\n";
		    }
		    else
		    {
		    	$htmlEditForm .= '<strong>' . get_lang('Create a new document') . '</strong>' . "\n";
		    }

		    $htmlEditForm .= '<form action="' . $_SERVER['PHP_SELF'] . '?docId='.$docId.'" method="post">' . "\n"
		    .    claro_form_relay_context()
		    .	 '<input type="hidden" name="claroFormId" value="'.uniqid('').'" />' . "\n"
		    .	 '<input type="hidden" name="cmd" value="exEdit" />' . "\n"

		    // title
		    .	 '<label for="title">' . get_lang('Title') . '</label>&nbsp;<span class="required">*</span><br />' . "\n"
		    .	 '<input type="text" name="title" id="title" maxlength="255" value="'.htmlspecialchars($document->getTitle()).'" /><br />' . "\n"
		    // description
		    .	 '<label for="title">' . get_lang('Description') . '</label><br />' . "\n"
		    .	 '<textarea name="description" id="description" cols="50" rows="5">'.htmlspecialchars($document->getDescription()).'</textarea><br />'

		    .	 '<span class="required">*</span>&nbsp;'.get_lang('Denotes required fields') . '<br />' . "\n"
		    .    '<input type="submit" value="' . get_lang('Ok') . '" />&nbsp;' . "\n"
		    .    claro_html_button($_SERVER['PHP_SELF'], get_lang('Cancel'))
		    .    '</form>' . "\n"
		    ;

		    $dialogBox->form($htmlEditForm);
		}

		if( $cmd == 'exDelete' )
	    {
	    	if( $document->delete() )
	    	{
	    		$dialogBox->success( get_lang('Document succesfully deleted') );
	    	}
	    	else
	    	{
	    		$dialogBox->error( get_lang('Fatal error : cannot delete') );
	    	}
	    }

	    if( $cmd == 'rqDelete' )
	    {
	        $htmlConfirmDelete = get_lang('Are you sure to delete document "%docTitle" ?', array('%docTitle' => htmlspecialchars($document->getTitle()) ))
			.	 '<br /><br />'
	        .    '<a href="' . $_SERVER['PHP_SELF'] . '?cmd=exDelete&amp;docId='.$docId.'">' . get_lang('Yes') . '</a>'
	        .    '&nbsp;|&nbsp;'
	        .    '<a href="' . $_SERVER['PHP_SELF'] . '">' . get_lang('No') . '</a>'
	        ;

	        $dialogBox->question( $htmlConfirmDelete );
	    }

		if( $cmd == 'exVisible' )
	    {
	    	$document->setVisible();

	    	$document->save();
	    }

	    if( $cmd == 'exInvisible' )
	    {
	    	$document->setInvisible();

	    	$document->save();
	    }
	}

	/*
	 * Output
	 */

	$cssLoader = CssLoader::getInstance();
    $cssLoader->load( 'clauthor', 'screen');
    // optional since we are using default display type
    $claroline->setDisplayType( CL_PAGE );

	$out = '';
	$htmlHeaders = '';

	$claroline->display->header->addHtmlHeader($htmlHeaders);

	$nameTools = get_lang('Authoring');

	$out .= claro_html_tool_title($nameTools);

	$out .= $dialogBox->render();

	$cmdMenu = array();
	if($is_allowedToEdit)
	{
	    $cmdMenu[] = claro_html_cmd_link('index.php?cmd=rqEdit'. claro_url_relay_context('&amp;'),get_lang('Create a new document'));
	}

	$out .= '<p>'
	.    claro_html_menu_horizontal( $cmdMenu )
	.    '</p>';

	$out .= '<table class="claroTable emphaseLine" width="100%" border="0" cellspacing="2">' . "\n"
	.    '<thead>' . "\n"
	.    '<tr class="headerX" align="center" valign="top">' . "\n"
	.	 '<th>' . get_lang('Document') . '</th>' . "\n"
	.	 '<th>' . get_lang('Creation date') . '</th>' . "\n";

	if( $is_allowedToEdit )
	{
	    $out .= '<th>' . get_lang('Modify') . '</th>' . "\n"
	    .    '<th>' . get_lang('Delete') . '</th>' . "\n"
	    .    '<th>' . get_lang('Visibility') . '</th>' . "\n";
	}

    $out .= '</tr>' . "\n"
    .    '</thead>' . "\n";

	// list display
	$listLoader = new docList();

	if( $is_allowedToEdit )
	{
		// load all documents
		$docListAray = $listLoader->load(true);
	}
	else
	{
		// load only visible documents
		$docListAray = $listLoader->load(false);
	}



	if( !empty($docListAray) && is_array($docListAray) )
    {
        $i = 0;
        $lpCount = count($docListAray);
        $totalProgress = 0;

        $out .= '<tbody>' . "\n";

        foreach( $docListAray as $aDoc )
        {
            $i++;
            $out .= '<tr>' . "\n";

            // title
            $out .= '<td>' . "\n"
            .    '<a href="doc.php?docId='.$aDoc['id'].'" title="'.htmlspecialchars(strip_tags($aDoc['description'])).'">'
            .    claro_html_icon('learnpath') . '&nbsp;'
            .    htmlspecialchars($aDoc['title'])
            .    '</a>' . "\n"
            .    '</td>' . "\n"

            .	 '<td align="center">' . "\n"
            .    $aDoc['creationTime']
            .    '</td>' . "\n";

			if( $is_allowedToEdit )
			{
				$out .= '<td align="center">' . "\n"
	            .    '<a href="' . $_SERVER['PHP_SELF'] . '?cmd=rqEdit&amp;docId='.$aDoc['id'].'">'
	            .    claro_html_icon('edit')
	            .    '</a>' . "\n"
	            .    '</td>' . "\n"

	            .	 '<td align="center">' . "\n"
	            .    '<a href="' . $_SERVER['PHP_SELF'] . '?cmd=rqDelete&amp;docId='.$aDoc['id'].'">'
	            .    claro_html_icon('delete')
	            .    '</a>' . "\n"
	            .    '</td>' . "\n";

				if( $aDoc['visibility'] == 'VISIBLE' )
				{
		            $out .= '<td align="center">' . "\n"
		            .    '<a href="' . $_SERVER['PHP_SELF'] . '?cmd=exInvisible&amp;docId='.$aDoc['id'].'">'
		            .    claro_html_icon('visible')
		            .    '</a>' . "\n"
		            .    '</td>' . "\n";
				}
				else
				{
					$out .= '<td align="center">' . "\n"
		            .    '<a href="' . $_SERVER['PHP_SELF'] . '?cmd=exVisible&amp;docId='.$aDoc['id'].'">'
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
        .    '<td align="center" colspan="3">' . get_lang('No documents') . '</td>' . "\n"
        .    '</tr>' . "\n"
        .    '</tfoot>' . "\n";
    }

	$out .= '</table>' . "\n";



	$claroline->display->body->appendContent($out);

    echo $claroline->display->render();
?>