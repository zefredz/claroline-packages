<div id="epcAsyncQueryContainer"><img src="<?php echo get_icon_url('loading'); ?>" alt="loading..." /></div>
<script type="text/javascript">
    $(function(){
        $.get(
            '<?php echo get_module_url('ICEPC'); ?>/worker.php',
            {
                cmd: '<?php echo $this->cmd; ?>',
                epcSearchString: '<?php echo $this->epcSearchString; ?>',
                epcAcadYear: '<?php echo $this->epcAcadYear; ?>',
                epcSearchFor: '<?php echo $this->epcSearchFor; ?>',
                epcLinkExistingStudentsToClass: '<?php echo $this->epcLinkExistingStudentsToClass; ?>',
                epcValidatePendingUsers: '<?php echo $this->epcValidatePendingUsers; ?>'
            },
            function(resp){
            $('#epcAsyncQueryContainer').empty().html(resp);
            registerCollapseBehavior();
        });
    });
</script>