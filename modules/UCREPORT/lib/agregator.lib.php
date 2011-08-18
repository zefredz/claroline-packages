<?php // $Id$
/**
 * Student Report for Claroline
 *
 * @version     UCREPORT 2.1.0 $Revision$ - Claroline 1.9
 * @copyright   2001-2010 Universite catholique de Louvain (UCL)
 * @license     http://www.gnu.org/copyleft/gpl.html (GPL) GENERAL PUBLIC LICENSE
 * @package     UCREPORT
 * @author      Frederic Fervaille <frederic.fervaille@uclouvain.be>
 */

/**
 * A class to manage item that will be processed by report
 * @property array $pluginList
 * @property array $userList
 * @property array $itemList
 * @property array $dataList
 * @property array $selectedList
 * @property float $averageScore
 */
class Agregator
{
    protected $pluginList;
    protected $userList;
    protected $itemList;
    protected $dataList;
    protected $averageScore;
    
    /**
     * Constructor
     * @param array $itemList
     */
    public function __construct( $pluginList , $userList , $itemList , $reset = false )
    {
        $this->pluginList = $pluginList;
        
        if ( $reset )
        {
            $this->load( $userList , $itemList );
        }
        else
        {
            $this->userList = $userList;
            $this->itemList = $itemList;
            $this->load();
        }
    }
    
    /**
     *
     */
    public function load( $userList = null , $itemList = null )
    {
        if ( $userList )
        {
            foreach( $userList as $userDatas )
            {
                $userId = $userDatas[ 'user_id' ];
                $this->userList[ $userId ][ 'firstname' ] = $userDatas[ 'prenom' ];
                $this->userList[ $userId ][ 'lastname' ] = $userDatas[ 'nom' ];
            }
        }
        
        $setActive = true;
        
        if ( ! $itemList && $this->itemList )
        {
            $itemList = $this->itemList;
            $setActive = false;
        }
        
        $this->itemList = array();
        $this->dataList = array();
        
        foreach( $this->pluginList as $plugin )
        {
            $label = $plugin->getToolLabel();
            
            $reportDatas = $plugin->export( $this->userList );
            
            foreach( $reportDatas[ 'item' ] as $itemId => $datas )
            {
                if ( isset( $itemList[ $itemId ][ 'selected' ] ) )
                {
                    $this->itemList[ $itemId ] = $datas;
                    $this->itemList[ $itemId ][ 'weight' ] = $datas[ 'weight' ];
                }
            }
            
            foreach( $reportDatas[ 'data' ] as $userId => $itemDatas )
            {
                foreach( $itemDatas as $itemId => $datas )
                {
                    $this->dataList[ $userId ][ $itemId ] = $datas;
                }
            }
        }
        
        $this->setProportionalWeight();
        if ( $setActive )
        {
            $this->setActive();
        }
        $this->setFinalScore();
        $this->setAverageScore();
        $this->setGlobalResult();
    }
    
    /**
     *
     */
    public function getItemList()
    {
        return $this->itemList;
    }
    
    /**
     *
     */
    public function getDataList()
    {
        return $this->dataList;
    }
    
    /**
     *
     */
    public function getUserList()
    {
        return $this->userList;
    }
    
    /**
     *
     */
    public function getAverageScore()
    {
        return $this->averageScore;
    }
    
    /**
     * Gets average score
     * @param string $toolName
     * @param int $itemId
     * @return float $averageScore
     */
    public function getAverage( $itemId = null )
    {
        if ( $itemId )
        {
            $total = $this->itemList[ $itemId ][ 'total' ];
            $count = $this->itemList[ $itemId ][ 'submission_count' ];
            
            return round( $total / $count , 3 );
        }
        else
        {
            foreach( $this->itemList as $itemId => $datas )
            {
                $total = $this->itemList[ $itemId ][ 'total' ];
                $count = $this->itemList[ $itemId ][ 'submission_count' ];
                
                $this->itemList[ $itemId ][ 'average' ] = round( $total / $count , 3 );
            }
        }
    }
    
