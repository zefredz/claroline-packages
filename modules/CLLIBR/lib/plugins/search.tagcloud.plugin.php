<?php // $Id$
/**
 * Online library for Claroline
 *
 * @version     CLLIBR 0.5.0 $Revision$ - Claroline 1.9
 * @copyright   2001-2011 Universite catholique de Louvain (UCL)
 * @license     http://www.gnu.org/copyleft/gpl.html (GPL) GENERAL PUBLIC LICENSE
 * @package     CLLIBR
 * @author      Frederic Fervaille <frederic.fervaille@uclouvain.be>
 */

/**
 * A tag cloud
 */
class TagCloud extends Search
{
    protected $cloud;
    protected $nbMax;
    protected $cmd;
    
    /**
     * Constructor
     */
    public function __construct( $database , $cmd = 'index.php?cmd=rqSearch' )
    {
        parent::__construct( $database );
        $this->cmd = $cmd;
        $this->load();
    }
    
    /**
     * Loads the tag cloud datas
     * This method is called by the constructor
     */
    public function load()
    {
        $result = $this->database->query( "
            SELECT
                value
            FROM
                `{$this->tbl['library_metadata']}`
            WHERE
                name = \"keyword\"" );
        
        $this->cloud = array();
        $this->nbMax = 0;
        
        foreach( $result as $line )
        {
            $value = $line[ 'value' ];
            if ( ! isset( $this->cloud[ $value ] ) ) $this->cloud[ $value ] = 0;
            $this->cloud[ $value ]++;
        }
        
        ksort( $this->cloud );
        $this->nbMax = max( $this->cloud );
    }
    
    /**
     * Renders the Tag cloud
     * @return string $html
     */
    public function render()
    {
        $html = '<div id="tagCloud">';
        
        foreach( $this->cloud as $tag => $count )
        {
            $size = round( 20 * $count / $this->nbMax );
            $html .= '<a href="' . $this->cmd .'&keyword=' . $tag . '" style="font-size: ' . $size . 'pt; margin: 5px;">'
                   . $tag . '</a>';
        }
        
        $html .= '</div>';
        
        return $html;
    }
    
    /**
     * Get the result for a specified keyword
     * @param string $keyword
     * @return array $result
     */
    public function search( $keyword )
    {
        if ( ! array_key_exists( $keywords , $this->cloud ) )
        {
            throw new Exception( 'invalid keyword' );
        }
        
        return $this->resultSet = $this->database->query( "
            SELECT
                R.id
                R.title
                R.description
                R.creation_date
            FROM
                `{$this->tbl['library_resource']}` AS R
            INNER JOIN
                `{$this->tbl['library_metadata']}` AS M
            ON
                R_id = M.resource_id
            WHERE
                M.name = \"keyword\"
            AND
                M.value = " . $this->database->quote( $keyword ) . "
            ORDER BY
                R.creation_date DESC" );
    }
    
    /**
     *
     */
    public function bake()
    {
        $result = array();
        
        foreach( $this->resultSet as $line )
        {
            $result[ $line[ 'id' ] ] = array( 'title' => $line[ 'title' ]
                                            , 'description' => $line[ 'description' ]
                                            , 'date' => $line[ 'creation_date' ] );
        }
        
        return $this->searchResult = $result;
    }
}