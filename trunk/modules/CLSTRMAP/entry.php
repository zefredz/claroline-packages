<?php

$url = get_path( 'rootWeb' );

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
