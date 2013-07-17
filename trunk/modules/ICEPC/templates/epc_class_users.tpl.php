<table class="claroTable">
    <thead>
        <tr>
            <th><?php echo get_lang( 'id' ); ?>
            <th><?php echo get_lang( 'First name' ); ?></th>
            <th><?php echo get_lang( 'Last name' ); ?></th>
            <th><?php echo get_lang( 'email' ); ?></th>
            <th><?php echo get_lang( 'NOMA' ); ?></th>
            <th><?php echo get_lang( 'Year' ); ?></th>
            <th><?php echo get_lang( 'In course' ); ?></th>
        </tr>
    </thead>
    <tbody>
    <?php if (count( $this->classUserList ) ): ?>
    <?php foreach ( $this->classUserList as $userId => $classUser ): ?>
        <tr>
            <th><?php echo $userId; ?>
            <td><?php echo $classUser['firstname']; ?></td>
            <td><?php echo $classUser['lastname']; ?></td>
            <td><?php echo $classUser['email']; ?></td>
            <?php if (isset($this->epcUserData[$userId])): ?>
            <td><?php echo $this->epcUserData[$userId]['noma']; ?></td>
            <td><?php echo $this->epcUserData[$userId]['sigle_anet']; ?></td>
            <?php else: ?> 
            <td> - </td>
            <td> - </td>
            <?php endif; ?>
            <td><?php echo isset( $this->courseUserList[$userId] ) ? get_lang('Yes') : get_lang('No'); ?></td>
        </tr>
    <?php endforeach; ?>
    <?php else: ?>
        <tr><td colspan="7"><?php echo get_lang("No student to display"); ?></td></tr>
    <?php endif; ?>
    </tbody>
</table>