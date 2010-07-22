<?php // $Id$
/**
 * Claroline Poll Tool
 *
 * @version     UCREPORT 0.8.0 $Revision$ - Claroline 1.9
 * @copyright   2001-2009 Universite Catholique de Louvain (UCL)
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
    <?php if( ! $this->reportId ) : ?>
<span>
    <a class="claroCmd" href="<?php echo htmlspecialchars( Url::Contextualize( $_SERVER['PHP_SELF'].'?cmd=rqCreateReport') ); ?>">
        <img src="<?php echo get_icon_url( 'export_list' ); ?>" alt="generate" />
        <?php echo get_lang( 'Generate the report' ); ?>
    </a>
</span>
<span>
    <a class="claroCmd" href="<?php echo htmlspecialchars( Url::Contextualize( $_SERVER['PHP_SELF'].'?cmd=rqEditReport') ); ?>">
        <img src="<?php echo get_icon_url( 'settings' ); ?>" alt="edit" />
        <?php echo get_lang( 'Report settings' ); ?>
    </a>
</span>
    <?php endif; ?>
<span>
    <a class="claroCmd" href="<?php echo htmlspecialchars( Url::Contextualize( $_SERVER['PHP_SELF'].'?cmd=exExport2xml') ); ?>">
        <img src="<?php echo get_icon_url( 'export' ); ?>" alt="export" />
        <?php echo get_lang( 'Export to MS-Excel xlsx file' ); ?>
    </a>
</span>
<span>
    <a class="claroCmd" href="<?php echo htmlspecialchars( Url::Contextualize( $_SERVER['PHP_SELF'].'?cmd=exExport2csv') ); ?>">
        <img src="<?php echo get_icon_url( 'export' ); ?>" alt="export" />
        <?php echo get_lang( 'Export to csv' ); ?>
    </a>
</span>
<?php endif; ?>
<span>
    <a class="claroCmd" href="<?php echo htmlspecialchars( Url::Contextualize( $_SERVER['PHP_SELF'].'?cmd=exExport2pdf') ); ?>">
        <img src="<?php echo get_icon_url( 'export' ); ?>" alt="export" />
        <?php echo get_lang( 'Export to pdf' ); ?>
    </a>
</span>