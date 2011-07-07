<?php // $Id$
/**
 * Online library for Claroline
 *
 * @version     CLLIBR 0.7.0 $Revision$ - Claroline 1.9
 * @copyright   2001-2011 Universite catholique de Louvain (UCL)
 * @license     http://www.gnu.org/copyleft/gpl.html (GPL) GENERAL PUBLIC LICENSE
 * @package     CLLIBR
 * @author      Frederic Fervaille <frederic.fervaille@uclouvain.be>
 */ ?>

<?php if ( $this->userId ) : ?>
<span>
    <a class="claroCmd" href="<?php echo htmlspecialchars( $_SERVER['PHP_SELF'].'?cmd=rqShowLibrarylist'); ?>">
        <img src="<?php echo get_icon_url( 'icon' ); ?>" alt="<?php echo get_lang( 'Libraries' ); ?>" />
        <?php echo get_lang( 'Libraries' ); ?>
    </a>
</span>
<span>
    <a class="claroCmd" href="<?php echo htmlspecialchars( Url::Contextualize( $_SERVER['PHP_SELF'].'?cmd=rqShowBookmark') ); ?>">
        <img src="<?php echo get_icon_url( 'bookmark' ); ?>" alt="<?php echo get_lang( 'Bookmark' ); ?>" />
        <?php echo get_lang( 'My bookmark' ); ?>
    </a>
</span>
<?php endif; ?>

<fieldset id="bibliography">
    <legend><?php echo get_lang( 'Documents' ); ?></legend>
    <table class="claroTable emphaseLine" style=" width: 100%;">
        <thead>
            <tr class="headerX">
                <th>
                    <?php echo get_lang( 'Title' ); ?>
                </th>
                <th>
                    <?php echo get_lang( 'Author(s)' ); ?>
                </th>
                <!--
                <th>
                    <?php echo get_lang( 'Selected' ); ?>
                </th>
                -->
                <th>
                    <?php echo get_lang( 'Commands' ); ?>
                </th>
    <?php if ( $this->edit_allowed ) : ?>
                <th>
                    <?php echo get_lang( 'Visibility' ); ?>
                </th>
    <?php endif; ?>
            </tr>
        </thead>
        <tbody>
    <?php if ( ! empty( $this->resourceList ) ) : ?>
        <?php foreach ( $this->resourceList as $resourceId => $datas ) : ?>
            <?php if ( $datas[ 'is_visible' ] || $this->edit_allowed ) : ?>
            <tr>
                <td>
                    <a href="<?php echo htmlspecialchars( Url::Contextualize( $_SERVER['PHP_SELF'].'?cmd=rqView&resourceId='. $resourceId ) );?>">
                        <?php echo $datas[1]->get( Metadata::TITLE ); ?>
                    </a>
                </td>
                <td> 
                    <?php echo $datas[1]->get( 'author' ); ?>
                </td>
                <!--
                <td align="center">
                    <input type="checkbox" name="select[<?php echo $resourceId; ?>]" />
                </td>
                -->
                <td align="center">
                <?php if ( $this->userId ) : ?>
                    <a href="<?php echo htmlspecialchars( Url::Contextualize( $_SERVER['PHP_SELF'].'?cmd=exBookmark&resourceId='. $resourceId ) );?>">
                        <img src="<?php echo get_icon_url( 'bookmark' ); ?>" alt="<?php echo get_lang( 'Add to my bookmark' ); ?>"/>
                    </a>
                <?php endif; ?>
                    <!--
                    <a href="<?php echo htmlspecialchars( Url::Contextualize( $_SERVER['PHP_SELF'].'?cmd=exExport&resourceId='. $resourceId ) );?>">
                        <img src="<?php echo get_icon_url( 'export' ); ?>" alt="<?php echo get_lang( 'Export' ); ?>"/>
                    </a>
                    -->
                <?php if ( $this->edit_allowed ) : ?>
                    <a href="<?php echo htmlspecialchars( Url::Contextualize( $_SERVER['PHP_SELF'].'?cmd=rqRemove&resourceId='. $resourceId ) );?>">
                        <img src="<?php echo get_icon_url( 'delete' ); ?>" alt="<?php echo get_lang( 'Delete' ); ?>"/>
                    </a>
                </td>
                <td align="center">
                    <?php if ( $datas[ 'is_visible' ] ) : ?>
                    <a href="<?php echo htmlspecialchars( Url::Contextualize( $_SERVER['PHP_SELF'].'?cmd=exInvisible&resourceId='. $resourceId ) );?>">
                        <img src="<?php echo get_icon_url( 'visible' ); ?>" alt="<?php echo get_lang( 'visible' ); ?>"/>
                    </a>
                    <?php else : ?>
                    <a href="<?php echo htmlspecialchars( Url::Contextualize( $_SERVER['PHP_SELF'].'?cmd=exVisible&resourceId='. $resourceId ) );?>">
                        <img src="<?php echo get_icon_url( 'invisible' ); ?>" alt="<?php echo get_lang( 'invisible' ); ?>"/>
                    </a>
                    <?php endif; ?>
                </td>
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
</fieldset>

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
                    <a href="<?php echo htmlspecialchars( Url::Contextualize( $_SERVER['PHP_SELF'].'?cmd=rqShowCatalogue&libraryId='. $id ) );?>">
                        <?php echo $title; ?>
                    </a>
                </td>
                <td align="center">
                    <a href="<?php echo htmlspecialchars( Url::Contextualize( $_SERVER['PHP_SELF'].'?cmd=rqRemoveLibrary&libraryId='. $id ) );?>">
                        <img src="<?php echo get_icon_url( 'delete' ); ?>" alt="<?php echo get_lang( 'Delete' ); ?>"/>
                    </a>
                </td>
            </tr>
    <?php endforeach; ?>
        </tbody>
    </table>
</fieldset>
<?php endif; ?>