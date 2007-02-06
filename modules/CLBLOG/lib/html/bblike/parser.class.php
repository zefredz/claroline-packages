<?php // $Id$

    // vim: expandtab sw=4 ts=4 sts=4:
    
    /**
     * BBLike Parser class
     *
     * @version     1.9 $Revision$
     * @copyright   2001-2007 Universite catholique de Louvain (UCL)
     * @author      Frederic Minne <zefredz@claroline.net>
     * @license     http://www.gnu.org/copyleft/gpl.html 
     *              GNU GENERAL PUBLIC LICENSE
     * @package     HTML.BBLike
     */
    
    if ( count( get_included_files() ) == 1 ) die( '---' );

    class BBLike_Parser
    {
        function parseTags( $str )
        {
            $tags = array( 'b', 'i', 'u', 'strike', 'p', 'pre' );
            
            foreach ( $tags as $tag )
            {
                $str = str_replace( '['.$tag.']', '<'.$tag.'>', $str );
                $str = str_replace( '[/'.$tag.']', '</'.$tag.'>', $str );
            }
            
            $str = str_replace( '[br]', '<br />', $str );
            $str = str_replace( '[code]', '<pre class="codeFragment">', $str );
            $str = str_replace( '[/code]', '</pre>', $str );
            
            return $str;
        }
        
        function parseList( $str )
        {
            $str = str_replace( '[list]', '<ul>', $str );
            $str = str_replace( '[/list]', '</ul>', $str );
            $str = str_replace( '[li]', '<li>', $str );
            $str = str_replace( '[/li]', '</li>', $str );
            
            return $str;
        }
        
        function parseUrl( $str )
        {
            $str = preg_replace(
                '~\[url=([^\]]*)\]~'
                , "<a rel=\"nofollow\" href=\"$1\">"
                , $str );
                
            $str = preg_replace(
                '~\[url\]([^\[]*)~'
                , "<a rel=\"nofollow\" href=\"$1\">$1"
                , $str );
                
            $str = str_replace( '[/url]', '</a>', $str );
                
            return $str;
        }
        
        function parseQuote( $str )
        {
            $str = preg_replace(
                '~\[quote=([^\]]*)\]~'
                , "<i>$1</i> said :<pre class=\"quote\">"
                , $str );
                
            $str = str_replace(
                '[quote]'
                , "<pre class=\"quote\">"
                , $str );
                
            $str = str_replace( '[/quote]', '</pre>', $str );
                
            return $str;
        }
        
        /* function highlightCode( $str )
        {
            $regexp = '#\[code=([a-z]*)\](.*?)\[/code\]#si';
            $matches = array();
            if ( 0 != ( $count = preg_match ( $regexp, $str, $matches ) ) )
            {
                if ( 1 === $count )
                {
                    $language = $matches[1];
                    $code = $matches[2];
                    $geshi =& new Geshi( $code, $language );
                    $replacement = '<div class="codeFragment">'.$geshi->parse_code().'</div>';
                    $to_replace = '[code='.$language.']'.$code.'[/code]';
                    $str = str_replace( $to_replace, $replacement, $str );
                }
                else
                {
                    for ( $i = 0; $i < $count; $i++ )
                    {
                        $language = $matches[1][$i];
                        $code = $matches[2][$i];
                        $geshi =& new Geshi( $code, $language );
                        $replacement = '<div class="codeFragment">'.$geshi->parse_code().'</div>';
                        $to_replace = '[code='.$language.']'.$code.'[/code]';
                        $str = str_replace( $to_replace, $replacement, $str );
                    }
                }
                return $str;
            }
            else
            {
                return $str;
            }
        } */
        
        function parseImage( $str )
        {
            $str = preg_replace(
                '~\[img=([^\]]*)\]([^\[]*)~'
                , "<img src=\"$1\" alt=\"$2\" />"
                , $str );
                
            $str = preg_replace(
                '~\[img\]([^\[]*)~'
                , "<img src=\"$1\" alt=\"$1\" />"
                , $str );
                
            $str = str_replace( '[/img]', '', $str );
            
            return $str;
        }
        
        function parseNl( $str )
        {
            return preg_replace( '/(\r\n|\r|\n)[ \t]*(\r\n|\r|\n)/', "<br />", $str );
        }
        
        function parse( $str )
        {
            // FIXME : XSS
            // $str = htmlspecialchars( $str );
            // $str = Comment_Parser::highlightCode( $str );
            $str = BBLike_Parser::parseTags( $str );
            $str = BBLike_Parser::parseList( $str );
            $str = BBLike_Parser::parseUrl( $str );
            $str = BBLike_Parser::parseQuote( $str );
            $str = BBLike_Parser::parseImage( $str );
            $str = BBLike_Parser::parseNl( $str );
            
            return $str;
        }
        
        function getTagList()
        {
            $tagList = array( 
                  'list'
                , 'code'
                // , 'code='
                , 'pre'
                , 'b'
                , 'i'
                , 'u'
                , 'li'
                , 'strike'
                , 'br'
                , 'p'
                , 'quote'
                , 'quote='
                , 'url'
                , 'url='
                , 'img'
                , 'img=' );
            
            return $tagList;
        }
    }
?>