
// Fonction de confirmation de suppression d'une F.A.Q.
function js_fct_confirm_delete_faq(id) {

	if (confirm("Etes-vous certain de vouloir supprimer définitivement cette F.A.Q. ?")) {
		document.location = "index.php?fuseaction=delete_faq&id=" + id + "&confirm=true";
	}

}

// Fonction de confirmation de suppression d'une catégorie
function js_fct_confirm_delete_category(id) {

	if (confirm("Etes-vous certain de vouloir supprimer définitivement cette catégorie ?")) {
		document.location = "index.php?fuseaction=delete_category&id=" + id + "&confirm=true";
	}

}

