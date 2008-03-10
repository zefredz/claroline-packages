<?php
try
{
    $nameTools = get_lang('Send mail to users');
    // load Claroline kernel
    require dirname(__FILE__) . '/../../claroline/inc/claro_init_global.inc.php';

    if ( ! claro_is_platform_admin() )
    {
        claro_die('Not allowed');
    }
    
    require_once dirname(__FILE__) . '/lib/userinput.lib.php';
    require_once dirname(__FILE__) . '/lib/inputfilters.lib.php';
    require_once dirname(__FILE__) . '/lib/form.lib.php';
    
    $userInput = FilteredUserInput::getInstance();
    
    $allowedCommands = array( 'compose', 'checkBefore', 'send' );
    $defaultCommand = 'compose';
    
    $allowedAddressees = array( 'all', 'creators', 'managers', 'nocourse', 'admin' );
    $defaultAddressee = 'admin';
    
    $userInput->setFilter( 
        'cmd', 
        array( new AllowedValueListFilter( $allowedCommands ), 'isValid' )
    );
    
    $userInput->setFilter( 
        'addressee', 
        array( new AllowedValueListFilter( $allowedAddressees ), 'isValid' )
    );
    
    // get user variables
    $cmd = $userInput->get( 'cmd', $defaultCommand );
    $cmd = $userInput->get( 'addressee', $defaultAddressee );
    
    if ( $cmd == 'checkBefore' || $cmd == 'send' )
    {
        $userInput->setFilter( 
            'subject', 
            array( new NotEmptyFilter(), 'isValid' )
        );
        
        $userInput->setFilter( 
            'message', 
            array( new NotEmptyFilter(), 'isValid' )
        );
        
        $subject = $userInput->getMandatory('subject');
        $message = $userInput->getMandatory('message');
    }
    else
    {
        $subject = $userInput->get('subject','');
        $message = $userInput->get('message','');
    }
    
    if ( $cmd == 'compose' )
    {
        $optionList = array();
        $optionList['nocourse'] = get_lang('Users with no course');
        $optionList['admin'] = get_lang('Administrators');
        $optionList['managers'] = get_lang('Course managers');
        // $optionList['todelete'] = get_lang('Users marked to delete');
        $optionList['creators'] = get_lang('Course creators');
        $optionList['all'] = get_lang('All users');
        
        $form = new Form;
        $form->addElement( new InputHidden( 'cmd', 'checkBefore' ) );
        $input = new InputText( 'subject', htmlspecialchars($subject) );
        $input->setLabel( get_lang( 'Subject' )  . ' : ' );
        $form->addElement( $input, true );
        $text = new Textarea( 'message', $message, 80, 30 );
        $text->setLabel( get_lang( 'Message' )  . ' : ' );
        $form->addElement( $text, true );
        $select = SelectBox::fromArray( 'adressee', $optionList, $addressee );
        $select->setLabel( get_lang( 'Addressee' )  . ' : ' );
        $form->addElement( $select, true );
        $form->addElement( new InputSubmit( 'submit', get_lang('Check before sending') ) );
        $form->addElement( new InputCancel( 'cancel', get_lang('Cancel'), $_SERVER['PHP_SELF'] ) );
    }

// <------------------------------- CONTINUE FROM HERE

    require_once get_path('includePath') . '/claro_init_header.inc.php';
    
    echo claro_html_tool_title( $nameTools );
    
    if ( isset( $message ) )
    {
        echo claro_html_message_box( '<p>'.$message.'</p>' );
    }
    
    if ( $cmd == 'compose' )
    {
        echo $form->render();
    }
    
    require_once get_path('includePath') . '/claro_init_footer.inc.php';
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
?>