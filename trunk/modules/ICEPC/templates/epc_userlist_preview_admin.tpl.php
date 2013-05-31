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
        
        <?php 
            if ( ! $lineAdded ) : 
                $lineAdded = true; 
            endif;
            
            if (isset( $this->classUserList[$user->username] ) ) : 
                unset ($this->classUserList[$user->username]); 
            endif;
        ?>
        
        <tr>
            <td><?php echo $user->firstname; ?></td>
            <td><?php echo $user->lastname; ?></td>
            <td><?php echo $user->email; ?></td>
            <td><?php echo $user->noma; ?></td>
            <td><?php echo $user->officialCode; ?></td>
            <td><?php echo $user->sigleAnet; ?></td>
            <td><?php echo isset( $this->platformToUpdate[ $user->username ] ) ? get_lang('Update') : get_lang('New'); ?></td>
        </tr>
        
    <?php endforeach; ?>
        
    <?php endif; ?>
        
    <?php if ( ! $lineAdded ): ?>
        <tr>
            <td colspan="5">
                <?php echo get_lang("No user to add"); ?>
            </td>
        </tr>
    <?php endif; ?>
        
    <?php foreach ( $this->classUserList as $user ): ?>
        
        <tr>
            <td><?php echo $user->firstname; ?></td>
            <td><?php echo $user->lastname; ?></td>
            <td><?php echo $user->email; ?></td>
            <td><?php echo $user->noma; ?></td>
            <td><?php echo $user->officialCode; ?></td>
            <td> - </td>
            <td><?php echo get_lang('Deleted'); ?></td>
        </tr>
        
    <?php endforeach;?>
        
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
    <input type="submit" name="epcSubmitSearch" value="<?php echo get_lang ( 'Import/Update' ); ?>" />
    <?php endif; ?>
    <a href="<?php echo Url::Contextualize(get_module_url('ICEPC') . '/admin.php' ); ?>">
        <input type="button" name="epcCancelSearch" id="epcCancetSearch" value="<?php echo get_lang ( 'Cancel' ); ?>" />
    </a>
</form>
