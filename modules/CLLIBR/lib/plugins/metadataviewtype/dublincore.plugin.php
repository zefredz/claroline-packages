<?php // $Id$
/**
 * Online library for Claroline
 *
 * @version     CLLIBR 0.2.7 $Revision$ - Claroline 1.9
 * @copyright   2001-2010 Universite catholique de Louvain (UCL)
 * @license     http://www.gnu.org/copyleft/gpl.html (GPL) GENERAL PUBLIC LICENSE
 * @package     CLLIBR
 * @author      Frederic Fervaille <frederic.fervaille@uclouvain.be>
 */

class DublinCore extends MetaDataView
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
                                 , 'publication date' => 'date' );
    
    public function render()
    {
        $render = '';
        
        foreach( $this->metadatas as $name => $values )
        {
            if ( in_array( $name , $this->propertyList ) )
            {
                $render .= '<meta name="DC.' . $name . '" content = "' . implode( ", " , $values ) . '" />' . "\n";
            }
        }
        
        return $render;
    }
}