<?php if( $this->toHelpDesk ) : ?>
Cher membre du Service Desk, 

Voici la proc�dure � suivre:


1. V�rifiez que le compte "<?php echo $this->userData[ 'username' ] ?>" figure bien dans le ldap.
S'il est introuvable, veuillez attribuer cet incident � l'UDS ICAMPUS.


2. S'il existe, v�rifiez qu'il poss�de bien la ressource "icampus.*".
C'est � dire :  icampus.student
                icampus.uclouvain
                icampus.extern
                icampus.alumni

Si ce n'est pas le cas, c'est qu'il n'a pas acc�s � iCampus.
Cela arrive, par exemple, si l'�tudiant(e) n'est pas en ordre de paiement pour son inscription.
V�rifiez �ventuellement s'il y a un probl�me avec son inscription dans EPC, ou renvoyez-le/la vers son secr�tariat administratif.


3. Si l'utilisateur/trice a perdu son mot de passe ou son identifiant global, suivez la proc�dure habituelle (http://www.uclouvain.be/4040).


4. Si le compte est valide (c�d qu'il poss�de la ressource "icampus.*"), demandez � l'utilisateur/trice si elle parvient � se connecter sur le portail (http://www.uclouvain.be) � l'aide du m�me identifiant et mot de passe que celui qu'il/elle a utilis� pour tenter d'entrer dans iCampus.
Si cela fonctionne, c'est que le probl�me provient bien d'iCampus. Vous pouvez alors assigner l'incident � l'UDS iCampus.
Sinon, il ne s'agit pas d'un probl�me li� � iCampus.


Merci

L'�quipe iCampus



<?php endif; ?>

INFOS SUR L'UTILISATEUR:
------------------------

Nom : <?php echo $this->userData[ 'lastName' ]; ?>


Pr�nom : <?php echo $this->userData[ 'firstName' ]; ?>


Email : <?php echo $this->userData[ 'mail' ]; ?>

<?php if( $this->userData[ 'officialCode' ] ) : ?>

FGS : <?php echo $this->userData[ 'officialCode' ]; ?>

<?php endif; ?>
<?php if( $this->userData[ 'username' ] ) : ?>

Identifiant : <?php echo $this->userData[ 'username' ] ?>

<?php endif; ?>
<?php if( array_key_exists( 'userId' , $this->userData ) && $this->userData[ 'userId' ] ) : ?>

Lien vers la page d'�dition du profil : <?php echo get_path( 'rootWeb' ) . 'claroline/admin/admin_profile.php?uidToEdit=' . $this->userData[ 'userId' ]; ?>


Source d'authentification : <?php echo $this->userData[ 'authSource' ]; ?>


Date de la derni�re connexion : <?php echo date( 'Y-m-d H:i:s' , $this->userData[ 'lastLogin' ] ); ?>

<?php endif; ?>

Membre de l'UCL? : <?php echo ( ! array_key_exists( 'UCLMember' , $this->userData ) || $this->userData[ 'UCLMember' ] == '1' ) ? 'Oui' : 'Non'; ?>


Gestionnnaire de cours? : <?php echo $this->userData[ 'courseManager' ] ? 'Oui' : 'Non'; ?>


Page d'origine de la demande : <?php echo getFullPath( $this->ticket->get( 'urlOrigin' ) ); ?>


Infos syst�me (OS, navigateur) : <?php echo $this->ticket->get( 'userAgent' ); ?>


Adresse IP : <?php echo $this->userData[ 'IP_address' ]; ?>


Javascript activ� : <?php echo $this->userData[ 'jsEnabled' ] ? 'Oui' : 'NON'; ?>


Cookies accept�s : <?php echo $this->userData[ 'cookieEnabled' ] ? 'Oui' : 'NON'; ?>


<?php if( $this->userData[ 'courseCode' ] ) : ?>
Code cours concern� : <?php echo $this->userData[ 'courseCode' ]; ?>  <?php if( $this->userData[ 'courseId' ] ) : ?> ( code syst�me : <?php echo $this->userData[ 'courseId' ] ; ?> )<?php endif; ?>
<?php endif; ?>


Num�ro de ticket : <?php echo $this->ticket->get( 'ticketId' ); ?>




DESCRIPTION DU PROBLEME :
-------------------------

<?php echo str_replace( '&acute;' , "'" , $this->userData[ 'issueDescription' ] ); ?>





<?php if( $this->autoMailContent ) : ?>
<?php echo $this->mailSent ? "UN MAIL DE REPONSE AUTOMATIQUE A ETE ENVOYE A L'UTILISATEUR" : "UN MAIL DE REPONSE AUTOMATIQUE AURAIT DU ETRE ENVOYE A L'UTILISATEUR, MAIS L'ENVOI A ECHOUE"; ?>


<?php echo $this->autoMailContent; ?>
<?php endif; ?>