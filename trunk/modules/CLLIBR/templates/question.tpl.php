<?php // $Id$
/**
 * Online library for Claroline
 *
 * @version     CLLIBR 1.1.5 $Revision$ - Claroline 1.11.5
 * @copyright   2001-2013 Universite catholique de Louvain (UCL)
 * @license     http://www.gnu.org/copyleft/gpl.html (GPL) GENERAL PUBLIC LICENSE
 * @package     CLLIBR
 * @author      Frederic Fervaille <frederic.fervaille@uclouvain.be>
 */ ?>

<p><?php echo $this->msg ?></p>
<form method="post" action="<?php echo claro_htmlspecialchars( Url::Contextualize( $_SERVER['PHP_SELF'] . '?cmd=' . $this->urlAction ) ); ?>" >
<?php foreach( $this->xid as $xid => $id ) : ?>
    <input type="hidden" name="<?php echo $xid; ?>" value="<?php echo $id; ?>" />
<?php endforeach; ?>
    <input type="submit" name="" value="<?php echo get_lang( 'Yes' ); ?>" />
    <a href="<?php echo  claro_htmlspecialchars( Url::Contextualize( $_SERVER['PHP_SELF'] . '?cmd=' . $this->urlCancel ) );?>">
        <input type="button" name="cancel" value="<?php echo get_lang( 'Cancel' );?>" />
    </a>
</form>