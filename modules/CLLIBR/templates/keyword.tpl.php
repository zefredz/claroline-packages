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

<table class="claroTable emphaseLine" style=" width: 100%;">
    <thead>
        <tr class="headerX">
            <th>
                <?php echo get_lang( 'Title'); ?>
            </th>
            <th>
                <?php echo get_lang( 'Associated keywords' ); ?>
            </th>
        </tr>
    </thead>
    <tbody>
<?php if ( $this->result ) : ?>
    <?php foreach( $this->result as $resourceId => $datas ) : ?>
    <tr>
        <td>
            <a href="<?php echo htmlspecialchars( Url::Contextualize( $_SERVER['PHP_SELF'].'?cmd=rqView&resourceId='. $resourceId ) );?>">
                <?php echo $datas[ 'title' ]; ?>
            </a><br />
        </td>
        <td>
        <?php foreach( $datas[ 'keywords' ] as $keyword ) : ?>
            <a href="<?php echo htmlspecialchars( Url::Contextualize( $_SERVER['PHP_SELF'].'?cmd=rqSearch&keyword=' . $keyword ) ); ?>">
                <?php echo $keyword; ?>
            </a>&nbsp;
        <?php endforeach; ?>
        </td>
    </tr>
    <?php endforeach; ?>
<?php else: ?>
        <tr>
            <td class="empty" colspan="6"><?php echo get_lang( 'No result' ); ?></td>
        </tr>
<?php endif; ?>
    </tbody>
</table>