<div id="mainContent">
    <fieldset id="archive" class="collapsible collapsed">
        <legend><a class="doCollapse" href="#"><?php echo get_lang( 'Archive course content' ); ?></a></legend>
        <div class="collapsible-wrapper" style="display: none;" >
            <form id="archiveCourse"
                  method="post"
                  action="<?php echo claro_htmlspecialchars( 'index.php?cmd=archive' ); ?>">
                <table class="claroTable emphaseLine" style="width: 100%;">
                    <thead>
                        <tr class="headerX">
                            <th><?php echo get_lang( 'Title' ); ?></th>
                            <th><input id="selectAll4Archive"
                                       name="selectAll"
                                       type="checkbox"
                                       checked="checked" /></th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php foreach( $this->itemList as $itemType => $itemList ) : ?>
                        <tr>
                            <td class="itemName" colspan="4"><?php echo get_lang( $itemType ); ?></td>
                        </tr>
                        <?php if( ! empty( $itemList ) ) : ?>
                            <?php foreach( $itemList as $itemId => $itemData ) : ?>
                        <tr>
                            <td><?php echo $itemData[ 'title' ]; ?></td>
                            <td align="center"><input name="item[<?php echo $itemType . '_' . $itemId; ?>]"
                                                      type="checkbox"
                                                      checked="checked" /></td>
                        </tr>
                            <?php endforeach; ?>
                        <?php else : ?>
                        <tr>
                            <td class="empty" colspan="4"><?php echo get_lang( 'empty' ); ?></td>
                        </tr>
                        <?php endif; ?>
                    <?php endforeach; ?>
                    </tbody>
                </table>
                <input type="submit" name="" value="<?php echo get_lang( 'Archive' ); ?>" />
            </form>
        </div>
    </fieldset>
    <fieldset id="export" class="collapsible collapsed">
        <legend><a class="doCollapse" href="#"><?php echo get_lang( 'Import course into Moodle' ); ?></a></legend>
        <div class="collapsible-wrapper" style="display: none;" >
            <form id="exportCourse"
                  method="post"
                  action="<?php echo claro_htmlspecialchars( 'index.php?cmd=export' ); ?>">
                <table class="claroTable emphaseLine" style="width: 100%;">
                    <thead>
                        <tr class="headerX">
                            <th><?php echo get_lang( 'Title' ); ?></th>
                            <th><input id="selectAll4import"
                                       name="selectAll"
                                       type="checkbox"
                                       checked="checked" /></th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php foreach( $this->itemList as $itemType => $itemList ) : ?>
                        <tr>
                            <td class="itemName" colspan="4"><?php echo get_lang( $itemType ); ?></td>
                        </tr>
                        <?php if( ! empty( $itemList ) ) : ?>
                            <?php foreach( $itemList as $itemId => $itemData ) : ?>
                        <tr>
                            <td><?php echo $itemData[ 'title' ]; ?></td>
                            <td align="center"><input name="item[<?php echo $itemType . '_' . $itemId; ?>]"
                                                      type="checkbox"
                                                      checked="checked" /></td>
                        </tr>
                            <?php endforeach; ?>
                        <?php else : ?>
                        <tr>
                            <td class="empty" colspan="4"><?php echo get_lang( 'empty' ); ?></td>
                        </tr>
                        <?php endif; ?>
                    <?php endforeach; ?>
                    </tbody>
                </table>
                <input type="submit" name="" value="<?php echo get_lang( 'Export' ); ?>" />
            </form>
        </div>
    </fieldset>
</div>