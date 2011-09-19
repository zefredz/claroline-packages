<?php // $Id$
/**
 * Online library for Claroline
 *
 * @version     CLLIBR 0.9.2 $Revision$ - Claroline 1.9
 * @copyright   2001-2011 Universite catholique de Louvain (UCL)
 * @license     http://www.gnu.org/copyleft/gpl.html (GPL) GENERAL PUBLIC LICENSE
 * @package     CLLIBR
 * @author      Frederic Fervaille <frederic.fervaille@uclouvain.be>
 */

class Bibliography extends MetaDataView implements Exportable
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
    
    protected $template = array( 'default' => '<strong>%title</strong>, %authors. <em>%publisher</em>, %location, %date.'
                               , 'book' => '%authors. (%date) <em>%title</em>. %location : %publisher, %pages' );
    
    public function export( $type )
    {
        if ( ! array_key_exists( $type , $this->template ) )
        {
            $type = 'default';
        }
        
        $cite = $this->template[ $type ];
        
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