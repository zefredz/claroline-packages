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