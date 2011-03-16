<?php // $Id$
/**
 * Online library for Claroline
 *
 * @version     CLLIBR 0.3.0 $Revision$ - Claroline 1.9
 * @copyright   2001-2011 Universite catholique de Louvain (UCL)
 * @license     http://www.gnu.org/copyleft/gpl.html (GPL) GENERAL PUBLIC LICENSE
 * @package     CLLIBR
 * @author      Frederic Fervaille <frederic.fervaille@uclouvain.be>
 */ ?>

<span>
    <a class="claroCmd" href="<?php echo htmlspecialchars( Url::Contextualize( get_module_url( 'CLLIBR' ) .'/index.php?context=librarylist' ) ); ?>">
        <img src="<?php echo $this->icon; ?>" alt="<?php echo get_lang( 'Libraries' ); ?>" />
        <?php echo get_lang( 'Libraries' ); ?>
    </a>
</span>
<!-- NOT IMPLEMENTED YET
<span>
    <a class="claroCmd" href="<?php echo htmlspecialchars( Url::Contextualize( get_module_url( 'CLLIBR' ) .'/index.php?cmd=exExportAll' ) ); ?>">
        <img src="<?php echo get_icon_url( 'export' ); ?>" alt="<?php echo get_lang( 'Export' ); ?>" />
        <?php echo get_lang( 'Export all' ); ?>
    </a>
</span>
<span>
    <a class="claroCmd" href="<?php echo htmlspecialchars( Url::Contextualize( get_module_url( 'CLLIBR' ) .'/index.php?cmd=exExportSelected') ); ?>">
        <img src="<?php echo get_icon_url( 'export' ); ?>" alt="<?php echo get_lang( 'Export' ); ?>" />
        <?php echo get_lang( 'Export selected' ); ?>
    </a>
</span>
-->
<table class="claroTable emphaseLine" style=" width: 100%;">
    <thead>
        <tr class="headerX">
            <th>
                <?php echo get_lang( 'Title'); ?>
            </th>
            <th>
                <?php echo get_lang( 'Author(s)' ); ?>
            </th>
            <th>
                <?php echo get_lang( 'Collection(s)' ); ?>
            </th>
            <th>
                <?php echo get_lang( 'Selected' ); ?>
            </th>
            <th>
                <?php echo get_lang( 'Commands' ); ?>
            </th>
        </tr>
    </thead>
    <tbody>
<?php if ( $this->resourceList ) : ?>
    <?php foreach ( $this->resourceList as $resourceId => $metadata ) : ?>
        <tr>
            <td>
                <a href="<?php echo htmlspecialchars( Url::Contextualize( get_module_url( 'CLLIBR' ) .'/index.php?cmd=rqView&resourceId='. $resourceId ) );?>">
                    <?php echo implode( ', ' , $metadata->get( 'title' ) ); ?>
                </a>
            </td>
            <td> 
                <?php echo implode( ', ' , $metadata->get( 'author' ) ); ?>
            </td>
            <td> 
                <?php echo implode( ', ' , $metadata->get( 'collection' ) ); ?>
            </td>
            <td align="center">
                <input type="checkbox" name="select[<?php echo $resourceId; ?>]" />
            </td>
            <td align="center">
                <a href="<?php echo htmlspecialchars( Url::Contextualize( get_module_url( 'CLLIBR' ) .'/index.php?cmd=exExport&resourceId='. $resourceId ) );?>">
                    <img src="<?php echo get_icon_url( 'export' ); ?>" alt="<?php echo get_lang( 'Export' ); ?>"/>
                </a>
                <a href="<?php echo htmlspecialchars( Url::Contextualize( get_module_url( 'CLLIBR' ) .'/index.php?cmd=exRemove&context=bookmark&resourceId='. $resourceId ) );?>">
                    <img src="<?php echo get_icon_url( 'delete' ); ?>" alt="<?php echo get_lang( 'Delete' ); ?>"/>
                </a>
            </td>
        </tr>
    <?php endforeach; ?>
<?php else: ?>
        <tr>
            <td style="text-align: center; font-style: italic; color: silver;" colspan="6"><?php echo get_lang( 'Empty bibliography' ); ?></td>
        </tr>
<?php endif; ?>
    </tbody>
</table>