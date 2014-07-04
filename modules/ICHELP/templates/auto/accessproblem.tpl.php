<?php if( ! $this->userData[ 'cookieEnabled' ] ) : ?>
Vous ne parvenez pas à vous authentifier sur la plateforme car votre navigateur refuse actuellement les cookies!
Changez la configuration de celui-ci afin que les cookies soient acceptés, puis essayez de vous connecter à nouveau.

Si cela ne fonctionne toujours pas, e<?php else : ?>
E<?php endif; ?>ffectuez les vérifications suivantes :
<?php if( $this->userData[ 'UCLMember' ] ) : ?>

Parvenez-vous à vous authentifier sur le portail de l'UCL ( à cette adresse : http://www.uclouvain.be/page_connexion.html ) ?
    Dans le cas contraire, le problème provient sans doute de votre compte global.
    Contactez alors le service-desk de l'UCL :
        mail : service-desk@uclouvain.be
        tél :  010 / 47 82 82
        web :  http://www.uclouvain.be/8282
<?php endif; ?>

Vérifiez bien que vous respectez scrupuleusement la casse en tapant votre mot de passe.
En effet, le système considère les majuscules et les minuscules comme des caractères distincts.

Si vous avez activé l'autocomplétion des mots de passe au sein de votre navigateur, désactivez-la.
Il se peut que ce dernier complète le champ avec des données erronées...

Si après avoir entré vos identifiant et mot de passe rien ne se passe (càd que le navigateur revient à la même page sans message d'erreur), essayez de vous connecter via cette adresse : https://icampus.uclouvain.be (remarquez le petit "s" juste après "http")

<?php if( $this->userData[ 'UCLMember' ] ) : ?>
Enfin, si vous pensez avoir oublié votre mot de passe, vous pouvez le réinitialiser à cette adresse : http://www.uclouvain.be/4040
<?php else : ?>
Enfin, si vous pensez avoir oublié votre mot de passe, vous pouvez le récupérer en entrant l'adresse mail auquel votre compte est associé à cette adresse: http://icampus.uclouvain.be/module/ICPASSWD/
<?php endif; ?>