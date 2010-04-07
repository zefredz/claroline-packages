<div class="claroDialogBox boxInfo" id="generation">
    <img src="<?php echo get_icon( 'loading' ); ?>" />
    <?php echo get_lang( 'Please wait... Courses\' status is reseting.'); ?>    
</div>
<script type="text/javascript">
    $(document).ready( function() {
        if( confirm( "<?php echo get_lang( 'Are you sure what you want to reset courses\' status ?'); ?>" ) )
        {
            $.getJSON(
                "<?php echo Url::Contextualize( get_module_url( 'CLSTATS' ) . '/backends/backend.php?cmd=resetCoursesStatus' ); ?>",
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
        }
    });
</script>