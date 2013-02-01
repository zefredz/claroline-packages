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
        return $this->searchResult = $this->database->query( "
            SELECT
                T.resource_id          AS id,
                T.metadata_value       AS title,
                K.metadata_value       AS keyword
            FROM
                `{$this->tbl['library_metadata']}` AS T
            INNER JOIN
                `{$this->tbl['library_metadata']}` AS M
            ON
                T.resource_id = M.resource_id
            INNER JOIN
                `{$this->tbl['library_metadata']}` AS K
            ON
                T.resource_id = K.resource_id
            AND
                T.metadata_name = " . $this->database->quote( Metadata::TITLE ) . "
            WHERE
                K.metadata_name = " . $this->database->quote( Metadata::KEYWORD ) . "
            AND
                M.metadata_name = " . $this->database->quote( Metadata::KEYWORD ) . "
            AND
                M.metadata_value = " . $this->database->quote( $keyword ) );
    }
    
    /**
     *
     */
    public function bake()
    {
        $result = array();
        
        foreach( $this->searchResult as $line )
        {
            $result[ $line[ 'id' ] ][ 'title' ] = $line[ 'title' ];
            $result[ $line[ 'id' ] ][ 'keywords' ][] = $line[ 'keyword' ];
        }
        
        return $this->bakedResult = array( $result );
    }
}