<?php // $Id$

/**
 * Keyring management
 *
 * @version     1.9$Revision$
 * @copyright   2001-2008 Universite catholique de Louvain (UCL)
 * @author      Frederic Minne <zefredz@claroline.net> Revision by Sokay Benjamin
 * @license     http://www.gnu.org/copyleft/gpl.html
 *              GNU GENERAL PUBLIC LICENSE version 2 or later
 * @package     CLKRNG
 */

//admin tool
$tlabelReq = 'CLKRNG';

//Load Claroline Kernel
require dirname(__FILE__) . '/../../claroline/inc/claro_init_global.inc.php';

//NameTool
$nameTools = get_lang('Service key administration');

//Check
if ( ! claro_is_platform_admin() )
{
    claro_disp_auth_form();
}

//Import used lib
FromKernel::uses ('utils/input.lib','utils/validator.lib','utils/datagrid.lib');

require_once dirname(__FILE__) . '/lib/keyring.lib.php';

require_once dirname(__FILE__) . '/lib/form.lib.php';

//Init vars
$keyring = Keyring::getInstance();
$userInput = Claro_UserInput::getInstance();

$allowedCommandList = array( 'list','rqAdd','exAdd','rqDelete','exDelete','rqEdit','exEdit');
$userInput->setValidator('cmd',new Claro_Validator_AllowedList($allowedCommandList));

try
{
    $cmd = $userInput->get('cmd', 'list');
}
catch(Exception $e)
{
    die(get_lang('Invalid action'));
}

$dialogBox = new dialogBox();
$error = false;

//Delete execution
if ('exDelete' == $cmd)
{
    $serviceName = $userInput->get( 'serviceName' );
    $serviceHost = $userInput->get( 'serviceHost' );

    if ( empty ( $serviceName ) || empty ( $serviceHost ) )
    {
        $errorMesssage = get_lang('Missing service name or host');
        $error = true;
        $cmd = 'list';
    }

    else
    {
        try
        {
            $keyring->delete( $serviceName, $serviceHost);
            $successMessage = get_lang('Service %service deleted',
                array( '%service' => claro_htmlspecialchars($serviceName.':'.$serviceHost) ) );
            $cmd = 'list';
        }
        catch(Exception $e)
        {
            $errorMessage = get_lang('Cannot delete service %service',
                array( '%service' => claro_htmlspecialchars($serviceName.':'.$serviceHost) ) );
            $cmd = 'list';
        }
    }
}

//Delete request
if ( 'rqDelete' == $cmd)
{

    $serviceName = $userInput->get( 'serviceName' );
    $serviceHost = $userInput->get( 'serviceHost' );

    if ( empty ( $serviceName ) || empty ( $serviceHost ) )
    {
        $errorMesssage = get_lang('Missing service name or host');
        $error = true;
        $cmd = 'list';
    }
    else
    {

        $confirmMessage = get_lang('Delete service %service ?',
            array( '%service', claro_htmlspecialchars( $serviceName . ':'. $serviceHost ) ) );

        $form = new Form;
        $form->addElement( new InputHidden( 'cmd', 'exDelete' ) );
        $form->addElement( new InputHidden( 'serviceName', claro_htmlspecialchars($serviceName) ) );
        $form->addElement( new InputHidden( 'serviceHost', claro_htmlspecialchars($serviceHost) ) );
        $form->addElement( new InputSubmit( 'submit', get_lang('Yes') ) );
        $form->addElement( new InputCancel( 'cancel', get_lang('No'), $_SERVER['PHP_SELF'] ) );
    }
}

//Edition request
if ( 'rqEdit' == $cmd )
{
    $serviceName = $userInput->get( 'serviceName' );
    $serviceHost = $userInput->get( 'serviceHost' );

    if ( empty ( $serviceName ) || empty ( $serviceHost ) )
    {
        $errorMessage = get_lang('Missing service name or host');
        $error = true;
        $cmd = 'list';
    }
    else
    {
        $service = $keyring->get( $serviceName, $serviceHost );
    }
}

