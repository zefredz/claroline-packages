<?php // $Id$

// vim: expandtab sw=4 ts=4 sts=4:

/**
 * Resource Resolver for the CLLP tool
 *
 * @version 1.9 $Revision$
 * @copyright (c) 2001-2008 Universite catholique de Louvain (UCL)
 * @license http://www.gnu.org/copyleft/gpl.html (GPL) GENERAL PUBLIC LICENSE
 * @author claroline Team <cvs@claroline.net>
 * @author Dimitri Rambout <dim@claroline.net>
 * @package GRAPPLE
 *
 */

FromKernel::uses('fileManage.lib', 'file.lib');

class GRAPPLE_Resolver implements ModuleResourceResolver
{
    public function resolve ( ResourceLocator $locator )
    {
        $baseUrl = get_module_url('GRAPPLE');
        
        if ( $locator->hasResourceId() )
        {
            require_once( get_module_path('GRAPPLE') . '/lib/item.class.php' );
            
            $item = new item();
            
            if( $item->load( $locator->getResourceId() ) )
            {
                switch( $item->getType() )
                {
                    case 'MODULE' :
                    {
                        //$url = "scormServer.php?cmd=rqContentUrl&cidReq={$locator->getCourseId()}&pathId={$item->getPathId()}&itemId={$locator->getResourceId()}";
                        
                        $resolver = new ResourceLinkerResolver();
                        $itemUrl = $resolver->resolve(ClarolineResourceLocator::parse($item->getSysPath()));
                        
                        // fix ? or &amp; depending if there is already a ? in url
                        $itemUrl .= ( false === strpos($itemUrl, '?') )? '?':'&';
                        
                        $itemUrl .= 'calledFrom=GRAPPLE&embedded=true'; 
                        
                        // temporary fix for document tool (FIXME when linker will be updated)
                        // we have to open a frame that will discuss with API and open the document instead 
                        // of directly opening it
                        $itemUrl = str_replace('backends/download.php','document/connector/cllp.frames.cnr.php',$itemUrl);
                        
                        return $itemUrl;
                    }
                    break;
                    case 'SCORM' :
                    {
                        $scormBaseUrl = get_path('coursesRepositoryWeb') . claro_get_course_path() . '/scormPackages/path_' . $item->getPathId() . '/';
    
                        return $itemUrl = $scormBaseUrl . $item->getSysPath();
                    }
                    brak;
                }
                
            }
            else
            {
                return get_module_entry_url('GRAPPLE');
            }
        }
        else
        {
            return get_module_entry_url('GRAPPLE');
        }
    }

    public function getResourceName( ResourceLocator $locator)
    {
        if( $locator->hasResourceId() && $locator->inCourse() )
        {
            return $this->_getTitle( $locator->getCourseId(), $locator->getResourceId() );
        }
        
        return false;
    }
    
    /**
     * @param  $course_sys_code identifies a course in data base
     * @param  $id integer who identifies the exercice
     * @return the title of a annoncement
     */
    function _getTitle( $courseId , $itemId )
    {
        $tbl_cdb_names = get_module_course_tbl( array( 'lp_item' ), $courseId );
        $tblItem = $tbl_cdb_names['lp_item'];

        $sql = 'SELECT `title`
                FROM `'.$tblItem.'`
                WHERE `id`='. (int) $itemId;
        $title = claro_sql_query_get_single_value($sql);

        return $title;
    }
}

/**
 * Class GRAPPLE Navigator
 *
 * @package GRAPPLE
 *
 */
class GRAPPLE_Navigator implements ModuleResourceNavigator
{
    public function getResourceId( $params = array() )
    {
        return false;
    }
    
    public function isNavigable( ResourceLocator $locator )
    {
        if (  $locator->hasResourceId() )
        {
            if( strpos( $locator->getResourceId(), 'path_') === FALSE )
            {
                return false;
            }
            else
            {
                return true;
            }
        }
        else
        {
            return $locator->inModule() && $locator->getModuleLabel() == 'GRAPPLE';
        }
    }
    
    public function getParentResourceId( ResourceLocator $locator )
    {
        return false;
    }
    
    public function getResourceList( ResourceLocator $locator )
    {
        $tbl_cdb_names = get_module_course_tbl( array( 'lp_path', 'lp_item', 'lp_attempt', 'lp_item_attempt', 'lp_item_blockcondition' ), $locator->getCourseId() );
        $tblPath = $tbl_cdb_names['lp_path'];
        $tblItem = $tbl_cdb_names['lp_item'];

        $resourceList = new LinkerResourceIterator();
        
        if( strpos( $locator->getResourceId(), 'path_') !== FALSE )
        {
            require_once( get_module_path('GRAPPLE') . '/lib/item.class.php' );
            
            $pathId = (int) str_replace( 'path_', '', $locator->getResourceId() );
            
            $itemList = new PathItemList($pathId);
            $itemListArray = $itemList->getFlatList();
            
            foreach( $itemListArray as $item )
            {
                $fileLoc = new ClarolineResourceLocator(
                    $locator->getCourseId(),
                    'GRAPPLE',
                    //'viewer/scormServer.php?cmd=rqContentUrl&cidReq=' . $locator->getCourseId() . '&pathId=' . $pathId . '&itemId=' . $item['id']
                    $item['id']
                );
                
                $fileResource = new LinkerResource(
                    str_repeat('&nbsp;&nbsp;', $item['deepness'] ) . $item['title'],
                    $fileLoc,
                    $item['type'] == 'CONTAINER' ? false : true,
                    $item['visibility'] == 'VISIBLE' ? true :false,
                    $item['type'] == 'CONTAINER' ? false : true
                );
                
                $resourceList->addResource( $fileResource );
            }
        }
        else
        {
        
            $sql = "SELECT `id`, `title`, `visibility`
                    FROM `". $tblPath ."`
                    ORDER BY `title` ASC";
            
            $pathList = claro_sql_query_fetch_all_rows($sql);
    
            foreach( $pathList as $path )
            {    
                $fileLoc = new ClarolineResourceLocator(
                    $locator->getCourseId(),
                    'GRAPPLE',
                    'path_' . $path['id']
                );
    
                $fileResource = new LinkerResource(
                    $path['title'],
                    $fileLoc,
                    false,
                    ($path['visibility'] == 'VISIBLE' ? true : false),
                    true
                );
    
                $resourceList->addResource( $fileResource );
            }
        }

        return $resourceList;
    }
}
