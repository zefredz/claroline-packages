<?php // $Id$
/**
 * Online library for Claroline
 *
 * @version     CLLIBR 0.3.5 $Revision$ - Claroline 1.9
 * @copyright   2001-2011 Universite catholique de Louvain (UCL)
 * @license     http://www.gnu.org/copyleft/gpl.html (GPL) GENERAL PUBLIC LICENSE
 * @package     CLLIBR
 * @author      Frederic Fervaille <frederic.fervaille@uclouvain.be>
 */ ?>

<p><?php echo $this->msg ?></p>
<form method="post" action="<?php echo htmlspecialchars( Url::Contextualize( $_SERVER['PHP_SELF'] . '?cmd=' . $this->urlAction ) ); ?>" >
<?php foreach( $this->xid as $xid => $id ) : ?>
    <input type="hidden" name="<?php echo $xid; ?>" value="<?php echo $id; ?>" />
<?php endforeach; ?>
    <input type="submit" name="" value="<?php echo get_lang( 'Yes' ); ?>" />
    <?php echo claro_html_button( htmlspecialchars( Url::Contextualize( $_SERVER['PHP_SELF'] . '?cmd=' . $this->urlCancel ) ) , get_lang( 'Cancel' ) ); ?>
</form>