//Add request
if ( 'rqAdd' == $cmd )
{
    $service = array('serviceName' => '','serviceHost' => '','serviceKey' => '');
}

//Form creation
if ( ('rqEdit' == $cmd || 'rqAdd' == $cmd) && !$error )
{
    $form = new Form;
    $input = new InputText('serviceName', claro_htmlspecialchars($service['serviceName']) );
    $input->setLabel( get_lang('Service name') . ':' );
    $form->addElement( $input, true );
    $input = new InputText( 'serviceHost', claro_htmlspecialchars($service['serviceHost']) );
    $input->setLabel( get_lang( 'Service host' )  . ':' );
    $form->addElement( $input, true );
    $input = new InputText( 'serviceKey', claro_htmlspecialchars($service['serviceKey']) );
    $input->setLabel( get_lang('Service key') . ':' );
    $form->addElement( $input, true );

    if ( 'rqEdit' == $cmd )
    {
        $form->addElement( new InputHidden( 'oldServiceName', claro_htmlspecialchars($serviceName) ) );
        $form->addElement( new InputHidden( 'oldServiceHost', claro_htmlspecialchars($serviceHost) ) );
    }

    $form->addElement( new InputHidden( 'cmd', ( $cmd == 'rqAdd' ? 'exAdd' : 'exEdit' ) ) );
    $form->addElement( new InputSubmit( 'submit', get_lang('Submit') ) );
    $form->addElement( new InputCancel( 'cancel', get_lang('Cancel'), $_SERVER['PHP_SELF'] ) );
}

//Add execution
if('exAdd'== $cmd)
{
    $serviceName = $userInput->get( 'serviceName' );
    $serviceHost = $userInput->get( 'serviceHost' );
    $serviceKey = $userInput->get( 'serviceKey' );

    $ok = true;

    if    (empty( $serviceName) || empty ($serviceKey)|| empty ($serviceHost))
    {
        $errorMessage = '';
    }

    if ( empty ( $serviceName ))
    {
        $serviceName = '';
        $errorMessage = get_lang('Missing new service name') . '<br>';
        $ok = false;
    }

    if ( empty ( $serviceHost))
    {
        $serviceHost = '';
        $errorMessage .= get_lang('Missing new service host') . '<br>';
        $ok = false;
    }

    if ( empty ( $serviceKey ))
    {
        $serviceKey = '';
        $errorMessage .= get_lang('Missing new service key');
        $ok = false;
    }
    
    if($keyring->check($serviceName, $serviceHost, $serviceKey))
    {
        $errorMessage = get_lang('Key already exist for service %service',
            array( '%service' => claro_htmlspecialchars( $serviceName . ':'. $serviceHost ) ) );
        $ok = false;
    }

    if ( !$ok )
    {
        $error = true;

        $form = new Form;
        $input = new InputText('serviceName', claro_htmlspecialchars($serviceName) );
        $input->setLabel(get_lang('Service name') . ':' );
        $form->addElement( $input, true );
        $input = new InputText( 'serviceHost', claro_htmlspecialchars($serviceHost) );
        $input->setLabel( get_lang( 'Service host' )  . ':' );
        $form->addElement( $input, true );
        $input = new InputText( 'serviceKey', claro_htmlspecialchars($serviceKey) );
        $input->setLabel( get_lang('Service key') . ':' );
        $form->addElement( $input, true );

        $form->addElement( new InputHidden( 'cmd', 'exAdd'));
        $form->addElement( new InputSubmit( 'submit', get_lang('Submit') ) );
        $form->addElement( new InputCancel( 'cancel', get_lang('Cancel'), $_SERVER['PHP_SELF'] ) );
            
    }
    else
    {
        $keyring->add( $serviceName, $serviceHost, $serviceKey );
        $successMessage = get_lang('Service key added for service %service',
            array( '%service' => claro_htmlspecialchars( $serviceName . ':'. $serviceHost ) ) );
        $cmd = 'list';
    }    
}

