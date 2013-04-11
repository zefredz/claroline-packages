<?php if( $this->userData[ 'UCLMember' ] ) : ?>
Si vous êtes bien membre de l'UCL, veuillez réinitialliser votre mot de passe à cette adresse : http://www.uclouvain.be/4040
<?php else : ?>
Si vous n'êtes pas membre de l'UCL (càd que vous n'avez pas de compte global UCL), vous pouvez récupérer votre mot de passe en entrant l'adresse mail auquel votre compte est associé à cette adresse : <a href="http://icampus.uclouvain.be/module/ICPASSWD/" >http://icampus.uclouvain.be/module/ICPASSWD/</a>
<?php endif; ?>