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
 * @property array $toolList
 * @property array $itemList
 */
class Selector
{
    protected $pluginList;
    protected $itemList;
    
    /**
     * Constructor
     * @param array $itemList
     */
    public function __construct( $pluginList )
    {
        $this->pluginList = $pluginList;
        $this->load();
    }
    
    /**
     *
     */
    public function load()
    {
        $this->itemList = array();
        
        foreach( $this->pluginList as $plugin )
        {
            $toolName = $plugin->getToolName();
            $toolLabel = $plugin->getToolLabel();
            $reportDatas = $plugin->export();
            
            $this->itemList[ $toolLabel ][ 'name' ] = $toolName;
            
            foreach( $reportDatas[ 'item' ] as $itemId => $itemDatas )
            {
                $this->itemList[ $toolLabel ][ 'item' ][ $itemId ] = $itemDatas;
            }
        }
    }
    
    /**
     * Getter for $itemList
     * @return array $itemList
     */
    public function getItemList()
    {
        return $this->itemList;
    }
}