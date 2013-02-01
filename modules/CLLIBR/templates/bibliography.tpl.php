<?php // $Id$
/**
 * Online library for Claroline
 *
 * @version     CLLIBR 1.1.5
 * @copyright   2001-2013 Universite catholique de Louvain (UCL)
 * @license     http://www.gnu.org/copyleft/gpl.html (GPL) GENERAL PUBLIC LICENSE
 * @package     CLLIBR
 * @author      Frederic Fervaille <frederic.fervaille@uclouvain.be>
 */ ?>

<?php include( 'selector.tpl.php' ); ?>

<form method="post"
      action="<?php echo claro_htmlspecialchars( Url::Contextualize( $_SERVER['PHP_SELF'] ) );?>">
    <fieldset id="bibliography">
        <legend><?php echo get_lang( 'Documents' ); ?></legend>
        <table class="claroTable emphaseLine" style=" width: 100%;">
            <thead>
                <tr class="headerX">
                    <th>
                        <a href="<?php echo claro_htmlspecialchars( Url::Contextualize( get_module_url( 'CLLIBR' ) .'/index.php?cmd=rqShowBibliography&libraryId=' . $this->libraryId ) ); ?>">
                        <?php echo get_lang( 'Title'); ?>
                        </a>
                    </th>
                    <th>
                        <a href="<?php echo claro_htmlspecialchars( Url::Contextualize( get_module_url( 'CLLIBR' ) .'/index.php?cmd=rqShowBibliography&sort=author' . $this->libraryId ) ); ?>">
                        <?php echo get_lang( 'Author(s)' ); ?>
                        </a>
                    </th>
                <?php if( $this->userId ) : ?>
                    <?php if ( count( $this->resourceList ) > 1 ) : ?>
                    <th>
                        <input id="selectAll" type="checkbox" />
                    </th>
                    <?php endif; ?>
                    <th>
                        <?php echo get_lang( 'Actions' ); ?>
                    </th>
                <?php endif; ?>
        <?php if ( $this->edit_allowed ) : ?>
                    <th>
                        <?php echo get_lang( 'Visibility' ); ?>
                    </th>
        <?php endif; ?>
                </tr>
            </thead>
            <tbody>
        <?php if ( ! empty( $this->resourceList ) ) : ?>
            <?php foreach ( $this->resourceList as $resource ) : ?>
                <?php if ( $resource[ 'is_visible' ] || $this->edit_allowed ) : ?>
                <tr>
                    <td>
                        <a href="<?php echo claro_htmlspecialchars( Url::Contextualize( $_SERVER['PHP_SELF'].'?cmd=rqView&resourceId='. $resource[ 'id' ] ) );?>">
                            <?php echo $resource[ 'title' ]; ?>
                        </a>
                    </td>
                    <td> 
                        <?php echo $resource[ 'author' ]; ?>
                    </td>
                    <?php if( $this->userId ) : ?>
                        <?php if ( count( $this->resourceList ) > 1 ) : ?>
                    <td align="center">
                        <input class="resourceSelect" type="checkbox" name="resource[<?php echo $resource[ 'id' ]; ?>]" />
                    </td>
                        <?php endif; ?>
                    <td align="center">
                        <a title="<?php echo get_lang( 'Add to my bookmark' ); ?>" href="<?php echo claro_htmlspecialchars( Url::Contextualize( $_SERVER['PHP_SELF'].'?cmd=exBookmark&resourceId='. $resource[ 'id' ] ) );?>">
                            <img src="<?php echo get_icon_url( 'bookmark' ); ?>" alt="<?php echo get_lang( 'Add to my bookmark' ); ?>"/>
                        </a>
                        <!--
                        <a href="<?php echo claro_htmlspecialchars( Url::Contextualize( $_SERVER['PHP_SELF'].'?cmd=exExport&resourceId='. $resource[ 'id' ] ) );?>">
                            <img src="<?php echo get_icon_url( 'export' ); ?>" alt="<?php echo get_lang( 'Export' ); ?>"/>
                        </a>
                        -->
                        <?php if ( $this->edit_allowed ) : ?>
                        <a title="<?php echo get_lang( 'Remove' ); ?>" href="<?php echo claro_htmlspecialchars( Url::Contextualize( $_SERVER['PHP_SELF'].'?cmd=rqRemove&resourceId='. $resource[ 'id' ] ) );?>">
                            <img src="<?php echo get_icon_url( 'delete' ); ?>" alt="<?php echo get_lang( 'Remove' ); ?>"/>
                        </a>
                    </td>
                    <td align="center">
                            <?php if ( $resource[ 'is_visible' ] ) : ?>
                        <a href="<?php echo claro_htmlspecialchars( Url::Contextualize( $_SERVER['PHP_SELF'].'?cmd=exInvisible&resourceId='. $resource[ 'id' ] ) );?>">
                            <img src="<?php echo get_icon_url( 'visible' ); ?>" alt="<?php echo get_lang( 'Visible' ); ?>"/>
                        </a>
                            <?php else : ?>
                        <a href="<?php echo claro_htmlspecialchars( Url::Contextualize( $_SERVER['PHP_SELF'].'?cmd=exVisible&resourceId='. $resource[ 'id' ] ) );?>">
                            <img src="<?php echo get_icon_url( 'invisible' ); ?>" alt="<?php echo get_lang( 'Invisible' ); ?>"/>
                        </a>
                            <?php endif; ?>
                    </td>
                        <?php endif; ?>
                    <?php endif; ?>
                </tr>
                <?php endif; ?>
            <?php endforeach; ?>
        <?php else: ?>
                <tr>
                    <td class="empty" colspan="5"><?php echo get_lang( 'Empty bibliography' ); ?></td>
                </tr>
        <?php endif; ?>
            </tbody>
        </table>
        <?php if ( $this->userId && count( $this->resourceList ) > 1 ) : ?>
        <div id="onSelected">
            <span class="claroCmd"><?php echo get_lang( 'On selected items' ); ?>:</span>
            <select name="cmd">
                <option value="exBookmark"><?php echo get_lang( 'Add to my bookmark' ); ?></option>
            <?php if ( $this->edit_allowed ) : ?>
                <option value="exRemove"><?php echo get_lang( 'Remove from bibliography' ); ?></option>
            <?php endif; ?>
            </select>
            <input id="submit" type="submit" name="submit" value="<?php echo get_lang( 'OK' ); ?>" />
        </div>
        <?php endif; ?>
    </fieldset>
