Effectuez les vérifications suivantes:

<?php if( $this->userData[ 'UCLMember' ] ) : ?>
Parvenez-vous à vous authentifier sur le portail de l'UCL ( à cette adresse : http://www.uclouvain.be ) ?
    Dans le cas contraire, le problème provient sans doute de votre compte global.
    Contactez alors le service-desk de l'UCL:
        mail: service-desk@uclouvain.be
        tél:  010 / 47 82 82
        web:  http://www.uclouvain.be/8282
<?php endif; ?>

Vérifiez bien que vous respectez scrupuleusement la casse en tapant votre mot de passe.
En effet, le système considère les majuscules et les minuscules comme des caractères distincts.

Si vous avez activé l'autocomplétion des mots de passe au sein de votre navigateur, désactivez-la.
Il se peut que ce dernier complète le champ avec des données erronées...

<?php if( $this->userData[ 'UCLMember' ] ) : ?>
Enfin, si vous pensez avoir oublié votre mot de passe, vous pouvez le réinitialiser à cette adresse : http://www.uclouvain.be/4040
<?php else : ?>
Enfin, si vous pensez avoir oublié votre mot de passe, vous pouvez le récupérer en entrant l'adresse mail auquel votre compte est associé à cette adresse: <a href="http://icampus.uclouvain.be/module/ICPASSWD/" >http://icampus.uclouvain.be/module/ICPASSWD/</a>
<?php endif; ?>