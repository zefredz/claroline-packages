<?php // $Id$
/**
 * Online library for Claroline
 *
 * @version     CLLIBR 0.3.3 $Revision$ - Claroline 1.9
 * @copyright   2001-2011 Universite catholique de Louvain (UCL)
 * @license     http://www.gnu.org/copyleft/gpl.html (GPL) GENERAL PUBLIC LICENSE
 * @package     CLLIBR
 * @author      Frederic Fervaille <frederic.fervaille@uclouvain.be>
 */

/**
 * An abstract class for search engines
 * @param ResultSet $resultSet
 * @param array $searchResult
 */
abstract class Search
{
    protected $resultSet;
    protected $searchResult;
    
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
        return $this->searchResult;
    }
}