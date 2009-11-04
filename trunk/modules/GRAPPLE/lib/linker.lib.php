<?php

class GRAPPLE_ResourceLinker extends ResourceLinker
{
    /**
     * Redefines renderLinkerBlock while ResourceLinker::renderLinkerBlock do not allow to pass 
     * bacend url as argument
     *
     * @return unknown
     */
    public static function renderLinkerBlock()
    {
        parent::init();
        
        // Init Client Side Linker
        JavascriptLoader::getInstance()->load('jquery.livequery');
        JavascriptLoader::getInstance()->load('claroline.linker');
        // init linkerFronted
        ClaroHeader::getInstance()->addInlineJavascript(
             'linkerFrontend.base_url = "'.get_module_url('GRAPPLE').'/backends/linker.php";' . "\n"
            .'linkerFrontend.deleteIconUrl = "'.get_icon_url('delete').'";'
            .'Claroline.lang["Attach"] = "'.get_lang('Attach').'";'
            .'Claroline.lang["Delete"] = "'.get_lang('Delete').'";'
        );
        CssLoader::getInstance()->load('linker', 'all');
        
        return '<div id="lnk_panel">' . "\n"
            . '<div id="lnk_ajax_loading"><img src="'.get_icon_url('loading').'" alt="" /></div>' . "\n"
            . '<div id="lnk_selected_resources"></div>' . "\n"
            . '<h4 id="lnk_location"></h4>' . "\n"
            . '<div id="lnk_back_link"></div>'
            . '<div id="lnk_resources"></div>' . "\n"
            . '<div id="lnk_hidden_fields"></div>' . "\n"
            . '</div>' . "\n\n"
            ;
    }
    
}
/*
class CLLP_ResourceLinkerNavigator extends ResourceLinkerNavigator
{
    public static function loadModuleNavigator( $moduleLabel )
    {
        $navigatorClass = 'CLLP_' . $moduleLabel . '_Navigator';
        
        if ( ! class_exists( $navigatorClass ) )
        {
            $navigatorPath = get_module_path( $moduleLabel ) . '/connector/cllp.linker.cnr.php';
            
            if ( file_exists( $navigatorPath ) )
            {
                include_once $navigatorPath;
            }
        }
        
        if ( class_exists( $navigatorClass ) )
        {
            $navigator = new $navigatorClass();
            
            return $navigator;
        }
        
        return false;
    }
    
    public function isNavigable( $locator )
    {
        if ( $locator instanceof ExternalResourceLocator )
        {
            return false;
        }
        
        if ( $locator->inModule() )
        {
            if ( $navigator = self::loadModuleNavigator( $locator->getModuleLabel() ) )
            {
                return $navigator->isNavigable( $locator );
            }
            else
            {
                return false;
            }
        }
        else
        {
            if ( $locator->inGroup() )
            {
                $navigator = new GroupNavigator;
                
                return $navigator->isNavigable( $locator );
            }
            elseif ( $locator->inCourse() )
            {
                $navigator = new CourseNavigator;
                
                return $navigator->isNavigable( $locator );
            }
            else
            {
                return false;
            }
        }
    }
}
*/
?>