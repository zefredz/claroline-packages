<?php if( $this->userData[ 'UCLMember' ] || substr( $this->userData[ 'mail' ] , -12 ) == 'uclouvain.be' ) : ?>
Si vous �tes bien membre de l'UCL, veuillez r�initialiser votre mot de passe � cette adresse : http://www.uclouvain.be/4040

Si vous avez �galement perdu votre identifiant, contactez le Service desk de l'UCL :
- par t�l�phone : 010 / 47 82 82
- ou par mail   : 8282@uclouvain.be
<?php else : ?>
Si vous n'�tes pas membre de l'UCL (c�d que vous n'avez pas de compte global UCL), vous pouvez r�cup�rer votre mot de passe en entrant l'adresse mail auquel votre compte est associ� � cette adresse : <a href="http://icampus.uclouvain.be/module/ICPASSWD/" >http://icampus.uclouvain.be/module/ICPASSWD/</a>
<?php endif; ?>