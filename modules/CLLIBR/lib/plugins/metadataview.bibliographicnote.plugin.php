<?php // $Id$
/**
 * Online library for Claroline
 *
 * @version     CLLIBR 1.1.5
 * @copyright   2001-2013 Universite catholique de Louvain (UCL)
 * @license     http://www.gnu.org/copyleft/gpl.html (GPL) GENERAL PUBLIC LICENSE
 * @package     CLLIBR
 * @author      Frederic Fervaille <frederic.fervaille@uclouvain.be>
 */

class BibliographicNote extends MetaDataView implements Renderable
{
    protected $propertyList = array( 'title'
                                   , 'authors'
                                   , 'publisher'
                                   , 'location'
                                   , 'date'
                                   , 'pages' );
    
    protected $translator = array( 'author' => 'authors'
                                 , 'publication date' => 'date'
                                 , 'pagination' => 'pages' );
    
    protected $templateList = array( 'default' => '<strong>%title</strong>, %authors. <em>%publisher</em>, %location, %date.'
                                   , 'book' => '%authors. (%date) <em>%title</em>. %location : %publisher, %pages' );
    
    public function render()
    {
        $cite = $this->templateList[ $this->type ];
        
        foreach( $this->metadatas as $metadata => $value )
        {
            if ( is_array( $value ) )
            {
                $value = implode( ', ' , $value );
            }
            
            $cite = str_replace( '%' . $metadata , $value , $cite );
        }
        
        return $cite;
    }
}