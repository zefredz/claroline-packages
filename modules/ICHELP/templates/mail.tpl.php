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
<?php if( $this->ticket->get( 'userId' ) ) : ?>

Source d'authentification : <?php echo $this->userData[ 'authSource' ]; ?>

Date de la dernière connexion : <?php echo date( 'Y-m-d H:i:s' , $this->userData[ 'lastLogin' ] ); ?>
<?php endif; ?>

Membre de l'UCL? : <?php echo ( ! array_key_exists( 'UCLMember' , $this->userData ) || $this->userData[ 'UCLMember' ] == '1' ) ? 'Oui' : 'Non'; ?>


Gestionnnaire de cours? : <?php echo $this->userData[ 'courseManager' ] ? 'Oui' : 'Non'; ?>


Page d'origine de la demande : <?php echo $this->ticket->get( 'httpReferer' ); ?>


Infos système (OS, navigateur) : <?php echo $this->ticket->get( 'userAgent' ); ?>


Javascript activé : <?php echo $this->userData[ 'jsEnabled' ] ? 'Oui' : 'NON'; ?>


Cookies acceptés : <?php echo $this->ticket->get( 'cookieEnabled' ) ? 'Oui' : 'NON'; ?>


<?php if( $this->userData[ 'courseId' ] ) : ?>
Code cours concerné : <?php echo $this->userData[ 'courseId' ]; ?>
<?php endif; ?>


<?php if( $this->autoMail ) : ?>
UN MAIL DE REPONSE AUTOMATIQUE A ETE ENVOYE A L'UILISATEUR

<?php endif; ?>

DESCRIPTION DU PROBLEME :
-------------------------

<?php echo $this->userData[ 'message' ]; ?>