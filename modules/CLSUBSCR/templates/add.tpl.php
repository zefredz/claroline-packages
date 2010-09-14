<?php // $Id$

/**
 * Subscription
 *
 * @version     CLSUBSCR 0.2 $Revision$ - Claroline 1.9
 * @copyright   2001-2010 Universite catholique de Louvain (UCL)
 * @license     http://www.gnu.org/copyleft/gpl.html (GPL) GENERAL PUBLIC LICENSE
 * @package     CLSUBSCR
 * @author      Dimitri Rambout <dim@claroline.net>
 */

if ( count( get_included_files() ) == 1  || !claro_is_allowed_to_edit() )
{
   // die( 'The file ' . basename(__FILE__) . ' cannot be accessed directly, use include instead' );
}

?>
<form name="createSubscription" method="post" action="<?php echo htmlspecialchars( php_self() . claro_url_relay_context( '?' ) ); ?>" >
   <?php if( isset( $this->id ) ) : ?>
   <input type="hidden" name="subscrId" value="<?php echo (int) $this->id; ?>" />
   <input type="hidden" name="cmd" value="exEdit" />
   <?php else : ?>
   <input type="hidden" name="cmd" value="exAdd" />
   <?php endif; ?>
   <input type="hidden" name="type" id="typeUnique" value="unique" />
   <fieldset>
       <legend><?php echo get_lang( 'Properties' ); ?></legend>
       <dl>
           <dt><label for="title"><?php echo get_lang( 'Title' ); ?> :</label></dt>
           <dd><input type="text" id="title" name="title" value="<?php echo isset( $this->title ) ? $this->title : ''; ?>" /></dd>
           <dt><label for="description"><?php echo get_lang( 'Description' ); ?> :</label></dt>
           <dd><?php echo claro_html_advanced_textarea( 'description', ( isset( $this->description ) ? $this->description : '' ) );  ?></dd>
           <dt><label><?php echo get_lang( 'Subscription\'s type' ); ?> :</label></dt>
           <dd>
               <input type="radio" name="context" id="contextUser" value="user" <?php echo ! isset( $this->context ) || $this->context == 'user' ? 'checked="checked"' : '';  ?> /><label for="contextUser"><?php echo get_lang( 'Unique user' ); ?></label><br />
               <input type="radio" name="context" id="contextGroup" value="group" <?php echo isset( $this->context ) && $this->context == 'group' ? 'checked="checked"' : '';  ?> /><label for="contextGroup"><?php echo get_lang( 'Group' ); ?></label>
           </dd>
           <!--dt><label for="type"><?php echo get_lang( 'Subscriptions by user/group' ); ?> :</label></dt>
           <dd-->
               <!--input type="radio" name="type" id="typeUnique" value="unique" <?php echo isset( $this->type ) && $this->type == 'unique' ? 'checked="checked"' : ''; ?> /><label for="typeUnique"><?php echo get_lang( 'One' ); ?></label><br /-->
               <!--input type="radio" name="type" id="typeMultiple" value="multiple" <?php echo isset( $this->type ) && $this->type == 'multiple' ? 'checked="checked"' : ''; ?> /><label for="typeMultiple"><?php echo get_lang( 'Multiple choices' ); ?></label><br /-->
               <!--input type="radio" name="type" id="typePreference" value="preference" <?php echo isset( $this->type ) && $this->type == 'preference' ? 'checked="checked"' : ''; ?> /><label for="typePreference"><?php echo get_lang( 'By preference' ); ?></label-->
           <!--/dd-->
           <dt><label for="modifiable"><?php echo get_lang( 'Users can modify their choices' ); ?> :</label></dt>
           <dd>
               <input type="radio" name="modifiable" id="modifiable" value="modifiable" <?php echo ! isset( $this->isModifiable ) || $this->isModifiable ? 'checked="checked"' : '';  ?> /><label for="contextUser"><?php echo get_lang( 'Yes' ); ?></label><br />
               <input type="radio" name="modifiable" id="not_modifiable" value="not_modifiable" <?php echo isset( $this->isModifiable ) && ! $this->isModifiable ? 'checked="checked"' : '';  ?> /><label for="contextGroup"><?php echo get_lang( 'No' ); ?></label>
           </dd>
       </dl>
   </fieldset>
   <fieldset id="advanced" class="collapsible collapsed">
       <legend><a href="#" class="doCollapse"><?php echo get_lang('Advanced settings'); ?></a></legend>
       <div class="collapsible-wrapper">
           <dl>
               <dt><?php echo get_lang( 'Visibility' ); ?></dt>
               <dd>
                  <input type="radio" name="visibility" id="invisible" value="invisible" <?php echo isset( $this->visibility ) && $this->visibility == 'invisible' ? 'checked="checked"' : ''; ?> /><label for="invisible"><?php echo get_lang( 'Invisible (users cannot subscribe)' ); ?></label><br />
                  <input type="radio" name="visibility" id="visible" value="visible" <?php echo isset( $this->visibility ) ? ( $this->visibility == 'visible' ? 'checked="checked"' : '' ) : 'checked="checked"'; ?> /><label for="visible"><?php echo get_lang( 'Visible' ); ?></label><br />
                  <div style="margin-left: 5px;">
                     <input type="checkbox" name="visibilityFrom" id="visibilityFrom" value="1" <?php echo isset( $this->visibilityFrom ) && $this->visibilityFrom ? 'checked="checked"' : ''; ?> /><label for="visibilityFrom"><?php echo get_lang( 'Starting date'); ?></label>
                     <?php echo claro_html_date_form( 'visibilityFromDay', 'visibilityFromMonth', 'visibilityFromYear', ( isset( $this->visibilityFrom ) ? $this->visibilityFrom : time() ) ); ?>
                     <?php echo claro_html_time_form( 'visibilityFromHour', 'visibilityFromMinute',  ( isset( $this->visibilityFrom ) ? $this->visibilityFrom : time() ) ); ?><br />
                     <input type="checkbox" name="visibilityTo" id="visibilityTo" value="1" <?php echo isset( $this->visibilityTo ) && $this->visibilityTo ? 'checked="checked"' : ''; ?> /><label for="visibilityTo"><?php echo get_lang( 'Stopping date'); ?></label>
                     <?php echo claro_html_date_form( 'visibilityToDay', 'visibilityToMonth', 'visibilityToYear', ( isset( $this->visibilityTo ) ? $this->visibilityTo : time() + 86400 ) ); ?>
                     <?php echo claro_html_time_form( 'visibilityToHour', 'visibilityToMinute',  ( isset( $this->visibilityTo ) ? $this->visibilityTo : time() ) ); ?><br />
                  </div>                     
               </dd>            
           </dl>
       </div>
   </fieldset>
   <input type="submit" name="submitButton" value="<?php echo get_lang('Save'); ?>" />
   <?php echo claro_html_button($_SERVER['PHP_SELF'], get_lang('Cancel')); ?>
</form>
