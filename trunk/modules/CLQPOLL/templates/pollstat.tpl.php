<?php // $Id$

/**
 * Claroline Poll Tool
 *
 * @version     CLQPOLL 0.9.9 $Revision$ - Claroline 1.9
 * @copyright   2001-2009 Universite Catholique de Louvain (UCL)
 * @license     http://www.gnu.org/copyleft/gpl.html (GPL) GENERAL PUBLIC LICENSE
 * @package     CLQPOLL
 * @author      Frederic Fervaille <frederic.fervaille@uclouvain.be>
 */ ?>

<?php echo claro_html_tool_title( $this->pageTitle ); ?>

<a class="claroCmd" href="<?php echo htmlspecialchars( Url::Contextualize( $_SERVER['PHP_SELF'].'?cmd=rqViewPoll&pollId='
                                                                          . $this->poll->getId() ) ); ?>">
    <img src="<?php echo get_icon_url( 'poll' ); ?>" alt="<?php echo get_lang( 'View poll'); ?>"/>
    <?php echo get_lang( 'View poll' ); ?>
</a>

<h2>
    <?php echo $this->poll->getTitle(); ?>
</h2>

    <?php if ( $this->poll->getAllVoteList() ) : ?>
<div class="stat" style="width: <?php echo 60 + ( 160 * $this->pollStat->getColumnCount() ); ?>px;
                         height: <?php echo 340 * $this->pollStat->getRawCount(); ?>px;">
    <h3>
        <?php echo $this->poll->getQuestion(); ?>
    </h3>
    <h4>
        <span>
        <?php echo count( $this->poll->getAllVoteList() ) . ' ' . get_lang( 'votes' ); ?>
        </span>
            <?php if ( $this->poll->getOption( '_type' ) == '_multi' ) : ?>
        <span>
                <?php echo $this->pollStat->getEmptyVoteCount() . ' ' . get_lang( 'blank votes' ); ?>
        </span>
            <?php endif; ?>
    </h4>
    <ul>
        <?php foreach ( $this->pollStat->getGraph() as $label => $data ) : ?>
        <li>
            <div class="percent"><?php echo $data[ 'percent' ]; ?></div>
            <div class="histobar" style="<?php echo $data[ 'style' ]; ?>"><?php if ( $data[ 'count' ] > 0 ) echo $data[ 'count' ]; ?></div>
            <div class="label"><?php echo $data[ 'label' ]; ?></div>
        </li>
        <?php endforeach; ?>
    </ul>
</div>
    <?php else : ?>
<h3>
    <?php echo get_lang( 'No vote for this poll'); ?>
</h3>
    <?php endif; ?>
