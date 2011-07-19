<?php // $Id$
/**
 * Online library for Claroline
 *
 * @version     CLLIBR 0.8.6 $Revision$ - Claroline 1.9
 * @copyright   2001-2011 Universite catholique de Louvain (UCL)
 * @license     http://www.gnu.org/copyleft/gpl.html (GPL) GENERAL PUBLIC LICENSE
 * @package     CLLIBR
 * @author      Frederic Fervaille <frederic.fervaille@uclouvain.be>
 */ ?>

<?php if ( ! isset( $this->courseId ) ) : ?>
<span> 
    <a class="claroCmd" href="<?php echo htmlspecialchars( Url::Contextualize( get_module_url( 'CLLIBR' ) .'/index.php?cmd=rqShowLibrarylist' ) ); ?>">
        <img src="<?php echo $this->icon; ?>" alt="<?php echo get_lang( 'Libraries' ); ?>" />
        <?php echo get_lang( 'Libraries' ); ?>
    </a>
</span>
<?php endif; ?>
<table class="claroTable emphaseLine" style=" width: 100%;">
    <thead>
        <tr class="headerX">
            <th>
                <a href="<?php echo htmlspecialchars( Url::Contextualize( get_module_url( 'CLLIBR' ) .'/index.php?cmd=rqShowBookmark' ) ); ?>">
                <?php echo get_lang( 'Title'); ?>
                </a>
            </th>
            <th>
                <a href="<?php echo htmlspecialchars( Url::Contextualize( get_module_url( 'CLLIBR' ) .'/index.php?cmd=rqShowBookmark&sort=author' ) ); ?>">
                <?php echo get_lang( 'Author(s)' ); ?>
                </a>
            </th>
            <!--
            <th>
                <?php echo get_lang( 'Selected' ); ?>
            </th>
            -->
            <th>
                <?php echo get_lang( 'Actions' ); ?>
            </th>
        </tr>
    </thead>
    <tbody>
<?php if ( ! empty( $this->resourceList ) ) : ?>
    <?php foreach ( $this->resourceList as $resource ) : ?>
        <tr>
            <td>
                <a href="<?php echo htmlspecialchars( Url::Contextualize( get_module_url( 'CLLIBR' ) .'/index.php?cmd=rqView&resourceId='. $resource[ 'id' ] ) );?>">
                    <?php echo $resource[ 'title' ]; ?>
                </a>
            </td>
            <td> 
                <?php echo $resource[ 'author' ]; ?>
            </td>
            <!--
            <td align="center">
                <input type="checkbox" name="select[<?php echo $resource[ 'id' ]; ?>]" />
            </td>
            -->
            <td align="center">
                <!--
                <a href="<?php echo htmlspecialchars( Url::Contextualize( get_module_url( 'CLLIBR' ) .'/index.php?cmd=exExport&resourceId='. $resource[ 'id' ] ) );?>">
                    <img src="<?php echo get_icon_url( 'export' ); ?>" alt="<?php echo get_lang( 'Export' ); ?>"/>
                </a>
                -->
                <a title="<?php echo get_lang( 'Remove' ); ?>" href="<?php echo htmlspecialchars( Url::Contextualize( get_module_url( 'CLLIBR' ) .'/index.php?cmd=exUnbookmark&resourceId='. $resource[ 'id' ] ) );?>">
                    <img src="<?php echo get_icon_url( 'delete' ); ?>" alt="<?php echo get_lang( 'Remove' ); ?>"/>
                </a>
            </td>
        </tr>
    <?php endforeach; ?>
<?php else: ?>
        <tr>
            <td style="text-align: center; font-style: italic; color: silver;" colspan="5"><?php echo get_lang( 'Empty bookmark' ); ?></td>
        </tr>
<?php endif; ?>
    </tbody>
</table>