
function openQuiz(path, id) {
	var winQuiz;
	if (!winQuiz || winQuiz.closed){
		//var largeur = 1000;
		var largeur = screen.width-150;
		var hauteur = screen.height-100;
		if (largeur < 790) largeur = 790;
		if (hauteur < 400) hauteur = 400;
	
		var winl = (screen.width-largeur)/2;
		var wint = (screen.height-hauteur)/2 - 50;
		if (winl < 0) winl = 0;
		if (wint < 0) wint = 0;
		
		var settings = "status,scrollbars,menubar,resizable,width="+largeur+",height="+hauteur+",top="+wint+",left="+winl;
	//	winQuiz = window.open( "/claroline/v1/module/NETQUIZ/netquiz/authparticipant.php?id=" + id + "&auth=1&qi=8V6Y5F399&qv=T2U6K4R74","entree_netquiz",settings );
	//	 + "&auth=1&qi=8V6Y5F399&qv=T2U6K4R74"
		winQuiz = window.open( path + "/quiz.html","entree_netquiz",settings );
	} else {
		winQuiz.focus()
	}
}

function openNetquiz(path) {
	var winQuiz;
	if (!winQuiz || winQuiz.closed){
		//var largeur = 1000;
		var largeur = screen.width-150;
		var hauteur = screen.height-100;
		if (largeur < 790) largeur = 790;
		if (hauteur < 400) hauteur = 400;
	
		var winl = (screen.width-largeur)/2;
		var wint = (screen.height-hauteur)/2 - 50;
		if (winl < 0) winl = 0;
		if (wint < 0) wint = 0;
		
		var settings = "status,scrollbars,menubar,resizable,width="+largeur+",height="+hauteur+",top="+wint+",left="+winl;
		winQuiz = window.open( path + "/index.php","entree_netquiz",settings );
	} else {
		winQuiz.focus()
	}
}