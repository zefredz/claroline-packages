<?php  // $Id$
    
    // vim: expandtab sw=4 ts=4 sts=4:
    
    if ( count( get_included_files() ) == 1 ) die( '---' );
    
    /**
     * Service definition
     *  provides :
     *  - AbstractService super class for user defined services
     *  - ScriptService service-based output buffering script service
     *  - ObScriptService PHP native output buffering script service
     * 
     * @author      Frederic Minne <zefredz@claroline.net>
     * @copyright   Copyright &copy; 2006-2007, Frederic Minne
     * @license     http://www.gnu.org/copyleft/gpl.html GNU General Public License
     * @version     1.0
     * @package     Service
     */
    
    require_once dirname(__FILE__) . '/../error/handling.class.php';
    
    /**
     * Abstract Service
     * @abstract
     */
    class AbstractService extends Error_Handling
    {
        var $output = '';
        
        /**
         * Set service execution output
         * @access  protected
         * @param   output string service output string
         */
        function setOutput( $output )
        {
            $this->output = $output;
        }
        
        /**
         * Get service execution output
         * @param   string service output string
         */
        function getOutput()
        {
            return $this->output;
        }
        
        /**
         * Execute service
         * @abstract
         */
        function run()
        {
            $this->setError( "Abstract Method " .__FUNCTION__. " Called in " .__CLASS__, 1000 );
            return false;
        }
    }
    
    // Common Services
    
    /**
     * Script Service
     * Execute a given script that uses set/getOutput methods
     * to communicate execution result to calling Service object
     */
    class ScriptService extends AbstractService
    {
        var $scriptPath;
        
        /**
         * Constructor
         * @param   scriptPath string path to the script to call
         */
        function ScriptService( $scriptPath )
        {
            $this->scriptPath = $scriptPath;
        }
        
        function run()
        {
            if ( ! file_exists( $this->scriptPath ) )
            {
                $this->setError( "File not found "
                    . $this->scriptPath
                    , 404 );
                    
                return false;
            }
            else
            {
                require_once $this->scriptPath;
                
                return true;
            }
        }
    }
    
    /**
     * Output Buffering Script Service
     * Execute a given script by using ob_* functions
     * to retreive execution result from called script
     */
    class ObScriptService extends ScriptService
    {
        function run()
        {
            if ( ! file_exists( $this->scriptPath ) )
            {
                $this->setError( "File not found "
                    . $this->scriptPath
                    , 404 );
                    
                return false;
            }
            else
            {
                ob_start();
                require_once $this->scriptPath;
                $output = ob_get_contents();
                ob_end_clean();
                
                $this->setOutput( $output );
                
                return true;
            }
        }
    }
?>