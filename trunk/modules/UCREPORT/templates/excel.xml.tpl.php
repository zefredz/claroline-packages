<?php // $Id$
/**
 * Claroline Poll Tool
 *
 * @version     UCREPORT 0.8.0 $Revision$ - Claroline 1.9
 * @copyright   2001-2009 Universite Catholique de Louvain (UCL)
 * @license     http://www.gnu.org/copyleft/gpl.html (GPL) GENERAL PUBLIC LICENSE
 * @package     UCREPORT
 * @author      Frederic Fervaille <frederic.fervaille@uclouvain.be>
 */ ?>
<?php $nb = count( $this->assignmentDataList );
      $row = 1; ?>
<?php echo '<?xml version="1.0"?>' . "\n" . '<?mso-application progid="Excel.Sheet"?>' . "\n"; ?>
<Workbook xmlns="urn:schemas-microsoft-com:office:spreadsheet"
          xmlns:o="urn:schemas-microsoft-com:office:office"
          xmlns:x="urn:schemas-microsoft-com:office:excel"
          xmlns:ss="urn:schemas-microsoft-com:office:spreadsheet"
          xmlns:html="http://www.w3.org/TR/REC-html40">
    <DocumentProperties xmlns="urn:schemas-microsoft-com:office:office">
        <Title><?php echo get_lang( 'Report' ) . ' : ' . $this->courseData[ 'name' ]; ?></Title>
        <Author><?php echo $this->courseData[ 'titular' ]; ?></Author>
        <LastAuthor><?php echo $this->userData[ 'firstName' ] . ' ' . $this->userData[ 'lastName' ]; ?></LastAuthor>
        <Created><?php echo date( 'c' ); ?></Created>
        <LastSaved><?php echo date( 'c' ); ?></LastSaved>
        <Company><?php echo get_conf( 'institution_name' ); ?></Company>
        <Version>1.0</Version>
    </DocumentProperties>
    <ExcelWorkbook xmlns="urn:schemas-microsoft-com:office:excel">
        <WindowHeight>9000</WindowHeight>
        <WindowWidth>14000</WindowWidth>
        <WindowTopX>240</WindowTopX>
        <WindowTopY>80</WindowTopY>
        <ProtectStructure>False</ProtectStructure>
        <ProtectWindows>False</ProtectWindows>
    </ExcelWorkbook>
    <Styles>
        <Style ss:ID="Default" ss:Name="Normal">
            <Alignment ss:Vertical="Bottom"/>
            <Borders/>
            <Font ss:FontName="Verdana"/>
            <Interior/>
            <NumberFormat/>
            <Protection/>
        </Style>
        <Style ss:ID="s21" ss:Name="Default">
        </Style>
        <Style ss:ID="s27" ss:Parent="s21">
            <Font ss:FontName="Verdana"/>
        </Style>
        <Style ss:ID="s28" ss:Parent="s21">
            <Font ss:FontName="Verdana" ss:Bold="1"/>
            <Interior ss:Color="#FFCC99" ss:Pattern="Solid"/>
        </Style>
        <Style ss:ID="s29" ss:Parent="s21">
            <Interior ss:Color="#FFEEDD" ss:Pattern="Solid"/>
        </Style>
    </Styles>
    <Worksheet ss:Name="Sheet1">
        <Table ss:ExpandedColumnCount="<?php echo $nb + 2; ?>" 
               ss:ExpandedRowCount="20" x:FullColumns="1"
               x:FullRows="1" ss:StyleID="s21">
            <Column ss:StyleID="s21" ss:Width="140.0"/>
    <?php foreach( $this->assignmentDataList as $assignment ) : ?>
        <?php if ( $assignment[ 'active' ] ) : ?>
            <Column ss:StyleID="s21" ss:Width="<?php echo strlen( $assignment[ 'title' ] ) * 8; ?>"/>
        <?php endif; ?>
    <?php endforeach; ?>
            <Column ss:StyleID="s21" ss:Width="160.0"/>
            <Row ss:AutoFitHeight="0" ss:Height="12.0">
                <Cell ss:StyleID="s28">
                    <Data ss:Type="String"><?php echo get_lang( 'Name' ); ?></Data>
                </Cell>
    <?php foreach( $this->assignmentDataList as $assignment ) : ?>
        <?php if ( $assignment[ 'active' ] ) : ?>
                <Cell ss:StyleID="s28">
                    <Data ss:Type="String"><?php echo $assignment[ 'title' ]; ?></Data>
                </Cell>
        <?php endif; ?>
    <?php endforeach; ?>
                <Cell ss:StyleID="s28">
                    <Data ss:Type="String"><?php echo get_lang( 'Weighted global score' ); ?></Data>
                </Cell>
            </Row>
            <Row ss:AutoFitHeight="0" ss:Height="12.0">
                <Cell ss:StyleID="s29">
                    <Data ss:Type="String"><?php echo get_lang( 'Weight' ); ?></Data>
                </Cell>
    <?php foreach( $this->assignmentDataList as $assignment ) : ?>
        <?php if ( $assignment[ 'active' ] ) : ?>
                <Cell ss:StyleID="s29">
                    <Data ss:Type="Number"><?php echo $assignment[ 'proportional_weight' ]; ?></Data>
                </Cell>
        <?php endif; ?>
    <?php endforeach; ?>
                <Cell ss:Formula="=SUM(RC[-<?php echo $nb + 1; ?>]:RC[-1])"
                      ss:StyleID="s29">
                    <Data ss:Type="Number">1.0</Data>
                </Cell>
            </Row>
            <Row ss:AutoFitHeight="0" ss:Height="12.0">
                <Cell ss:StyleID="s29">
                    <Data ss:Type="String"><?php echo get_lang( 'Average' ); ?></Data>
                </Cell>
    <?php foreach( $this->assignmentDataList as $assignment ) : ?>
        <?php if ( $assignment[ 'active' ] ) : ?>
            <?php if ( isset( $assignment[ 'average' ] ) ) : ?>
                <Cell ss:Formula="=AVERAGE(R[1]C:R[<?php echo count( $this->userList ); ?>]C)"
                      ss:StyleID="s29">
                    <Data ss:Type="Number"><?php echo $assignment[ 'average' ]; ?></Data>
                </Cell>
            <?php else : ?>
                <Cell ss:StyleID="s29">
                    <Data ss:Type="String" />
                </Cell>
            <?php endif; ?>
        <?php endif; ?>
    <?php endforeach; ?>
                <Cell ss:Formula="=SUMPRODUCT(RC[-<?php echo $nb + 1; ?>]:RC[-1],R[-1]C[-<?php echo $nb + 1; ?>]:R[-1]C[-1])"
                      ss:StyleID="s29">
                    <Data ss:Type="Number"><?php echo $this->averageScore; ?></Data>
                </Cell>
            </Row>
    <?php foreach( $this->reportDataList as $userId => $userReport ) : ?>
        <?php $row++; ?>
            <Row ss:AutoFitHeight="0" ss:Height="12.0">
                <Cell>
                    <Data ss:Type="String"><?php echo $this->userList[ $userId ][ 'lastname' ] . ' ' . $this->userList[ $userId ][ 'firstname' ]; ?></Data>
                </Cell>
        <?php foreach( $this->assignmentDataList as $id => $assignment ) : ?>
            <?php if ( $assignment[ 'active' ] ) : ?>
                <?php if ( isset( $userReport[ $id ] ) ) : ?>
                <Cell>
                    <Data ss:Type="Number"><?php echo $userReport[ $id ]; ?></Data>
                </Cell>
                <?php else : ?>
                <Cell>
                    <Data ss:Type="String" />
                </Cell>
                <?php endif; ?>
            <?php endif; ?>
        <?php endforeach; ?>
                <Cell ss:Formula="=SUMPRODUCT(RC[-<?php echo $nb + 1; ?>]:RC[-1],R[-<?php echo $row; ?>]C[-<?php echo $nb + 1; ?>]:R[-<?php echo $row; ?>]C[-1])"
                      ss:StyleID="s29">
                    <Data ss:Type="Number"><?php echo $this->userList[ $userId ][ 'final_score' ]; ?></Data>
                </Cell>
            </Row>
    <?php endforeach; ?>
        </Table>
        <WorksheetOptions xmlns="urn:schemas-microsoft-com:office:excel">
            <PageLayoutZoom>0</PageLayoutZoom>
            <Selected/>
            <Panes>
                <Pane>
                    <Number>3</Number>
                    <RangeSelection>R1</RangeSelection>
                </Pane>
            </Panes>
            <ProtectObjects>False</ProtectObjects>
            <ProtectScenarios>False</ProtectScenarios>
        </WorksheetOptions>
    </Worksheet>
</Workbook>
