<?php // $Id$
    
    // vim: expandtab sw=4 ts=4 sts=4:
    
    /**
     * Main Controller for Blog Application
     *
     * @version     2.0 $Revision$
     * @copyright   2001-2014 Universite catholique de Louvain (UCL)
     * @author      Frederic Minne <zefredz@claroline.net>
     * @license     http://www.gnu.org/copyleft/gpl.html 
     *              GNU GENERAL PUBLIC LICENSE
     * @package     CLBLOG
     */
    
// {{{ SCRIPT INITIALISATION
{
    $GLOBALS['nameTools'] = get_lang( 'Blog' );
    
    if ( claro_is_in_a_group() )
    {
        $groupId = claro_get_current_group_id();
    }
    else
    {
        $groupId = 0;
    }
    
    // local variable initialisation
    $isAllowedToEdit = claro_is_allowed_to_edit();
    
    // display
    $dispPostList           = false;
    $dispPostForm           = false;
    $dispPost               = false;
    $dispCommentForm        = false;
    $dispConfirmDelComment  = false;
    $dispConfirmDelPost     = false;
    
    // success dialog
    $dispSuccess            = false;
    $successMsg             = '';
    
    // error dialog
    $dispError              = false; // display error box
    $fatalError             = false; // if set to true, the script ends after 
                                     // displaying the error
    $errorMsg = '';                  // error message to display
    $dispErrorBoxBackButton = false; // display back button on error
    $err                    = '';    // error string
    
    // script initalisation
    From::Module('CLBLOG')->uses( 'html/template.class','html/datagrid/template.class'
        , 'blog/post.class', 'blog/comment.class', 'blog/utils.class'
        , 'user.lib.php' );
    
    FromKernel::uses( 'utils/htmlsanitizer.lib' );
}
// }}}
// {{{ MODEL
{
    // model code here
    $blogTables = get_module_course_tbl( array( 'blog_posts', 'blog_comments' )
        , claro_get_current_course_id() );
        
    $bp = new Blog_Post( Claroline::getDatabase(), $blogTables );
    $bc = new Blog_Comment( Claroline::getDatabase(), $blogTables );
    $san = new Claro_Html_Sanitizer;
    $dialogBox = new DialogBox;
}
// }}}
// {{{ CONTROLLER
{
    try
    {
        // controller code here
        $allowedActions = claro_is_allowed_to_edit() || claro_is_group_member()
            ? array( 
                  'showList'
                , 'showPost'
                , 'rqAddPost'
                , 'exAddPost'
                , 'rqEditPost'
                , 'exEditPost'
                , 'rqDelPost'
                , 'exDelPost'
                , 'exAddComment'
                , 'rqDelComment'
                , 'exDelComment'
                , 'rqEditComment'
                , 'exEditComment' )
            : array( 
                  'showList'
                , 'showPost'
                , 'exAddComment'
                , 'rqEditComment'
                , 'exEditComment' )
            ;
            
        $userInput->setValidator( 
            'action', 
            new Claro_Validator_AllowedList( $allowedActions ) 
        );
        
        try
        {
            $action = $userInput->get( 'action', 'showList' );
        }
        catch ( Claro_Input_Exception $e )
        {
            pushClaroMessage($e->getMessage(), 'error');
            $dialogBox->error(get_lang("You are not allowed to execute this action"));
            $action = 'showList';
        }
        
        $userInput->setValidator('postId', new Claro_Validator_ValueType('digit') );
        
        $postId = $userInput->get( 'postId', null );
        
        $postTitle = $san->sanitize( trim( $userInput->get( 'postTitle', '' ) ) );
            
        $postChapo = $san->sanitize( trim( $userInput->get( 'postChapo', '' ) ) );
            
        $postContents = $san->sanitize( trim( $userInput->get( 'postContents', '' ) ) );
        
        $userInput->setValidator('commentId', new Claro_Validator_ValueType('digit') );
        
        $commentId = $userInput->get( 'commentId', null );
            
        $commentContents = $san->sanitize( trim( $userInput->get( 'commentContents', '' ) ) );
        
        // Check postId and load post
        if ( !empty( $postId ) && ! $bp->postExists( $postId ) )
        {
            $err = get_lang('Cannot execute %s action on given post : %s'); 
            $reason = get_lang('post not found in database');
    
            $dialogBox->error(sprintf( $err, $action, $reason ));
            
            $fatalError = true;
            
            $action = 'showPostList';
            $tag = null;
        }
        elseif ( !empty($postId) )
        {
            $post = $bp->getPost( $postId );
                  
            if ( is_null ( $post ) )
            {
                $err = 'Cannot execute %s on post : %s'; 
                
                $reason = 'unknown error';
    
                $dialogBox->error(sprintf( $err, $action, $reason ));

                $fatalError = true;
            }
        }
        else
        {
            // ????
        }
        
        // Check comment id
        if ( !empty( $commentId ) && ! $bc->commentExists( $commentId ) )
        {
            $$err = get_lang('Cannot execute %s action on given comment : %s'); 
            $reason = get_lang('comment not found in database');
    
            $dialogBox->error(sprintf( $err, $action, $reason ));
            
            
            $fatalError = true;
            
            $action = 'showPost';
            $tag = null;
        }
        else
        {
            // ???
        }
        
        if ( 'exDelComment' === $action )
        {
            if ( !empty( $commentId ) )
            {
                if ( $bc->deleteComment( $commentId ) )
                {
                    $dialogBox->success(get_lang( 'Comment deleted' ));
                    $action = 'showPost';
                }
                else
                {
                    $err = get_lang('Cannot delete comment : %s'); 
                
                    $reason = get_lang('not found');
                    
                    $dialogBox->error(sprintf( $err, $reason ));
                    
                    $action = 'showPost';
                }
                
                $commentId = null;
            }
            else
            {
                $err = get_lang('Cannot delete comment : %s'); 
                $reason = get_lang('missing id');
    
                $dialogBox->error(sprintf( $err, $reason ));
                
                $action = 'showPost';
            }
        }
        
        if ( 'exDelPost' === $action )
        {
            if ( !empty( $postId ) )
            {
                if ( $bp->deletePost( $postId ) )
                {
                    $bc->deletePostComment( $postId );
                    $dialogBox->success(get_lang( 'Post deleted' ));
                    $action = 'showList';
                }
                else
                {
                    $err = get_lang('Cannot delete post : %s'); 
                
                    $reason = get_lang('not found');
                    
                    $dialogBox->error(sprintf( $err, $reason ));
                
                    $action = 'showList';
                }
            }
            else
            {
                $err = get_lang('Cannot delete comment : %s'); 
                $reason = get_lang('missing id');
    
                $dialogBox->error(sprintf( $err, $reason ));
        
                
                
                $action = 'showList';
            }
        }
        
        if ( 'exAddComment' === $action )
        {
            if ( !empty ( $commentContents ) )
            {
                if ( !empty( $postId ) )
                {
                    if ( empty( $commentId ) )
                    {
                        $commentId = $bc->addComment( $postId
                            , claro_get_current_user_id()
                            , $commentContents );
                            
                        if ( !$commentId )
                        {
                            $dialogBox->error(get_lang('Cannot save comment')); 
                        }
                        
                        $commentId = null;
                    }
                    else
                    {
                        $err = get_lang('Cannot save comment : %s'); 
                        
                        $bc->editComment( $commentId
                            , $postId
                            , claro_get_current_user_id()
                            , $commentContents );
                    }
                        
                    $commentContents = '';
                    
                    $action = 'showPost';
                }
                else
                {
                    $err = get_lang('Cannot save comment : %s'); 
                    $reason = get_lang('missing post id');
                    $dialogBox->error(sprintf( $err, $reason ));
                    $action = 'showList';
                }
            }
            else
            {
                $err = get_lang('Cannot save comment : %s'); 
                $reason = get_lang('empty contents');
    
                $dialogBox->error(sprintf( $err, $reason ));
                
                $action = 'showPost';
            }
        }
        
        if ( 'exAddPost' === $action )
        {
            if ( !empty ( $postTitle ) )
            {
                if ( empty( $postId ) )
                {
                    $postId = $bp->addPost( claro_get_current_user_id()
                        , $postTitle
                        , $postContents
                        , $postChapo
                        , $groupId );
                    
                    if ( $postId )
                    {
                        $action = 'showPost';
                    }
                    else
                    {
                        $dialogBox->error('Cannot save post');
                
                        $action = 'showList';
                    }
                }
                else
                {
                    $bp->updatePost( $postId
                        , claro_get_current_user_id()
                        , $postTitle
                        , $postContents
                        , $postChapo
                        , $groupId );
                        
                    $action = 'showPost';
                }
            }
            else
            {
                $err = 'Cannot save post : %s'; 
                $reason = 'empty title';
    
                $dialogBox->error(sprintf( $err, $reason ));
        
                
                $action = 'rqAddPost';
            }
        }
            
        if ( 'rqAddPost' === $action )
        {
            $dispPostForm = true;
            $postTitle = '';
            $postChapo = '';
            $postContents = '';
            $nextAction = 'exAddPost';
        }
        
        if ( 'rqEditPost' === $action )
        {
            $dispPostForm = true;
            $postTitle = $post['title'];
            $postChapo = $post['chapo'];
            $postContents = $post['contents'];
            $nextAction = 'exAddPost';
        }
        
        if ( 'rqDelPost' === $action )
        {
            if ( !empty( $postId ) )
            {
                $postTitle = $post['title'];
                
                $confirmDelPost = '<p>'
                    . get_lang( 'You are going to delete the following post : %title%'
                        , array( '%title%' => htmlspecialchars($postTitle) ) )
                    . '<br /><br />'
                    . "\n"
                    ;
                    
                $confirmDelPost .= get_lang( 'Continue ?' ) . '<br /><br />' . "\n"
                    . '<a href="'
                    . $_SERVER['PHP_SELF']
                    . '?page=blog&amp;action=exDelPost&amp;postId='
                    . (int) $postId
                    . '">'
                    . '[' 
                    . get_lang( 'Yes' ) 
                    . ']</a>&nbsp;' 
                    . '<a href="'
                    . $_SERVER['PHP_SELF']
                    . '?page=blog">[' 
                    . get_lang( 'No' ) 
                    . ']</a>' . '</p>'
                    . "\n"
                    ;
                    
                $dialogBox->question( $confirmDelPost );
            }
            else
            {
                $err = 'Cannot delete post : %s'; 
                $reason = 'missing id';
    
                $dialogBox->error(sprintf( $err, $reason ));
        
                
                $action = 'showList';
            }
        }
        
        if ( 'rqDelComment' === $action )
        {
            if ( !empty( $commentId ) )
            {
                $confirmDelComment = '<p>'
                    . get_lang( 'You are going to delete the comment.' )
                    . '<br /><br />'
                    . "\n"
                    ;
                    
                $confirmDelComment .= get_lang( 'Continue ?' ) . '<br /><br />' . "\n"
                    . '<a href="'
                    . $_SERVER['PHP_SELF']
                    . '?page=blog&amp;action=exDelComment&amp;postId='
                    . (int) $postId
                    . '&amp;commentId='.(int)$commentId.'">'
                    . '[' 
                    . get_lang( 'Yes' ) 
                    . ']</a>&nbsp;' 
                    . '<a href="'
                    . $_SERVER['PHP_SELF']
                    . '?page=blog">[' 
                    . get_lang( 'No' ) 
                    . ']</a>' . '</p>'
                    . "\n"
                    ;
                    
                $dialogBox->question( $confirmDelComment );
            }
            else
            {
                $err = 'Cannot delete comment : %s'; 
                $reason = 'missing id';
    
                $dialogBox->error(sprintf( $err, $reason ));
        
                
                $action = 'showPost';
            }
        }
        
        if ( 'rqEditComment' === $action )
        {
            if ( !empty( $commentId ) )
            {
                $comment = $bc->getComment( $commentId );
                
                if ( $comment )
                {
                    $dispCommentForm = true;
                    $commentContents = $comment['contents'];
                    $nextAction = 'exAddComment';
                }
                else
                {
                    $err = 'Cannot load comment : %s'; 
                    
                    $reason = 'comment not found';
                    $dialogBox->error(sprintf( $err, $reason ));
            
                    
                    
                    $action = 'showPost';
                }
            }
            else
            {
                $err = 'Cannot load comment : %s';
                $reason = 'missing id';
    
                $dialogBox->error(sprintf( $err, $reason ));
        
                
                
                $action = 'showPost';
            }
        }
        
        if ( 'showPost' === $action )
        {
            if ( !empty( $postId ) )
            {
                $post = $bp->getPost( $postId );
                $commentList = iterator_to_array($bc->getPostComment( $postId ));
                
                $userIdList = array();
            
                foreach ( $commentList as $key => $comment )
                {
                    $userIdList[] = (int)$comment['userId'];
                }
                
                $userIdList[] = $post['userId'];
                
                $userIdList = array_unique( $userIdList );
                
                $ul = getCourseUserList( $userIdList );
                
                foreach ( $commentList as $key => $comment )
                {
                    $user = $ul[(int)$comment['userId']];
                    $commentList[$key]['user'] = $user['prenom'] . ' ' . $user['nom'];
                }
                
                $user = $ul[(int)$post['userId']];
                
                $post['user'] = $user['prenom'] . ' ' . $user['nom'];
                
                if ( ! $post )
                {
                    $err = 'Cannot load post : %s'; 
                    
                    $reason = 'post not found';
                    $dialogBox->error(sprintf( $err, $reason ));
            
                    
                    
                    $action = 'showList';
                }
                else
                {
                    $dispPost = true;
                    $dispCommentForm = true;
                    $nextAction = 'exAddComment';
                }
            }
            else
            {
                $err = 'Cannot load post : %s'; 
                $reason = 'missing id';
    
                $dialogBox->error(sprintf( $err, $reason ));
        
                
                
                $action = 'showList';
            }
        }
            
        if ( 'showList' === $action )
        {
            $postList = iterator_to_array($bp->getAll( $groupId ));
            $userIdList = array();
            
            foreach ( $postList as $key => $post )
            {
                $userIdList[] = (int)$post['userId'];
            }
            
            $userIdList = array_unique( $userIdList );
            
            $ul = getCourseUserList( $userIdList );
            
            foreach ( $postList as $key => $post )
            {
                $user = $ul[(int)$post['userId']];
                $postList[$key]['user'] = $user['prenom'] . ' ' . $user['nom'];
                $postList[$key]['comments'] = $bc->getCommentNumber( (int)$post['id'] );
            }
            
            if ( count ( $postList ) > 0 )
            {
                foreach ( $postList as $id => $post )
                {
                    if ( empty( $post['chapo'] ) )
                    {
                        $max = strlen( $post['contents'] );
                        $max = $max > 255 ? $max - 4 : $max;
                        $postList[$id]['chapo'] = substr( $post['contents'], 0, $max );
                        $postList[$id]['user'] = $post['userId'] == 0 
                            ? get_lang('Unknown') 
                            : get_lang('%firstName% %lastName%', array(
                                '%firstName%' => $ul[$post['userId']]['prenom'],
                                '%lastName%' => $ul[$post['userId']]['nom'] ) )
                            ;
                    }
                }
            }
            
            $dispPostList = true;
        }
    }
    catch ( Exception $e )
    {
        if ( true === $dispErrorBoxBackButton )
        {
            $errorMessage .= '<p><a href="'
                . $_SERVER['PHP_SELF']
                . '?page=list">['.get_lang('Back').']</a></p>'
                . "\n"
                ;
        }
        
        $dialogBox->error(sprintf( $err, "{$e}" ));
        $fatalError = true;
    }
}
// }}}
// {{{ VIEW
{
    $output = '';
    
    $output .= claro_html_tool_title( get_lang('Blog') );
    
    $output .= $dialogBox->render();
    
    // no fatal error
    if ( true != $fatalError )
    {        
        if ( $dispPostForm )
        {
            $form = '<div class="formContainer"><form method="post" action="'.$_SERVER['PHP_SELF']
                . '?page=blog&amp;action='
                . $nextAction.'" name="editPostForm" id="editPostForm">' . "\n"
                . '<fieldset id="editPost">' . "\n"
                . '<legend>'
                . ( $nextAction === 'exAddPost' ? get_lang('New post') : get_lang('Edit post') )
                . '</legend>' . "\n"
                . '<dl><dt class="row">' . "\n"
                . '<label for="postTitle">'.get_lang( 'Title' ).'&nbsp;:&nbsp;</label></dt>' . "\n"
                . '<dd><input name="postTitle" value="'.htmlspecialchars( $postTitle ).'" type="text" size="80" />' . "\n"
                . '</dd>' . "\n"
                . '<dt class="row">' . "\n"
                . '<label for="postChapo">'.get_lang( 'Header' ).'&nbsp;:&nbsp;</label></dt>' . "\n"
                // . '<dd><textarea name="postChapo" cols="60" rows="3">'.$san->sanitize( $postChapo ).'</textarea>' . "\n"
                . '<dd>'.claro_html_simple_textarea('postChapo', $san->sanitize( $postChapo ))."\n"
                . '</dd>' . "\n"
                . '<dt class="row">' . "\n"
                . '<label for="postContents">'.get_lang( 'Contents' ).'&nbsp;:&nbsp;</label></dt>' . "\n"
                // . '<dd><textarea name="postContents" cols="60" rows="10">'.htmlspecialchars($san->sanitize( $postContents )).'</textarea>' . "\n"
                . '<dd>'.claro_html_advanced_textarea( 'postContents', $san->sanitize( $postContents ) ). "\n"
                . '</dd>' . "\n"
                . '<dt>&nbsp;</dt><dd class="btnrow">' . "\n"
                . '<input type="hidden" name="claroFormId" value="' . uniqid('') . '" />'
                . ( $postId ? '<input type="hidden" value="'.$postId.'" name="postId" />' : '' )
                . '<input name="submit" value="'.get_lang('Ok').'" type="submit" />&nbsp;'
                . '<input name="cancel" value="'.get_lang('Cancel').'" type="button" '
                . 'onclick="window.location=\''.$_SERVER['PHP_SELF'].'?page=blog'
                . '\'" />' . "\n"
                . '</dd>' . "\n"
                . '</dl></fieldset>' . "\n"
                . '</form></div>' . "\n"
                ;
            $output .= $form;
        }
        
        if ( $dispPost )
        {
            $output .= '<p>' . claro_html_icon_button(
                $_SERVER['PHP_SELF'] . '?page=blog'
                , 'parent'
                , get_lang('Back') ) . '</p>' . "\n";

            if ( $isAllowedToEdit )
            {
                $output .= '<p>'
                    . claro_html_icon_button(
                        $_SERVER['PHP_SELF'] . '?page=blog&amp;action=rqEditPost'
                            . '&amp;postId=' . (int) $postId
                        , 'edit'
                        , get_lang('Edit')
                        , get_lang('Click to edit this post') )
                    . '&nbsp;|&nbsp;'
                    . claro_html_icon_button(
                        $_SERVER['PHP_SELF'] . '?page=blog&amp;action=rqDelPost'
                            . '&amp;postId=' . (int) $postId
                        , 'delete'
                        , get_lang('Delete')
                        , get_lang('Click to delete this post') )
                    . '</p>'
                    . "\n"
                    ;
            }
            
            $tpl = '<div class="post">' 
                . '<h2 class="postTitle">%title%</h2>'."\n"
                .'<p class="postInfo">'
                . get_lang('Posted on %ctime% by user %user%')
                . '</p>'."\n"
                . '<p class="postChapo">%apply(blog_sanitize_html,chapo)%</p>'."\n"
                . '<div class="postContents">%apply(blog_sanitize_html,contents)%</div>'."\n"
                . '</div>'
                ;
            
            $template = new HTML_Template( $tpl );
            $template->allowCallback();
            $template->registerCallback( 'chapo', 'blog_sanitize_html' );
            $template->registerCallback( 'contents', 'blog_sanitize_html' );
            
            $output .= $template->render( $post );
            
            $output .= '<div class="postComments">' . "\n";
            
            $output .= '<h3><a name="comments"></a>'
                . get_lang('Comments').'</h3>' 
                . "\n"
                ;
                
            $tpl = '<div class="postComment"><p class="postInfo">'
                . get_lang('Posted on %ctime% by user %user%')
                . '</p>' . "\n"
                . '<p>%apply(blog_sanitize_html,contents)%</p>'
                ;
                
            if ( $isAllowedToEdit )
            {
                $tpl .= claro_html_icon_button(
                        $_SERVER['PHP_SELF'] . '?page=blog&amp;action=rqEditComment'
                            . '&amp;commentId=%int(id)%&amp;postId='.(int) $postId
                        , 'edit'
                        , get_lang('Edit')
                        , get_lang('Click to edit this comment') )
                    . '&nbsp;|&nbsp;'
                    . claro_html_icon_button(
                        $_SERVER['PHP_SELF'] . '?page=blog&amp;action=rqDelComment'
                            . '&amp;commentId=%int(id)%&amp;postId='.(int) $postId
                        , 'delete'
                        , get_lang('Delete')
                        , get_lang('Click to delete this comment') )
                    . "\n"
                    ;
            }
            
            $tpl .= '</div>' . "\n";
                
            $template = new HTML_Template( $tpl );
            $template->allowCallback();
            $template->registerCallback( 'contents', 'blog_sanitize_html' );
            
            $datagrid = new HTML_Datagrid_Template;
            $datagrid->setTemplate( $template );
            $datagrid->setData( $commentList );
            $output .= $datagrid->render();
            
            $output .= '</div>' . "\n";
        }
        
        if ( $dispCommentForm )
        {
            $commentForm = '<div class="formContainer">' . "\n"
                . '<form method="post" action="'.$_SERVER['PHP_SELF']
                . '?page=blog&amp;action=' . $nextAction
                . '" name="editPostForm" id="editPostForm">' . "\n"
                . '<fieldset id="editPost">' . "\n"
                . '<dl><dt class="row">' . "\n"
                . '<label for="commentContents">'.get_lang( 'Comment' ).'&nbsp;:&nbsp;</label>' . "\n"
                // . '</dt><dd><textarea id="commentContents" name="commentContents" cols="60" rows="10">'.$san->sanitize( $commentContents ).'</textarea>' . "\n"
                . '</dt><dd>'.claro_html_simple_textarea('commentContents', $san->sanitize( $commentContents ) )."\n"
                . '</dd>' . "\n"
                . '<dt class="btnrow">&nbsp;</dt>' . "\n"
                . '<dd><input type="hidden" name="claroFormId" value="' . uniqid('') . '" />'
                . ( $postId ? '<input type="hidden" value="'.$postId.'" name="postId" />' : '' )
                . ( $action === 'rqEditComment' && $commentId ? '<input type="hidden" value="'.$commentId.'" name="commentId" />' : '' )
                . '<input name="submit" value="'.get_lang('Ok').'" type="submit" />' . "\n"
                . ( $action === 'rqEditComment' 
                    ? '<input type="button" value="'
                        . get_lang('Cancel')
                        . '" onclick="window.location=\''.$_SERVER['PHP_SELF']
                        .'?page=blog'
                        . ( $postId ? '&amp;action=showPost&amp;postId='.(int)$postId : '')
                        . '\'" />'
                        . "\n" 
                    : '' )
                . '</dd>' . "\n"
                . '</dl></fieldset>' . "\n"
                . '</form></div>' . "\n"
                ;
            
            $output .= '<h3>' 
                . ( $action === 'rqEditComment' ? get_lang('Edit comment') : get_lang('Post your comment') )
                . '</h3>' . "\n"
                . $commentForm
                ;
        }
        
        if ( $dispPostList )
        {
            $tpl = '<div class="post">'
                . '<h2 class="postTitle">%title%</h2>'."\n"
                . '<p class="postInfo">'
                . get_lang('Posted on %ctime% by user %user%')
                . '</p>'."\n"
                . '<p class="postChapo">%apply(blog_sanitize_html,chapo)%</p>'."\n"
                . '<p>'
                ;
                
            if ( $isAllowedToEdit )
            {
                $tpl .= claro_html_icon_button(
                        $_SERVER['PHP_SELF'] . '?page=blog&amp;action=rqEditPost'
                            . '&amp;postId=%int(id)%'
                        , 'edit'
                        , get_lang('Edit')
                        , get_lang('Click to edit this post') )
                    . '&nbsp;|&nbsp;'
                    . claro_html_icon_button(
                        $_SERVER['PHP_SELF'] . '?page=blog&amp;action=rqDelPost'
                            . '&amp;postId=%int(id)%'
                        , 'delete'
                        , get_lang('Delete')
                        , get_lang('Click to delete this post') )
                    . '&nbsp;|&nbsp;'
                    . "\n"
                    ;
            }
            
            $tpl .= claro_html_icon_button(
                    $_SERVER['PHP_SELF'] . '?page=blog&amp;action=showPost'
                        . '&amp;postId=%int(id)%'
                    , ''
                    , get_lang('Read more...') )
                . '&nbsp;|&nbsp;'
                . claro_html_icon_button(
                    $_SERVER['PHP_SELF'] . '?page=blog&amp;action=showPost'
                        . '&amp;postId=%int(id)%#comments'
                    , ''
                    , get_lang('Comments (%comments%)') )
                ;
            
            $tpl .= '</p></div>' . "\n";
                
            $template = new HTML_Template( $tpl );
            $template->allowCallback();
            $template->registerCallback( 'chapo', 'blog_sanitize_html' );
            
            $datagrid = new HTML_Datagrid_Template;
            $datagrid->setTemplate( $template );
            $datagrid->setData( $postList );
            $addLink = '<p>'
                . claro_html_icon_button(
                    $_SERVER['PHP_SELF'] . '?page=blog&amp;action=rqAddPost'
                    , 'new'
                    , get_lang('Add a post')
                    , get_lang('Click here to add a new post') )
                . '</p>'
                . "\n"
                ;
                    
            $datagrid->setHeader( $addLink );
            $datagrid->setFooter( $addLink );
            $output .= $datagrid->render();
        }
    }
    else
    {
        // ????
    }
    
    /*$claroline->display->banner->breadcrumbs->append(
        get_lang("Blog"), 
        $_SERVER['PHP_SELF']);*/
        
    if ( 'rqAddPost' === $action || 'rqEditPost' === $action )
    {
        $claroline->display->banner->breadcrumbs->append(
            get_lang("Add/Edit post"));
    }
    
    if ( 'showList' === $action )
    {
        $claroline->display->banner->breadcrumbs->append(
            get_lang("Posts"));
    }
    
    if ( 'showPost' === $action )
    {
        $claroline->display->banner->breadcrumbs->append(
            $post['title']);
    }
    
    return $output;
}
// }}}
