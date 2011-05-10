<?php // $Id$

/**
 * CLAROLINE
 * $Revision$
 * @copyright (c) 2001-2011 Universite catholique de Louvain (UCL)
 * @license http://www.gnu.org/copyleft/gpl.html (GPL) GENERAL PUBLIC LICENSE
 * @package CLPAGES
 * @author Claroline team <info@claroline.net>
 */

// vim: expandtab sw=4 ts=4 sts=4 foldmethod=marker:

try
{

    // load Claroline kernel
    $tlabelReq = 'CLPAGES';
    require_once dirname(__FILE__) . '/../../claroline/inc/claro_init_global.inc.php';
    
    $nameTools = get_lang('Pages');
    claro_set_display_mode_available(true);
    
    FromKernel::uses(
        'utils/input.lib',
        'utils/validator.lib'
    );
    
    From::Module('CLPAGES')->uses(
        'clpages.lib',
        'pluginRegistry.lib'
    );
    
    // load and register all plugins
    $pluginRegistry = pluginRegistry::getInstance();
    
    $userInput = Claro_UserInput::getInstance();

    $pageId = (int) $userInput->get( 'pageId', null );
    
    $slide = (int) $userInput->get( 'slide', 1 );
    
    if ( $slide < 1 )
    {
        $slide = 1;
    }

    if ( !$pageId )
    {
        header("Location: ./index.php");
        exit();
    }
    else
    {
        $page = new Page();

        if (!$page->load($pageId))
        {
            // required
            header("Location: ../index.php");
            exit();
        }
    }

    /*
     * Output
     */
    CssLoader::getInstance()->load('clpages', 'screen');

    if (claro_is_allowed_to_edit())
    {
        // we should not need any javascript for normal user
        // output stuff
        JavascriptLoader::getInstance()->load('jquery');
        //JavascriptLoader::getInstance()->load('jquery.interface');
        JavascriptLoader::getInstance()->load('jquery.livequery');
        JavascriptLoader::getInstance()->load('jquery.json');
        JavascriptLoader::getInstance()->load('jquery.form');
        JavascriptLoader::getInstance()->load('claroline');
        JavascriptLoader::getInstance()->load('clpages');

        CssLoader::getInstance()->load('clpages_admin', 'screen');

        $htmlHeaders = "\n"
            . '<script type="text/javascript">' . "\n"
            . '  var cidReq = "' . claro_get_current_course_id() . '";' . "\n"
            . '  var pageDisplayMode ="' . $page->getDisplayMode() . '";' . "\n"
            . '  var pageId = "' . $page->getId() . '";' . "\n"
            . '  var moduleUrl = "' . get_module_url('CLPAGES') . '/";' . "\n"
            . '</script>' . "\n\n";

        // do not work at this time with jsloader
        $htmlHeaders .= "\n"
            . '<script type="text/javascript" src="' . get_path('url') . '/claroline/editor/tiny_mce/tiny_mce/tiny_mce.js" ></script>' . "\n"
            . '<script language="javascript" type="text/javascript">' . "\n"
            . 'var text_dir = "' . get_locale("text_dir") . '";' . "\n"
            . 'var mimeTexURL = "' . get_conf('claro_texRendererUrl') . '";' . "\n"
            . '</script>' . "\n\n"
            . '<script type="text/javascript" src="' . get_path('url') . '/claroline/editor/tiny_mce/advanced.conf.js" ></script>' . "\n";


        Claroline::getDisplay()->header->addHtmlHeader($htmlHeaders);
    }
    elseif ($page->getDisplayMode() == 'SLIDE')
    {
        JavascriptLoader::getInstance()->load('jquery');
        JavascriptLoader::getInstance()->load('slide');
    }

    // the real thing starts here

    $out = '';

    $out .= claro_html_tool_title($page->getTitle())
        . '<div id="pageContainer">' . "\n";

    // edition menu
    if (claro_is_allowed_to_edit())
    {
        $pluginRegistry = pluginRegistry::getInstance();
        
        $availablePlugins = $pluginRegistry->getList();
        
        $plugins = array ();
        
        // sort by category
        foreach ($availablePlugins as $type => $details)
        {
            $plugins[$details['category']][$type] = $details;
        }

        ksort($plugins);

        $out .= '<div id="pageSidebar">' . "\n"
            . '<img src="' . get_icon_url('loading') . '" alt="' 
            . get_lang('Loading...') . '" id="loading" width="16" height="16" />' . "\n"
            . '<strong>' . get_lang('Add a composant') . '</strong>' . "\n"
            ;

        foreach ($plugins as $category => $categoryPlugins)
        {
            if ( !empty( $category ) )
            {
                $out .= '<p class="pluginCategory">' . ucfirst(strtolower($category)) . '</>' . "\n";
            }
            
            $out .= '<ul class="pluginList">' . "\n";

            foreach ($categoryPlugins as $type => $pluginDetails)
            {
                $img = '';

                if (!empty($pluginDetails['img']))
                {
                    $iconUrl = get_icon_url($pluginDetails['img']);

                    if (!is_null($iconUrl))
                    {
                        $img = 'style="background: url(' . $iconUrl . ') center right no-repeat; "';
                    }
                }

                $out .= '<li ' . $img . '>'
                    . '<a href="#" onclick="javascript:addComponent(\'' . $type . '\');return false;">'
                    . $pluginDetails['displayName']
                    . '</a>'
                    . '</li>'
                    ;
            }

            $out .= '</ul>' . "\n";
        }
        $out .= '</div>' . "\n";
    }

    //add s5 slide show path

    if ($page->getDisplayMode() == 'SLIDE')
    {
        $out .= '<span>
        <a href="'. htmlspecialchars(Url::Contextualize(get_module_url('CLPAGES')
            . '/lib/s5/s5.php?pageId='.$page->getId() ) ) . '" rel="popup">' 
            . claro_html_icon('slide') . '&nbsp;'
            . get_lang('Run the slides show') 
            . '</a>
        </span>
        ';
    }

    $out .= '<div id="componentsContainer" class="componentWrapper">' . "\n\n";

    $componentList = $page->getComponentList();

    if ($page->getDisplayMode() == 'SLIDE' && !claro_is_allowed_to_edit())
    {
        // Nothing to display
    }
    else
    {
        $componentTemplate = new ModuleTemplate( 'CLPAGES', 'component.tpl.php' );
        // page view
        foreach ( $componentList as $component )
        {
            if ($component->isVisible() || claro_is_allowed_to_edit())
            {
                $componentTemplate->assign( 'component', $component );
                $out .= $componentTemplate->render();
            }
            else
            {
                // do not display block
            }
        }
    }



    $out .= '</div>' . "\n\n" // componentsContainer
        . '<div class="spacer"></div>' . "\n"
        . '</div>' . "\n\n" // pageContainer
        ;

    Claroline::getDisplay()->body->appendContent($out);
    
    Claroline::getDisplay()->header->setTitle( get_lang('Pages') . ' - ' . $page->getTitle() );

    ClaroBreadCrumbs::getInstance()->setCurrent(
        get_lang('Pages'), Url::Contextualize(get_module_entry_url('CLPAGES'))
    );

    if (claro_is_allowed_to_edit())
    {
        ClaroBreadCrumbs::getInstance()->append(
            $page->getTitle()
        );
    }
    else
    {
        ClaroBreadCrumbs::getInstance()->append(
            $page->getTitle()
        );
    }

    echo Claroline::getDisplay()->render();
}
catch (Exception $e)
{
    if (claro_debug_mode())
    {
        $dialogBox->error('<pre>' . $e->__toString() . '</pre>');
    }
    else
    {
        $dialogBox->error($e->getMessage());
    }
    
    Claroline::getDisplay()->body->appendcontent(claro_html_tool_title(get_lang('Pages')));

    // add the error message to the claroline page
    Claroline::getDisplay()->body->appendcontent($dialogBox->render());
    
    ClaroBreadCrumbs::getInstance()->setCurrent(
        get_lang('Pages'), Url::Contextualize(get_module_entry_url('CLPAGES'))
    );
    
    Claroline::getDisplay()->header->setTitle( get_lang('Pages') );
    
    echo Claroline::getDisplay()->render();
}
