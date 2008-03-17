<?php // $Id$

    // vim: expandtab sw=4 ts=4 sts=4:
    
    /**
     * PHP-based templating system
     *
     * @version     1.8-backport $Revision$
     * @copyright   2001-2008 Universite catholique de Louvain (UCL)
     * @author      Claroline Team <info@claroline.net>
     * @author      Frederic Minne <zefredz@claroline.net>
     * @license     http://www.gnu.org/copyleft/gpl.html
     *              GNU GENERAL PUBLIC LICENSE version 2 or later
     * @package     display
     */

    if ( count( get_included_files() ) == 1 )
    {
        die( 'The file ' . basename(__FILE__) . ' cannot be accessed directly, use include instead' );
    }
    
    /**
     * Class to convert a PHP error to an Exception
     * 
     * taken from php.net online PHP manual
     */
    class PHP_Error_Exception extends Exception
    {
       public function __construct ( $code, $message, $file, $line )
       {
           parent::__construct($message, $code);
           $this->file = $file;
           $this->line = $line;
       }
    }
    
    /**
     * Error handler to convert PHP errors to Exceptions and so have
     * only one error handling system to handle
     * 
     * taken from php.net online PHP manual
     */
    function exception_error_handler( $code, $message, $file, $line )
    {
        throw new PHP_Error_Exception( $code, $message, $file, $line );
    }
    
    /**
     * Exception handler to be used inside an output buffer
     */
    function claro_ob_exception_handler( $e )
    {
        // get buffer contents
        $buffer = ob_get_contents();
        // close the output buffer
        ob_end_clean();
        // display the buffer contents
        echo $buffer;
        // display the exception
        if ( claro_debug_mode() )
        {
            echo '<pre>' . $e->__toString() . '</pre>';
        }
        else
        {
            echo '<p>' . $e->getMessage() . '</p>';
        } 
    }
    
    /**
     * Start output buffering
     */
    function claro_ob_start()
    {
        // set error handlers for output buffering :
        set_error_handler( 'exception_error_handler', error_reporting() & ~E_STRICT );
        set_exception_handler('claro_ob_exception_handler');
        // start output buffering
        ob_start();
    }
    
    /**
     * Stop output buffering
     */
    function claro_ob_end_clean()
    {
        // end output buffering
        ob_end_clean();     
        // restore original error handlers
        restore_exception_handler();
        restore_error_handler();
    }
    
    /**
     * Return buffer contents
     */
    function claro_ob_get_contents()
    {
        return ob_get_contents();
    }
    
    /**
     * Simple PHP-based template class
     */
    class PhpTemplate
    {
        protected $_templatePath;
        
        /**
         * Constructor
         * @param   string $templatePath path to the php template file
         */
        public function __construct( $templatePath )
        {
            $this->_templatePath = $templatePath;
        }
        
        /**
         * Assign a value to a variable
         * @param   string $name
         * @param   mixed $value
         */
        public function assign( $name, $value )
        {
            $this->$name = $value;
        }
        
        /**
         * Render the template
         * @return  string
         * @throws  Exception if file not found or error/exception in the template
         */
        public function render()
        {
            if ( file_exists( $this->_templatePath ) )
            {
                claro_ob_start();
                include $this->_templatePath;
                $render = claro_ob_get_contents();
                claro_ob_end_clean();
                
                return $render;
            }
            else
            {
                throw new Exception("Template file not found {$this->templatePath}");
            }
        }
        
        /**
         * Show a block in the template given its name 
         * (ie set the variable with the block name to true)
         * @param   string $blockName
         */
        public function showBlock( $blockName )
        {
            $this->$blockName = true;
        }
        
        /**
         * Hide a block in the template given its name 
         * (ie set the variable with the block name to false)
         * @param   string $blockName
         */
        public function hideBlock( $blockName )
        {
            $this->$blockName = false;
        }
    }
?>