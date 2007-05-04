var sLetters = new Array("a","b","c","d","e","f","g","h","i","j","k","l","m","n","o","p","q","r","s","t","u","v","w","x","y","z");


function rollImg(oImg){
	//For manipulation only
	var sCurrentImgName = oImg.src.substring(oImg.src.lastIndexOf('/') + 1,oImg.src.length);
	var sCurrentStatus = sCurrentImgName.substr(sCurrentImgName.lastIndexOf('_'),5);
	
	//Will be used for the final string building
	var sPath = oImg.src.substring(0,oImg.src.lastIndexOf('/') + 1);
	var sShortImgName = sCurrentImgName.substr(0,sCurrentImgName.lastIndexOf('_'));
	var sImgExt = sCurrentImgName.substring(sCurrentImgName.lastIndexOf('.'),sCurrentImgName.length);
	
	//New status
	var sNewStatus = ((sCurrentStatus == '_norm') ? '_roll' : '_norm');
	
	var sNewImgName = sPath + sShortImgName + sNewStatus + sImgExt;
	
	oImg.src = sNewImgName;
}
function toggleImg(sImgName){
	//For manipulation only
	var oImg = getObj(sImgName);
	var sCurrentImgName = oImg.src.substring(oImg.src.lastIndexOf('/') + 1,oImg.src.length);
	var sCurrentStatus = sCurrentImgName.substring(sCurrentImgName.lastIndexOf('_'),sCurrentImgName.lastIndexOf('.'));
	
	//Will be used for the final string building
	var sPath = oImg.src.substring(0,oImg.src.lastIndexOf('/') + 1);
	var sShortImgName = sCurrentImgName.substr(0,sCurrentImgName.lastIndexOf('_'));
	var sImgExt = sCurrentImgName.substring(sCurrentImgName.lastIndexOf('.'),sCurrentImgName.length);
	
	//New status
	var sNewStatus = ((sCurrentStatus == '_on') ? '_off' : '_on');
	
	var sNewImgName = sPath + sShortImgName + sNewStatus + sImgExt;
	
	oImg.src = sNewImgName;
}
function toggleDiv(sDivName){
	var oDiv = getObj(sDivName);
	oDiv.style.display = ((oDiv.style.display == 'none') ? 'block' : 'none');
}
function toggleSavoirPlus(sSavoirPlusName){
	toggleImg('img' + sSavoirPlusName);
	toggleDiv('div' + sSavoirPlusName);
}
function getObj(sObjName){
	if (document.getElementById)
	{
		return document.getElementById(sObjName);
	}
	else if (document.all)
	{
		return document.all[sObjName];
	}
	else if (document.layers)
	{
		if (document.layers[sObjName])
		{
	   		return document.layers[sObjName];
		}
		else
		{
			return document.layers.testP.layers[sObjName];
		}
	}
}
function checkDBFileForm(){
	var sMsg = "Vous devez remplir les champs obligatoires.";
	
	if(getObj("txtLocation").value == ""){
		alert(sMsg);
		return false;
	}
	if(getObj("txtUser").value == ""){
		alert(sMsg);
		return false;
	}
	if(getObj("txtDatabase").value == ""){
		alert(sMsg);
		return false;
	}
	
	return true;
}

