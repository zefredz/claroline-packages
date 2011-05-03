<!--
    $Id$
    
    Podcast submission form template
    * @version     1.9 $Revision$
    * @copyright   2001-2009 Universite catholique de Louvain (UCL)
    * @author      Frederic Minne <zefredz@claroline.net>
    * @license     http://www.gnu.org/copyleft/gpl.html
    *              GNU GENERAL PUBLIC LICENSE version 2 or later
    * @package     icpcrdr
-->
<form name="editPodcast" action="<?php echo $this->actionUrl; ?>" method="post">
<?php if(!is_null($this->id)) : ?>
    <input type="hidden" name="podcastId" value="<?php echo $this->id; ?>" />
<?php endif; ?>
    <fieldset>
        <legend><?php echo get_lang('Edit information for the curent link'); ?></legend>
        <dl>
            <dt><label for="url"><?php echo get_lang('Feed url'); ?>&nbsp;<span class="required">*</span>&nbsp;:</label></dt>
            <dd><input type="text" name="url" id="url" size="60" maxlength="200" value="<?php echo $this->url; ?>" /></dd>
            <dt><label for="title"><?php echo get_lang('Title'); ?>&nbsp;<span class="required">*</span>&nbsp;:</label></dt>
            <dd>
                <input type="text" name="title" id="title" size="60" maxlength="200" value="<?php echo $this->title; ?>" />
                <input type="button" name="gettitle" id="getTitleFromFeed" value="<?php echo get_lang("Get title from feed");?>" />
            </dd>
            <dt><?php echo get_lang('Visibility'); ?>&nbsp;:</dt>
            <dd>
                <input type="radio" id="visibility_visible" name="visibility" value="visible" <?php if( $this->visibility == 'visible' ) : echo 'checked="checked"'; endif; ?> />
                <label for="visibility_visible"><?php echo get_lang('Visible'); ?>&nbsp;<img src="<?php echo get_icon_url('visible'); ?>" alt="" /></label><br />
                <input type="radio" id="visibility_invisible" name="visibility" value="invisible" <?php if( $this->visibility == 'invisible' ) : echo 'checked="checked"'; endif; ?> />
                <label for="visibility_invisible"><?php echo get_lang('Invisible'); ?>&nbsp;<img src="<?php echo get_icon_url('invisible'); ?>" alt="" /></label>
            </dd>
        </dl>
    </fieldset>
    <div style="text-align: center;">
        <input type="submit" name="submit" value="<?php echo get_lang('Ok'); ?>" />&nbsp;&nbsp;
        <a href="<?php echo $_SERVER['PHP_SELF']; ?>"><input type="button" name="cancel" value="<?php echo get_lang('Cancel'); ?>" /></a>
    </div>
</form>
<script type="text/javascript">
$(document).ready( function(){
    $("#getTitleFromFeed").click(
        function() {
            $.get(
                '<?php echo get_module_url('ICPCRDR'); ?>/proxy.php',
                {
                    url: $("#url").val()
                },
                function(response){
                    $("#title").val( $(response).find('channel>title').text() );
                }
            );
        }
    );
});
</script>