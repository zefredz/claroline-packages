<?php // $Id$

    // vim: expandtab sw=4 ts=4 sts=4:
    
    if ( count( get_included_files() ) == 1 ) die( '---' );
    
    /**
     * @author  Frederic Minne <zefredz@claroline.net>
     * @copyright Copyright &copy; 2006, Frederic Minne
     * @license http://www.gnu.org/copyleft/gpl.html GNU General Public License
     * @version 1.0
     * @package Glossary
     */
    
    require_once dirname(__FILE__) . '/../url.lib.php';
    require_once dirname(__FILE__) . '/../javascript/popuphelper.class.php';
    
    /**
    * Text Highlighter class for the glossary tool
    */
    class Glossary_Highlighter
    {
        /**
        * highlight a word in a given text
        * @param    string $text text to highlight
        * @param    string $word word to highlight in the text
        * @param    string $callback url callback
        * @return   highlighted string
        */
        function highlight( $text, $word, $callback = '' )
        {
            // initialising
            
            if ( empty( $callback ) )
            {
                $callback = $_SERVER['PHP_SELF'];
            }
            
            $urlVarValue = $this->urlHexEncode( $word );
            $urlVarName = 'word';
            
            $callback = add_request_variable_to_url( $callback
                , $urlVarName, $urlVarValue );
                
            $callback = add_request_variable_to_url( $callback
                , 'inPopup', 'true' );
            
            // Use %$word% for further replacement        
            $replacement = PopupHelper::popupLink( 
                $callback, 
                "%".$word."%", 
                $this->urlHexEncode('dictionary'), 
                300, 
                300, 
                'glossaryEntry' );
            
            // Use case insensitive modifier with %$word% replacement
            // to replace in text with exact match for word and avoid
            // getting 'earl' replaced with 'Earl' when 'Earl' is in the
            // text word list
            $result = preg_replace( 
                '#('. $word .')#i'
                , str_replace( "%".$word."%", "\\1", $replacement)
                , $text);
            
            // cleaning result
            $result = preg_replace(
                '~(\<a href="#" onclick="[^"]+" class="glossaryEntry"\>[\w\s]*)'
                . '<a href="#" onclick="[^"]+" class="glossaryEntry">('
                . $word . ')</a>([\w\s]*\</a\>)~'
                , "$1$2$3"
                , $result
            );
            
            $result = preg_replace(
                '~(\w+)<a href="#" onclick="[^"]+" class="glossaryEntry">('
                . $word . ')</a>~'
                , "$1$2"
                , $result
            );
            
            $result = preg_replace( '~<a href="#" onclick="[^"]+" class="glossaryEntry">('
                . $word . ')</a>(\w+)~'
                , "$1$2"
                , $result
            );

            
            return $result;
        }
        
        /**
        * Highlight a list of words in the text
        * @param    array $list list of words
        * @param    string $text text to highlight
        * @param    string $callback url callback for highlighter
        * @return   string highlighted text
        * @see      Glossary_Highlighter#highlight( $text, $word, $callback = '' )
        */
        function highlightList( $text, $list, $callback = '' )
        {
            $this->sort( $list );
            
            foreach ( $list as $word )
            {
                 $text = $this->highlight( $text, $word, $callback );
                 // FIXME this is a patch !!!!!
                 // $text = Glossary_Highlighter::highlight( $text, ucfirst($word), $callback );
            }

            return $text;
        }
        
        /**
        * Sort a list of words based on their length
        * NB:   this is OK since 'mail' is smaller than 'mailbox' and
        *       'software' is smaller than 'software engineering'
        * @param    array $list reference to the list to sort
        * @return   void (the list passed by *reference* has been modified)
        */
        function sort( &$list )
        {
            usort( $list, array( "Glossary_Highlighter", "compare" ) );
        }
        
        /**
        * Compare strings based on their length
        * @param    string $a
        * @param    string $b
        * @return
        *   0   if strlen( $a ) is the same as strlen( $b )
        *   -1  if strlen( $a ) is greater than strlen( $b )
        *   +1  if strlen( $a ) is smaller than strlen( $b )
        */
        function compare( $a, $b )
        {
            if ( strlen( $a ) == strlen( $b ) )
            {
                return 0;
            }
            elseif ( strlen( $a ) > strlen( $b ) )
            {
                return -1;
            }
            else
            {
                return 1;
            }
        }
        
        /**
        * Strong hexadecimal encoding for url : all characters are converted to
        * hexadecimal value
        * @param    string $str string to encode
        * @return   string encoded string
        */
        function urlHexEncode ( $str )
        {
            $encoded = bin2hex( $str );
            $encoded = chunk_split( $encoded, 2, '%' );
            $encoded = '%'.substr( $encoded, 0, strlen( $encoded ) - 1 );

            return $encoded;
        }
    }
    
    class Glossary_Print_Highlighter extends Glossary_Highlighter
    {
        /**
        * highlight a word in a given text
        * @param    string $text text to highlight
        * @param    string $word word to highlight in the text
        * @param    string $callback url callback
        * @return   highlighted string
        */
        function highlight( $text, $word, $callback = '' )
        {
            $replacement = '<span class="word">%'.$word.'%</span>';
            
            $result = preg_replace( 
                '#('. $word .')#i'
                , str_replace( "%".$word."%", "\\1", $replacement)
                , $text);
            
            // cleaning result
            $result = preg_replace(
                '~(\<span class="word"\>+)~'
                , '<span class="word">'
                , $result
            );
            
            $result = preg_replace(
                '~(\</span\>+)~'
                , '</span>'
                , $result
            );

            
            return $result;
        }
    }
?>