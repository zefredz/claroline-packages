<?php // $Id$
/**
 * Student Report for Claroline
 *
 * @version     UCREPORT 2.1.0 $Revision$ - Claroline 1.9
 * @copyright   2001-2010 Universite catholique de Louvain (UCL)
 * @license     http://www.gnu.org/copyleft/gpl.html (GPL) GENERAL PUBLIC LICENSE
 * @package     UCREPORT
 * @author      Frederic Fervaille <frederic.fervaille@uclouvain.be>
 */ ?>
<span>
    <a class="claroCmd" href="<?php echo htmlspecialchars( Url::Contextualize( $_SERVER['PHP_SELF'] ) ); ?>">
        <img src="<?php echo get_icon_url( 'go_left' ); ?>" alt="back" />
        <?php echo get_lang( 'Back to the report list' ); ?>
    </a>
</span>
<?php if ( claro_is_allowed_to_edit() ) : ?>
    <?php if( ! $this->id ) : ?>
<span>
    <a class="claroCmd" href="<?php echo htmlspecialchars( Url::Contextualize( $_SERVER['PHP_SELF'].'?cmd=rqPublish') ); ?>">
        <img src="<?php echo get_icon_url( 'export_list' ); ?>" alt="generate" />
        <?php echo get_lang( 'Publish the report' ); ?>
    </a>
</span>
    <?php endif; ?>
<span>
    <a class="claroCmd" href="<?php echo htmlspecialchars( Url::Contextualize( $_SERVER['PHP_SELF'].'?cmd=ex' . $this->id ? 'Re' : 'Ex' . 'port2xml&id=' . $this->id ) ); ?>">
        <img src="<?php echo get_icon_url( 'export' ); ?>" alt="export" />
        <?php echo get_lang( 'Export to MS-Excel xlsx file' ); ?>
    </a>
</span>
<span>
    <a class="claroCmd" href="<?php echo htmlspecialchars( Url::Contextualize( $_SERVER['PHP_SELF'].'?cmd=ex' . $this->id ? 'Re' : 'Ex' . 'port2csv&id=' . $this->id ) ); ?>">
        <img src="<?php echo get_icon_url( 'export' ); ?>" alt="export" />
        <?php echo get_lang( 'Export to csv' ); ?>
    </a>
</span>
<?php endif; ?>
<span>
    <a class="claroCmd" href="<?php echo htmlspecialchars( Url::Contextualize( $_SERVER['PHP_SELF'].'?cmd=ex' . ( $this->id ? 'Re' : 'Ex' ) . 'port2pdf&id=' . $this->id ) ); ?>">
        <img src="<?php echo get_icon_url( 'export' ); ?>" alt="export" />
        <?php echo get_lang( 'Export to pdf' ); ?>
    </a>
</span>