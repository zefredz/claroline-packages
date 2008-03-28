<?php // $Id$

    // vim: expandtab sw=4 ts=4 sts=4:
    
    /**
     * Description
     *
     * @version     1.8-backport $Revision$
     * @copyright   2001-2007 Universite catholique de Louvain (UCL)
     * @author      Frederic Minne <zefredz@claroline.net>
     * @license     http://www.gnu.org/copyleft/gpl.html
     *              GNU GENERAL PUBLIC LICENSE version 2 or later
     * @package     icprint
     */
try
{
    $nameTools = 'Service key administration';    
    // load Claroline kernel
    require dirname(__FILE__) . '/../../claroline/inc/claro_init_global.inc.php';
    
    if ( ! claro_is_platform_admin() )
    {
        claro_disp_auth_form();
    }
    
    if ( function_exists( 'sqlite_factory' ) )
    {
        require_once dirname(__FILE__) . '/keyring/keyring-sqlite.lib.php';
    }
    else
    {
        require_once dirname(__FILE__) . '/keyring/keyring-csv.lib.php';
    }
    
    require_once dirname(__FILE__) . '/lib/request/userinput.lib.php';
    require_once dirname(__FILE__) . '/lib/request/inputfilters.lib.php';
    require_once dirname(__FILE__) . '/lib/html/form.lib.php';
    
    require_once dirname(__FILE__) . '/lib/datagrid.lib.php';
    
    $keyring = Keyring::getInstance();
    
    $userInput = FilteredUserInput::getInstance();
    
    $allowedCommandList = array( 'list'
        , 'rqAdd', 'exAdd'
        , 'rqDelete', 'exDelete'
        , 'rqEdit', 'exEdit' );
        
    $userInput->setFilter( 
        'cmd', 
        array( new AllowedValueListFilter( $allowedCommandList ), 'isValid' ) 
    );
        
    $cmd = $userInput->get( 'cmd', 'list' ); 
    
    if ( 'rqDelete' == $cmd )
    {
        $serviceName = $userInput->getMandatory( 'serviceName' );
        $serviceHost = $userInput->getMandatory( 'serviceHost' );
        
        if ( empty ( $serviceName ) || empty ( $serviceHost ) )
        {
            throw new Exception("Missing service name or key !");
        }
        
        $form = new Form;
        $form->addElement( new InputHidden( 'cmd', 'exDelete' ) );
        $form->addElement( new InputHidden( 'serviceName', htmlspecialchars($serviceName) ) );
        $form->addElement( new InputHidden( 'serviceHost', htmlspecialchars($serviceHost) ) );
        $form->addElement( new Label( 'Delete service "'.htmlspecialchars($serviceName).'" ?', 'submit') , true );
        $form->addElement( new InputSubmit( 'submit', get_lang('Yes') ) );
        $form->addElement( new InputCancel( 'cancel', get_lang('No'), $_SERVER['PHP_SELF'] ) );
    }
    
    if ( 'exDelete' == $cmd )
    {
        $serviceName = $userInput->getMandatory( 'serviceName' );
        $serviceHost = $userInput->getMandatory( 'serviceHost' );
        
        if ( empty ( $serviceName ) || empty ( $serviceHost ) )
        {
            throw new Exception("Missing service name or host !");
        }
        
        $keyring->delete( $serviceName, $serviceHost );
        
        $message = 'service ' . htmlspecialchars($serviceName) 
            . ':' . htmlspecialchars($serviceHost) . ' deleted';
        
        $cmd = 'list';
    }
    
    if ( 'rqEdit' == $cmd )
    {
        $serviceName = $userInput->getMandatory( 'serviceName' );
        $serviceHost = $userInput->getMandatory( 'serviceHost' );
        
        if ( empty ( $serviceName ) || empty ( $serviceHost ) )
        {
            throw new Exception("Missing service name or host !");
        }
        
        $service = $keyring->get( $serviceName, $serviceHost );
    }
    
    if ( 'rqAdd' == $cmd )
    {
        $service = array( 
            'serviceName' => '', 
            'serviceHost' => '', 
            'serviceKey' => '' 
        );
    }
    
    if ( 'rqEdit' == $cmd || 'rqAdd' == $cmd )
    {
        $form = new Form;
        $input = new InputText( 'serviceName', htmlspecialchars($service['serviceName']) );
        $input->setLabel( get_lang( 'Service' )  . ':' );
        $form->addElement( $input, true );
        $input = new InputText( 'serviceHost', htmlspecialchars($service['serviceHost']) );
        $input->setLabel( get_lang( 'Host address' )  . ':' );
        $form->addElement( $input, true );
        $input = new InputText( 'serviceKey', htmlspecialchars($service['serviceKey']) );
        $input->setLabel( get_lang( 'serviceKey' ) . ':' );
        $form->addElement( $input, true );
        
        if ( 'rqEdit' == $cmd )
        {
            $form->addElement( new InputHidden( 'oldServiceName', htmlspecialchars($serviceName) ) );
            $form->addElement( new InputHidden( 'oldServiceHost', htmlspecialchars($serviceHost) ) );
        }
        
        $form->addElement( new InputHidden( 'cmd', ( $cmd == 'rqAdd' ? 'exAdd' : 'exEdit' ) ) );
        $form->addElement( new InputSubmit( 'submit', get_lang('Submit') ) );
        $form->addElement( new InputCancel( 'cancel', get_lang('Cancel'), $_SERVER['PHP_SELF'] ) );
    }
    
    if ( 'exAdd' == $cmd || 'exEdit' == $cmd )
    {
        $serviceName = $userInput->getMandatory( 'serviceName' );
        $serviceHost = $userInput->getMandatory( 'serviceHost' );
        $serviceKey = $userInput->getMandatory( 'serviceKey' );
        
        if ( empty ( $serviceName ) || empty ( $serviceKey ) || empty( $serviceHost ) )
        {
            throw new Exception("Missing new service name, host or key !");
        }
        
        if ( 'exAdd' == $cmd )
        {
            $keyring->add( $serviceName, $serviceHost, $serviceKey );
        }
        else
        {
            $oldServiceName = $userInput->getMandatory( 'oldServiceName' );
            $oldServiceHost = $userInput->getMandatory( 'oldServiceHost' );
            
            if ( empty ( $oldServiceName ) || empty( $oldServiceHost ) )
            {
                throw new Exception("Missing old service name or host !");
            }
            
            $keyring->update( $oldServiceName, $oldServiceHost, $serviceName, $serviceHost, $serviceKey );
        }
        
        $message = ( 
            ( $cmd == 'exAdd' )
                ? 'Service key added for service '
                : 'Service key changed for service ' )
            . htmlspecialchars($serviceName) . ':' . htmlspecialchars($serviceHost)
            ;
            
        $cmd = 'list';
    }
    
    if ( 'list' == $cmd )
    {
        $list = $keyring->getServiceList();

        $serviceList = new Claro_Utils_Clarogrid;
        
        $serviceList->emphaseLine();
        $serviceList->setEmptyMessage( get_lang( 'No service registered' ) );
        $serviceList->setTitle( get_lang( 'Registered services' ) );
        $serviceList->setRows( $list );
        
        $serviceList->addDataColumn( 'serviceName', get_lang( 'Service name' ) );
        $serviceList->addDataColumn( 'serviceHost', get_lang( 'Service host' ) );
        $serviceList->addDataColumn( 'serviceKey', get_lang( 'Service key' ) );
        
        $serviceList->addColumn(
            'edit',
            get_lang('Edit'),
            '<a href="'.$_SERVER['PHP_SELF']
                .'?cmd=rqEdit&amp;serviceName=%uu(serviceName)%&amp;serviceHost=%uu(serviceHost)%">'
                . claro_html_icon('edit').'</a>'
        );
        
        $serviceList->addColumn(
            'delete',
            get_lang('Delete'),
            '<a href="'.$_SERVER['PHP_SELF']
                .'?cmd=rqDelete&amp;serviceName=%uu(serviceName)%&amp;serviceHost=%uu(serviceHost)%" '
                . 'onclick="return deleteService(\'%serviceName%\',\'%serviceHost%\');">'
                . claro_html_icon('delete').'</a>'
        );
        
        $serviceList->setFooter('<a href="'
            .$_SERVER['PHP_SELF'].'?cmd=rqAdd">'
            .claro_html_icon('new').' ' . get_lang( 'Register a service' ) .'</a>');
    }
    
    // Display
    
    $htmlHeadXtra[] = '<script type="text/javascript">
function deleteService ( serviceName, serviceHost )
{
    if (confirm(" Are you sure to delete "+ serviceName + ":" + serviceHost + " ?"))
    {
        window.location=\''.$_SERVER['PHP_SELF'].'?cmd=exDelete&serviceName=\'+escape(serviceName)+\'&serviceHost=\'+escape(serviceHost);
        return false;
    }
    else
    {
        return false;
    }
}
</script>';

    $htmlHeadXtra[] = '<link rel="stylesheet" type="text/css" href="./css/form.css" media="all" />';

    $noQUERY_STRING = true;
    
    require_once get_path('includePath') . '/claro_init_header.inc.php';
    
    echo claro_html_tool_title( $nameTools );
    
    if ( isset( $message ) )
    {
        echo claro_html_message_box( '<p>'.$message.'</p>' );
    }
    
    if ( 'rqDelete' == $cmd )
    {
        echo $form->render();
    }
    
    if ( 'rqAdd' == $cmd || 'rqEdit' == $cmd )
    {
        echo $form->render();
    }
    
    if ( 'list' == $cmd )
    {
        echo $serviceList->render();
    }
    
    // echo gethostbyaddr($_SERVER['REMOTE_ADDR']);
    
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