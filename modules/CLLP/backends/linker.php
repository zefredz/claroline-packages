<?php // $Id$

/**
 * Linker used to display available resources.
 * The file cllp.linker.cnr.php need to be in the connector folder of the module.
 * If the file is present, the linker will list the available resources for this module.
 *
 * @version 0.1 $Revision$
 * @copyright (c) 2001-2007 Universite catholique de Louvain (UCL)
 * @license http://www.gnu.org/copyleft/gpl.html (GPL) GENERAL PUBLIC LICENSE
 * @package CLLP
 * @author Dimitri Rambout <dim@claroline.net>
 *
 * @return string JSON content
 */

try
{
    require_once dirname(__FILE__) . '/../../../claroline/inc/claro_init_global.inc.php';
    
    FromKernel::uses( 'core/linker.lib', 'utils/ajax.lib' );

    ResourceLinker::init();

    $locator = isset( $_REQUEST['crl'] )
        ? ClarolineResourceLocator::parse($_REQUEST['crl'])
        : ResourceLinker::$Navigator->getCurrentLocator( array() );
        ;

    if ( !ResourceLinker::$Navigator->isNavigable( $locator ) )
    {
        throw new Exception('Resource not navigable');
    }
    
    $resourceList = ResourceLinker::$Navigator->getResourceList( $locator );
    
    $elementList = $resourceList->toArray();

    // CLLP filter : get only resources of modules that defines a file cllp.linker.cnr.php
    // if the module do not define that it means that its resources are not 'scorm compliant'
    // and can therefore not be use in learning path
    if( !empty($elementList) )
    {
        foreach ($elementList as $element )
        {
            $elementLocator = ClarolineResourceLocator::parse($element['crl']);
            if( $elementLocator instanceof ClarolineResourceLocator )
            {
                if( $elementLocator->inModule() )
                {
                    $moduleLabel = $elementLocator->getModuleLabel();
                    
                    $navigatorPath = get_module_path( $moduleLabel ) . '/connector/cllp.linker.cnr.php';
                        
                    if ( file_exists( $navigatorPath ) )
                    {
                        // add this element to elementList to display
                        $cllpCompatibleElementList[] = $element;
                    }
                }
            }
        }
    }
    else
    {
        $cllpCompatibleElementList = array();
    }
    
    $resourceArr = array();
    $resourceArr['name'] = ResourceLinker::$Resolver->getResourceName( $locator );
    $resourceArr['crl'] = $locator->__toString();
    
    $parent = ResourceLinker::$Navigator->getParent( $locator );
    
    $resourceArr['parent'] = (empty($parent) ? false : $parent->__toString());
    $resourceArr['resources'] = $cllpCompatibleElementList;
    
    $response = new Json_Response( $resourceArr );
}
catch (Exception $e )
{
    $response = new Json_Exception( $e );
}

echo $response->toJson();
exit;
?>