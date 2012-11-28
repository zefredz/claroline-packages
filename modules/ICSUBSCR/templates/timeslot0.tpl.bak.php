<h3><?php echo get_lang( 'Step 1 : choose the dates' ); ?></h3>
<form method="post"
      action="<?php echo htmlspecialchars( Url::Contextualize( get_module_url( 'ICSUBSCR' ) .'/timeslot.php?step=1&sessionId=' . $this->sessionId ) ); ?>">
<div id="datePicker" />
<div id="dateList" ></div>
<script type="text/javascript" charset="utf-8">
    var k = new Kalendae(datePicker, {
            months:1,
            mode:'single',
            selected:[Kalendae.moment().subtract({M:1}), Kalendae.moment().add({M:1})]
    });
    var nbToAdd = 0;
    k.subscribe('change',function(){
        nbToAdd++;
        var content="<span><input id=\"dateToAdd"+nbToAdd+"\" type=\"text\" name=\"data[dateToAdd]["+nbToAdd+"]\" value=\""+this.getSelected()+"\" size=\"8\" \/>"+
                    "<a id=\"delx"+nbToAdd+"\" class=\"claroCmd\" href=\"#delx"+nbToAdd+"\">"+
                    " <?php echo get_lang( 'Delete' ); ?>"+
                    "<\/a><br \/>"+
                    "<script>"+
                    "    $(\"#delx"+nbToAdd+"\").click(function(){"+
                    "        $(this).parent().remove();"+
                    "    });"+
                    "<\/script><\/span>";
        $("#dateList").append(content);
    });
</script>
<div id="nextStep">
<input type="submit"
       value="<?php echo get_lang( 'Next step' ); ?>" />
</div></form>