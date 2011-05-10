<!-- $Id$ -->

<div class="componentEditor">
    
<div id="errorMessage_<?php echo $this->component->getId(); ?>"></div>

<form id="form_<?php echo $this->component->getId(); ?>" action="ajaxHandler.php" method="post">
    
    <?php echo claro_form_relay_context(); ?>
    <input type="hidden" name="cmd" value="exEdit" />
    <input type="hidden" name="pageId" value="<?php echo $this->component->getPageId(); ?>" />
    <input type="hidden" name="itemId" value="<?php echo $this->component->getId(); ?>" />
    <input type="hidden" name="itemType" value="<?php echo $this->component->getType(); ?>" />
    
    <fieldset>
        <dl>
            <dt>
                <label for="title_<?php echo $this->component->getId();?>">
                <?php echo get_lang('Title'); ?>
                </label>
            </dt>
            <dd>
                <input type="text" 
                       name="title_<?php echo $this->component->getId(); ?>" 
                       id="title_<?php echo $this->component->getId(); ?>" 
                       maxlength="255" 
                       value="<?php echo htmlspecialchars($this->component->getTitle()); ?>" />
                &nbsp;
                <input type="checkbox" 
                       name="titleVisibility_<?php echo $this->component->getId(); ?>" 
                       id="titleVisibility_<?php echo $this->component->getId(); ?>" 
                       value="VISIBLE" <?php echo ($this->component->isTitleVisible() ? ' checked="checked"' : ''); ?> />
            </dd>
            <dt>
                <label for="titleVisibility_<?php echo $this->component->getId(); ?>">
                    <?php echo get_lang('Display title'); ?>
                </label>
            </dt>
            <dd>
                <?php echo $this->component->editor(); ?>
            </dd>
            <dt>
                &nbsp;
            </dt>
            <dd>
                <input type="submit" 
                       value="<?php echo get_lang('Ok'); ?>" />
                &nbsp;
                <input type="button" 
                       id="bCancel_<?php echo $this->component->getId(); ?>" 
                       value="<?php echo get_lang('Cancel'); ?>"/>
            </dd>
        </dl>
    </fieldset>
</form>
 </div>
