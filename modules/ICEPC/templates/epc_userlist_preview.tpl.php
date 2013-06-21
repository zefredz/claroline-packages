<?php echo $this->responseInfo ; ?>

<p><?php echo get_lang('You are going to import the following students in your course'); ?></p>

<?php $lineAdded = false; ?>
<table class="claroTable">
    <thead>
        <tr>
            <th><?php echo get_lang( 'First name' ); ?></th>
            <th><?php echo get_lang( 'Last name' ); ?></th>
            <th><?php echo get_lang( 'email' ); ?></th>
            <th><?php echo get_lang( 'NOMA'); ?></th>
            <th><?php echo get_lang( 'FGS'); ?></th>
            <th><?php echo get_lang( 'Year'); ?></th>
            <th><?php echo get_lang( 'Import type'); ?>
        </tr>
    </thead>
    <tbody>
    <?php if (count( $this->userListIterator ) ): ?>
    <?php foreach ( $this->userListIterator as $user ): ?>
        <?php if ( !isset( $this->courseUserList[$user->username] ) 
            || isset( $this->courseUserToUpdateList[$user->username ] ) ): ?>
        <?php $lineAdded = true; ?>
        <tr>
            <td><?php echo $user->firstname; ?></td>
            <td><?php echo $user->lastname; ?></td>
            <td><?php echo $user->email; ?></td>
            <td><?php echo $user->noma; ?></td>
            <td><?php echo $user->officialCode; ?></td>
            <td><?php echo $user->sigleAnet; ?></td>
            <td><?php echo isset( $this->courseUserToUpdateList[ $user->username ] ) ? get_lang('Update') : get_lang('New'); ?></td>
        </tr>
        <?php endif; ?>
    <?php endforeach; ?>
    <?php endif; ?>
    <?php if ( ! $lineAdded ): ?>
        <tr>
            <td colspan="5">
                <?php if (count($this->userListIterator)): ?>
                <?php echo get_lang("All students are already enrolled to your course"); ?><br />
                <?php echo get_lang("You can still update cached data (NOMA, year of study...) about the users in your course"); ?>
                <?php else: ?>
                <strong>
                <?php echo get_lang("No user found, please check the program or course number you have entered"); ?>
                </strong>
                <?php endif; ?>
            </td>
        </tr>
    <?php endif; ?>
    </tbody>
</table>

<form class="msform" action="<?php echo $this->actionUrl; ?>" method="post" id="epcQueryForm">
    <?php echo claro_form_relay_context (); ?>
    <input type="hidden" id="csrf_token" name="csrf_token" value="<?php echo $_SESSION[ 'csrf_token' ]; ?>" />
    <input type="hidden" id="cmd" name="cmd" value="exImport" />
    <input type="hidden" id="epcSearchString" name="epcSearchString" value="<?php echo $this->epcSearchString; ?>" />
    <input type="hidden" id="epcAcadYear" name="epcAcadYear" value="<?php echo $this->epcAcadYear; ?>" />
    <input type="hidden" id="epcSearchFor" name="epcSearchFor" value="<?php echo $this->epcSearchFor; ?>" />
    <input type="hidden" id="epcLinkExistingStudentsToClass" name="epcLinkExistingStudentsToClass" value="<?php echo $this->epcLinkExistingStudentsToClass; ?>" />
    <input type="hidden" id="epcValidatePendingUsers" name="epcValidatePendingUsers" value="<?php echo $this->epcValidatePendingUsers; ?>" />
    <?php if ( $lineAdded ): ?>
    <input type="submit" name="epcSubmitSearch" value="<?php echo get_lang ( 'Import' ); ?>" />
    <a href="<?php echo Url::Contextualize(get_module_url('ICEPC')); ?>">
        <input type="button" name="epcCancelSearch" id="epcCancetSearch" value="<?php echo get_lang ( 'Cancel' ); ?>" />
    </a>
    <?php elseif ( count( $this->userListIterator) ): ?>
    <input type="submit" name="epcSubmitSearch" value="<?php echo get_lang ( 'Update cached data' ); ?>" />
    <a href="<?php echo Url::Contextualize(get_module_url('ICEPC')); ?>">
        <input type="button" name="epcCancelSearch" id="epcCancetSearch" value="<?php echo get_lang ( 'Cancel' ); ?>" />
    </a>
    <?php else: ?>
    <a href="<?php echo Url::Contextualize(get_module_url('ICEPC')); ?>">
        <input type="button" name="epcCancelSearch" id="epcCancetSearch" value="<?php echo get_lang ( 'Back' ); ?>" />
    </a>
    <?php endif; ?>
</form>
