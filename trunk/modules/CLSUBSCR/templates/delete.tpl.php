<div class="claroDialogBox boxWarning">
    <div class="claroDialogMsg msgForm">
    <?php echo get_lang( 'Are you sure that you want to delete the session <strong>%sessionTitle</strong> and all slots linked to it ?', array( '%sessionTitle' => $this->title ) )
        .     '<br /><br />'
        .    '<a href="' . $_SERVER['PHP_SELF'] . '?cmd=exDelete&amp;subscrId=' . $this->id . claro_url_relay_context( '&' ) . '">' . get_lang('Yes') . '</a>'
        .    '&nbsp;|&nbsp;'
        .    '<a href="' . $_SERVER['PHP_SELF'] . '">' . get_lang('No') . '</a>'
        ;
    ?>
    </div>
</div>
    