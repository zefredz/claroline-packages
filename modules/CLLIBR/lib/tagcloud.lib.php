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
                metadata_value as keyword
            FROM
                `{$this->tbl['library_metadata']}`
            WHERE
                metadata_name = " . $this->database->quote( Metadata::KEYWORD ) );
        
        $this->cloud = array();
        
        foreach( $result as $line )
        {
            $keyword = $line[ 'keyword' ];
            if ( ! isset( $this->cloud[ $keyword ] ) ) $this->cloud[ $keyword ] = 0;
            $this->cloud[ $keyword ]++;
        }
        
        ksort( $this->cloud );
        $this->nbMax = ! empty( $this->cloud )
                     ? max( $this->cloud )
                     : 0;
    }
    
    /**
     * Renders the Tag cloud
     * @return string $html
     */
    public function render()
    {
        $html = '';
        
        if ( ! empty( $this->cloud ) )
        {
            foreach( $this->cloud as $tag => $count )
            {
                $size = round( 20 * $count / $this->nbMax );
                $html .= '<a href="'
                       . claro_htmlspecialchars( $this->cmd .'&keyword=' . $tag )
                       . '" style="font-size: ' . $size . 'pt; margin: 5px;">'
                       . $tag . '</a>' . "\n";
            }
        }
        
        return $html;
    }
    
    /**
     * Gets all keywords
     * @return array $keywordList
     */
    public function getAllKeywords()
    {
        return array_keys( $this->cloud );
    }
}