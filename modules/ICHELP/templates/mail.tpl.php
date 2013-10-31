<?php if( $this->toHelpDesk ) : ?>
Cher membre du Service Desk, 

Voici la procédure à suivre:


1. Vérifiez que le compte "<?php echo $this->userData[ 'username' ] ?>" figure bien dans le ldap.
S'il est introuvable, veuillez attribuer cet incident à l'UDS ICAMPUS.


2. S'il existe, vérifiez qu'il possède bien la ressource "icampus.*".
C'est à dire :  icampus.student
                icampus.uclouvain
                icampus.extern
                icampus.alumni

Si ce n'est pas le cas, c'est qu'il n'a pas accès à iCampus.
Cela arrive, par exemple, si l'étudiant(e) n'est pas en ordre de paiement pour son inscription.
Vérifiez éventuellement s'il y a un problème avec son inscription dans EPC, ou renvoyez-le/la vers son secrétariat administratif.


3. Si l'utilisateur/trice a perdu son mot de passe ou son identifiant global, suivez la procédure habituelle (http://www.uclouvain.be/4040).


4. Si le compte est valide (càd qu'il possède la ressource "icampus.*"), demandez à l'utilisateur/trice si elle parvient à se connecter sur le portail (http://www.uclouvain.be) à l'aide du même identifiant et mot de passe que celui qu'il/elle a utilisé pour tenter d'entrer dans iCampus.
Si cela fonctionne, c'est que le problème provient bien d'iCampus. Vous pouvez alors assigner l'incident à l'UDS iCampus.
Sinon, il ne s'agit pas d'un problème lié à iCampus.


Merci

L'équipe iCampus



<?php endif; ?>

INFOS SUR L'UTILISATEUR:
------------------------

Nom : <?php echo $this->userData[ 'lastName' ]; ?>


Prénom : <?php echo $this->userData[ 'firstName' ]; ?>


Email : <?php echo $this->userData[ 'mail' ]; ?>

<?php if( $this->userData[ 'officialCode' ] ) : ?>

FGS : <?php echo $this->userData[ 'officialCode' ]; ?>

<?php endif; ?>
<?php if( $this->userData[ 'username' ] ) : ?>

Identifiant : <?php echo $this->userData[ 'username' ] ?>

<?php endif; ?>
<?php if( array_key_exists( 'userId' , $this->userData ) && $this->userData[ 'userId' ] ) : ?>

Lien vers la page d'édition du profil : <?php echo get_path( 'rootWeb' ) . 'claroline/admin/admin_profile.php?uidToEdit=' . $this->userData[ 'userId' ]; ?>


Source d'authentification : <?php echo $this->userData[ 'authSource' ]; ?>


Date de la dernière connexion : <?php echo date( 'Y-m-d H:i:s' , $this->userData[ 'lastLogin' ] ); ?>

<?php endif; ?>

Membre de l'UCL? : <?php echo ( ! array_key_exists( 'UCLMember' , $this->userData ) || $this->userData[ 'UCLMember' ] == '1' ) ? 'Oui' : 'Non'; ?>


Gestionnnaire de cours? : <?php echo $this->userData[ 'courseManager' ] ? 'Oui' : 'Non'; ?>


Page d'origine de la demande : <?php echo getFullPath( $this->ticket->get( 'urlOrigin' ) ); ?>


Infos système (OS, navigateur) : <?php echo $this->ticket->get( 'userAgent' ); ?>


Adresse IP : <?php echo $this->userData[ 'IP_address' ]; ?>


Javascript activé : <?php echo $this->userData[ 'jsEnabled' ] ? 'Oui' : 'NON'; ?>


Cookies acceptés : <?php echo $this->userData[ 'cookieEnabled' ] ? 'Oui' : 'NON'; ?>


<?php if( $this->userData[ 'courseCode' ] ) : ?>
Code cours concerné : <?php echo $this->userData[ 'courseCode' ]; ?>  <?php if( $this->userData[ 'courseId' ] ) : ?> ( code système : <?php echo $this->userData[ 'courseId' ] ; ?> )<?php endif; ?>
<?php endif; ?>


Numéro de ticket : <?php echo $this->ticket->get( 'ticketId' ); ?>




DESCRIPTION DU PROBLEME :
-------------------------

<?php echo str_replace( '&acute;' , "'" , $this->userData[ 'issueDescription' ] ); ?>





<?php if( $this->autoMailContent ) : ?>
<?php echo $this->mailSent ? "UN MAIL DE REPONSE AUTOMATIQUE A ETE ENVOYE A L'UTILISATEUR" : "UN MAIL DE REPONSE AUTOMATIQUE AURAIT DU ETRE ENVOYE A L'UTILISATEUR, MAIS L'ENVOI A ECHOUE"; ?>


<?php echo $this->autoMailContent; ?>
<?php endif; ?>