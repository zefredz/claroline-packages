<?php // $Id$
/**
 * Online library for Claroline
 *
 * @version     CLLIBR 0.7.0 $Revision$ - Claroline 1.9
 * @copyright   2001-2011 Universite catholique de Louvain (UCL)
 * @license     http://www.gnu.org/copyleft/gpl.html (GPL) GENERAL PUBLIC LICENSE
 * @package     CLLIBR
 * @author      Frederic Fervaille <frederic.fervaille@uclouvain.be>
 */

/**
 * A tag cloud
 */
class KeywordSearch extends Search
{
    /**
     * Get the result for a specified keyword
     * @param string $keyword
     * @return array $result
     */
    public function search( $keyword )
    {
        return $this->resultSet = $this->database->query( "
            SELECT
                T.resource_id AS id,
                T.value AS title,
                K.value AS keyword
            FROM
                `{$this->tbl['library_metadata']}` AS T,
                `{$this->tbl['library_metadata']}` AS K
            WHERE
                T.id = K.resource_id
            AND
                K.name = " . $this->database->quote( Metadata::KEYWORD ) . "
            AND
                K.value = " . $this->database->quote( $keyword ) );
    }
    
    /**
     *
     */
    public function bake()
    {
        $result = array();
        
        foreach( $this->resultSet as $line )
        {
            $result[ $line[ 'id' ] ][ 'title' ] = $line[ 'title' ];
            $result[ $line[ 'id' ] ][ 'keywords' ][] = $line[ 'keyword' ];
        }
        
        return $this->searchResult = $result;
    }
}