<?php // $Id$

    // vim: expandtab sw=4 ts=4 sts=4:

    /**
     * @author  Frederic Minne <zefredz@claroline.net>
     * @copyright Copyright &copy; 2006-2014, Université catholique de Louvain
     * @license http://www.gnu.org/copyleft/gpl.html GNU General Public License
     * @version 2.0
     * @package HTML.Datagrid
     */
    
    
    require_once dirname(__FILE__) . '/../template.class.php';
    
    class HTML_Datagrid_Template
    {
        protected $template;
        protected $data;
        protected $footer = '';
        protected $header = '';
        protected $emptyMessage = null;
        
        function setEmptyMessage( $str )
        {
            $this->emptyMessage = $str;
        }
        
        function setData( $data )
        {
            $this->data = $data;
        }
        
        function setTemplate( $template )
        {
            $this->template = $template;
        }
        
        function setFooter( $footer )
        {
            $this->footer = $footer;
        }
        
        function setHeader( $header )
        {
            $this->header = $header;
        }
        
        function render()
        {
            $output = '';
            
            if ( !empty( $this->header ) )
            {
                $output .= $this->header  . "\n";
            }
            
            if ( count( $this->data ) > 0 )
            {
                $first = 0;
                $last = count($this->data)-1;
                $current = 0;
                
                foreach ( $this->data as $row )
                {
                    $rowOutput = $this->template->render( $row );
                    
                    if ( $current !== $first )
                    {
                        $rowOutput = preg_replace('/%ifisfirst\([^\)]*\)%/','', $rowOutput);
                    }
                    else
                    {
                        $rowOutput = preg_replace('/%ifisfirst\(([^\)]*)\)%/',"$1", $rowOutput);
                    }

                    if ( $current !== $last )
                    {
                        $rowOutput = preg_replace('/%ifislast\([^\)]*\)%/','', $rowOutput);
                    }
                    else
                    {
                        $rowOutput = preg_replace('/%ifislast\(([^\)]*)\)%/',"$1", $rowOutput);
                    }
                    
                    $current++;
                    
                    $output .= $rowOutput;
                }
            }
            else
            {
                if ( is_null( $this->emptyMessage ) )
                {
                    $this->emptyMessage = get_lang('Empty');
                }
                
                $output .= $this->emptyMessage;
            }
            
            if ( !empty( $this->footer ) )
            {
                $output .= $this->footer . "\n";
            }
            
            return $output;
        }
    }
