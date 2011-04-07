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
 * A VERY simple class for single fulltext search
 */
class FulltextSeach
{
    /**
     * @param array $searchQuery
     */
    public function search( $searchString )
    {
        return $this->resultSet = $this->database->query( "
            SELECT
                resource_id,
                name,
                value,
                MATCH (name,value) AGAINST (" . $this->database()->quote( $searchString ) . ") AS score
            FROM
                `{$this->tbl['library_metadata']}`
            WHERE
                MATCH (name,value) AGAINST (" . $this->database()->quote( $searchString ) . ")"
        );
    }
    
    /**
     *
     */
    public function bake()
    {
        $result = array();
        
        foreach( $this->resultSet as $line )
        {
            $match = array( 'name' => $line[ 'name' ]
                          , 'value' => $line[ 'value' ] );
            
            $result[ $id ][ 'matches' ][] = $match;
            $result[ $id ][ 'score' ] = $line[ 'score' ];
        }
        
        return $this->searchResult = $result;
    }
}