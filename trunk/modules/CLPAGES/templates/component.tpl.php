<!-- $Id$ -->

<div 
    id="component_<?php echo $this->component->getId(); ?>" 
    class="type_<?php echo $this->component->getType();?> sortableComponent <?php echo ($this->component->isVisible() ? '' : ' invisible'); ?>">

    <?php if ( $this->component->isTitleVisible() || claro_is_allowed_to_edit() ): ?>
        
    <div class="componentHeader">

        <?php if ( claro_is_allowed_to_edit() ): ?>
            
        <span class="componentHeaderCmd">
            
            <a href="#" class="mkUpCmd">
                <?php echo claro_html_icon('move_up', null, get_lang('Move up')); ?>
            </a>
            &nbsp;
            <a href="#" class="mkDownCmd">
                <?php echo claro_html_icon('move_down', null, get_lang('Move down')); ?>
            </a>
            &nbsp;
            <a href="#" class="mkInvisibleCmd" 
                <?php echo (!$this->component->isVisible() ? 'style="display:none"' : ''); ?>>
                <?php echo claro_html_icon('visible', null, get_lang('Make invisible')); ?>
            </a>
            <a href="#" class="mkVisibleCmd"
                <?php echo ($this->component->isVisible() ? 'style="display:none"' : ''); ?>>
                <?php echo claro_html_icon('invisible', null, get_lang('Make visible')); ?>
            </a>
            &nbsp;
            <a href="#" class="toggleEditorCmd">
                <?php echo claro_html_icon('edit'); ?></a>
            &nbsp;

            <?php if ($this->component->getPageDisplayMode() == 'SLIDE'): ?>
            
            <a rel="popup" 
               href="<?php echo htmlspecialchars(Url::Contextualize(get_module_url('CLPAGES')
                    . '/lib/s5/s5.php?pageId='.$this->component->getPageId()
                    . '&componentId='. $this->component->getId())); ?>" 
               class="s5ViewerCmd">
                <?php echo claro_html_icon('slide', null, get_lang('View')); ?>
            </a>
            &nbsp;
            
            <?php endif; ?>

            <a href="#" class="deleteComponentCmd">
                <?php echo claro_html_icon('delete', null, get_lang('Delete')); ?>
            </a>
            
        </span>
        
        <?php endif; ?> <!-- is_allowed_to_edit -->

        <span 
            class="componentHeaderTitle <?php echo ($this->component->isTitleVisible() ? '' : ' invisible');?>">
            &nbsp;<?php echo htmlspecialchars($this->component->getTitle()); ?>
        </span>
    </div>
    
    <?php endif; ?> <!-- title_visible -->

    <div class="componentContent">
        
    <?php echo $this->component->render(); ?>
        
    </div>
</div>