function isInArray(s,a){
	for(var i = 0;i < a.length;i++){
		if(a[i] == s){
			return true;
		}
	}
	return false;
}
function getLetter(i){
	var sLetter = "";
	
	if(i < 27){
		sLetter = sLetters[i - 1];
	}else{
		for(var j = 0; j < Math.ceil(i / 26);j++){
			sLetter += sLetters[(i % 26) - 1];
		}
	}
	
	return sLetter;
}
function getRound(i,nb){
	var iFac = Math.pow(10,nb);
	
	return (Math.round(i * iFac)/iFac);
}
function encodeString(s){
	var encodedHtml = s.replace(" ","%20");
	
	alert(encodedHtml);
	
	return encodedHtml;
}
function getShuffledOrder(N) {
	var J, K, Q = new Array(N);
	for (J = 0; J < N; J++) {
		K = Random(J + 1);
		Q[J] = Q[K];
		Q[K] = J;
	}
	return Q;
}
function Random(N) {
	return Math.floor(N * (Math.random() % 1));
}
function copyArray(from){
	var toReturn = new Array();

	for(var i = 0;i < from.length;i++){
		toReturn[i] = from[i];
	}
	
	return toReturn;
}
function newSize(newW,newH) {
	if(document.all && !document.getElementById) {
 		document.all['flashDiv'].style.pixelWidth = newW;
 		document.all['flashDiv'].style.pixelHeight = newH;
 		//document.all['input0'].style.pixelHeight = newH;
	}else{
		document.getElementById('flashDiv').style.width = newW;
		document.getElementById('flashDiv').style.height = newH;
		//document.getElementById('input0').style.height = newH;
	}
}
function openWindowsAndCenter(URL,W,H){
	var width = W;
	var height = H;
	var winl = (screen.width-width)/2;
	var wint = (screen.height-height)/2 - 50;
	if (winl < 0) winl = 0;
	if (wint < 0) wint = 0;
	
	window.open(URL,"Detail",'top='+wint+',left='+winl+',width='+width+',height='+height+',toolbar=0,location=0,directories=0,status=0,menubar=0,scrollbars=1,resizable=0').focus();
}
function openWindowsAndCenterNSB(URL,W,H,NAME){
	var width = W;
	var height = H;
	var winl = (screen.width-width)/2;
	var wint = (screen.height-height)/2 - 50;
	if (winl < 0) winl = 0;
	if (wint < 0) wint = 0;
	
	window.open(URL,NAME,'top='+wint+',left='+winl+',width='+width+',height='+height+',toolbar=0,location=0,directories=0,status=0,menubar=0,scrollbars=0,resizable=0').focus();
}
function postForm(s){
	getObj(s).submit();
}
//FUNCTIONS EMPRUNTER


function MM_findObj(n, d) { //v4.01
  var p,i,x;  if(!d) d=document; if((p=n.indexOf("?"))>0&&parent.frames.length) {
    d=parent.frames[n.substring(p+1)].document; n=n.substring(0,p);}
  if(!(x=d[n])&&d.all) x=d.all[n]; for (i=0;!x&&i<d.forms.length;i++) x=d.forms[i][n];
  for(i=0;!x&&d.layers&&i<d.layers.length;i++) x=MM_findObj(n,d.layers[i].document);
  if(!x && d.getElementById) x=d.getElementById(n); return x;
}


function flevDivPositionValue(sDiv, sProperty) { // v2.1, Marja Ribbers-de Vroed, FlevOOware
	this.opera = (window.opera); // Opera 5+
	this.ns4 = (document.layers); // Netscape 4.x
	this.ns6 = (document.getElementById && !document.all && !this.opera); // Netscape 6+
	this.ie = (document.all);  // Internet Explorer 4+

  var sValue = ""; docObj = eval("MM_findObj('" + sDiv + "')"); if (docObj == null) {return 0;}
	if ((sProperty == "left") || (sProperty == "top")) {
		if (!this.ns4) {docObj = docObj.style;} 
		sValue = eval("docObj." + sProperty);
		
		if ((this.ie) && (sValue == "")) { // IE (on PC) bug with nested layers
			if (sProperty == "top") { sValue = eval(sDiv + ".offsetTop"); } 
			else { sValue = eval(sDiv + ".offsetLeft"); } 
		};
	}
	else {
		if (this.opera) {
			docObj = docObj.style;
			if (sProperty == "height") { sValue = docObj.pixelHeight; } 
			else if (sProperty == "width") { sValue = docObj.pixelWidth; } 
		}
		else if (this.ns4) {sValue = eval("docObj.clip." + sProperty);} 
		else if (this.ns6) {sValue = document.defaultView.getComputedStyle(docObj, "").getPropertyValue(sProperty); } 
	    else if (this.ie) { 
			if (sProperty == "width") { sValue = eval(sDiv + ".offsetWidth"); } 
			else if (sProperty == "height") { sValue = eval(sDiv + ".offsetHeight"); } 
		}
   	}
	sValue = (sValue == "") ? 0 : sValue; 
	if (isNaN(sValue)) { if (sValue.indexOf('px') > 0) { sValue = sValue.substring(0,sValue.indexOf('px')); } } 
	return parseInt(sValue); 
}

