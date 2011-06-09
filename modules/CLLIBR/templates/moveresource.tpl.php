<?php // $Id$
/**
 * Online library for Claroline
 *
 * @version     CLLIBR 0.6.2 $Revision$ - Claroline 1.9
 * @copyright   2001-2011 Universite catholique de Louvain (UCL)
 * @license     http://www.gnu.org/copyleft/gpl.html (GPL) GENERAL PUBLIC LICENSE
 * @package     CLLIBR
 * @author      Frederic Fervaille <frederic.fervaille@uclouvain.be>
 */ ?>

<strong><?php echo get_lang( 'Select the library' ); ?>:</strong>
<div>
    <?php foreach( $this->libraryList[ 'user' ] as $id => $datas ) : ?>
    <?php echo $datas[ 'title' ]; ?>
    <a href="<?php echo htmlspecialchars( Url::Contextualize( $_SERVER['PHP_SELF'].'?cmd=exMoveResource&libraryId=' . $id . '&resourceId='. $this->resourceId ) );?>">
        <img src="<?php echo get_icon_url( 'move' ); ?>" alt="<?php echo get_lang( 'Move' ); ?>"/>
    </a><br />
    <?php endforeach; ?>
</div>