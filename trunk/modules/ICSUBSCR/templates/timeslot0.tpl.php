<h3><?php echo get_lang( 'Step 1 : choose the dates' ); ?></h3>
<div id="datePicker" />
<script type="text/javascript" charset="utf-8">
    var k = new Kalendae(datePicker, {
            months:1,
            mode:'single',
            lang:'fr',
            selected:[Kalendae.moment().subtract({M:1}), Kalendae.moment().add({M:1})]
    });
    Kalendae.moment.lang('fr',langDef['fr']);
    k.subscribe('change',function(){
        alert(this.getSelected());
    });
</script>
