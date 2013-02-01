<?php // $Id$
/**
 * Online library for Claroline
 *
 * @version     CLLIBR 1.1.5 $Revision$ - Claroline 1.11.5
 * @copyright   2001-2013 Universite catholique de Louvain (UCL)
 * @license     http://www.gnu.org/copyleft/gpl.html (GPL) GENERAL PUBLIC LICENSE
 * @package     CLLIBR
 * @author      Frederic Fervaille <frederic.fervaille@uclouvain.be>
 */

/**
 * An abstract class for search engines
 * @param array or ResultSet $searchResult
 * @param array bakedResult
 */
abstract class Search
{
    protected $searchResult;
    protected $bakedResult;
    
    protected $database;
    
    /**
     * Constructor
     */
    public function __construct( $database )
    {
        $this->database = $database;
        $this->tbl = get_module_main_tbl( array( 'library_metadata'
                                               , 'library_resource' ) );
    }
    
    abstract public function search( $searchString );
    abstract public function bake();
    
    /**
     * Gets the result
     * @return array $result
     */
    public function getResult()
    {
        return $this->bakedResult;
    }
    
    /**
     * Static method
     * Highlight a part of a given text
     * @param string $highlight
     * @param string $phrase
     * @return string $highlightedPhrase
     */
    public static function highlight( $highlight , $phrase )
    {
        return str_ireplace( $highlight
                            , '<strong>' . $highlight . '</strong>'
                            , $phrase );
    }
}