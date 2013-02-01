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

<?php include( dirname(__FILE__) . '/searchform.tpl.php' ); ?>

<div id="mainContent">
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
        <?php foreach ( $this->result as $score => $lines ) : ?>
            <?php foreach( $lines as $id => $datas ) : ?>
        <tr>
            <td class="searchResult">
                <a href="<?php echo claro_htmlspecialchars( Url::Contextualize( $_SERVER['PHP_SELF'].'?cmd=rqView&resourceId='. $id ) );?>">
                    <?php echo $datas[ 'title' ]; ?>
                </a>
                <ul class="matches">
                <?php foreach( $datas[ 'matches' ] as $match => $value ) : ?>
                    <li>
                        <strong><?php echo get_lang( $match ); ?>: </strong><span class="match"><?php echo $value; ?></span>
                    </li>
                <?php endforeach; ?>
                </ul>
            </td>
            <td align="center">
                <?php $nbStar = $score < 60 ? ceil( $score ) : 60; ?>
                <?php for( $i = 0; $i < $nbStar; $i+=10 ) : ?>
                <img src="<?php echo get_icon_url( 'star' ); ?>" alt="+"/>
                <?php endfor; ?>
            </td>
        </tr>
            <?php endforeach; ?>
        <?php endforeach; ?>
    <?php else: ?>
            <tr>
                <td class="empty" colspan="6"><?php echo get_lang( 'No result' ); ?></td>
            </tr>
    <?php endif; ?>
        </tbody>
    </table>
</div>