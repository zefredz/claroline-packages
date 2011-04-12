// $Id$
/**
 * Online library for Claroline
 *
 * @version     CLLIBR 0.3.4 $Revision$ - Claroline 1.9
 * @copyright   2001-2011 Universite catholique de Louvain (UCL)
 * @license     http://www.gnu.org/copyleft/gpl.html (GPL) GENERAL PUBLIC LICENSE
 * @package     CLLIBR
 * @author      Frederic Fervaille <frederic.fervaille@uclouvain.be>
 */

$(document).ready(function(){
    $("#resourceStorage").change(function(){
        var storageType = $(this).val();
        $("#resourceSelect").empty();
        if ( storageType == 'file' ){
            $("#resourceSelect").append("<input type=\"file\" name=\"uploadedFile\" value=\"\"/>");
        }
        else if ( storageType == 'url' ){
            $("#resourceSelect").append("<input type=\"text\" name=\"resourceUrl\" value=\"\"/>");
        }
    });
    
    $("#linkedResource").select(function(){
        $("#resourceSelect").append("<strong><?php echo get_lang( '' ); ?></strong>"+
                                        "<input type=\"text\""+
                                                "name=\"resourceUrl\""+
                                                "value=\"\"/>");
    });
    
    var nbToAdd=0;
    $("#addMetadata").click(function(){
        nbToAdd++;
        var content="<input id=\"metadatax"+nbToAdd+"\" type=\"text\" name=\"add["+nbToAdd+"]\" value=\"\" size=\"40\" \/><br />"+
                    "<a id=\"delx"+nbToAdd+"\" class=\"delMetadata claroCmd\" href=\"#delx"+nbToAdd+"\">"+
                    "<\/a>"+
                    "<script>"+
                    "    $(\"#delx"+nbToAdd+"\").click(function(){'"+
                    "    $(this).parent().remove();'"+
                    "    });"+
                    "<\/script>";
        
        $("#metadataList").append(content);
    });
    
    $(".delMetadata").click(function(){
        var metadataId = $(this).attr("id").substr(3);
        $("#metadata"+metadataId).attr({name:"del["+metadataId+"]"});
        $("#metadata"+metadataId).parent().hide();
    });
});
