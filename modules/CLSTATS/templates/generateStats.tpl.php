<div class="claroDialogBox boxInfo" id="generation">
    <img src="<?php echo get_icon( 'loading' ); ?>" />
    <?php echo get_lang( 'Please wait... Statistics\' generation is running.'); ?>    
</div>
<script type="text/javascript">
    $(document).ready( function() {
        $.getJSON(
            "<?php echo htmlentities(Url::Contextualize( get_module_url( 'CLSTATS' ) . '/backends/backend.php?cmd=generateStats&reset=' . $this->reset ) ); ?>",
            function( response )
            {
                if( response.success )
                {
                    $('#generation').attr( 'class', 'claroDialogBox boxSuccess' );
                }
                else
                {
                    $('#generation').attr( 'class', 'claroDialogBox boxError' );
                }
                $( '#generation' ).text( response.response );
            }
        )        
    });
</script>