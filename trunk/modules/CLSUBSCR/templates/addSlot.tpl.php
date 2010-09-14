<?php // $Id$

/**
 * Subscription
 *
 * @version     CLSUBSCR 1.0-alpha $Revision$ - Claroline 1.9
 * @copyright   2001-2010 Universite catholique de Louvain (UCL)
 * @license     http://www.gnu.org/copyleft/gpl.html (GPL) GENERAL PUBLIC LICENSE
 * @package     CLSUBSCR
 * @author      Dimitri Rambout <dim@claroline.net>
 */

if( ! isset( $this->subscription ) )
{
    claro_die();
}
?>
<div style="border-bottom: 1px #CCC solid;">
    <span style="font-weight: bold;"><?php echo $this->subscription->getTitle(); ?></span>
    <div><?php echo $this->subscription->getDescription(); ?></div>
</div>
<div class="claroDialogBox boxQuestion">
    <div class="claroDialogMsg msgForm">
        <?php echo get_lang( 'How many slots do you want to create ?' ); ?>
        <br /><br />
        <form name="addSlot" action="<?php echo $_SERVER['PHP_SELF'] . claro_url_relay_context( '?' ); ?>" method="post">
            <input type="hidden" name="cmd" value="rqSlotAdd" />
            <input type="hidden" name="subscrId" value="<?php echo $this->subscription->getId(); ?>" />
            <?php echo get_lang( 'Create' ); ?>
            <input type="text" name="slots" value="" style="width: 20px; text-align: right;" />
            <?php echo get_lang( 'slots with' ); ?>
            <input type="text" name="places" value="" style="width: 20px; text-align: right;" />
            <?php echo get_lang( 'places' ); ?>
            <input type="submit" name="buttonOk" value="<?php echo get_lang( 'Ok' ); ?>" />
        </form>
    </div>
</div>
<!--div style="text-align: right;">
    <input type="button" name="buttonAddSlot" value="<?php echo get_lang( 'Add a slot' ); ?>" />
</div-->
<?php
if( isset( $this->slots ) ) :
?>
<form name="addSlotPlaces" action="<?php echo htmlspecialchars( php_self() . claro_url_relay_context( '?' ) ); ?>" method="post">
    <input type="hidden" name="cmd" value="exSlotAdd" />
    <input type="hidden" name="slots" value="<?php echo $this->slots; ?>" />
    <input type="hidden" name="subscrId" value="<?php echo $this->subscription->getId(); ?>" />
<?php
    for( $i=0; $i < $this->slots; $i++ ) :
        $error = ( isset( $this->slotsContent['errors'][ $i ] ) && $this->slotsContent['errors'][ $i ] == true ? true : false );
        $title = ( isset( $this->slotsContent['title'][ $i ] ) ? $this->slotsContent['title'][ $i ] : '' );
        $description = ( isset( $this->slotsContent['description'][ $i ] ) ? $this->slotsContent['description'][ $i ] : '' );
        $availableSpace = (int) ( isset( $this->slotsContent['places'][ $i ] ) ? $this->slotsContent['places'][ $i ] : $this->places );
?>
    <div class="claroDialogBox <?php echo isset( $this->slotsContent['errors'][ $i ] ) ? ($error == true  ? 'boxError' : 'boxSuccess') : ''; ?>">
        <div class="claroDialogMsg msgForm">
            <fieldset style="border: none; margin: 0; padding: 0;">
                <dl>
                    <dt><label for="title"><?php echo get_lang( 'Title' ); ?> :</label></dt>
                    <dd>
                        <input type="text" name="title[]" style="width: 300px; <?php echo $error == true && empty( $title ) ? 'border-color: #900' : ''; ?>" value="<?php echo $title; ?>" />
                        <div style="float: right;">
                            <input type="text" name="places[]" value="<?php echo $availableSpace; ?>" style="width: 20px; text-align: right; <?php echo $error == true && $availableSpace <= 0 ? 'border-color: #900' : ''; ?>" /> <?php echo get_lang( 'places' ); ?>
                        </div>
                    </dd>
                    <dt><label for="description"><?php echo get_lang( 'Description' ); ?> :</label></dt>
                    <dd>
                        <?php //echo claro_html_textarea_editor( 'description[]', '', 6, 15, '', 'simple'); ?>
                        <textarea style="width: 100%; height: 50px;" name="description[]"><?php echo $description ?></textarea>
                    </dd>
                </dl>
            </fieldset>
        </div>
    </div>
<?php
    endfor;
?>
    <input type="submit" name="buttonSave" value="<?php echo get_lang( 'Save' ); ?>" />
</form>
<?php
endif;
?>

