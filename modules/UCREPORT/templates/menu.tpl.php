<?php // $Id$
/**
 * Student Report for Claroline
 *
 * @version     UCREPORT 0.9.3 $Revision$ - Claroline 1.9
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
<?php if ( $this->assignmentDataList[ Report::EXAMINATION_ID ][ 'active' ] ) : ?>
<span>
    <a class="claroCmd" href="<?php echo htmlspecialchars( Url::Contextualize( $_SERVER['PHP_SELF'].'?cmd=rqEditScores') ); ?>">
        <img src="<?php echo get_icon_url( 'edit' ); ?>" alt="edit" />
        <?php echo get_lang( 'Edit examination scores' ); ?>
    </a>
</span>
<?php endif; ?>
    <?php elseif ( isset( $this->assignmentDataList[ Report::EXAMINATION_ID ] ) ) : ?>
<span>
    <a class="claroCmd" href="<?php echo htmlspecialchars( Url::Contextualize( $_SERVER['PHP_SELF'].'?cmd=rqShowScores&reportId=' . $this->reportId ) ); ?>">
        <img src="<?php echo get_icon_url( 'mime/text-x-generic' ); ?>" alt="comment" />
        <?php echo get_lang( 'See examination scores' ); ?>
    </a>
</span>
    <?php endif; ?>
<span>
    <a class="claroCmd" href="<?php echo htmlspecialchars( Url::Contextualize( $_SERVER['PHP_SELF'].'?cmd=exExport2xml&reportId=' . $this->reportId ) ); ?>">
        <img src="<?php echo get_icon_url( 'export' ); ?>" alt="export" />
        <?php echo get_lang( 'Export to MS-Excel xlsx file' ); ?>
    </a>
</span>
<span>
    <a class="claroCmd" href="<?php echo htmlspecialchars( Url::Contextualize( $_SERVER['PHP_SELF'].'?cmd=exExport2csv&reportId=' . $this->reportId ) ); ?>">
        <img src="<?php echo get_icon_url( 'export' ); ?>" alt="export" />
        <?php echo get_lang( 'Export to csv' ); ?>
    </a>
</span>
<?php endif; ?>
<span>
    <a class="claroCmd" href="<?php echo htmlspecialchars( Url::Contextualize( $_SERVER['PHP_SELF'].'?cmd=exExport2pdf&reportId=' . $this->reportId ) ); ?>">
        <img src="<?php echo get_icon_url( 'export' ); ?>" alt="export" />
        <?php echo get_lang( 'Export to pdf' ); ?>
    </a>
</span>