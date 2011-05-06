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
try
{
    // vim: expandtab sw=4 ts=4 sts=4 foldmethod=marker:

    // load Claroline kernel
    $tlabelReq = 'CLPAGES';
    require_once dirname(__FILE__) . '/../../claroline/inc/claro_init_global.inc.php';
    
    claro_set_display_mode_available(true);
    
    FromKernel::uses(
        'utils/input.lib',
        'utils/validator.lib',
        'display/layout.lib'
    );
    
    From::Module('CLPAGES')->uses(
        'clpages.lib',
        'pluginRegistry.lib'
    );
    
    $userInput = Claro_UserInput::getInstance();
    
    if ( claro_is_allowed_to_edit() )
    {
        $userInput->setValidator('cmd', new Claro_Validator_AllowedList( array(
            'rqDelete', 'exDelete',
            'rqEdit', 'exEdit',
            'exVisible', 'exInvisible',
        ) ) );
    }
    
    $cmd = $userInput->get( 'cmd', null );

    $pageId = (int) $userInput->get( 'pageId', null );    

    $dialogBox = new DialogBox();


    /*
     * Admin only commands
     */
    if( claro_is_allowed_to_edit() )
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
            $page->setDisplayMode($_REQUEST['displayMode']);
            
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
            .     '<input type="hidden" name="claroFormId" value="'.uniqid('').'" />' . "\n"
            .     '<input type="hidden" name="cmd" value="exEdit" />' . "\n"

            // title
            .     '<label for="title">' . get_lang('Title') . '</label>&nbsp;<span class="required">*</span><br />' . "\n"
            .     '<input type="text" name="title" id="title" maxlength="255" value="'.htmlspecialchars($page->getTitle()).'" /><br />' . "\n"
            // description
            .     '<label for="description">' . get_lang('Description') . '</label><br />' . "\n"
            .     '<textarea name="description" id="description" cols="50" rows="5">'.htmlspecialchars($page->getDescription()).'</textarea><br />'
            // display mode
            .     get_lang('Display') . '&nbsp;<span class="required">*</span><br />' . "\n"
            .     '<input type="radio" name="displayMode" id="displayModePage" value="PAGE" '.($page->getDisplayMode() == 'PAGE' ?'checked="checked"':'').'>'
            .     '<label for="displayModePage">'.get_lang('One page').'</label><br />' . "\n"
            .     '<input type="radio" name="displayMode" id="displayModeSlide" value="SLIDE" '.($page->getDisplayMode() == 'SLIDE' ?'checked="checked"':'').'>'
            .     '<label for="displayModeSlide">'.get_lang('Slideshow').'</label>' . "\n"
            .     '<br /><br />'
            // end form
            .     '<span class="required">*</span>&nbsp;'.get_lang('Denotes required fields') . '<br />' . "\n"
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
            .     '<br /><br />'
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
    
    $pageList = new ModuleTemplate( 'CLPAGES', 'pagelist.tpl.php' );

    $cmdMenu = array();
    
    if(claro_is_allowed_to_edit())
    {
        $cmdMenu[] = claro_html_cmd_link('index.php?cmd=rqEdit'. claro_url_relay_context('&amp;'),get_lang('Create a new page'));
    }
    
    $pageList->assign( 'cmdMenu', $cmdMenu );

    $listLoader = new pageList();

    if( claro_is_allowed_to_edit() )
    {
        // all
        $pageList->assign( 'pageList', $listLoader->load(true) );
    }
    else
    {
        // only visible
        $pageList->assign( 'pageList', $listLoader->load(false) );
    }

    CssLoader::getInstance()->load( 'clpages', 'screen');
    
    $nameTools = get_lang('Pages');
    
    Claroline::getDisplay()->header->setTitle( $nameTools );
    ClaroBreadCrumbs::getInstance()->setCurrent( $nameTools );
    
    Claroline::getDisplay()->body->appendContent( claro_html_tool_title($nameTools) );
    Claroline::getDisplay()->body->appendContent( $dialogBox->render() );
    
    Claroline::getDisplay()->body->appendContent( $pageList->render() );

}
catch ( Exception $e )
{
    if ( claro_debug_mode() )
    {
        $dialogBox->error( '<pre>' . $e->__toString() . '</pre>' );
    }
    else
    {
        $dialogBox->error( $e->getMessage() );
    }
    
    // add the title of the module to the claroline page
    $nameTools = get_lang('Pages');
    Claroline::getDisplay()->body->appendcontent( claro_html_tool_title( $nameTools ) );

    // add the error message to the claroline page
    Claroline::getDisplay()->body->appendcontent( $dialogBox->render() );
}

echo Claroline::getDisplay()->render();
