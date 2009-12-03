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
                <option value="0"><?php echo get_lang( 'Select report date...' ); ?>
                <?php foreach( $this->reports as $report ) : ?>
                <option value="<?php echo $report['date']; ?>"><?php echo claro_date( 'Y/m/d - H:i', $report['date'] ); ?></option>
                <?php endforeach; ?>
            </select>        
        </form>        
        <div>            
            <h4><?php echo get_lang( 'Report generated on %date', array( '%date' => claro_date( 'Y/m/d - H:i', $this->reportDate) ) ); ?></h4>
            
            <?php echo $this->subMenu; ?>
<?php
    if( !is_null( $this->report ) ) :
?>    
        <?php
        if( $this->display == 'details' ) :
        ?>        
            <table class="claroTable emphaseLine" width="100%" border="0" cellspacing="2">
                <thead>
                    <tr class="headerX" align="center" valign="top">
                        <th><?php echo get_lang( 'Tool' ); ?></th>
                        <th><?php echo get_lang( 'Item' ); ?></th>
                        <th><?php echo get_lang( 'Max Usage' ); ?></th>
                        <th><?php echo get_lang( 'Average Usage' ); ?></th>
                        <th><?php echo get_lang( 'Zero Item' ); ?></th>
                        <th><?php echo get_lang( '1 Item' ); ?></th>
                        <th><?php echo get_lang( '2 Items' ); ?></th>
                        <th><?php echo get_lang( '3 Items' ); ?></th>
                        <th><?php echo get_lang( '4 Items' ); ?></th>
                        <th><?php echo get_lang( '5 Items' ); ?></th>
                        <th><?php echo get_lang( 'More than 5 Items' ); ?></th>
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
                        <td style="text-align: center;"><?php echo $thisReport['zero']; ?></td>
                        <td style="text-align: center;"><?php echo $thisReport['one']; ?></td>
                        <td style="text-align: center;"><?php echo $thisReport['two']; ?></td>
                        <td style="text-align: center;"><?php echo $thisReport['three']; ?></td>
                        <td style="text-align: center;"><?php echo $thisReport['four']; ?></td>
                        <td style="text-align: center;"><?php echo $thisReport['five']; ?></td>
                        <td style="text-align: center;"><?php echo $thisReport['moreFive']; ?></td>                    
                    </tr>
                <?php
                endforeach;
                ?>
                </tbody>
            </table>
        <?php
        else :
        ?>
            <table class="claroTable emphaseLine" width="100%" border="0" cellspacing="2">
                <thead>
                    <tr class="headerX" align="center" valign="top">
                        <th><?php echo get_lang( 'Tool' ); ?></th>
                        <th><?php echo get_lang( 'Max Usage' ); ?></th>
                        <th><?php echo get_lang( 'Average Usage' ); ?></th>
                        <th><?php echo get_lang( 'Less than 5 Items' ); ?></th>
                        <th><?php echo get_lang( '5 Items or more' ); ?></th>
                    </tr>
                </thead>
                <tbody>
                <?php
                foreach( $this->report as $toolLabel => $items ) :
                    foreach( $items as $thisReport ) :                    
                ?>
                    <tr>
                        <td style="text-align: left;"><?php echo claro_get_tool_name( $toolLabel ); ?></td>
                        <td style="text-align: center;"><?php echo $thisReport['max']; ?></td>
                        <td style="text-align: center;"><?php echo $thisReport['average']; ?></td>
                        <td style="text-align: center;"><?php echo $thisReport['lessFive']; ?></td>
                        <td style="text-align: center;"><?php echo $thisReport['moreFive']; ?></td>                    
                    </tr>
                <?php
                    endforeach;
                endforeach;
                ?>
                </tbody>
            </table>
        <?php
        endif;
        ?>
            <table class="claroTable emphaseLine" width="35%" border="0" cellspacing="2">
                <thead>
                    <tr class="headerX" align="center" valign="top">
                        <th><?php echo get_lang( 'Variable' ); ?></th>
                        <th><?php echo get_lang( 'Value' ); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    foreach( $this->usageReport as $usage )
                    {
                        switch( $usage['label'] )
                        {
                            case'moreFive' :
                                {
                    ?>
                    <tr>
                        <td><?php echo get_lang( 'Course with high usage (4 or more tools > 5)' ); ?></td>
                        <td><?php echo $usage['value']; ?></td>
                    </tr>                    
                    <?php
                                }
                                break;
                            case 'lessFive' :
                                {
                    ?>
                    <tr>
                        <td><?php echo get_lang( 'Course with low usage (all tools < 5)' ); ?></td>
                        <td><?php echo $usage['value']; ?></td>
                    </tr>                    
                    <?php
                                }
                                break;
                    ?>
                    <?php
                        }
                    }
                    ?>
                </tbody>
            </table>
        </div>
        <form name="compareReport" action="index.php?cmd=view" method="post">
            <input type="submit" name="submitButton" value="<?php echo get_lang('Compare with'); ?>" />&nbsp;&nbsp;
            <select name="compareReport">
                <option value="0"><?php echo get_lang( 'Select another report date...' ); ?>
                <?php
                foreach( $this->reports as $report ) :
                    if( $report['date'] != $this->reportDate ) :
                ?>
                <option value="<?php echo $report['date']; ?>"><?php echo claro_date( 'Y/m/d - H:i', $report['date'] ); ?></option>
                <?php
                    endif;
                endforeach;
                ?>
            </select>        
        </form>
<?php
    endif;
?>
</div>
<?php
endif;
?>