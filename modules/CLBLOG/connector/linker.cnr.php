<?php // $Id$

// vim: expandtab sw=4 ts=4 sts=4:

/**
 * CLAROLINE
 *
 * Resource Resolver for the Wiki tool.
 *
 * @version     $Revision$
 * @copyright   (c) 2001-2014, Universite catholique de Louvain (UCL)
 * @license     http://www.gnu.org/copyleft/gpl.html (GPL) GENERAL PUBLIC LICENSE
 * @author      claroline Team <cvs@claroline.net>
 * @package     CLBLOG
 */

class CLBLOG_Resolver implements ModuleResourceResolver
{
    public function resolve ( ResourceLocator $locator )
    {
        if ( $locator->hasResourceId() )
        {
            $parts = explode( '/', ltrim( $locator->getResourceId(), '/' ) );
            
            if( count($parts) == 1 )
            {
                $url = new Url( get_module_entry_url('CLBLOG') );
                $url->addParam( 'postId', (int) $parts[0] );
                $url->addParam( 'action', 'showPost' );
                
                return $url->toUrl();
            }
            elseif( count( $parts ) == 2 )
            {
                $url = new Url( get_module_entry_url('CLBLOG') );
                $url->addParam( 'postId', (int) $parts[0] );
                $url->addParam( 'action', 'showPost' );
                
                return $url->toUrl() ."#comment_".(int)$parts[1];
            }
            else
            {
                return get_module_entry_url( 'CLBLOG' );
            }
        }
        else
        {
            return get_module_entry_url('CLBLOG');
        }
    }

    public function getResourceName( ResourceLocator $locator)
    {
        if ( $locator->hasResourceId() )
        {
            $parts = explode( '/', ltrim( $locator->getResourceId(), '/' ) );
            
            $tbl = get_module_course_tbl( array('blog_posts','blog_comments'), $locator->getCourseId() );
            
            if( count($parts) == 1 )
            {
                $sql = "SELECT `title`\n"
                    . "FROM `".$tbl['blog_posts']."`\n"
                    . "WHERE `id` = ". (int) $parts[0]
                    ;
                
                $res = Claroline::getDatabase()->query($sql);
                $res->setFetchMode( Database_ResultSet::FETCH_VALUE );
                
                return $res->fetch();
            }
            elseif( count( $parts ) == 2 )
            {
                $sql = "SELECT `title`\n"
                    . "FROM `".$tbl['blog_posts']."`\n"
                    . "WHERE `id` = ". (int) $parts[0]
                    ;
                
                $post = Claroline::getDatabase()->query($sql);
                $post->setFetchMode( Database_ResultSet::FETCH_VALUE );
                
                $sql = "SELECT `contents`\n"
                    . "FROM `".$tbl['blog_comments']."`\n"
                    . "WHERE `id` = ". (int) $parts[1]
                    ;
                
                $comment = Claroline::getDatabase()->query($sql);
                $comment->setFetchMode( Database_ResultSet::FETCH_VALUE );
                
                return $post->fetch() . ' > ' . substr($comment->fetch(),0,15).'[...]';
            }
            else
            {
                $moduleName = get_module_data('CLBLOG', 'moduleName' );
                return get_lang( $moduleName );
            }
        }
        else
        {
            $moduleName = get_module_data('CLOBLOG', 'moduleName' );
            return get_lang( $moduleName );
        }
    }
}

class CLBLOG_Navigator implements ModuleResourceNavigator
{
    public function getResourceId( $params = array() )
    {
        if ( isset( $params['postId'] ) )
        {
            $resourceId = (int) $params['postId'];
            
            if ( isset( $params['commentId'] ) )
            {
                $resourceId .= '/' . (int) $params['commentId'];
            }
            
            return $resourceId;
        }
        else
        {
            return false;
        }
    }
    
    public function isNavigable( ResourceLocator $locator )
    {
        if (  $locator->hasResourceId() )
        {
            $parts = explode( '/', ltrim( $locator->getResourceId(), '/' ) );
            
            if ( count( $parts ) <= 1 )
            {
                return true;
            }
            else
            {
                return false;
            }
        }
        else
        {
            return $locator->inModule() && $locator->getModuleLabel() == 'CLBLOG';
        }
    }
    
    public function getParentResourceId( ResourceLocator $locator )
    {
        if ( $locator->hasResourceId() )
        {
            $parts = explode( '/', ltrim( $locator->getResourceId(), '/' ) );
            
            if ( count($parts) == 2 )
            {
                return $parts[0];
            }
            else
            {
                return false;
            }
        }
        else
        {
            return false;
        }
    }
    
    public function getResourceList( ResourceLocator $locator )
    {
        $tbl = get_module_course_tbl( array('blog_posts','blog_comments'), $locator->getCourseId() );
        
        if ( $locator->inGroup() )
        {
            $groupSql = "WHERE groupId = "
                . Claroline::getDatabase()->escape($locator->getGroupId())
                ;
        }
        else
        {
            $groupSql = "WHERE groupId = 0";
        }
        
        $resourceList = new LinkerResourceIterator;
        
        if ( $locator->hasResourceId() )
        {
            $parts = explode( '/', ltrim( $locator->getResourceId(), '/' ) );
            
            // if ( count( $parts ) == 1 )
            {
                $sql = "SELECT `id`, `contents`\n"
                    . "FROM `{$tbl['blog_comments']}`\n"
                    . "WHERE postId = " . Claroline::getDatabase()->escape($parts[0])
                    ;
                
                $res = Claroline::getDatabase()->query($sql);
                
                foreach ( $res as $comment )
                {
                    $commentLoc = new ClarolineResourceLocator(
                        $locator->getCourseId(),
                        'CLBLOG',
                        (int) $parts[0] . '/' . (int) $comment['id']
                    );
                    
                    $commentResource = new LinkerResource(
                        substr ( $comment['contents'], 0, 15 ) . '[...]',
                        $commentLoc,
                        true,
                        true,
                        false
                    );
                    
                    $resourceList->addResource( $commentResource );
                }
            }
        }
        else
        {
            $sql = "SELECT `id`, `title`\n"
                . "FROM `{$tbl['blog_posts']}`\n"
                . $groupSql
                ;
            
            $res = Claroline::getDatabase()->query($sql);
            
            foreach ( $res as $post )
            {
                $postLoc = new ClarolineResourceLocator(
                    $locator->getCourseId(),
                    'CLBLOG',
                    (int) $post['id']
                );
                
                $postResource = new LinkerResource(
                    $post['title'],
                    $postLoc,
                    true,
                    true,
                    true
                );
                
                $resourceList->addResource( $postResource );
            }
        }
        
        return $resourceList;
    }
}
