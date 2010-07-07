<?php // $Id$

// vim: expandtab sw=4 ts=4 sts=4:

/**
 * Administration script for Terms Of Use module, can be access through the
 * page of the icterms module in the platform administration
 *
 * @version     1.0 $Revision$
 * @copyright   2001-2010 Universite catholique de Louvain (UCL)
 * @author      Frederic Minne <zefredz@claroline.net>
 * @license     http://www.gnu.org/copyleft/gpl.html
 *              GNU GENERAL PUBLIC LICENSE version 2 or later
 * @package     icterms
 */

try
{
    //Load claroline kernel
    // $tlabelReq = 'ICTERMS';
    $cidReset = true;
    $cidReq = false;
    
    $_GLOBALS['icterms_page_allowed'] = true;
    
    require_once dirname( __FILE__ ) . '/../../claroline/inc/claro_init_global.inc.php';
    
    language::load_module_translation('ICTERMS');
    load_module_config('ICTERMS');
    
    FromKernel::uses('utils/input.lib','utils/validator.lib');
    
    require_once dirname(__FILE__).'/lib/icterms.lib.php';
    
    if ( ! claro_is_platform_admin() )
    {
        claro_die(get_lang('Not allowed'));
    }
    
    // Allow to edit the textzone
    $userInput = Claro_UserInput::getInstance();
    $dialogBox = new DialogBox;
    
    $cmd = $userInput->get('cmd','rqEditTermsOfUse');
    $touContents = trim( $userInput->get('touContents') );
    
    switch ( $cmd )
    {
        case 'exEditTemsOfUse':
            $confirmEmpty = ('true' == $userInput->get('confirmEmpty','false'));
                
            if( $confirmEmpty )
            {
                icterms_delete_terms_contents();
                $touContents = icterms_get_terms_contents();
                $dialogBox->success(get_lang('Terms of use reset to platform defaults'));
            }
            else
            {
                if ( !empty( $touContents ) )
                {
                    icterms_put_terms_contents($touContents);
                    $dialogBox->success(get_lang('Terms of use changed successfuly'));
                }
                else
                {
                    
                    $dialogBox->error(get_lang('Cannot change terms of use : terms of use empty'));
                    $dialogBox->info(get_lang('If you want to reset the terms of use to the platform defaults, check the box at the bottom of the form'));
                }
            }
            // no break;
        default:
            $dialogBox->info(get_lang('You can set the terms of use of your campus in the form below'));
            
            if ( empty($touContents) )
            {
                $touContents = icterms_get_terms_contents();
            }
            
            $tpl = new ModuleTemplate('ICTERMS','toueditform.tpl.php');
            $tpl->assign('touContents', $touContents );
            
            $dialogBox->form($tpl->render());
            
            break;
    }
    
    Claroline::getDisplay()->body->appendContent($dialogBox->render());
    
    echo Claroline::getDisplay()->render();
}
catch (Exception $e)
{
    if ( !claro_debug_mode() )
    {
        claro_die( $e->getMessage() );
    }
    else
    {
        claro_die( $e->__toString() );
    }
}
