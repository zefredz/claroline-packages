<?php
    function checkDBFile(){
        include("settings.inc.php");
        
        if(!file_exists($sDBFileName)){
            return false;
        }else{
            include($sDBFileName);
            if(isset($sMySQLLocation,$sMySQLUser,$sMySQLPassword,$sMySQLDatabase)){
                return true;
            }else{
                return false;
            }
        }
    }
    
    function createDBFile($sMySQLLocation,$sMySQLUser,$sMySQLPassword,$sMySQLDatabase){
        include("settings.inc.php");
        
        $sDBFileContent = array();
        
        $sDBFileContent[0] = "<?php\n";
        $sDBFileContent[1] = "\$sMySQLLocation = \"$sMySQLLocation\";\n";
        $sDBFileContent[2] = "\$sMySQLUser = \"$sMySQLUser\";\n";
        $sDBFileContent[3] = "\$sMySQLPassword = \"$sMySQLPassword\";\n";
        $sDBFileContent[4] = "\$sMySQLDatabase = \"$sMySQLDatabase\";\n";
        $sDBFileContent[5] = "?>";
        
        $fDBFile = fopen($sDBFileName,"w+");

        for($i = 0;$i < count($sDBFileContent);$i++){
            fwrite($fDBFile,$sDBFileContent[$i]);
        }
    }
    function showDBForm($iErr){
        global $sLR;
        include("settings.inc.php");
        
        
        if(file_exists($sDBFileName)){
            include($sDBFileName);
        }
        $sErrorMsgs = array();
        $sMsg = "";
        
        $sErrorMsgPrefix = $sLR["con_e_msg"] . "<br /><br />";
        
        $sErrorMsgs[2003] = $sLR["con_enr_msg"];
        $sErrorMsgs[2005] = $sLR["con_eloc_msg"];
        $sErrorMsgs[1045] = $sLR["con_ead_msg"];
        $sErrorMsgs[1] = $sLR["con_edb_msg"];
        
        if(isset($sErrorMsgs[$iErr])){
           $sMsg = $sErrorMsgPrefix . $sErrorMsgs[$iErr]; 
        }
        
        if(isset($sMySQLLocation,$sMySQLUser,$sMySQLPassword,$sMySQLDatabase)){
            $sLocation = $sMySQLLocation;
            $sUser = $sMySQLUser;
            $sPassword = $sMySQLPassword;
            $sDatabase = $sMySQLDatabase;
        }else{
            $sLocation = "localhost";
            $sUser = "";
            $sPassword = "";
            $sDatabase = "";
        }
    ?>
	
	    <div align="center">
		    <table cellpadding="0" cellspacing="0" border="0" width="550">
			    <tr>
				    <td width="100%" height="125"><img src="images/spacer.gif" width="100%" height="125" /></td>
			    </tr>
			    <tr>
				    <td width="100%" align="center">
					    <font class="section_header"><?php echo $sLR["con_title"]; ?></font>
				    </td>
			    </tr>
			    <tr>
				    <td width="100%" height="30"><img src="images/spacer.gif" width="100%" height="30" /></td>
			    </tr>
			    <tr>
				    <td width="100%" class="InputCell" style="padding:0px;">
					    <table cellpadding="0" cellspacing="0" border="0" width="100%">
						    <tr>
							    <td width="100%" colspan="2" height="10">
								    <img src="images/spacer.gif" width="100%" height="10" />
							    </td>
						    </tr>
						    <tr>
							    <td width="50%" align="right" valign="middle">
								    <strong><?php echo $sLR["con_loc_lbl"]; ?> :&nbsp;&nbsp;</strong>
							    </td>
							    <td width="50%" align="left" valign="middle">
								    <?php echo $sLocation; ?>
							    </td>
						    </tr>
                                                    <tr>
							    <td width="100%" colspan="2" height="5"><img src="images/spacer.gif" width="100%" height="5" /></td>
						    </tr>
                                                    <tr>
							    <td width="50%" align="right" valign="middle">
								    <strong><?php echo $sLR["con_un_lbl"]; ?> :&nbsp;&nbsp;</strong>
							    </td>
							    <td width="50%" align="left" valign="middle">
								    <?php echo $sUser; ?>
							    </td>
						    </tr>
                                                    <tr>
							    <td width="100%" colspan="2" height="5"><img src="images/spacer.gif" width="100%" height="5" /></td>
						    </tr>
                                                    <tr>
							    <td width="50%" align="right" valign="middle">
								    <strong><?php echo $sLR["con_db_lbl"]; ?> :&nbsp;&nbsp;</strong>
							    </td>
							    <td width="50%" align="left" valign="middle">
								    <?php echo $sDatabase; ?>
							    </td>
						    </tr>
						    <tr>
							    <td width="100%" colspan="2" height="5"><img src="images/spacer.gif" width="100%" height="5" /></td>
						    </tr>
                                                    <tr>
							    <td width="50%" align="right" valign="middle">
								    <strong><?php echo $sLR["con_pw_lbl"]; ?> :&nbsp;&nbsp;</strong>
							    </td>
							    <td width="50%" align="left" valign="middle">
								    ********************
							    </td>
						    </tr>
						    <tr>
							    <td width="100%" colspan="2" height="5"><img src="images/spacer.gif" width="100%" height="5" /></td>
						    </tr>
						    <tr>
							    <td width="100%" colspan="2" height="20">
								    <img src="images/spacer.gif" width="100%" height="20" />
							    </td>
						    </tr>
                                                    <tr>
							    <td width="100%" colspan="2" height="5" align="center"><font class="red"><?php echo $sMsg; ?></font></td>
						    </tr>
						    <tr>
							    <td width="100%" colspan="2" height="5"><img src="images/spacer.gif" width="100%" height="5" /></td>
						    </tr>
					    </table>
				    </td>
			    </tr>
		    </table>
	    </div>
    <?php
}

?>