//Edition execution
if ('exEdit' == $cmd)
{
    $serviceName = $userInput->get('serviceName' );
    $serviceHost = $userInput->get( 'serviceHost' );
    $serviceKey = $userInput->get( 'serviceKey' );
    $oldServiceName = $userInput->get( 'oldServiceName' );
    $oldServiceHost = $userInput->get( 'oldServiceHost' );

    if ( empty ( $oldServiceName ) || empty( $oldServiceHost ))
    {
        $errorMessage = get_lang('Missing old service name or host');
        $error = true;
        $cmd = 'list';
    }
    else if    (empty( $serviceName) || empty ($serviceKey)|| empty ($serviceHost))
    {
        $errorMessage = get_lang('Missing service name, key or host');
        $error = true;
        $cmd = 'list';
    }
    else
    {
        $keyring->update( $oldServiceName, $oldServiceHost, $serviceName, $serviceHost, $serviceKey );
        $successMessage = get_lang('Service key changed for service %service',
            array( '%service' => claro_htmlspecialchars( $serviceName . ':'. $serviceHost ) ) );
        $cmd = 'list';
    }
}

//List display
if ( 'list' == $cmd )
{
    $list = $keyring->getServiceList();

    $serviceList = new Claro_Utils_Clarogrid;

    $serviceList->emphaseLine();
    $serviceList->setEmptyMessage( get_lang('No service registered') );
    $serviceList->setTitle( get_lang('Registered services') );
    $serviceList->setRows( $list );

    $serviceList->addDataColumn( 'serviceName', get_lang('Service name') );
    $serviceList->addDataColumn( 'serviceHost', get_lang('Service host') );
    $serviceList->addDataColumn( 'serviceKey', get_lang('Service key') );

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
    .claro_html_icon('new').' ' . get_lang('Register a service') .'</a>');
}

// Display
$GLOBALS['claroline']['display']->header->addHtmlHeader('<script type="text/javascript">
function deleteService ( serviceName, serviceHost )
{
    if (confirm("'. get_lang('Are you sure to delete').' "+ serviceName + ":" + serviceHost + " ?"))
    {
        window.location=\''.$_SERVER['PHP_SELF'].'?cmd=exDelete&serviceName=\'+escape(serviceName)+\'&serviceHost=\'+escape(serviceHost);
        return false;
    }
    else
    {
        return false;
    }
}
</script>');

//$GLOBALS['claroline']['display']->header->addHtmlHeader('<link rel="stylesheet" type="text/css" href="./css/form.css" media="all" />');

$GLOBALS['claroline']['display']->body->appendContent(claro_html_tool_title($nameTools));

//Success message
if ( isset($successMessage) )
{
    $dialogBox->success($successMessage);
    $GLOBALS['claroline']['display']->body->appendContent($dialogBox->render());
}

//Error message
if ( isset($errorMessage) )
{
    $dialogBox->error($errorMessage);
    $GLOBALS['claroline']['display']->body->appendContent($dialogBox->render());
}

//Add execution display
if ( 'exAdd' == $cmd && $error)
{
    $GLOBALS['claroline']['display']->body->appendContent($form->render());
}

//Confirm delete display
if ( 'rqDelete' == $cmd && !$error)
{
    $dialogBox->question($confirmMessage);
    $GLOBALS['claroline']['display']->body->appendContent($dialogBox->render());
    $GLOBALS['claroline']['display']->body->appendContent($form->render());
}

//Form display
if ( ('rqAdd' == $cmd || 'rqEdit' == $cmd) && !$error )
{
    $GLOBALS['claroline']['display']->body->appendContent($form->render());
    $GLOBALS['claroline']['display']->body->appendContent($dialogBox->render());
}

//List display
if ( 'list' == $cmd)
{
    $GLOBALS['claroline']['display']->body->appendContent($serviceList->render());
}

ClaroBreadCrumbs::getInstance()->prepend( get_lang('Administration'), get_path('rootAdminWeb') );

//return body html required
echo $GLOBALS['claroline']['display']->render();
