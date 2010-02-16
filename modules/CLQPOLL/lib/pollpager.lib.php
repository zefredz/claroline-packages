<?php // $Id$

/**
 * Claroline Poll Tool
 *
 * @version     CLQPOLL 0.6.0 $Revision$ - Claroline 1.9
 * @copyright   2001-2009 Universite Catholique de Louvain (UCL)
 * @license     http://www.gnu.org/copyleft/gpl.html (GPL) GENERAL PUBLIC LICENSE
 * @package     CLQPOLL
 * @author      Frederic Fervaille <frederic.fervaille@uclouvain.be>
 */

/**
 * Quick pager
 * @property array $data the array to split into page
 * @property int $pageCount the number of pages
 * @property int $lineCount the number of lines per page
 * @property boolean $is_lineCountChanged
 * @property array $pageList the paged data list
 */
class Pager
{
    protected $data;
    protected $pageCount;
    protected $lineCount;
    protected $is_lineCountChanged = false;
    protected $pageList = array();
    
    /**
     * Constructor
     * @param array $data
     * @param int $lineCount
     */
    public function __construct( $data , $lineCount = 10 )
    {
        if ( ! is_array( $data ) || ! (int)$lineCount )
        {
            throw new Exception( 'Wrong arguments' );
        }
        
        $this->data = $data;
        $this->lineCount = $lineCount;
    }
    
    /**
     * Gets the paged data list
     * @param boolean $force : if true, forces the reading in database
     * @return array $pageList
     */
    public function getPageList( $force = false )
    {
        if ( empty( $this->pageList ) || $force )
        {
            $this->pageList = array_chunk( $this->data , $this->lineCount );
        }
        
        return $this->pageList;
    }
    
    /**
     * Gets the number of pages
     * @param boolean $force : if true, forces the reading in database
     * @return int $lineCount
     */
    public function getPageCount( $force = false )
    {
        if ( ! $this->pageCount || $force || $this->is_lineCountChanged )
        {
            $this->pageCount = count( $this->getPageList( $force ) );
            $this->is_lineCountChanged = false;
        }
        
        return $this->pageCount;
    }
    
    /**
     * Common getter for $lineCount
     * @return int $lineCount
     */
    public function getLineCount()
    {
        return $this->lineCount;
    }
    
    /**
     * Setter for $lineCount
     * @param int $lineCount
     */
    public function setLineCount( $lineCount )
    {
        if ( ! (int)$lineCount )
        {
            throw new Exception( 'Invalid argument' );
        }
        
        $this->lineCount = $lineCount;
        $this->is_lineCountChanged = true;
    }
    
    /**
     * Gets the page with the specified number
     * @param int $pageNb
     * @return array $pageList[ $pageNb ]
     */
    public function getPage( $pageNb )
    {
        if ( count( $this->getPageCount() ) < $pageNb )
        {
            throw new Exception( 'This page does not exist!' );
        }
        
        return $this->pageList[ $pageNb ];
    }
}