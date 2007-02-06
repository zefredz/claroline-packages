<?php  // $Id$
    
    // vim: expandtab sw=4 ts=4 sts=4 foldmethod=marker:
    
    if ( count( get_included_files() ) == 1 ) die( '---' );
    
    /**
     * Display functions
     * @author  Frederic Minne <zefredz@claroline.net>
     * @copyright Copyright &copy; 2006, Frederic Minne
     * @license http://www.gnu.org/copyleft/gpl.html GNU General Public License
     * @version 1.0
     * @package Glossary
     */
     
    require_once dirname(__FILE__) . '/../javascript/popuphelper.class.php';
	
    /**
     * Display toolbar for the glossary tool
     * @return  string toolbar
     */
	function displayGlossaryMenu()
    {
        $output = '<p>'
            . '<a class="claroCmd" href="'
            . $_SERVER['PHP_SELF'].'?page=text">'
            . '<img src="'.get_icon('glossary_text.gif').'" alt="['
            . get_lang( 'Texts' ) . ']"/> '
            . get_lang( 'Texts' )
            . '</a>'
            . '&nbsp;|&nbsp;'
            . '<a class="claroCmdDisabled" href="'
            . $_SERVER['PHP_SELF'].'?page=list">' // &amp;action=showDict&amp;dictionaryId=0">'
            . '<img src="'.get_icon('glossary_dict.gif').'" alt="['
            . get_lang( 'Dictionary' ) . ']"/> '
            . get_lang( 'Dictionary' )
            . '</a>'
            . '&nbsp;|&nbsp;'
            . '<a class="claroCmd" href="'
            . $_SERVER['PHP_SELF'].'?page=tags&amp;action=showTagList">'
            . '<img src="'.get_icon('glossary_tags.gif').'" alt="['
            . get_lang( 'Tags' ) . ']"/> '
            . get_lang( 'Tags' )
            . '</a>'
            . '</p>' . "\n"
            ;
            
        return $output;
    }
    
    /**
     * Display dictionary selector
     * @param   array dictionaryList
     * @param   int defaultDictionary id of the selected dictionary
     * @return  string dictionary selector
     */
    function dictionarySelector( $dictionaryList, $defaultDictionary )
    {
        $dictionaryList = array_merge( array( 
            array( 'id' => 0, 
                'name' => get_lang( 'Default' ), 
                'description' => get_lang('Default dictionary') )
            )
            ,$dictionaryList
        );
        
        $select = '<select name="dictionaryId">' . "\n";
        
        foreach ( $dictionaryList as $dictionary )
        {
            $selected = ( (int)$dictionary['id'] === $defaultDictionary ) ? ' selected="selected"' : '';
            
            $select .= '<option value="' . $dictionary['id'] . '"' . $selected . '>'
                . htmlspecialchars($dictionary['name'])
                . '</option>'
                . "\n"
                ;
        }
        
        $select .= '</select>' . "\n";
        
        return $select;
    }
    
    /**
     * Add dictionaryId and parentId to url
     * NOT USED
     * @param   string url (reference)
     * @param   int dictionaryId
     * @param   int parentId
     * @return  string url
     */
    function makeGlossaryBaseUrl( &$url, $dictionaryId, $parentId )
    {
        if ( ! is_null( $dictionaryId ) ) 
        {
            add_request_variable_to_url( $url, 'dictionaryId', (int) $dictionaryId );
        }
        
        if ( ! is_null( $parentId ) ) 
        {
            add_request_variable_to_url( $url, 'parentId', (int) $parentId );
        }
    }
    
    /**
     * Display help link
     * @param   string about help subject
     * @return  string help link
     */
    function helpLink( $about )
    {
        $callback = $_SERVER['PHP_SELF'] . '?page=help&amp;inPopup=true&amp;about='
            . rawurlencode( $about )
            ;
            
        return PopupHelper::popupLink( 
            $callback, 
            get_lang( 'Help' ), 
            rawurlencode( $about ), 
            400, 
            600, 
            'claroCmd',
            get_icon('help_little.gif') );
    }
?>