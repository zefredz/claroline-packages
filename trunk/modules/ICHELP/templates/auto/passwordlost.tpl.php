<?php if( $this->userData[ 'UCLMember' ] ) : ?>
Si vous êtes bien membre de l'UCL, veuillez réinitialliser votre mot de passe à cette adresse: http://www.uclouvain.be/4040
<?php else : ?>
A moins que vous ne soyez membre de l'UCL (càd que vous ayez un compte global UCL), vous pouvez récupérer votre mot de passe en entrant l'adresse mail auquel votre compte est associé à cette adresse: <a href="http://icampus.uclouvain.be/module/ICPASSWD/" >http://icampus.uclouvain.be/module/ICPASSWD/</a>
<?php endif; ?>