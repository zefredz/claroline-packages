<script type="text/javascript">
    $(document).ready(function(){
        function fixHeight(){
            var iframeWidth=$("#openMeetingsSession").width();
            if(iframeWidth>1200){
                var iframeHeight=720;
            }else{
                var iframeHeight = iframeWidth*0.6;
            }
            $("#openMeetingsSession").attr({height:iframeHeight+"px;"});
        }
        fixHeight();
        $(window).resize(function(){
            fixHeight();
        });
    });
</script>
<iframe id="openMeetingsSession"
        src="<?php echo $this->url; ?>"
        width="100%"
        height="600" />