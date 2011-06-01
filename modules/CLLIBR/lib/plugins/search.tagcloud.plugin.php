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
class TagCloud
{
    protected $cloud;
    protected $nbMax;
    protected $cmd;
    
    /**
     * Constructor
     */
    public function __construct( $database , $cmd = 'index.php?cmd=rqSearch' )
    {
        $this->database = $database;
        $this->tbl = get_module_main_tbl( array( 'library_metadata' ) );
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
                resource_id,
                value
            FROM
                `{$this->tbl['library_metadata']}`
            WHERE
                name = `keyword`" );
        
        $this->cloud = array();
        $this->nbMax = 0;
        
        foreach( $result as $line )
        {
            $value = $line[ 'value' ];
            if ( ! isset( $this->cloud[ $value ] ) ) $this->cloud[ $value ] = 0;
            $this->cloud[ $value ][ 'count' ]++;
            $this->cloud[ $value ][ 'result' ][] = $line[ 'resource_id' ];
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
        
        foreach( $this->cloud as $tag => $data )
        {
            $size = int( 20 * $data[ 'count' ] / $this->nbMax );
            $html .= '<a href="' . $this->cmd .'&keyword=' . $tag . '" style="font-size: ' . $size . 'pt;">'
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
    public function getResult( $keyword )
    {
        if ( ! array_key_exists( $keywords , $this->cloud ) )
        {
            throw new Exception( 'invalid keyword' );
        }
        
        return $this->cloud[ $keyword ][ 'result' ];
    }
}