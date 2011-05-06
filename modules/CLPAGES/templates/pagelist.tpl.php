<!-- $id$ -->

<?php if ( claro_is_allowed_to_edit() && !empty( $this->cmdMenu) ): ?>
<p>
    <?php echo claro_html_menu_horizontal( $this->cmdMenu ); ?>
</p>
<?php endif; ?>

<table class="claroTable emphaseLine" width="100%" border="0" cellspacing="2">
    <thead>
        <tr class="headerX" align="center" valign="top">
            <th><?php echo get_lang('Page'); ?></th>
            <th><?php echo get_lang('Creation date'); ?></th>

            <?php if( claro_is_allowed_to_edit() ): ?>
            
            <th><?php echo get_lang('Modify'); ?></th>
            <th><?php echo get_lang('Delete'); ?></th>
            <th><?php echo get_lang('Visibility'); ?></th>
            
            <?php endif; ?>

        </tr>
    </thead>

    <tbody>

    <?php if( !empty( $this->pageList ) && is_array($this->pageList) ): ?>

        <?php foreach( $this->pageList as $aPage ): ?>
        
        <tr>

            <td>
                <a 
                    href="<?php echo htmlspecialchars( Url::Contextualize(
                        get_module_url('CLPAGES').'/page.php?pageId='.$aPage['id'] )); ?>" 
                    title="<?php echo htmlspecialchars(strip_tags($aPage['description'])); ?>">
                    <?php echo claro_html_icon('page'); ?>&nbsp;
                    <?php echo htmlspecialchars($aPage['title']); ?>
                </a>
            </td>

            <td align="center">
                <?php echo htmlspecialchars( $aPage['creationTime'] );?>
            </td>
            
            <!-- start of manager commands -->
            <?php if( claro_is_allowed_to_edit() ): ?> 

                <td align="center">
                <a 
                    href="<?php echo htmlspecialchars( Url::contextualize(
                        $_SERVER['PHP_SELF'] . '?cmd=rqEdit&pageId='.$aPage['id'] ) ); ?>">
                    <?php echo claro_html_icon('edit'); ?>
                </a>
                </td>

                <td align="center">
                <a 
                    href="<?php echo htmlspecialchars( Url::contextualize( 
                        $_SERVER['PHP_SELF'].'?cmd=rqDelete&pageId='.$aPage['id'] ) ); ?>">
                    <?php echo claro_html_icon('delete'); ?>
                </a>
                </td>

                <?php if( $aPage['visibility'] == 'VISIBLE' ): ?>

                <td align="center">
                    <a 
                        href="<?php echo htmlspecialchars( Url::contextualize(
                            $_SERVER['PHP_SELF'].'?cmd=exInvisible&pageId='.$aPage['id'] ) ); ?>">
                        <?php echo claro_html_icon('visible'); ?>
                    </a>
                </td>

                <?php else: ?>

                <td align="center">
                    <a 
                        href="<?php echo htmlspecialchars( Url::contextualize(
                            $_SERVER['PHP_SELF'].'?cmd=exVisible&pageId='.$aPage['id'] ) ); ?>">
                        <?php echo claro_html_icon('invisible'); ?>
                    </a>
                </td>

                <?php endif; ?>
            
            <?php endif; ?>
            <!-- end of manager commands -->

        </tr>
        
        <?php endforeach; ?>
        
    <?php else: ?>
        
        <tr>
            <td align="center" colspan="<?php echo (claro_is_allowed_to_edit() ? '5' : '2'); ?>">
                    <?php echo get_lang('No pages'); ?>
            </td>
        </tr>
        
    <?php endif; ?>
        
    </tbody>
</table>
