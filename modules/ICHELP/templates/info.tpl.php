<?php if( $this->ticketData[ 'officialCode' ] ) : ?>
<strong>FGS :</strong> <?php echo $this->ticketData[ 'officialCode' ]; ?><br />
<?php endif; ?>

<?php if( $this->ticketData[ 'username' ] ) : ?>
<strong>Identifiant :</strong> <?php echo  $this->ticketData[ 'username' ]; ?><br />
<?php endif; ?>

<?php if( $this->ticket->get( 'userId' ) ) : ?>
<strong>Source d'authentification :</strong> <?php echo $this->ticketData[ 'authSource' ]; ?><br />
<strong>Date de la derni�re connexion :</strong> <?php echo date( 'Y-m-d H:i:s' , $this->ticketData['lastLogin' ] ); ?><br />
<?php endif; ?>

<strong>Membre de l'UCL? :</strong> <?php echo ( ! array_key_exists( 'UCLMember' , $this->ticketData ) || (int)$this->ticketData[ 'UCLMember' ] == 1 ) ? 'Oui' : 'Non'; ?><br />
<strong>Gestionnnaire de cours? :</strong> <?php echo $this->ticketData[ 'courseManager' ] ? 'Oui' : 'Non'; ?><br />
<strong>Page d'origine de la demande :</strong> <?php echo $this->ticket->get( 'urlOrigin' ); ?><br />
<strong>Infos syst�me (OS, navigateur) :</strong> <?php echo $this->ticket->get( 'userAgent' ); ?><br />
<strong>Javascript activ� :</strong> <?php echo $this->ticketData[ 'jsEnabled' ] ? 'Oui' : 'NON'; ?><br />
<strong>Cookies accept�s : </strong><?php echo $this->ticketData[ 'cookieEnabled' ] ? 'Oui' : 'NON'; ?><br />

<?php if( $this->ticketData[ 'courseId' ] ) : ?>
<strong>Code cours concern� :</strong> <?php echo $this->ticketData[ 'courseId' ]; ?><br />
<?php endif; ?>

<br />
<strong>Description du probl�me :</strong><br />
<?php
if( array_key_exists( 'issueDescription' , $this->ticketData ) )
{
    echo str_replace( '&acute;' , "'" , $this->ticketData[ 'issueDescription' ] );
}
else
{
    echo get_lang( 'No description' );
}