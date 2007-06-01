<?php // $Id$
    
    // vim: expandtab sw=4 ts=4 sts=4:
    // vim>600: set foldmethod=marker:
    
    if ( count( get_included_files() ) == 1 )
    {
        die( 'The file ' . basename(__FILE__) . ' cannot be accessed directly, use include instead' );
    }
    
    /**
     * Main Controller for Blog Application
     *
     * @version     1.9 $Revision$
     * @copyright   2001-2007 Universite catholique de Louvain (UCL)
     * @author      Frederic Minne <zefredz@claroline.net>
     * @license     http://www.gnu.org/copyleft/gpl.html 
     *              GNU GENERAL PUBLIC LICENSE
     * @package     CLBLOG
     */
    
// {{{ SCRIPT INITIALISATION
{
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
    require_once dirname(__FILE__) . '/../lib/html/template.class.php';
    require_once dirname(__FILE__) . '/../lib/html/datagrid/template.class.php';
    require_once dirname(__FILE__) . '/../lib/blog/post.class.php';
    require_once dirname(__FILE__) . '/../lib/blog/comment.class.php';
    require_once dirname(__FILE__) . '/../lib/blog/utils.class.php';
    require_once dirname(__FILE__) . '/../lib/user.lib.php';
}
// }}}
// {{{ MODEL
{
    // model code here
    $connection = new Claroline_Database_Connection;
    $bp = new Blog_Post( $connection, $GLOBALS['blogTables'] );
    $bc = new Blog_Comment( $connection, $GLOBALS['blogTables'] );
    $san = new HTML_Sanitizer;
}
// }}}
// {{{ CONTROLLER
{
    // controller code here
    if ( $isAllowedToEdit == true )
    {
        $allowedActions = array( 
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
            , 'exEditComment'
        );
    }
    else
    {
        $allowedActions = array( 
              'showList'
            , 'showPost'
            , 'exAddComment'
            , 'rqEditComment'
            , 'exEditComment'
        );
    }
    
    $action = ( isset( $_REQUEST['action'] ) 
            && in_array( $_REQUEST['action'], $allowedActions ) )
        ? $_REQUEST['action']
        : 'showList'
        ;
        
    $postId = isset( $_REQUEST['postId'] )
        ? (int) $_REQUEST['postId']
        : null
        ;
        
    $postTitle = isset( $_REQUEST['postTitle'] )
        ? trim( $_REQUEST['postTitle'] )
        : ''
        ;
        
    $postTitle = $san->sanitize( $postTitle );
        
    $postChapo = isset( $_REQUEST['postChapo'] )
        ? trim( $_REQUEST['postChapo'] )
        : ''
        ;
        
    $postChapo = $san->sanitize( $postChapo );
        
    $postContents = isset( $_REQUEST['postContents'] )
        ? trim( $_REQUEST['postContents'] )
        : ''
        ;
        
    $postContents = $san->sanitize( $postContents );
        
    $commentId = isset( $_REQUEST['commentId'] )
        ? (int) $_REQUEST['commentId']
        : null
        ;
        
    $commentContents = isset( $_REQUEST['commentContents'] )
        ? trim( $_REQUEST['commentContents'] )
        : ''
        ;
        
    $commentContents = $san->sanitize( $commentContents );
    
    // Check postId and load post
    if ( ! is_null( $postId ) && ! $bp->postExists( $postId ) )
    {
        $err = 'Cannot execute %s action on given post : %s'; 
        $reason = 'post not found in database';

        $errorMsg .= sprintf( $err, $action, $reason ) . "\n";
        
        $dispError = true;
        $fatalError = true;
        
        $action = 'showPostList';
        $tag = null;
    }
    elseif ( !is_null( $postId ) )
    {
        $post = $bp->getPost( $postId );
              
        if ( is_null ( $post ) )
        {
            $err = 'Cannot execute %s on post : %s'; 
            
            if ( $connection->hasError() )
            {
                $reason = $connection->getError();
            }
            else
            {
                $reason = 'unknown error';
            }

            $errorMsg .= sprintf( $err, $action, $reason ) . "\n";
        
            $dispError = true;
            $fatalError = true;
        }
    }
    else
    {
    }
    
    // Check comment id
    if ( ! is_null( $commentId ) && ! $bc->commentExists( $commentId ) )
    {
        $$err = 'Cannot execute %s action on given comment : %s'; 
        $reason = 'comment not found in database';

        $errorMsg .= sprintf( $err, $action, $reason ) . "\n";
        
        $dispError = true;
        $fatalError = true;
        
        $action = 'showPost';
        $tag = null;
    }
    else
    {
    }
    
    if ( 'exDelComment' === $action )
    {
        if ( ! is_null ( $commentId ) )
        {
            if ( $bc->deleteComment( $commentId ) )
            {
                $successMessage = get_lang( 'Comment deleted' );
                $action = 'showPost';
            }
            else
            {
                $err = 'Cannot delete comment : %s'; 
            
                if ( $connection->hasError() )
                {
                    $reason = $connection->getError();
                }
                else
                {
                    $reason = 'not found';
                }

                $errorMsg .= sprintf( $err, $reason ) . "\n";
            
                $dispError = true;
                $action = 'showPost';
            }
            
            $commentId = null;
        }
        else
        {
            $err = 'Cannot delete comment : %s'; 
            $reason = 'missing id';

            $errorMsg .= sprintf( $err, $reason ) . "\n";
    
            $dispError = true;
            
            $action = 'showPost';
        }
    }
    
    if ( 'exDelPost' === $action )
    {
        if ( ! is_null ( $postId ) )
        {
            if ( $bp->deletePost( $postId ) )
            {
                $bc->deletePostComment( $postId );
                $successMessage = get_lang( 'Post deleted' );
                $action = 'showList';
            }
            else
            {
                $err = 'Cannot delete post : %s'; 
            
                if ( $connection->hasError() )
                {
                    $reason = $connection->getError();
                }
                else
                {
                    $reason = 'not found';
                }

                $errorMsg .= sprintf( $err, $reason ) . "\n";
            
                $dispError = true;
                $action = 'showList';
            }
        }
        else
        {
            $err = 'Cannot delete comment : %s'; 
            $reason = 'missing id';

            $errorMsg .= sprintf( $err, $reason ) . "\n";
    
            $dispError = true;
            
            $action = 'showList';
        }
    }
    
    if ( 'exAddComment' === $action )
    {
        if ( !empty ( $commentContents ) )
        {
            if ( !is_null( $postId ) )
            {
                if ( is_null( $commentId ) )
                {
                    $commentId = $bc->addComment( $postId
                        , claro_get_current_user_id()
                        , $commentContents );
                        
                    if ( !$commentId )
                    {
                        $err = 'Cannot save comment : %s'; 
                        $reason = $connection->getError();

                        $errorMsg .= sprintf( $err, $reason ) . "\n";
                
                        $dispError = true;
                    }
                    
                    $commentId = null;
                }
                else
                {
                    $bc->editComment( $commentId
                        , $postId
                        , claro_get_current_user_id()
                        , $commentContents );
                        
                    if ( $connection->hasError() )
                    {
                        $err = 'Cannot save comment : %s'; 
                        $reason = $connection->getError();

                        $errorMsg .= sprintf( $err, $reason ) . "\n";
                
                        $dispError = true;
                    }
                }
                    
                $commentContents = '';
                
                $action = 'showPost';
            }
            else
            {
                $err = 'Cannot save comment : %s'; 
                $reason = 'missing post id';

                $errorMsg .= sprintf( $err, $reason ) . "\n";
        
                $dispError = true;
                $action = 'showList';
            }
        }
        else
        {
            $err = 'Cannot save comment : %s'; 
            $reason = 'empty contents';

            $errorMsg .= sprintf( $err, $reason ) . "\n";
    
            $dispError = true;
            
            $action = 'showPost';
        }
    }
    
    if ( 'exAddPost' === $action )
    {
        if ( !empty ( $postTitle ) )
        {
            if ( is_null( $postId ) )
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
                    $err = 'Cannot save post : %s'; 
                    $reason = $connection->getError();

                    $errorMsg .= sprintf( $err, $reason ) . "\n";
            
                    $dispError = true;
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
                    
                if ( $connection->hasError() )
                {
                    $err = 'Cannot save post : %s'; 
                    $reason = $connection->getError();

                    $errorMsg .= sprintf( $err, $reason ) . "\n";
            
                    $dispError = true;
                    
                    $action = 'showList';
                }
                else
                {
                    $action = 'showPost';
                }
            }
        }
        else
        {
            $err = 'Cannot save post : %s'; 
            $reason = 'empty title';

            $errorMsg .= sprintf( $err, $reason ) . "\n";
    
            $dispError = true;
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
        if ( ! is_null( $postId ) )
        {
            $postTitle = $post['title'];
            $dispConfirmDelPost = true;
        }
        else
        {
            $err = 'Cannot delete post : %s'; 
            $reason = 'missing id';

            $errorMsg .= sprintf( $err, $reason ) . "\n";
    
            $dispError = true;
            $action = 'showList';
        }
    }
    
    if ( 'rqDelComment' === $action )
    {
        if ( ! is_null( $commentId ) )
        {
            $dispConfirmDelComment = true;
        }
        else
        {
            $err = 'Cannot delete comment : %s'; 
            $reason = 'missing id';

            $errorMsg .= sprintf( $err, $reason ) . "\n";
    
            $dispError = true;
            $action = 'showPost';
        }
    }
    
    if ( 'rqEditComment' === $action )
    {
        if ( !is_null( $commentId ) )
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
                
                if ( $connection->hasError() )
                {
                    $reason = $connection->getError();
                }
                else
                {
                    $reason = 'comment not found';
                }

                $errorMsg .= sprintf( $err, $reason ) . "\n";
        
                $dispError = true;
                
                $action = 'showPost';
            }
        }
        else
        {
            $err = 'Cannot load comment : %s';
            $reason = 'missing id';

            $errorMsg .= sprintf( $err, $reason ) . "\n";
    
            $dispError = true;
            
            $action = 'showPost';
        }
    }
    
    if ( 'showPost' === $action )
    {
        if ( !is_null( $postId ) )
        {
            $post = $bp->getPost( $postId );
            $commentList = $bc->getPostComment( $postId );
            
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
                
                if ( $connection->hasError() )
                {
                    $reason = $connection->getError();
                }
                else
                {
                    $reason = 'post not found';
                }

                $errorMsg .= sprintf( $err, $reason ) . "\n";
        
                $dispError = true;
                
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

            $errorMsg .= sprintf( $err, $reason ) . "\n";
    
            $dispError = true;
            
            $action = 'showList';
        }
    }
        
    if ( 'showList' === $action )
    {
        $postList = $bp->getAll();
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
        
        if ( $connection->hasError() )
        {
            $err = 'Cannot find post : %s';
            $reason = 'invalid id';
    
            $errorMsg .= sprintf( $err, $reason ) . "\n";
            
            // $this->setOutput( MessageBox::FatalError( $errorMsg ) );
            
            $dispError = true;
            $fatalError = true;
        }
        else
        {  
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
}
// }}}
// {{{ VIEW
{
    $output = '';
    
    $output .= claro_html_tool_title( get_lang('Blog') );

    if ( true == $dispError )
    {
        // display error
        $errorMessage =  '<h2>'
            . ( ( true == $fatalError ) 
                ? get_lang( 'Error (Fatal)' ) 
                : get_lang( 'Error' ) )
            . '</h2>'
            . "\n"
            ;
        
        $errorMessage .= '<p>'
            . htmlspecialchars($errorMsg) . '</p>' 
            . "\n"
            ;
        // display back link    
        // but back to where ???? (in case of fatal error)
        if ( true === $dispErrorBoxBackButton )
        {
            $errorMessage .= '<p><a href="'
                . $_SERVER['PHP_SELF']
                . '?page=list">['.get_lang('Back').']</a></p>'
                . "\n"
                ;
        }
        
        if ( true === $fatalError )
        {
            $output .= MessageBox::FatalError( $errorMessage );
        }
        else
        {
            $output .= MessageBox::Error( $errorMessage );
        }
    }
    
    if ( true === $dispSuccess )
    {
        // display error
        $successMessage =  '<h2>'
            . get_lang( 'Success' )
            . '</h2>'
            . "\n"
            ;
        
        $successMessage .= '<p>'
            . htmlspecialchars($successMsg) . '</p>' 
            . "\n"
            ;
            
        $output .= MessageBox::Success( $successMessage );
    }
    
    // no fatal error
    if ( true != $fatalError )
    {
        // view code here
        
        if ( $dispConfirmDelPost )
        {
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
                
            $output .= MessageBox::Question( $confirmDelPost );
        }
        
        if ( $dispConfirmDelComment )
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
                
            $output .= MessageBox::Question( $confirmDelComment );
        }
        
        if ( $dispPostForm )
        {
            $form = '<div class="formContainer"><form method="post" action="'.$_SERVER['PHP_SELF']
                . '?page=blog&amp;action='
                . $nextAction.'" name="editPostForm" id="editPostForm">' . "\n"
                . '<fieldset id="editPost">' . "\n"
                . '<legend>'
                . ( $nextAction === 'exAddPost' ? get_lang('New post') : get_lang('Edit post') )
                . '</legend>' . "\n"
                . '<div class="row">' . "\n"
                . '<label for="postTitle">'.get_lang( 'Title' ).'&nbsp;:&nbsp;</label>' . "\n"
                . '<input name="postTitle" value="'.htmlspecialchars( $postTitle ).'" type="text" size="80" />' . "\n"
                . '</div>' . "\n"
                . '<div class="row">' . "\n"
                . '<label for="postChapo">'.get_lang( 'Header' ).'&nbsp;:&nbsp;</label>' . "\n"
                . '<textarea name="postChapo" cols="60" rows="3">'.$san->sanitize( $postChapo ).'</textarea>' . "\n"
                . '</div>' . "\n"
                . '<div class="row">' . "\n"
                . '<label for="postContents">'.get_lang( 'Contents' ).'&nbsp;:&nbsp;</label>' . "\n"
                . '<textarea name="postContents" cols="60" rows="10">'.htmlspecialchars($san->sanitize( $postContents )).'</textarea>' . "\n"
                . '</div>' . "\n"
                . '<div class="btnrow">' . "\n"
                . '<input type="hidden" name="claroFormId" value="' . uniqid('') . '" />'
                . ( $postId ? '<input type="hidden" value="'.$postId.'" name="postId" />' : '' )
                . '<input name="submit" value="'.get_lang('Ok').'" type="submit" />&nbsp;'
                . '<input name="cancel" value="'.get_lang('Cancel').'" type="button" '
                . 'onclick="window.location=\''.$_SERVER['PHP_SELF'].'?page=blog'
                . '\'" />' . "\n"
                . '</div>' . "\n"
                . '</fieldset>' . "\n"
                . '</form></div>' . "\n"
                ;
            $output .= $form;
        }
        
        if ( $dispPost )
        {
            $output .= '<p><a class="claroCmd" href="'
                . $_SERVER['PHP_SELF'] . '?page=blog">'
                . '<img src="'.get_icon('parent.gif').'" alt="[back]" />'
                . get_lang('Back')
                . '</a></p>'
                . "\n"
                ;
                
            if ( $isAllowedToEdit )
            {
                $output .= '<p>'
                    . '<a class="claroCmd" href="'
                    . $_SERVER['PHP_SELF'] . '?page=blog&amp;action=rqEditPost'
                    . '&amp;postId=' . (int) $postId . '">'
                    . '<img src="'.get_icon('edit.gif').'" alt="[back]" />'
                    . get_lang('Edit')
                    . '</a>'
                    . '&nbsp;|&nbsp;'
                    . '<a class="claroCmd" href="'
                    . $_SERVER['PHP_SELF'] . '?page=blog&amp;action=rqDelPost'
                    . '&amp;postId=' . (int) $postId . '">'
                    . '<img src="'.get_icon('delete.gif').'" alt="[back]" />'
                    . get_lang('Delete')
                    . '</a>'
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
                $tpl .= '<a class="claroCmd" href="'
                    . $_SERVER['PHP_SELF'] . '?page=blog&amp;action=rqEditComment'
                    . '&amp;commentId=%int(id)%&amp;postId='.(int) $postId.'">'
                    . '<img src="'.get_icon('edit.gif').'" alt="[back]" />'
                    . get_lang('Edit')
                    . '</a>'
                    . '&nbsp;|&nbsp;'
                    . '<a class="claroCmd" href="'
                    . $_SERVER['PHP_SELF'] . '?page=blog&amp;action=rqDelComment'
                    . '&amp;commentId=%int(id)%&amp;postId='.(int) $postId.'">'
                    . '<img src="'.get_icon('delete.gif').'" alt="[back]" />'
                    . get_lang('Delete')
                    . '</a>'
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
                . '<div class="row">' . "\n"
                . '<label for="commentContents">'.get_lang( 'Comment' ).'&nbsp;:&nbsp;</label>' . "\n"
                . '<textarea name="commentContents" cols="60" rows="10">'.$san->sanitize( $commentContents ).'</textarea>' . "\n"
                . '</div>' . "\n"
                . '<div class="btnrow">' . "\n"
                . '<input type="hidden" name="claroFormId" value="' . uniqid('') . '" />'
                . ( $postId ? '<input type="hidden" value="'.$postId.'" name="postId" />' : '' )
                . ( $commentId ? '<input type="hidden" value="'.$commentId.'" name="commentId" />' : '' )
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
                . '</div>' . "\n"
                . '</fieldset>' . "\n"
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
                $tpl .= '<a class="claroCmd" href="'
                    . $_SERVER['PHP_SELF'] . '?page=blog&amp;action=rqEditPost'
                    . '&amp;postId=%int(id)%">'
                    . '<img src="'.get_icon('edit.gif').'" alt="[back]" />'
                    . get_lang('Edit')
                    . '</a>'
                    . '&nbsp;|&nbsp;'
                    . '<a class="claroCmd" href="'
                    . $_SERVER['PHP_SELF'] . '?page=blog&amp;action=rqDelPost'
                    . '&amp;postId=%int(id)%">'
                    . '<img src="'.get_icon('delete.gif').'" alt="[back]" />'
                    . get_lang('Delete')
                    . '</a>'
                    . '&nbsp;|&nbsp;'
                    . "\n"
                    ;
            }
            
            $tpl .= '<a class="claroCmd" href="'. $_SERVER['PHP_SELF'] 
                . '?page=blog&amp;action=showPost'
                . '&amp;postId=%int(id)%'
                . '">'.get_lang('Read more...') . '</a>'
                . '&nbsp;|&nbsp;'
                . '<a class="claroCmd" href="'. $_SERVER['PHP_SELF'] 
                . '?page=blog&amp;action=showPost'
                . '&amp;postId=%int(id)%'
                . '#comments">'.get_lang('Comments (%comments%)') . '</a>'
                ;
            
            $tpl .= '</p></div>' . "\n";
                
            $template = new HTML_Template( $tpl );
            $template->allowCallback();
            $template->registerCallback( 'chapo', 'blog_sanitize_html' );
            
            $datagrid = new HTML_Datagrid_Template;
            $datagrid->setTemplate( $template );
            $datagrid->setData( $postList );
            $addLink = '<p><a class="claroCmd" href="'
                . $_SERVER['PHP_SELF']
                . '?page=blog&amp;action=rqAddPost"'
                . ' title="'.get_lang('Click here to add a new post').'">'
                . '<img src="'. get_icon('new.gif').'" alt="'
                . get_lang('Click here to add a new post').'" />'
                . '&nbsp;'.get_lang('Add a post').'</a></p>'
                . "\n"
                ;
                    
            $datagrid->setHeader( $addLink );
            $datagrid->setFooter( $addLink );
            $output .= $datagrid->render();
        }
    }
    else
    {
    }
    
    $GLOBALS['interbredcrump'][]= array ( 'url' => 'entry.php'
        , 'name' => get_lang("Blog"));
        
    if ( 'rqAddPost' === $action || 'rqEditPost' === $action )
    {
        $GLOBALS['interbredcrump'][]= array ( 'url' => Null
            , 'name' => get_lang("Add/Edit post"));
    }
    
    if ( 'showList' === $action )
    {
        $GLOBALS['interbredcrump'][]= array ( 'url' => Null
            , 'name' => get_lang("Posts"));
    }
    
    $this->setOutput( $output );
}
// }}}
?>