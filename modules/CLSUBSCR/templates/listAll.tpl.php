<?php
foreach( $this->subscriptions as $subscription ) :
    if( claro_is_allowed_to_edit() || $subscription['visibility'] == 'visible' ) :
        include( dirname( __FILE__ ) . '/subscription.tpl.php' );
    endif;
endforeach;
?>