<h4><?php echo get_lang("Main upgrade"); ?></h4>

<?php if( ! $this->mainupgradeDone ): ?>
    <ul>
        <li><a href="?cmd=executeMainUpgrade" class="confirmBefore"><?php echo get_lang("Execute main upgrade"); ?></a></li>
    </ul>
<?php else: ?>
    <ul>
        <li><span class="disabled"><?php echo get_lang("Main upgrade executed"); ?></span></li>
    </ul>
<?php endif; ?>

<p>
    <a class="claroCmd externalLink" href="./showlog.php?cmd=showMainUpgradeLog"><?php echo get_lang("Show log"); ?></a>
</p>

<h4><?php echo get_lang("Course upgrade"); ?></h4>

<?php if( ! $this->mainupgradeDone ): ?>
    <p>
        <?php echo get_lang("You need to execute main upgrade before course upgrade"); ?>
    </p>
<?php else: ?>
    <p>
        <label for="autoUpgrade"><?php echo get_lang("Upgrade course automaticaly when accessed"); ?></label>
        <input id="autoUpgrade" type="checkbox" name="autoUpgrade" value="true"<?php echo $this->autoUpgrade ? ' checked="checked"' : '';?> />
    </p>
    
    <ul class="upgradedCourseList">
        <li class="success">
            <?php echo get_lang("Done"); ?> : <?php echo $this->successCount; ?> (<a class="claroCmd" href="?cmd=showSuccess"><?php echo get_lang('show'); ?></a>)
        </li>
        <li class="warning">
            <?php echo get_lang("Partialy done"); ?> : <?php echo $this->partialCount; ?> (<a class="claroCmd" href="?cmd=showPartial"><?php echo get_lang('show'); ?></a>)
        </li>
        <li class="error">
            <?php echo get_lang("Failed"); ?> : <?php echo $this->failureCount; ?> (<a class="claroCmd" href="?cmd=showFailure"><?php echo get_lang('show'); ?></a>)
        </li>
        <li>
            <?php echo get_lang("To upgrade"); ?> : <?php echo $this->pendingCount; ?> (<a class="claroCmd" href="?cmd=showPending"><?php echo get_lang('show'); ?></a>)
        </li>
        <li>
            <?php echo get_lang("In progress"); ?> : <?php echo $this->startedCount; ?> (<a class="claroCmd" href="?cmd=showStarted"><?php echo get_lang('show'); ?></a>)
        </li>
    </ul>
    
    <p>
        <?php echo get_lang("Total number of courses"); ?> : <?php echo $this->totalNumberOfCourses; ?>
    </p>
    
    <p>
        <a class="claroCmd externalLink" href="./showlog.php?cmd=showCourseUpgradeLog"><?php echo get_lang("Show log"); ?></a>
        |
        <a class="claroCmd confirmBefore" href="?cmd=upgradeCourseBatch"><?php echo get_lang("Upgrade all remaining courses"); ?></a>
        
        <?php if ( claro_debug_mode() ): ?>
        |
        <a class="claroCmd confirmBefore" href="?cmd=resetUpgradeDatabase"><?php echo get_lang("Reset course upgrade database"); ?></a>
        <?php endif; ?>
        
    </p>
<?php endif; ?>

<script type="text/javascript">
/*<![CDATA[*/
    $(function(){
        $("a.confirmBefore").click(function(){
            var msg = $(this).text();
            
            if ( confirm( msg+"\n"+"<?php echo str_replace('"', '\\"', get_lang("Are you sure ?") ); ?>") ) {
                $(this).attr('href', $(this).attr('href')+'&doit=true');
                return true;
            }
            else {
                return false;
            }
        });
        
        $("a.externalLink").click(function(){
            $(this).attr('target','_blank');
        });
        
        $("#autoUpgrade").click(function(){
            if ( $(this).attr('checked') ) {
                $.post(
                    "<?php echo php_self(); ?>",
                    {cmd:'setAutoUpgrade', auto:'true'}
                );
            }
            else {
                $.post(
                    "<?php echo php_self(); ?>",
                    {cmd:'setAutoUpgrade', auto:'false'}
                );
            }
        });
    });
/*]]>*/
</script>