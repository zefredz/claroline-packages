<?php // $Id$

// vim: expandtab sw=4 ts=4 sts=4:

/**
 * Sqlite keyring
 *
 * @version     1.8-backport $Revision$
 * @copyright   2001-2007 Universite catholique de Louvain (UCL)
 * @author      Frederic Minne <zefredz@claroline.net>
 * @license     http://www.gnu.org/copyleft/gpl.html
 *              GNU GENERAL PUBLIC LICENSE version 2 or later
 * @package     icprint
 */

!defined( 'CLARO_DSN' ) && define ( 'CLARO_DSN', 'mysql://'.get_conf('dbLogin')
        .':'.get_conf('dbPass').'@'.get_conf('dbHost').'/'
        .get_conf('mainDbName') );

class Keyring // Sqlite implements Keyring
{
    protected static $instance = false;
    
    public static function getInstance()
    {
        if ( ! self::$instance )
        {
            self::$instance = new Keyring;
        }
        
        return self::$instance;
    }
    
    public static function checkKey ( $serviceName, $serviceHost, $serviceKey )
    {
        $mngr = self::getInstance();
        
        return $mngr->check ( $serviceName, $serviceHost, $serviceKey );
    }
    
    protected $db;
    
    protected function __construct ()
    {
        $this->db = PDOFactory::getConnection( CLARO_DSN );
        
        if ( $this->db->getAttribute(PDO::ATTR_ERRMODE) != PDO::ERRMODE_EXCEPTION )
        {
            $this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        }
    }
    
    public function add ( $serviceName, $serviceHost, $serviceKey )
    {
        $sql = "INSERT INTO `__CL_MAIN__icprint_services`(serviceName,serviceHost,serviceKey)\n"
            . "VALUES( :serviceName, :serviceHost, :serviceKey )"
            ;
            
        $params = array( 
            ':serviceName' => $serviceName,
            ':serviceHost' => $serviceHost,
            ':serviceKey' => $serviceKey
         );
            
        $this->executeQuery ( $sql, $params );
    }
    
    public function update ( $oldServiceName, $oldServiceHost, $serviceName, $serviceHost, $serviceKey )
    {
        $sql = "UPDATE `__CL_MAIN__icprint_services`\n"
            . "SET "
                . "serviceName = :serviceName,"
                . "serviceHost = :serviceHost,"
                . "serviceKey = :serviceKey "
            ."WHERE "
                . "serviceName = :oldServiceName"
                . " AND "
                . "serviceHost = :oldServiceHost"
            ;
        
        $params = array( 
            ':serviceName' => $serviceName,
            ':serviceHost' => $serviceHost,
            ':serviceKey' => $serviceKey,
            ':oldServiceName' => $oldServiceName,
            ':oldServiceHost' => $oldServiceHost
         );
            
        $this->executeQuery ( $sql, $params );
    }
    
    public function delete ( $serviceName, $serviceHost )
    {
        $sql = "DELETE FROM `__CL_MAIN__icprint_services`\n"
            ."WHERE "
                . "serviceName = :serviceName"
                . " AND "
                . "serviceHost = :serviceHost"
            ;
        
        $params = array( 
            ':serviceName' => $serviceName,
            ':serviceHost' => $serviceHost,
         );    
        
        $this->executeQuery ( $sql, $params );
    }
    
    public function get ( $serviceName, $serviceHost )
    {
        $sql = "SELECT serviceName,serviceHost,serviceKey\n"
            . "FROM `__CL_MAIN__icprint_services`\n"
            . "WHERE "
                . "serviceName = :serviceName"
                . " AND "
                . "serviceHost = :serviceHost"
            ;
        
        $params = array( 
            ':serviceName' => $serviceName,
            ':serviceHost' => $serviceHost,
         );    
        
        $result = $this->executeQuery ( $sql, $params );
            
        if ( ! $result->rowCount() )
        {
            throw new Exception("No service key found for {$serviceName}:{$serviceHost} in keyring");
        }
        else
        {
            return $result->fetch();
        }
    }
    
    public function check ( $serviceName, $serviceHost, $serviceKeyToCheck )
    {
        $serviceName = $this->get( $serviceName, $serviceHost );
        
        return $serviceName['serviceKey'] == $serviceKeyToCheck;
    }
    
    public function getServiceList()
    {
        $sql = "SELECT serviceName,serviceHost,serviceKey FROM `__CL_MAIN__icprint_services`";
        
        $result = $this->executeQuery( $sql );
            
        if ( ! $result )
        {
            throw new Exception ("Cannot get service list from keyring : " 
                . sqlite_error_string( $this->sqlite->lastError() ));
        }
        else
        {
            return $result->fetchAll( PDO::FETCH_ASSOC );
        }
    }
    
    protected static $queryCounter = 1;
    
    protected function executeQuery( $sql, $params = null )
    {
        $sql = $this->toClaroQuery( $sql );
        
        if ( get_conf('CLARO_DEBUG_MODE',false) && get_conf('CLARO_PROFILE_SQL',false) )
        {
            $start = microtime();
        }
        
        if ( ! is_array( $params ) || empty( $params ) )
        {
            $statement = $this->db->query( $sql );
        }
        else
        {
            $statement = $this->db->prepare( $sql );
            $statement->execute( $params );
        }
        
        if ( get_conf('CLARO_DEBUG_MODE',false) && get_conf('CLARO_PROFILE_SQL',false) )
        {
            $duration = microtime()-$start;
            $info = 'execution time : ' . ($duration > 0.001 ? '<b>' . round($duration,4) . '</b>':'&lt;0.001')  . '&#181;s'  ;
            $info .= ': affected rows :' . claro_sql_affected_rows();
            pushClaroMessage( '<br>Query counter : <b>pdo_' . self::$queryCounter++ . '</b> : ' . $info . '<br />'
                . '<code><span class="sqlcode">' . nl2br($sql) . '</span></code>'
                , 'pdo');
        }
        
        $statement->setFetchMode(PDO::FETCH_ASSOC);
        
        return $statement;
    }
    
    protected function toClaroQuery( $sql )
    {
        $sql = str_replace ('__CL_MAIN__',get_conf('mainTblPrefix'), $sql);
        $sql = str_replace('__CL_COURSE__'
            , claro_get_course_db_name_glued( claro_get_current_course_id() )
            , $sql );
            
        return $sql;
    }
}