</form>

<?php if ( ! empty( $this->courseLibraryList ) ) : ?>
<fieldset id="courseLibrary">
    <legend><?php echo get_lang( 'Libraries' ); ?></legend>
    <table class="claroTable emphaseLine" style=" width: 100%;">
        <thead>
            <tr class="headerX">
                <th>
                    <?php echo get_lang( 'Libraries'); ?>
                </th>
    <?php if ( $this->edit_allowed ) : ?>
                <th>
                    <?php echo get_lang( 'Commands' ); ?>
                </th>
    <?php endif; ?>
            </tr>
        </thead>
        <tbody>
    <?php foreach( $this->courseLibraryList as $id => $title ) : ?>
            <tr>
                <td>
                    <a href="<?php echo claro_htmlspecialchars( Url::Contextualize( $_SERVER['PHP_SELF'].'?cmd=rqShowCatalogue&libraryId='. $id ) );?>">
                        <?php echo $title; ?>
                    </a>
                </td>
        <?php if( $this->edit_allowed ) : ?>
                <td align="center">
                    <a href="<?php echo claro_htmlspecialchars( Url::Contextualize( $_SERVER['PHP_SELF'].'?cmd=rqRemoveLibrary&libraryId='. $id ) );?>">
                        <img src="<?php echo get_icon_url( 'delete' ); ?>" alt="<?php echo get_lang( 'Delete' ); ?>"/>
                    </a>
                </td>
        <?php endif; ?>
            </tr>
    <?php endforeach; ?>
        </tbody>
    </table>
</fieldset>
<?php endif; ?>