function flevPersistentLayer() { // v3.3, Marja Ribbers-de Vroed, FlevOOware
	var sD = arguments[0], oD = eval("MM_findObj('" + sD + "')"), iWW, iWH, iSX, iSY, iT = 10, sS = "";
	if (!document.layers) {oD = oD.style;}
	if (oD.tmpTimeout != null) {clearTimeout(oD.tmpTimeout);}
	var sXL = arguments[1], sXC = arguments[2], sXR = arguments[3], sYT = arguments[4], sYC = arguments[5], sYB = arguments[6];
	var iS = (arguments.length > 7) ? parseInt(arguments[7]) : 0, iPx = (arguments.length > 8) ? parseInt(arguments[8]) : 0;
	if (window.innerWidth) { // NS4, NS6 and Opera
		var oW = window; iWW = oW.innerWidth; iWH = oW.innerHeight; iSX = oW.pageXOffset; iSY = oW.pageYOffset; }
	else if (document.documentElement && document.documentElement.clientWidth) { // IE6 in standards compliant mode
		var oDE = document.documentElement; iWW = oDE.clientWidth; iWH = oDE.clientHeight; iSX = oDE.scrollLeft; iSY = oDE.scrollTop; }
	else if (document.body) { // IE4+
		var oDB = document.body; iWW = oDB.clientWidth; iWH = oDB.clientHeight; iSX = oDB.scrollLeft; iSY = oDB.scrollTop; }
	else {return;}
	
	var iCX = iNX = flevDivPositionValue(sD, 'left'), iCY = iNY = flevDivPositionValue(sD, 'top');
	
	if (sXL != "") {iNX = iSX + parseInt(sXL);} 
	else if (sXC != "") {iNX = Math.round(iSX + (iWW/2) - (flevDivPositionValue(sD, 'width')/2));}
	else if (sXR != "") {iNX = iSX + iWW - (flevDivPositionValue(sD, 'width') + parseInt(sXR));}
	if (sYT != "") {iNY = iSY + parseInt(sYT);}
	else if (sYC != "") {iNY = Math.round(iSY + (iWH/2) - (flevDivPositionValue(sD, 'height')/2));}
	else if (sYB != "") {iNY = iSY + (iWH - flevDivPositionValue(sD, 'height') - parseInt(sYB));}
	if ((iCX != iNX) || (iCY != iNY)) {
		if (iS > 0) {
			if (iPx > 0) { iT = iS;
				var iPxX = iPx, iPxY = iPx, iMX = Math.abs(iCX - iNX), iMY = Math.abs(iCY - iNY);
				// take care of diagonal movement
				if (iMX < iMY) {iPxY = (iMX != 0) ? ((iMY/iMX)*iPx) : iPx;}
				else {iPxX = (iMY != 0) ? ((iMX/iMY)*iPx) : iPx;}
				if (iPxX >= iMX) {iPxX = Math.min(Math.ceil(iPxX), iPx);}
				if (iPxY >= iMY) {iPxY = Math.min(Math.ceil(iPxY), iPx);}
				// temporary X/Y coordinates
				if ((iCX < iNX) && (iCX + iPxX < iNX)) {iNX = iCX + iPxX;}
				if ((iCX > iNX) && (iCX - iPxX > iNX)) {iNX = iCX - iPxX;}
				if ((iCY < iNY) && (iCY + iPxY < iNY)) {iNY = iCY + iPxY;}
				if ((iCY > iNY) && (iCY - iPxY > iNY)) {iNY = iCY - iPxY;} }
			else { 
				var iMX = ((iNX - iCX) / iS), iMY = ((iNY - iCY) / iS); 
				iMX = (iMX > 0) ? Math.ceil(iMX) : Math.floor(iMX); iNX = iCX + iMX; 
				iMY = (iMY > 0) ? Math.ceil(iMY) : Math.floor(iMY); iNY = iCY + iMY; } }
		if ((parseInt(navigator.appVersion)>4 || navigator.userAgent.indexOf("MSIE")>-1) && (!window.opera)) {sS="px";}
		if (iMX != 0) {eval("oD.left = '" + iNX + sS + "'");}
		if (iMY != 0) {eval("oD.top = '" + iNY + sS + "'");} }
	var sF = "flevPersistentLayer('" + sD + "','" + sXL + "','" + sXC + "','" + sXR + "','" + sYT + "','" + sYC + "','" + sYB + "'," + iS + "," + iPx + ")";
	oD.tmpTimeout = setTimeout(sF,10);
}

