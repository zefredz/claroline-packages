<?php // $Id$
/**
 * Online library for Claroline
 *
 * @version     CLLIBR 1.1.5
 * @copyright   2001-2013 Universite catholique de Louvain (UCL)
 * @license     http://www.gnu.org/copyleft/gpl.html (GPL) GENERAL PUBLIC LICENSE
 * @package     CLLIBR
 * @author      Frederic Fervaille <frederic.fervaille@uclouvain.be>
 */ ?>

<script type="text/javascript">
    $(document).ready(function(){
        $("#selectAll" ).click(function(){
            var is_checked=$(this).attr('checked');
            $(".resourceSelect").attr('checked',is_checked);
        });
    });
</script>