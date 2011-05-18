<?php // $Id$

/**
 * New message notifier
 *
 * @version     CLNEWMSG 0.8 $Revision$ - Claroline 1.9
 * @copyright   2001-2009 Universite Catholique de Louvain (UCL)
 * @license     http://www.gnu.org/copyleft/gpl.html (GPL) GENERAL PUBLIC LICENSE
 * @package     CLNEWMSG
 * @author      Frederic Fervaille <frederic.fervaille@uclouvain.be>
 */


$claro_buffer->append( '<script type="text/javascript">
    $( function(){ msgNotifier(); } );
</script>');

$claro_buffer->append( '<span id="newMsg"></span>' );

