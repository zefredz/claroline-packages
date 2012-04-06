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

$html = '<div id="clustrmaps-widget"></div>
        <script type="text/javascript">
            var _clustrmaps = {\'url\' : \'' . $url . '\',
                              \'user\' : 1002043,
                              \'server\' : \'3\',
                              \'id\' : \'clustrmaps-widget\',
                              \'version\' : 1,
                              \'date\' : \'2012-04-06\',
                              \'lang\' : \'fr\',
                              \'corners\' : \'square\' };
            (function (){ var s = document.createElement(\'script\');
            s.type = \'text/javascript\';
            s.async = true;
            s.src = \'http://www3.clustrmaps.com/counter/map.js\';
            var x = document.getElementsByTagName(\'script\')[0];
            x.parentNode.insertBefore(s, x);})();
        </script>
        <noscript>
            <a href="http://www3.clustrmaps.com/user/e1ff4a3b">
                <img src="http://www3.clustrmaps.com/stats/maps-no_clusters/ucline.uclouvain.be-thumb.jpg"
                     alt="Locations of visitors to this page" />
            </a>
        </noscript>';
    
$claro_buffer->append( $html );
