<?php
    $tlabelReq = array_key_exists( 'tlabelReq', $_REQUEST )
        ? $_REQUEST['tlabelReq']
        : ''
        ;
        
    require_once dirname(__FILE__) . '/../../../claroline/inc/claro_init_global.inc.php';
    header('Content-type: text/javascript');
?>
    var clarolineRepositoryWeb = '<?php echo clean_str_for_javascript(get_path('clarolineRepositoryWeb')); ?>';
    var clarolineModuleRepositoryWeb = '<?php echo clean_str_for_javascript(get_path('url').'/module'); ?>';
    var clarolineCurrentModule = '<?php echo clean_str_for_javascript(get_current_module_label()); ?>';
    
    if ( typeof _lang == 'undefined' ) {
        var _lang = {};
    }
    
    function get_icon( iconFile ){
        return '<img src="'+clarolineRepositoryWeb+'/img/'+iconFile+'" alt="'+iconFile+'" />';
    }
    
    function get_module_icon( iconFile, moduleLabel ){
    
        if ( typeof moduleLabel == 'undefined' )
        {
            moduleLabel = clarolineCurrentModule;
        }
        return '<img src="'+clarolineModuleRepositoryWeb+'/'+moduleLabel+'/img/'+iconFile+'" alt="'+iconFile+'" />';
    }
    
    function get_lang( str ) {
        if ( typeof _lang[str] != 'undefined' ) {
            return _lang[str];
        }
        else {
            return str;
        }
    }