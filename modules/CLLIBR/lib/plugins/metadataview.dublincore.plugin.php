<?php // $Id$
/**
 * Online library for Claroline
 *
 * @version     CLLIBR 0.6.4 $Revision$ - Claroline 1.9
 * @copyright   2001-2011 Universite catholique de Louvain (UCL)
 * @license     http://www.gnu.org/copyleft/gpl.html (GPL) GENERAL PUBLIC LICENSE
 * @package     CLLIBR
 * @author      Frederic Fervaille <frederic.fervaille@uclouvain.be>
 */

class DublinCore extends MetaDataView implements Renderable, Exportable
{
    protected $propertyList = array( 'title'
                                   , 'creator'
                                   , 'subject'
                                   , 'description'
                                   , 'publisher'
                                   , 'contributor'
                                   , 'date'
                                   , 'type'
                                   , 'format'
                                   , 'identifier'
                                   , 'source'
                                   , 'language'
                                   , 'relation'
                                   , 'coverage'
                                   , 'rights' );
    
    protected $translator = array( 'author' => 'creator'
                                 , 'publication date' => 'date'
                                 , 'ISBN' => 'identifier'
                                 , 'ISSN' => 'identifier' );
    
    public function render()
    {
        $render = '';
        
        foreach( $this->metadatas as $name => $values )
        {
            if ( ! is_array( $values) )
            {
                $values = array( $values );
            }
            
            if ( in_array( $name , $this->propertyList ) )
            {
                foreach( $values as $value )
                {
                    $render .= '<meta name="DC.' . $name . '" content = "' . $value . '" />' . "\n";
                }
            }
        }
        
        return $render;
    }
    
    public function Export( $url )
    {
        $xml  = '<?xml version="1.0"?>' . "\n";
        $xml .= '<!DOCTYPE rdf:RDF PUBLIC "-//DUBLIN CORE//DCMES DTD 2002/07/31//EN"
                "http://dublincore.org/documents/2002/07/31/dcmes-xml/dcmes-xml-dtd.dtd">' . "\n";
        $xml .= '<rdf:RDF xmlns:rdf="http://www.w3.org/1999/02/22-rdf-syntax-ns#"
                xmlns:dc ="http://purl.org/dc/elements/1.1/">' . "\n";
        $xml .= '    <rdf:Description rdf:about="' . $url . '">' . "\n";
        
        foreach( $this->metadatas as $name => $values )
        {
            if ( ! is_array( $values ) )
            {
                $values = array( $values );
            }
            
            if ( in_array( $name , $this->propertyList ) )
            {
                foreach( $values as $value )
                {
                    $xml .= '        <dc:' . $name . '>' . $value . '</dc:' . $name . '>' . "\n";
                }
            }
        }
        
        $xml .= '    </rdf:Description>' . "\n";
        $xml .= '</rdf:RDF>';
        
        return( $xml );
    }
}