lockArticlesAndFiles
====
Plugin pour [PluXml](https://pluxml.org) permettant d'afficher des fichiers contenus dans un répertoire depuis la rédaction d'un article et de protéger l'article par un mot de passe.

Par défaut l'article est visible par tout les visiteurs. Si un mot de passe est appliqué à l'article alors un formulaire est affiché au visiteur lui demandant le mot de passe et les url d'accès aux fichiers sont cryptées et non accessible directement.

Limitation
====
Testé sur PluXml 5.8.2

Pour le moment il est possible de définir un mot de passe uniquement pour les articles. Il est encore présent dans le code ce qui permet de définir un mot de passe pour les catégories et pages statiques mais tout est désactivé car non testé.

Configuration
====
Avant d'utiliser le plugin vous devez définir deux paramètres

    Masquer les articles des catégories protégées de la home page

N'affiche pas les articles protégés par un mot de passe sur la page d'accueil.

    Clé de cryptage utilisée pour la création des url des fichiers à télécharger

Par défaut PluXml utilise un salt pour crypter les url, afin d'augmenter la sécurité vous devez créer votre propre clé qui s'ajoutera au système de cryptage lors de génération des url des fichiers.

**ATTENTION : il n'y a aucun contrôle sur ce champ, il est juste limité à 72 caractères. Si vous le laissez vide alors aucune clé ne sera utilisée et seul le salt de PluXml sera utilisé.**

Dans le fichier article.php de votre thème ajouter :

    <?php eval($plxShow->callHook('displayLockdir', $plxShow->plxMotor->plxRecord_arts->f('lockdir'))); ?>

Usage
====
Lors de la rédaction d'un article deux champs supplémentaire sont disponible dans la barre latéral droite.

    Mot de passe
    Vous permet de définir le mot de passe de votre article

    Répertoire à lister
    Vous permet de choisir quel répertoire listé dans l'article

Lorsque le visiteur affichera l'article tout les fichiers présents dans le répertoire seront affiché sous forme de tableau, si se sont des images alors la vignette sera visible.

Changelog
====
* 1.4 - 28/04/2021
    * correction des Warning
    * correction suite à l'introduction de la durée de vie d'une session en 1.2
    
* 1.3 - 28/04/2021
    * Correction suite à la mise à jour 1.2

* 1.2 - 28/04/2021
    * Ajout d'une durée de vie de session. Si elle dépasse 5 mn alors le mot de passe est de nouveau demandé.

* 1.1 - 19/04/2021
    * Ajout de la gestion du répertoire par utilisateur.

* 1.0 - 14/04/2021
    * Mise à jour de la librairie [PasswordHash](http://www.openwall.com/phpass);
    * Ajout du champ *liste répertoire* dans rédaction article;

Remerciement
====
Ce plugin est basé sur lockArticles de [Rockyhorror](http://thepoulpe.net) et inspiré de kzDownload de [bazooka07](https://kazimentou.fr)
