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
                <?php echo get_lang( 'Relevancy' ); ?>
            </th>
        </tr>
    </thead>
    <tbody>
<?php if ( $this->result ) : ?>
    <?php foreach ( $this->result as $score => $datas ) : ?>
    <tr>
        <td class="searchResult">
            <a href="<?php echo htmlspecialchars( Url::Contextualize( $_SERVER['PHP_SELF'].'?cmd=rqView&resourceId='. $datas[ 'id' ] ) );?>">
                <?php echo $datas[ 'title' ]; ?>
            </a><br />
            <?php if ( isset( $datas[ 'matches' ][ 'description' ] ) ): ?>
            <small><?php echo $datas[ 'matches' ][ 'description' ]; ?></small>
            <?php endif; ?>
        </td>
        <td align="center">
            <?php $nbStar = $score < 60 ? ceil( $score ) : 60; ?>
            <?php for( $i = 0; $i < $nbStar; $i+=10 ) : ?>
            <img src="<?php echo get_icon_url( 'star' ); ?>" alt="+"/>
            <?php endfor; ?>
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