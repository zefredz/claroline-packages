<?php
if( $this->reports->numRows() == 0 ) :
?>
1
<div class="claroDialogBox boxInfo"><?php echo get_lang( 'No reports still generated.'); ?></div>
<?php
else :
?>
<div style="padding-top: 10px;">
    <form name="displayReport" action="index.php?cmd=view" method="post">
        <input type="submit" name="submitButton" value="<?php echo get_lang('Show report'); ?>" />&nbsp;&nbsp;
        <select name="report">
            <?php foreach( $this->reports as $report ) : ?>
            <option value="<?php echo $report['date']; ?>"><?php echo claro_date( 'Y/m/d - H:i', $report['date'] ); ?></option>
            <?php endforeach; ?>
        </select>        
    </form>
    <?php
    if( !is_null( $this->report ) ) :
    ?>
    <div>
        <h4><?php echo get_lang( 'Report generated on %date', array( '%date' => claro_date( 'Y/m/d - H:i', $this->reportDate) ) ); ?></h4>
        <table class="claroTable emphaseLine" width="100%" border="0" cellspacing="2">
            <thead>
                <tr class="headerX" align="center" valign="top">
                    <td><?php echo get_lang( 'Tool' ); ?></td>
                    <td><?php echo get_lang( 'Item' ); ?></td>
                    <td><?php echo get_lang( 'Max Usage' ); ?></td>
                    <td><?php echo get_lang( 'Average Usage' ); ?></td>
                    <td><?php echo get_lang( 'Less than 5 Items' ); ?></td>
                    <td><?php echo get_lang( 'More than 5 items' ); ?></td>
                </tr>
            </thead>
            <tbody>
            <?php
            foreach( $this->report as $thisReport ) :
            ?>
                <tr>
                    <td style="text-align: left;"><?php echo claro_get_tool_name( $thisReport['toolLabel'] ); ?></td>
                    <td style="text-align: left;"><?php echo $thisReport['itemName']; ?></td>
                    <td style="text-align: center;"><?php echo $thisReport['max']; ?></td>
                    <td style="text-align: center;"><?php echo $thisReport['average']; ?></td>
                    <td style="text-align: center;"><?php echo $thisReport['lessFive']; ?></td>
                    <td style="text-align: center;"><?php echo $thisReport['moreFive']; ?></td>                    
                </tr>
            <?php
            endforeach;
            ?>
            </tbody>
        </table>
    </div>
    <?php
    endif;
    ?>
</div>
<?php
endif;
?>