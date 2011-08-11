<?php // $Id$
/**
 * Student Report for Claroline
 *
 * @version     UCREPORT 2.1.0 $Revision$ - Claroline 1.9
 * @copyright   2001-2011 Universite catholique de Louvain (UCL)
 * @license     http://www.gnu.org/copyleft/gpl.html (GPL) GENERAL PUBLIC LICENSE
 * @package     UCREPORT
 * @author      Frederic Fervaille <frederic.fervaille@uclouvain.be>
 */

/**
 * Abstract class for Report 'plugins'
 * These plugins allow to send datas from Claroline tools to the Report object
 * @const DEFAULT_WEIGHT
 * @const DEFAULT_MAX_SCORE
 * @property $dataQueryResult
 * @property $itemQueryResult
 * @private $dataList
 * @private $itemList
 */
abstract class ReportPlugin
{
    const DEFAULT_WEIGHT = 100;
    const DEFAULT_MAX_SCORE = 100;
    
    protected $toolName;
    protected $toolLabel;
    
    protected $dataQueryResult;
    protected $itemQueryResult;
    
    protected $dataList;
    protected $itemList;
    
    /**
     * load datas needed by Report
     * @abstract
     */
    abstract public function load();
    
    /**
     * common getter for $toolName
     * @return string $toolName
     */
    public function getToolName()
    {
        return $this->toolName;
    }
    
    /**
     * Getter for $toolName
     * @return string $tollLabel
     */
    public function getToolLabel()
    {
        return $this->toolLabel;
    }
    
    /**
     * builds $itemList and $dataList
     */
    public function bake( $userList = array(), $itemList = array() )
    {
        $this->itemList = array();
        $this->dataList = array();
        
        if ( isset( $this->itemQueryResult ) )
        {
            foreach( $this->itemQueryResult as $line )
            {
                $itemId = $line[ 'id' ];
                $index = $this->toolLabel . '_' . $itemId;
                $is_visible = strtolower( $line[ 'visibility' ] ) == 'visible';
                
                if ( array_key_exists( $index , $itemList ) || empty( $itemList ) )
                {
                    $this->itemList[ $index ][ 'title'  ] = $line[ 'title' ];
                    $this->itemList[ $index ][ 'selected' ] = $is_visible;
                    $this->itemList[ $index ][ 'weight' ] = self::DEFAULT_WEIGHT;
                    $this->itemList[ $index ][ 'submission_count' ] = 0;
                    $this->itemList[ $index ][ 'total' ] = 0;
                }
            }
        }
        
        if ( isset( $this->dataQueryResult ) )
        {
            foreach( $this->dataQueryResult as $resultSet )
            {
                foreach( $resultSet as $line )
                {
                    $userId = $line[ 'user_id' ];
                    $itemId = $this->toolLabel . '_' . $line[ 'item_id' ];
                    
                    if ( array_key_exists( $userId , $userList ) || empty( $userList ) )
                    {
                        $this->dataList[ $userId ][ $itemId ] = $line[ 'score' ];
                        $this->itemList[ $itemId ][ 'submission_count' ]++;
                        $this->itemList[ $itemId ][ 'total' ] += $line[ 'score' ];
                    }
                }
            }
        }
    }
    
    /**
     * Exports result
     * @param array $userList
     * @return array $reportDatas
     */
    public function export( $userList = array() , $itemList = array() , $force = false )
    {
        if ( $force || ! $this->itemList )
        {
            $this->load();
        }
        
        $this->bake( $userList , $itemList );
        return array( 'item' => $this->itemList , 'data' => $this->dataList );
    }
}