    /**
     * A private method
     */
    private function setProportionalWeight()
    {
        $weightSum = 0;
        
        foreach( $this->itemList as $item )
        {
            if ( $item[ 'selected' ] )
            {
                $weightSum += $item[ 'weight' ];
            }
        }
        
        foreach( array_keys( $this->itemList  ) as $itemId )
        {
            if ( $this->itemList[ $itemId ][ 'selected' ] )
            {
                $proportionalWeight = round( $this->itemList[ $itemId ][ 'weight' ] / $weightSum , 3 );
            }
            else
            {
                $proportionalWeight = 0;
            }
            $this->itemList[ $itemId ][ 'proportional_weight' ] = $proportionalWeight;
        }
    }
    
    /**
     *
     */
    private function setActive()
    {
        /*
        $itemCount = count( $this->itemList );
        
        foreach( array_keys( $this->userList ) as $userId )
        {
            $markCount = count( $this->dataList[ $userId ] );
            $this->userList[ $userId ][ 'active' ] = $markCount == $itemCount;
        }
        */
        foreach( array_keys( $this->userList ) as $userId )
        {
            $is_active = true;
            
            foreach( $this->itemList as $itemId => $datas )
            {
                if ( isset( $datas[ 'selected' ] )
                && ! isset( $this->dataList[ $userId ][ $itemId ] ) )
                {
                    $is_active = false;
                }
            }
            
            $this->userList[ $userId ][ 'active' ] = $is_active;
            // The code above is über ugly: I MUST find another way to do this
        }
    }
    
    /**
     *
     */
    private function setFinalScore()
    {
        foreach( $this->userList as $userId => $userDatas )
        {
            if ( $userDatas[ 'active' ] )
            {
                $score = 0;
                
                foreach( $this->itemList as $itemId => $itemDatas )
                {
                    $score += isset( $this->dataList[ $userId ][ $itemId ] )
                            ? $this->dataList[ $userId ][ $itemId ] * $itemDatas[ 'proportional_weight' ]
                            : 0;
                }
                
                $this->userList[ $userId ][ 'final_score' ] = $score;
            }
        }
    }
    
    /**
     *
     */
    private function setAverageScore()
    {
        foreach( array_keys( $this->itemList ) as $itemId )
        {
            $activeCount = 0;
            $totalScore = 0;
            
            foreach( $this->userList as $userId => $userDatas )
            {
                if ( $userDatas[ 'active' ] )
                {
                    if ( ! isset( $this->dataList[ $userId ][ $itemId ] ) )
                    {
                        $this->dataList[ $userId ][ $itemId ] = 0;
                    }
                    $activeCount++;
                    $totalScore += $this->dataList[ $userId ][ $itemId ];
                }
            }
            
            $averageScore = $activeCount
                          ? round( $totalScore / $activeCount , 2 )
                          : 0;
            $this->itemList[ $itemId ][ 'average' ] = $averageScore;
        }
    }
    
    /**
     *
     */
    private function setGlobalResult()
    {
        $this->averageScore = 0;
        
        foreach( $this->itemList as $itemDatas )
        {
            $this->averageScore += $itemDatas[ 'average' ] * $itemDatas[ 'proportional_weight' ];
        }
    }
    
    /**
     *
     */
    public function setUserActive( $userId , $active = false )
    {
        return $this->userList[ $userId ][ 'active' ] = (boolean)$active;
    }
    
    /**
     * Resets the active users list
     * @return boolean true if process is successful
     */
    public function resetActiveUserList()
    {
        return $this->setActive();
    }
    
    /**
     * Exports all the report datas
     * @return array $reportDataList
     */
    public function export()
    {
        return array( 'users'   => $this->userList
                    , 'items'   => $this->itemList
                    , 'report'  => $this->dataList
                    , 'average' => $this->averageScore );
    }
    
    /**
     * Gets the present date
     */
    public function getDate()
    {
        return date( 'c' );
    }
}