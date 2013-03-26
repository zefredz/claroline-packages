<p><?php echo get_lang('You are going to import the following students in your course continue ?'); ?></p>

<form class="msform" action="<?php echo $this->actionUrl; ?>" method="post" id="epcQueryForm">
    <?php echo claro_form_relay_context (); ?>
    <input type="hidden" id="csrf_token" name="csrf_token" value="<?php echo $_SESSION[ 'csrf_token' ]; ?>" />
    <input type="hidden" id="cmd" name="cmd" value="exImport" />
    <input type="hidden" id="epcSearchString" name="epcSearchString" value="<?php echo $this->epcSearchString; ?>" />
    <input type="hidden" id="epcAcadYear" name="epcAcadYear" value="<?php echo $this->epcAcadYear; ?>" />
    <input type="hidden" id="epcSearchFor" name="epcSearchFor" value="<?php echo $this->epcSearchFor; ?>" />
    <input type="hidden" id="epcLinkExistingStudentsToClass" name="epcLinkExistingStudentsToClass" value="<?php echo $this->epcLinkExistingStudentsToClass; ?>" />
    <input type="submit" name="epcSubmitSearch" value="<?php echo get_lang ( 'Yes' ); ?>" />
    <a href="<?php echo Url::Contextualize(get_module_url('ICEPC')); ?>">
        <input type="button" name="epcCancelSearch" id="epcCancetSearch" value="<?php echo get_lang ( 'No' ); ?>" />
    </a>
</form>

<pre>
<?php var_dump( $this->responseInfo ); ?>
</pre>

<table class="claroTable">
    <thead>
        <tr>
            <th><?php echo get_lang( 'First name' ); ?></th>
            <th><?php echo get_lang( 'Last name' ); ?></th>
            <th><?php echo get_lang( 'email' ); ?></th>
            <th><?php echo get_lang( 'NOMA'); ?></th>
            <th><?php echo get_lang( 'FGS'); ?></th>
            <th><?php echo get_lang( 'Year'); ?></th>
        </tr>
    </thead>
    <tbody>
    <?php if (count( $this->userListIterator ) ): ?>
    <?php foreach ( $this->userListIterator as $user ): ?>
        <tr>
            <td><?php echo $user->firstname; ?></td>
            <td><?php echo $user->lastname; ?></td>
            <td><?php echo $user->email; ?></td>
            <td><?php echo $user->noma; ?></td>
            <td><?php echo $user->matriculeFgs; ?></td>
            <td><?php echo $user->sigleAnet; ?></td>
        </tr>
    <?php endforeach; ?>
    <?php else: ?>
        <tr><td colspan="5"><?php echo get_lang("No student to import"); ?></td></tr>
    <?php endif; ?>
    </tbody>
</table>
