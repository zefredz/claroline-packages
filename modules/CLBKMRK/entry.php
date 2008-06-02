<?php // $Id$

    // vim: expandtab sw=4 ts=4 sts=4 foldmethod=marker:

    // load Claroline kernel
    $tlabelReq = 'CLBKMRK';
    
    require_once dirname(__FILE__) . '/../../claroline/inc/claro_init_global.inc.php';
    
    uses(
        'utils/datagrid.lib',
        'utils/input.lib',
        'utils/validator.lib',
        'display/dialogBox.lib' );
    
    From::module('CLBKMRK')->uses('*');
    
    $userInput = Claro_UserInput::getInstance();
    
    $userInput->setValidator('cmd',
        new Claro_Validator_AllowedList(
            array('addBookmark','deleteBookmark','list') ) );
    
    $cmd = $userInput->get('cmd', 'list');
    
    if ( $cmd == 'addBookmark' )
    {
        $name = $userInput->get('name', get_lang('Untitled'));
        $url = $userInput->getMandatory('url');
        
        $bookmark = new Bookmark;
        $bookmark->setName( $name );
        $bookmark->setUrl( $url );
        $bookmark->setOwner( claro_get_current_user_id() );
        
        $bookmark->create();
    }
    
    if ( $cmd == 'deleteBookmark' )
    {
        $id = $userInput->getMandatory('id');
        
        $bookmark = Bookmark::load( $id );
        $bookmark->delete();
    }
    
    $bookmarks = Bookmark::loadAllForUser( claro_get_current_user_id() );
    $dialogBox = new DialogBox;
    
    $form = '<form id="addBookmarkForm" action="'.get_module_entry_url('CLBKMRK').'?cmd=addBookmark" method="post">'
        . '<label for="name">'.get_lang('Name').' : </label><input type="text" name="name" value="" /><br />'
        . '<label for="name">'.get_lang('Url').' : </label><input type="text" name="url" value="" /><br />'
        . '<input type="submit" name="submit" value="Add" />'
        . '</form>'
        . "\n"
        ;
    
    $dialogBox->form( $form );
    
    header('Content-Type: text/html; charset=UTF-8'); // Charset
    header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
    header("Expires: Mon, 26 Jul 1997 05:00:00 GMT"); // Date in the past
    
    
    $out = '';
    
    $out .= '<div id="addBookmarkButton">'
        . '<a class="claroCmd" href="" onclick="$(\'#addBookmark\').slideDown(); return false;">'
        . '<img src="'.get_icon_url('web','CLBKMRK').'" alt="" /> '
        . get_lang('Add a bookmark')
        . '</a>'
        . '</div>'
        . "\n"
        ;
    
    $out .= '<div id="addBookmark" style="display: none;">'
        . $dialogBox->render()
        . '</div>'
        . "<script type=\"text/javascript\">
        $('#addBookmarkForm').ajaxForm( {
        target: '#bookmarkList'
    } );
    
    var deleteBookmark = function( id ){
        $.ajax({
            url: '".get_module_entry_url('CLBKMRK')."',
            data: 'cmd=deleteBookmark&id='+id,
            success: function(response){
                $('#bookmarkList').empty().append(response);
            }
        });
    }
</script>"
        . "\n"
        ;
    
    if ( count( $bookmarks ) )
    {
        $out .= '<dl>' . "\n";
        
        foreach ( $bookmarks as $bookmark )
        {
            $out .=  '<dt id="bk_'.(int)$bookmark->id.'">'.htmlspecialchars($bookmark->name).'</dt>' . "\n"
                . '<dd>'
                . '<a href="'.htmlspecialchars($bookmark->url).'">'
                . cut_long_url_for_display( htmlspecialchars($bookmark->url) )
                . '</a>'
                . ' - <a href="" onclick="deleteBookmark('.(int)$bookmark->id.');return false;"><img src="'.get_icon_url('delete').'" alt="'.get_lang('Delete').'" /></a>'
                . '</dd>' . "\n"
                ;
        }
        
        $out .= '</dl>' . "\n";
        
        echo $out;
    }
    else
    {
        echo get_lang('No bookmark');
    }
?>
