<?php if( $this->userData[ 'UCLMember' ] || substr( $this->userData[ 'mail' ] , -12 ) == 'uclouvain.be' ) : ?>
Si vous êtes bien membre de l'UCL, veuillez réinitialiser votre mot de passe à cette adresse : http://www.uclouvain.be/4040

Si vous avez également perdu votre identifiant, contactez le Service desk de l'UCL :
- par téléphone : 010 / 47 82 82
- ou par mail   : 8282@uclouvain.be
<?php else : ?>
Si vous n'êtes pas membre de l'UCL (càd que vous n'avez pas de compte global UCL), vous pouvez récupérer votre mot de passe en entrant l'adresse mail auquel votre compte est associé à cette adresse : <a href="http://icampus.uclouvain.be/module/ICPASSWD/" >http://icampus.uclouvain.be/module/ICPASSWD/</a>
<?php endif; ?>