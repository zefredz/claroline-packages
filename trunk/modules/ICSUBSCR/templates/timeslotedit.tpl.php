<form   method="post"
        action="<?php echo htmlspecialchars( Url::Contextualize( $_SERVER['PHP_SELF'] . '?cmd=excreateSlot&sessionId=' . $this->session->getId() ) ); ?>" >
        Pour le jour suivant : <input  id="day"
        class="auto-kal"
        type="text"
        lang="<?php echo get_lang( '_lang_code' ); ?>"
        name="day"
        value="<?php echo date( get_lang( '_date' ) ); ?>"
        size="8" /><br />
D&eacute;couper la p&eacute;riode de 
<input id="begin"
       type="text"
       name="startHour"
       value="00:00"
       size="4" /> heures &agrave
<input id="end"
       type="text"
       name="startHour"
       value="00:00"
       size="4" /> heures

en
<input id="sliceNb"
       type="text"
       name="sliceNb"
       size="2"
       value="1" /> plages
    <input id="submitSlice" type="submit" name="submitSlice" value="<?php echo get_lang( 'Create' ); ?>" />
</form>