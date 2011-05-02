<!-- $Id$ -->
<form 
    action="<?php echo htmlspecialchars( $this->url ) ?>" 
    action="post">
    
    <?php echo claro_form_relay_context(); ?>
    
    <label for="groupChooser"><?php echo get_lang('Choose a group'); ?></label> :
    <select id="groupChooser" name="gidReq">
        
        <?php foreach ( $this->userGroupList as $group ): ?>
        
        <option value="<?php echo $group['id']; ?>">
            <?php echo htmlspecialchars($group['name'] ); ?>
        </option>
        
        <?php endforeach; ?>
        
    </select>
    
    <input type="submit" name="submit" value="<?php echo get_lang('Ok'); ?>" />
    
</form>
