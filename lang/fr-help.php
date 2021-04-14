<?php if(!defined('PLX_ROOT')) exit; ?>

<style>
h2	{ font-size: 2.0em; margin: .75em 0; color: #013162 } 
h3	{ font-size: 1.8em; margin: .83em 0; color: #13518C }
h4	{ font-size: 1.5em; margin: 1.12em 0; color: #2D7CC1 }
h5	{ font-size: 1.4em; font-style: italic; margin: 1.5em 0; color: #A4A4A4 } 
p	{ font-size: 1.2em; }
pre	{ font-size: 12px; }
</style>

<h2>Aide</h2>
<p>Fichier d'aide du plugin lockArticles</p>

<p>&nbsp;</p>
<h3>Installation</h3>
<p>Pensez &agrave; activer le plugin.<br/></p>

<p>&nbsp;</p>
<h3>Utilisation dans les articles</h3>
<p>
	Dans la page d'&eacute;dition de vos article, il y &agrave; un nouveau champs "Mot de passe" dans la sidebar.<br/>
	Indiquez y un mot de passe pour votre article.
</p>
<p>
	<b>Attention:</b> Pensez à mettre un chapo aux articles protégés avec un mot de passe.
</p>
<p>&nbsp;</p>

<h3>Utilisation dans les catégories</h3>
<p>Dans la page d'&eacute;dition de vos catégories, il y à un nouveau champs "Mot de passe".<br/>
	Indiquez y un mot de passe.<br/>
	Un mot de passe sera demandé pour tout article appartenant à une catégorie protégée.
</p>
<p>
	<b>Attention:</b> Les articles appartenant à une catégorie protégée, ne doivent avoir qu'une seule catégorie.
</p>
<p>&nbsp;</p>
<h3>Utilisation dans les pages statiques</h3>
<p>Dans la page d'&eacute;dition de vos pages statiques, il y à un nouveau champs "Mot de passe".<br/>
	Indiquez y un mot de passe.<br/>
	Un mot de passe sera demand&eacute; pour l'affichage de la page statique.
</p>
<h4>Affichage du cadenas.</h4>
<p>Editez le template "home.php" de votre thème. Ajoutez le code suivant à l'endroit o&ugrave; vous souhaitez voir apparaitre le cadenas:</p>
<pre>
	&lt;?php eval($plxShow->callHook('showIconIfLock')); ?&gt;
</pre>
<p>Cette fonction affiche un cadenas si l'article &agrave; un mot de passe.</p>
<p>Il vous faudra sans doute modifier &eacute;galement les fichiers: archives.php, categorie.php, tags.php</p>

