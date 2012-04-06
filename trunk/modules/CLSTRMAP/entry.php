<?php // $Id$

/**
 * Claroline Poll Tool
 *
 * @version     CLSTRMAP 1.0 $Revision$ - Claroline 1.9
 * @copyright   2001-2009 Universite catholique de Louvain (UCL)
 * @license     http://www.gnu.org/copyleft/gpl.html (GPL) GENERAL PUBLIC LICENSE
 * @package     CLSTRMAP
 * @author      Frederic Fervaille <frederic.fervaille@uclouvain.be>
 */

$url = get_conf( 'clustrMapsUrl' );

$html = '<a href="http://www2.clustrmaps.com/counter/maps.php?url=' . $url .'" id="clustrMapsLink">
         <img src="http://www2.clustrmaps.com/counter/index2.php?url=' . $url .'" 
            style="border:0px;" 
            alt="Locations of visitors to this page" 
            title="Locations of visitors to this page" 
            id="clustrMapsImg" />
        </a>
        <script type="text/javascript">
            function cantload() {
                img = document.getElementById("clustrMapsImg");
                img.onerror = null;
                img.src = "http://clustrmaps.com/images/clustrmaps-back-soon.jpg";
                document.getElementById("clustrMapsLink").href = "http://clustrmaps.com";
            }
            img = document.getElementById("clustrMapsImg");
            img.onerror = cantload;
        </script>';

$claro_buffer->append( $html );
