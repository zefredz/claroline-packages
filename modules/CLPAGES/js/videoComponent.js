/**
 * Adjust the editor html form dynamically in function of the the selected video type
 *
 * @param string id The video component id
 */
function setForm(id)
{
    save(lastType);
    var divType = "#videoType_" + id;
    var type = $(divType).val();
    var divIdentifiers = "#videoIdentifiers_" + id;
    $(divIdentifiers).empty();
    $(divIdentifiers).append(videoHtmlCode[type]["identifiers"]);
    var divParameters = "#videoParameters_" + id;
    $(divParameters).empty();
    $(divParameters).append(videoHtmlCode[type]["parameters"]);
    load(type);
    lastType = type;
}

/**
 * Save the input value in the videoDataList javascript variable BEFORE form modification
 *
 * @param string type The previous selected video type
 */
function save(videoType)
{
    for (var identifier in videoDataList[videoType]["identifiers"])
    {
        var identifierType = videoDataList[videoType]["identifiers"][identifier]["type"];
        var identifierId = videoDataList[videoType]["identifiers"][identifier]["id"];
        
        if (identifierType == "radio" )
        {
            if($(identifierId).attr("checked"))
            {
                videoDataList[videoType]["identifiers"][identifier]["value"] = "checked";
            }
            else
            {
                videoDataList[videoType]["identifiers"][identifier]["value"] = "";
            }
        }
        
        if (identifierType == "textBox" )
        {
            videoDataList[videoType]["identifiers"][identifier]["value"] = $(identifierId).attr("value");
        }
    }

    for (var parameter in videoDataList[videoType]["parameters"])
    {
        var parameterType = videoDataList[videoType]["parameters"][parameter]["type"];
        var parameterId = videoDataList[videoType]["parameters"][parameter]["id"];
        
        if (parameterType == "radio" )
        {
            if($(parameterId).attr("checked"))
            {
                videoDataList[videoType]["parameters"][parameter]["value"] = "checked";
            }
            else
            {
                videoDataList[videoType]["parameters"][parameter]["value"] = "";
            }
        }
        
        if (parameterType == "select" )
        {
            
            if($(parameterId).attr("selected"))
            {
                videoDataList[videoType]["parameters"][parameter]["value"] = "selected";
            }
            else
            {
                videoDataList[videoType]["parameters"][parameter]["value"] = "";
            }
        }
        
        if (parameterType == "textBox" )
        {
            videoDataList[videoType]["parameters"][parameter]["value"] = $(parameterId).attr("value");
        }
    }
}

/**
 * Load the input value in the videoDataList javascript variable after form modification
 *
 * @param string type The current selected video type
 */
function load(videoType)
{
    for (var identifier in videoDataList[videoType]["identifiers"])
    {
        var identifierType = videoDataList[videoType]["identifiers"][identifier]["type"];
        var identifierId = videoDataList[videoType]["identifiers"][identifier]["id"];
        var identifierValue = videoDataList[videoType]["identifiers"][identifier]["value"];
        
        if (identifierType == "radio" )
        {
            if(identifierValue == "checked")
            {
                $(identifierId).attr("checked","checked");
            }
            else
            {
                $(identifierId).removeAttr("checked");
            }
        }
        if (identifierType == "textBox" )
        {
            $(identifierId).val(identifierValue);
        }
    }

    for (var parameter in videoDataList[videoType]["parameters"])
    {
        var parameterType = videoDataList[videoType]["parameters"][parameter]["type"];
        var parameterId = videoDataList[videoType]["parameters"][parameter]["id"];
        var parameterValue = videoDataList[videoType]["parameters"][parameter]["value"];

        if (parameterType == "radio" )
        {
            if(parameterValue == "checked")
            {
                $(parameterId).attr("checked","checked");
            }
            else
            {
                $(parameterId).removeAttr("checked");
            }
        }
        if (parameterType == "select" )
        {
            if(parameterValue == "selected")
            {
                $(parameterId).attr("selected","selected");
            }
            else
            {
                $(parameterId).removeAttr("selected");
            }
        }
        if (parameterType == "textBox" )
        {
            $(parameterId).val(parameterValue);
        }
    }
}