<?php if( ! $this->userData[ 'cookieEnabled' ] ) : ?>
Vous ne parvenez pas � vous authentifier sur la plateforme car votre navigateur refuse actuellement les cookies!
Changez la configuration de celui-ci afin que les cookies soient accept�s, puis essayez de vous connecter � nouveau.

Si cela ne fonctionne toujours pas, e<?php else : ?>
E<?php endif; ?>ffectuez les v�rifications suivantes :
<?php if( $this->userData[ 'UCLMember' ] ) : ?>

Parvenez-vous � vous authentifier sur le portail de l'UCL ( � cette adresse : http://www.uclouvain.be/page_connexion.html ) ?
    Dans le cas contraire, le probl�me provient sans doute de votre compte global.
    Contactez alors le service-desk de l'UCL :
        mail : service-desk@uclouvain.be
        t�l :  010 / 47 82 82
        web :  http://www.uclouvain.be/8282
<?php endif; ?>

V�rifiez bien que vous respectez scrupuleusement la casse en tapant votre mot de passe.
En effet, le syst�me consid�re les majuscules et les minuscules comme des caract�res distincts.

Si vous avez activ� l'autocompl�tion des mots de passe au sein de votre navigateur, d�sactivez-la.
Il se peut que ce dernier compl�te le champ avec des donn�es erron�es...

Si apr�s avoir entr� vos identifiant et mot de passe rien ne se passe (c�d que le navigateur revient � la m�me page sans message d'erreur), essayez de vous connecter via cette adresse : https://icampus.uclouvain.be (remarquez le petit "s" juste apr�s "http")

<?php if( $this->userData[ 'UCLMember' ] ) : ?>
Enfin, si vous pensez avoir oubli� votre mot de passe, vous pouvez le r�initialiser � cette adresse : http://www.uclouvain.be/4040
<?php else : ?>
Enfin, si vous pensez avoir oubli� votre mot de passe, vous pouvez le r�cup�rer en entrant l'adresse mail auquel votre compte est associ� � cette adresse: http://icampus.uclouvain.be/module/ICPASSWD/
<?php endif; ?>