Effectuez les v�rifications suivantes:

<?php if( $this->userData[ 'UCLMember' ] ) : ?>
Parvenez-vous � vous authentifier sur le portail de l'UCL ( � cette adresse : http://www.uclouvain.be ) ?
    Dans le cas contraire, le probl�me provient sans doute de votre compte global.
    Contactez alors le service-desk de l'UCL:
        mail: service-desk@uclouvain.be
        t�l:  010 / 47 82 82
        web:  http://www.uclouvain.be/8282
<?php endif; ?>

V�rifiez bien que vous respectez scrupuleusement la casse en tapant votre mot de passe.
En effet, le syst�me consid�re les majuscules et les minuscules comme des caract�res distincts.

Si vous avez activ� l'autocompl�tion des mots de passe au sein de votre navigateur, d�sactivez-la.
Il se peut que ce dernier compl�te le champ avec des donn�es erron�es...

<?php if( $this->userData[ 'UCLMember' ] ) : ?>
Enfin, si vous pensez avoir oubli� votre mot de passe, vous pouvez le r�initialiser � cette adresse : http://www.uclouvain.be/4040
<?php else : ?>
Enfin, si vous pensez avoir oubli� votre mot de passe, vous pouvez le r�cup�rer en entrant l'adresse mail auquel votre compte est associ� � cette adresse: <a href="http://icampus.uclouvain.be/module/ICPASSWD/" >http://icampus.uclouvain.be/module/ICPASSWD/</a>
<?php endif; ?>