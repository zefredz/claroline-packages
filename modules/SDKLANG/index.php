<?php // $Id$

// Include Claroline Kernel
require dirname(__FILE__) . '/../../claroline/inc/claro_init_global.inc.php';

// Include libraries 
require get_path('includePath') . '/lib/embed.lib.php';
require dirname(__FILE__) . '/lib/translation.lib.php';

// Security check
if (!claro_is_platform_admin()) claro_disp_auth_form();

// Main section
$kernel_source_info = get_kernel_translation_source_info ();

$message_list = extract_kernel_message($kernel_source_info['path']);

echo '<h1>Kernel message</h1>';

echo '<pre>' ;
var_dump($message_list);
echo '</pre>' ;

echo count($message_list);

$moduleList = get_installed_module_list();

echo '<h1>Module List</h1>';

echo '<pre>' ;
var_dump($moduleList);
echo '</pre>' ;

foreach ( $moduleList as $module)
{
    $modulePath = get_module_path($module) ;
    
    if ( is_dir($modulePath) )
    {
        echo '<h1>' . $module . '</h1>';
        $message_list = extract_module_message($modulePath);
        
        echo '<pre>' ;
        var_dump($message_list);
        echo '</pre>' ;   
        echo count($message_list);     
    }

}

/*

// Display section

$claroline = new ClarolineScriptEmbed();

// Deal with interbredcrumps  and title variable
$interbredcrump[]  = array ('url' => get_path('rootAdminWeb'), 'name' => get_lang('Administration'));

$html = claro_html_tool_title( get_lang('Translations') );

// Script output
$claroline->setContent($html);

$claroline->output();

*/

?>
