<?php // $Id$
    
    // vim: expandtab sw=4 ts=4 sts=4:
    
    if ( count( get_included_files() ) == 1 ) die( '---' );
    
    /**
     * Error reporter Class
     * 
     * @author  Frederic Minne <zefredz@claroline.net>
     * @copyright Copyright &copy; 2006-2007, Frederic Minne
     * @license http://www.gnu.org/copyleft/gpl.html GNU General Public License
     * @version 1.0
     * @package Error
     */
    
    class Error_Reporter
    {
        /**
         * Display error report
         * @param   string summary
         * @param   string details
         * @return  string html code
         */
        function report( $summary, $details )
        {
            $id = uniqid('details');
            
            if ( empty( $details ) )
            {
                $display = '<p class="errorSummary">'.$summary.'</p>';
            }
            else
            {
                $display = <<<__ERRDISP__
<script type="text/javascript">
function toggleDetails( id )
{
    var details = document.getElementById( id );
    
    if ( details.style.display == 'block' )
    {
        details.style.display = 'none';
    }
    else
    {
        details.style.display = 'block';
    }
}
</script>
<p class="errorSummary">$summary
[<a href="javascript:toggleDetails('$id')">
details
</a>]</p>
<div id="$id" style="display: none;" class="errorDetails">
$details
</div>
__ERRDISP__;
            }

            return $display;
        }
    }
?>
