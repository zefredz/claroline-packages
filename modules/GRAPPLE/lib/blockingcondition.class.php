<?php //$Id$
if ( count( get_included_files() ) == 1 ) die( '---' );
/**
 * Library to manage blocking condition of a learning path
 *
 * @version 0.1 $Revision$
 * @copyright (c) 2001-2007 Universite catholique de Louvain (UCL)
 * @license http://www.gnu.org/copyleft/gpl.html (GPL) GENERAL PUBLIC LICENSE
 * @package GRAPPLE
 * @author Dimitri Rambout
 */

class blockingcondition
{
    /**
    * @var $blockconds contains the blocking conditions for an item
    */
    private $blockconds;
    
    /**
     * @var $item_id contains the general item_id
     */
    private $item_id;
    /**
     * @var $tblBlockcond name of the blocking condition table
     */
    private $tblBlockcond;
    /*
     * @var $tblItem name of the item table
     */
    private $tblItem;
    
    /**
     * Constructor
     *
     * Init database tables
     *
     * @author Dimitri Rambout <dim@claroline.net>
     */
    public function __construct( $item_id )
    {
        $this->item_id = (int) $item_id;
        
        $tblNameList = array(
            'lp_item_blockcondition', 'lp_item'
        );

        // convert to Claroline course table names
        $tbl_lp_names = get_module_course_tbl( $tblNameList, claro_get_current_course_id() );
        $this->tblBlockcond = $tbl_lp_names['lp_item_blockcondition'];
        $this->tblItem = $tbl_lp_names['lp_item'];
    }
    /**
     * Save the blocking conditions in the database
     * 
     * @author Dimitri Rambout <dim@claroline.net>
     * @return boolean
     */
    public function save()
    {
        if( !$this->checkBlockConds() )
        {
            return false;
        }
        else
        {
            $this->delete();
            
            $sql = "";
            foreach($this->blockconds['item'] as $key => $value)
            {                
                if( $sql )
                {
                    $sql .= ", ";
                }
                $sql .= " ( '". $this->item_id ."',
                            '".(int) $value."',
                            '".$this->blockconds['status'][$key]."',
                            '".$this->blockconds['operator'][$key]."',
                            '".(isset($this->blockconds['condition'][$key]) ? $this->blockconds['condition'][$key] : -1)."',
                            '".(int) $this->blockconds['raw_to_pass'][$key] ."') ";
            }
            $sql = "INSERT INTO `".$this->tblBlockcond."`
                        ( `item_id`, `cond_item_id`, `completion_status`, `operator`, `condition`, `raw_to_pass`) VALUES " . $sql;
            $insertedId = claro_sql_query_insert_id($sql);
            
            if( $insertedId )
            {
                return true;
            }
            else
            {
                return false;   
            }            
        }
    }
    
    
    /**
     * Load the blocking conditions if not already loaded. Based on $this->item_id
     * 
     * @author Dimitri Rambout <dim@claroline.net>
     * @return boolean
     */    
    public function load()
    {
        if( !is_null( $this->blockconds ) && is_array( $this->blockconds ) && count( $this->blockconds ) )
        {
            return true;
        }
        else
        {
            $sql = "SELECT * FROM `".$this->tblBlockcond."` WHERE `item_id` = '".$this->item_id."' ORDER BY `id`";
            $data = claro_sql_query_fetch_all_rows( $sql );
            
            if( is_null($data) || !count($data) || $data == false )
            {
                return false;
            }
            else
            {
                $blockconds = array();
                foreach($data as $k => $d)
                {
                    $blockconds['item'][] = $d['cond_item_id'];
                    $blockconds['operator'][] = $d['operator'];
                    $blockconds['status'][] = $d['completion_status'];
                    $blockconds['raw_to_pass'][] = $d['raw_to_pass'];
                    if($k)
                    {
                        $blockconds['condition'][] = $data[$k-1]['condition'];
                    }
                }
                $this->setBlockConds( $blockconds );
                
                return true;
            }
        }
    }
		
    /**
     * Eval the blocking conditions for an item (and parents if recursive is at true)
     * 
     * @author Dimitri Rambout <dim@claroline.net>
     * @param $item_id int id of the item
     * @param $recursive boolean set recursive
     * @return array of boolean (eval of the blocking conditions for each item)
     */
    public function evalBlockConds( $item_id, $recursive = false)
    {
        $block = array();
        
        $this->item_id = $item_id;
        $this->clearBlockConds();
        $this->load();
        
        if ( $this->checkBlockConds() )
        {
            $block = array_merge($block, (array) $this->evalBlockCond($this->getBlockConds()));
        }
        if( $recursive )
        {
            $sql = "SELECT i.`parent_id`
                FROM `".$this->tblItem."` i
                WHERE i.`id` = '".$item_id."'
                LIMIT 1";
            $item = claro_sql_query_fetch_single_row($sql);
            
            if( $item && isset($item['parent_id']) && $item['parent_id'] > 0 )
            {            
                $block = array_merge($block, (array) $this->evalBlockConds($item['parent_id'], $recursive));
            }    
        }
        
        return $block;        
    }
    
    /**
     * Clean the blocking conditions in memory
     * 
     * @author Dimitri Rambout <dim@claroline.net>
     */
    private function clearBlockConds()
    {
        $this->blockconds = null;
    }
    
    /**
    * Eval blocking conditions of an item
    * 
    * @author Dimitri Rambout <dim@claroline.net>
    * @param array $data Array of items that need to be evaluated
    * @return boolean True or False
    */ 
    private function evalBlockCond( $data )
    {
        $eval = "";
                
        foreach( $data['item'] as $key => $value )
        {
            $anItem = new item();
            if( $anItem->load($value) )
            {
                // get serialized attempt
                $thisAttempt = unserialize($_SESSION['thisAttempt']);
                // create new attempt for this item
                $itemAttempt = new itemAttempt();
                $itemAttempt->setAttemptId($thisAttempt->getId());
                $itemAttempt->setItemId($anItem->getId());
            
                // try to load itemAttempt
                $itemAttempt->load($thisAttempt->getId(), $anItem->getId());
                
                $eval .= "'".$data['status'][$key]."'";
                switch( $data['operator'][$key] )
                {
                    case '=' : $eval .= " == ";
                                break;
                }
                $eval .= "'".$itemAttempt->getCompletionStatus()."'";
                if( $data['status'][$key] == 'COMPLETED' && $data['raw_to_pass'][$key] > 0 )
                {
                    $eval .= " && " . (int) $data['raw_to_pass'][$key] ." <= " . (int) $itemAttempt->getScoreRaw()
                    ;
                }
            }
            
            if( $eval && isset($data['condition'][$key]) )
            {
                switch( $data['condition'][$key] )
                {
                    case 'AND'  :   $eval .= " && "; break;
                    case 'OR'   :   $eval .= " || "; break;
                }
            }
        }
        if($eval)
        {
            $eval = "if($eval){ return true; }else{ return false; }";
            
            return eval($eval);
        }
        else
        {
            return true;
        }
    }
    
    /**
    * Load blocking conditions from item and parent blocking conditions
    * 
    * @author Dimitri Rambout <dim@claroline.net>
    * @param int $item_id Id of the item
    * @param boolean $printable true or false
    * @return array blocking conditions
    */
    public function loadRecursive( $item_id, $printable )
    {
        
        $blocking_conditions = array();
        
        $sql = "SELECT i.`id`, i.`parent_id`, `title`
        FROM `".$this->tblItem."` i
        WHERE i.`id` = '".$item_id."'
        LIMIT 1";
        
        $item = claro_sql_query_fetch_single_row($sql);
        
        $sql = "SELECT * FROM `".$this->tblBlockcond."` WHERE `item_id` = '".$item_id."' ORDER BY `id`";
        $_data = claro_sql_query_fetch_all_rows($sql);
        if( $printable  && count($_data) )
        {
            foreach( $_data as $k => $d){
                $data[$item_id]['data']['item'][] = $d['cond_item_id'];
                $data[$item_id]['data']['operator'][] = $d['operator'];
                $data[$item_id]['data']['status'][] = $d['completion_status'];
                $data[$item_id]['data']['raw_to_pass'][] = $d['raw_to_pass'];
                
                if($k)
                {
                    $data[$item_id]['data']['condition'][] = $_data[$k-1]['condition'];
                }
            }
            $data[$item_id]['title'] = $item['title'];
            $data[$item_id]['id'] = $item['id'];
        }
        else
        {
            $data = $_data;
        }
        
        if( ($data && is_array($data) && count($data)) || ($_data && is_array($_data) && count($_data)) )
        {
            $blocking_conditions = array_merge($blocking_conditions, $data);            
        }
        
        if( $item && isset($item['parent_id']) && $item['parent_id'] > 0 )
        {
            $data = $this->loadRecursive($item['parent_id'], $printable);
            if( $data  && is_array($data) && count($data))
            {
                $blocking_conditions = array_merge($blocking_conditions, $data);
            }
        }
        
        return $blocking_conditions;
    }
    
    
    /**
     * Delete blocking conditions for the current item
     * 
     * @author Dimitri Rambout <dim@claroline.net>
     * @return boolean True if query ok, false is query not ok
     */
    public function delete()
    {
        if( !is_int($this->item_id) )
        {
                return false;
        }
        $sql = "DELETE FROM `".$this->tblBlockcond."` WHERE `item_id` = '".$this->item_id."'";
        
        return claro_sql_query( $sql );
    }
    /**
    * Check if each blocking condition are well formed
    * 
    * @author Dimitri Rambout <dim@claroline.net>
    * @return boolean True if check ok, false in other case
    */
    private function checkBlockConds()
    {
        if( !is_null($this->blockconds) && is_array($this->blockconds) )
        {
            if(( isset($this->blockconds['item']) && isset($this->blockconds['operator']) && isset($this->blockconds['status'])
                 && is_array($this->blockconds['item']) && is_array($this->blockconds['operator']) && is_array($this->blockconds['status'])
                 && count($this->blockconds['item']) > 0 &&  (count($this->blockconds['item']) == (count($this->blockconds['operator']) == (count($this->blockconds['status']))))
                 ) )
            {
                
                $error = false;
                foreach( $this->blockconds['item'] as $key => $value )
                {
                    $item = new item();
                    if( !(is_numeric($value) && $item->load($value)) )
                    {                        
                        $error = false;
                        break;
                    }
                    else
                    {
                        if($item->getType() == 'CONTAINER')
                        {
                            $error = false;
                            break;
                        }
                        
                        if( !($this->blockconds['operator'][$key] == '=') )
                        {
                            $error = false;
                            break;
                        }
                        
                        if( !($this->blockconds['status'][$key] == 'COMPLETED' || $this->blockconds['status'][$key] == 'INCOMPLETE' || $this->blockconds['status'][$key] == 'PASSED'))
                        {
                            $error = false;
                            break;
                        }
                        
                        if($key > 0)
                        {
                            if( !($this->blockconds['condition'][$key-1] == 'AND' || $this->blockconds['condition'][$key-1] == 'OR') )
                            {
                                $error = false;
                                break;
                            }    
                        }
                        
                    }
                }
                
                if( $error )
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
                return false;
            }
            
        }
        else
        {
            return false;
        }
    }
    
    
    /**
     * Set blocking conditions
     * 
     * @author Dimitri Rambout <dim@claroline.net>
     * @param array $data blocking conditions
     * @return boolean true
     */
    public function setBlockConds( $data )
    {
        $this->blockconds = $data;

        return true;
    }
    
    /**
     * Get blocking conditions
     * 
    * @author Dimitri Rambout <dim@claroline.net>
    * @return array Array of blocking conditions
    */
    public function getBlockConds()
    {
        return $this->blockconds;
    }
}
?>