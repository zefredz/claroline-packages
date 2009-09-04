<?php // $Id$
/**
 * Server Time
 *
 * @version     CLTIME-1.0alpha $Revision$ - Claroline 1.9
 * @copyright   2001-2009 Universite Catholique de Louvain (UCL)
 * @license     http://www.gnu.org/copyleft/gpl.html (GPL) GENERAL PUBLIC LICENSE
 * @package     CLTIME
 * @author      Frédéric Minne <frederic.minne@uclouvain.be>
 */
 
    ClaroHeader::getInstance()->addHtmlHeader('
    <script type="text/javascript">
        function timeExam(){
            
                $.ajax({
                
                    url: "'.get_module_url('CLTIME').'/index.php",
                    
                    success: function(data){
                    
                        $("#serverTimeApplet").html(data);
                        
                    }
                    
                });
                
                setTimeout(timeExam, ' . get_conf( 'refreshTime' ) * 1000 . ' );
                
            }
            
            $(function(){
                timeExam();
            });
    </script>');
    
?>