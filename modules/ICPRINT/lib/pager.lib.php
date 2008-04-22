<?php // $Id$

// vim: expandtab sw=4 ts=4 sts=4:
    
/**
 * Description
 *
 * @version     1.9 $Revision$
 * @copyright   2001-2008 Universite catholique de Louvain (UCL)
 * @author      Claroline Team <info@claroline.net>
 * @author      Frederic Minne <zefredz@claroline.net>
 * @license     http://www.gnu.org/copyleft/gpl.html
 *              GNU GENERAL PUBLIC LICENSE version 2 or later
 * @package     PACKAGE_NAME
 */

require_once dirname(__FILE__) . '/url.lib.php';

interface Claro_Utils_Pageable
{
    public function getPage( $currentOffset, $resultSetSize );
    
    //public function hasNext( $currentOffset, $resultSetSize );
    
    //public function hasPrev( $currentOffset, $resultSetSize );
    
    public function isOffsetValid( $offset );
    
    public function countPages( $resultSetSize );
}

class Claro_Utils_ArrayPager implements Claro_Utils_Pageable
{
    protected $array;
    
    public function __construct( $array )
    {
        $this->array = $array;
    }
    
    public function getPage( $currentOffset, $resultSetSize )
    {
        return array_slice( $this->array, $currentOffset, $resultSetSize );
    }
    
    /*public function hasNext( $currentOffset, $resultSetSize )
    {
        return ( count( $this->array ) - $currentOffset + $resultSetSize > 0 )
            || ( count( $this->array ) - $currentOffset + $resultSetSize == count( $this->array ) % $resultSetSize )
            ;
    }
    
    public function hasPrev( $currentOffset, $resultSetSize )
    {
        return $currentOffset - $resultSetSize >= 0;
    }*/
    
    public function isOffsetValid( $offset )
    {
        return $offset >= 0 && $offset < count( $this->array );
    }
    
    public function countPages( $resultSetSize )
    {
        return (int) count( $this->array ) / $resultSetSize
            + ( count( $this->array ) % $resultSetSize != 0  ? 1 : 0 );
    }
}

    
class Claro_Utils_DatagridPager
{
    protected $pageable;
    protected $datagrid;
    protected $pageSize;
    
    public function __construct( 
        $pageable, 
        $datagrid, 
        $pageSize = 25, 
        $baseUrl = '', 
        $pageNumberVar = 'pageNumber'
    )
    {
        $this->pageable = $pageable;
        $this->datagrid = $datagrid;
        $this->pageSize = 25;
        $this->baseUrl = empty ( $baseUrl )
            ? new Url( $_SERVER['PHP_SELF'] )
            : new Url( $baseUrl )
            ;
            
        $this->baseUrl->relayCurrentContext();
        $this->pageNumberVar = $pageNumberVar;
    }
    
    public function renderPager( $pageNumber )
    {
        $out = '<div class="slideNav">';
        
        if ( $pageNumber > 0 )
        {
            $this->baseUrl->replaceParam( $this->pageNumberVar, $pageNumber + 1, true );
            $out .= "<span class=\"prevSlide\"><a href=\"".htmlspecialchars($this->baseUrl->toUrl())
                ."\" title=\"".get_lang('Previous page')."\">&lt;&lt</a></span>";
        }
        else
        {
            $out .= "<span class=\"navDisabled\">&lt;&lt</span>";
        }
        
        // render current page of number of page
        $out .= "<span class=\"currentSlide\">"
            . get_lang("Page %pageNumber on %pageTotal", 
                array( 
                    '%pageNumber' => $pageNumber, 
                    '%pageTotal' => $this->pageable->countPages( $this->pageSize ) ) )
            . "</span>"
            ;
        
        if ( $pageNumber < $this->pageable->countPages( $this->pageSize ) )
        {
            $this->baseUrl->replaceParam( $this->pageNumberVar, $pageNumber + 1, true );
            $out .= "<span class=\"nextSlide\"><a href=\"".htmlspecialchars($this->baseUrl->toUrl())
                ."\" title=\"".get_lang('Next page')."\">&gt;&gt</a></span>";
        }
        else
        {
            $out .= "<span class=\"navDisabled\">&gt;&gt</span>";
        }
        
        return $out;
    }

    
    public function renderPage( $pageNumber = 0 )
    {
        $offset = $pageNumber * $this->pageSize;
        
        if ( $this->pageable->isOffsetValid( $offset ) )
        {
            $data = $this->pageable->getPage( $offset, $this->pageSize );
            $this->datagrid->setRows( $data );
            
            return $this->renderPager( $pageNumber )
                . $this->datagrid->render()
                . $this->renderPager( $pageNumber )
                ;
        }
        else
        {
            throw new Exception("Invalid offset {$offset}");
        }
    } 
}
