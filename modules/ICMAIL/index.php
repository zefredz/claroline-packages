<?php // $Id$

// vim: expandtab sw=4 ts=4 sts=4:

/**
 * Description
 *
 * @version     1.1 $Revision$
 * @copyright   2001-2010 Universite catholique de Louvain (UCL)
 * @author      Claroline Team <info@claroline.net>
 * @author      Frederic Minne <zefredz@claroline.net>
 * @license     http://www.gnu.org/copyleft/gpl.html
 *              GNU GENERAL PUBLIC LICENSE version 2 or later
 * @package     ICMAIL
 */

try
{
    
    // load Claroline kernel
    require dirname(__FILE__) . '/../../claroline/inc/claro_init_global.inc.php';
    $nameTools = get_lang('Send mail to users');

    if ( ! claro_is_platform_admin() )
    {
        claro_die('Not allowed');
    }
    
    FromKernel::uses('utils/input.lib', 'utils/validator.lib', 'sendmail.lib');
    require_once dirname(__FILE__) . '/lib/icmail.lib.php';
    
    $userInput = Claro_UserInput::getInstance();
    $dialogBox = new DialogBox();
    
    $allowedCommands = array( 'compose', 'send' );
    $defaultCommand = 'compose';
    
    $allowedAddressees = array(
        // 'all',
        'creators',
        'managers',
        'publicmanagers',
        // 'nocourse',
        'admin' );
    
    $defaultAddressee = 'admin';
    
    $userInput->setValidator( 
        'cmd', 
        new Claro_Validator_AllowedList( $allowedCommands )
    );
    
    $userInput->setValidator( 
        'addressee', 
        new Claro_Validator_AllowedList( $allowedAddressees )
    );
    
    // get user variables
    try
    {
        $cmd = $userInput->get( 'cmd', $defaultCommand );
    }
    catch ( Exception $e )
    {
        $dialogBox->error('Invalid command');
    }
    
    try
    {
        $addressee = $userInput->get( 'addressee', $defaultAddressee );
    }
    catch ( Exception $e )
    {
        $dialogBox->error('Invalid addressee');
        $addressee = $defaultAddressee;
        $cmd = 'compose';
    }
    
    if ( $cmd == 'send' )
    {
        try
        {
            $userInput->setValidator( 
                'subject', 
                new Claro_Validator_NotEmpty()
            );
            
            $subject = $userInput->getMandatory('subject');
        }
        catch ( Exception $e )
        {
            $dialogBox->error('Missing subject');
        }
        
        try
        {
            $userInput->setValidator( 
                'message', 
                new Claro_Validator_NotEmpty()
            );
            
            $message = $userInput->getMandatory('message');
        }
        catch ( Exception $e )
        {
            $dialogBox->error('Missing message');
        }
        
        $copyToAdmin = $userInput->get('copyToAdmin');
    }
    else
    {
        $subject = $userInput->get('subject','');
        $message = $userInput->get('message','');
    }
    
    if ( $cmd == 'compose' )
    {
        $optionList = array();
        // $optionList['nocourse'] = get_lang('Users with no course');
        $optionList['admin'] = get_lang('Administrators');
        $optionList['managers'] = get_lang('Course managers');
        $optionList['publicmanagers'] = get_lang('Course managers of public courses');
        // $optionList['todelete'] = get_lang('Users marked to delete');
        $optionList['creators'] = get_lang('Course creators');
        // $optionList['all'] = get_lang('All users');
        
        $form = new PhpTemplate( dirname(__FILE__) . '/templates/mailform.tpl.php' );
        $form->assign( 'subject', $subject );
        $form->assign( 'message', $message );
        $form->assign( 'addresseeTypeList', $optionList );
        $form->assign( 'selectedAddressee', $addressee );
    }
    
    if ( $cmd == 'send' )
    {
        $userList = ICMAIL::getUserList( $addressee );
        
        if ( count( $userList ) )
        {
            ICMAIL::sendHtmlMailToList( $userList, $message, $subject, get_conf('administrator_email'), get_conf('administrator_name') );
            
            // send copy to admin
            
            if ( $addressee != 'admin' && $copyToAdmin )
            {
                ICMAIL::sendHtmlMailToUser( get_conf('administrator_email'), get_conf('administrator_name'), $message, $subject, get_conf('administrator_email'), get_conf('administrator_name') );
            }
            
            $dialogBox->success('Message sent');
            
            $form = new PhpTemplate( dirname(__FILE__) . '/templates/mailhiddenform.tpl.php' );
            $form->assign( 'subject', $subject );
            $form->assign( 'message', $message );
            $form->assign( 'addressee', $addressee );
            
            $dialogBox->form( $form->render() );
        }
        else
        {
            $dialogBox->error('No user found');
        }
    }
    
    // DISPLAY

    Claroline::getInstance()->display->body->appendContent( claro_html_tool_title( $nameTools ) );
    Claroline::getInstance()->display->body->appendContent( $dialogBox->render() );
    
    if ( $cmd == 'compose' )
    {
        Claroline::getInstance()->display->body->appendContent( $form->render() );
    }
    
    echo Claroline::getInstance()->display->render();
}
catch ( Exception $e )
{
    if ( claro_debug_mode() )
    {
        claro_die( '<pre>' . $e->__toString() . '</pre>' );
    }
    else
    {
        claro_die( $e->getMessage() );
    }
}