function flevInitPersistentLayer() { // v3.3, Marja Ribbers-de Vroed, FlevOOware
	if (arguments.length < 8) {return;}
	var sD = arguments[0]; if (sD == "") {return;}
	var	oD = eval("MM_findObj('" + sD + "')"); if (!oD) {return;}
	var iCSS = parseInt(arguments[1]);
	var sXL = arguments[2], sXC = arguments[3], sXR = arguments[4], sYT = arguments[5], sYC = arguments[6], sYB = arguments[7];
	var iS = (arguments.length > 8) ? parseInt(arguments[8]) : 0, iPx = (arguments.length > 9) ? parseInt(arguments[9]) : 0;
	var sS = ((parseInt(navigator.appVersion)>4 || navigator.userAgent.indexOf("MSIE")>-1) && (!window.opera))? "px":"";
	
	if (iCSS != 0) { if (!document.layers) {oD = oD.style;} sXL = parseInt(oD.left), sYT = parseInt(oD.top);}
	var sF = "flevPersistentLayer('" + sD + "','" + sXL + "','" + sXC + "','" + sXR + "','" + sYT + "','" + sYC + "','" + sYB + "'," + iS + "," + iPx + ")";
	
	if (!document.layers) {oD = oD.style;}
  eval("oD.left = '" + arguments[2] + sS + "'");
	eval("oD.top = '" + arguments[5] + sS + "'");
	
	eval(sF);
}
function FormatNumber(num, decimalNum, bolLeadingZero, bolParens)
   /* IN - num:            the number to be formatted
           decimalNum:     the number of decimals after the digit
           bolLeadingZero: true / false to use leading zero
           bolParens:      true / false to use parenthesis for - num

      RETVAL - formatted number
   */
   {
       var tmpNum = num;

       // Return the right number of decimal places
       tmpNum *= Math.pow(10,decimalNum);
       tmpNum = Math.floor(tmpNum);
       tmpNum /= Math.pow(10,decimalNum);

       var tmpStr = new String(tmpNum);

       // See if we need to hack off a leading zero or not
       if (!bolLeadingZero && num < 1 && num > -1 && num !=0)
           if (num > 0)
               tmpStr = tmpStr.substring(1,tmpStr.length);
           else
               // Take out the minus sign out (start at 2)
               tmpStr = "-" + tmpStr.substring(2,tmpStr.length);                        


       // See if we need to put parenthesis around the number
       if (bolParens && num < 0)
           tmpStr = "(" + tmpStr.substring(1,tmpStr.length) + ")";


       return tmpStr;
   }

function trim(s) {
  while (s.substring(0,1) == ' ') {
    s = s.substring(1,s.length);
  }
  while (s.substring(s.length-1,s.length) == ' ') {
    s = s.substring(0,s.length-1);
  }
  return s;
}
function refresh(sSuffix){
	var sURL = unescape(window.location.href);
	
	if(sSuffix){
		sURL += sSuffix;
	}
	
    window.location.href = sURL;
}
function gotoAnchor(sAnchorName){	
	document.location.hash = "#" + sAnchorName;
}
function getScrollPos(){
	var iScrollPos = 0;
	
    if (navigator.appName == "Microsoft Internet Explorer"){
        iScrollPos = document.body.scrollTop + document.documentElement.scrollTop;
    }else{
        iScrollPos = window.pageYOffset;
    }
    
    return iScrollPos;
}
//Cookies
function createCookie(name,value,days)
{
	if (days)
	{
		var date = new Date();
		date.setTime(date.getTime()+(days*24*60*60*1000));
		var expires = "; expires="+date.toGMTString();
	}
	else var expires = "";
	document.cookie = name+"="+value+expires+"; path=/";
}

function readCookie(name)
{
	var nameEQ = name + "=";
	var ca = document.cookie.split(';');
	for(var i=0;i < ca.length;i++)
	{
		var c = ca[i];
		while (c.charAt(0)==' ') c = c.substring(1,c.length);
		if (c.indexOf(nameEQ) == 0) return c.substring(nameEQ.length,c.length);
	}
	return null;
}

function eraseCookie(name)
{
	createCookie(name,"",-1);
}