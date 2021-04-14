<?php if(!defined('PLX_ROOT')) exit; ?>

<style>
h2	{ font-size: 2.0em; margin: .75em 0; color: #013162 } 
h3	{ font-size: 1.8em; margin: .83em 0; color: #13518C }
h4	{ font-size: 1.5em; margin: 1.12em 0; color: #2D7CC1 }
h5	{ font-size: 1.4em; font-style: italic; margin: 1.5em 0; color: #A4A4A4 } 
p	{ font-size: 1.2em; }
pre	{ font-size: 12px; }
</style>

<h2>Ajuda</h2>
<p>Fichièr d'ajuda de l'extension lockArticles</p>

<p>&nbsp;</p>
<h3>Installacion</h3>
<p>Pensatz a activar l'extension.<br/></p>

<p>&nbsp;</p>
<h3>Utilizacion dins los articles</h3>
<p>
	Dins la pagina de modificacion de vòstres articles, i a un novèl camps "Senhal" dins la barra de costat.<br/>
	Picatz-i un senhal per vòstre article.
</p>
<p>
	<b>Mèfi:</b> Pensatz a metre un chapo als articles protegits amb un senhal.
</p>
<p>&nbsp;</p>

<h3>Utilizacion dins las categorias</h3>
<p>Dins la pagina de modificacion de vòstras categorias, i a un novèl camps "Senhal".<br/>
	Picatz-i un senhal<br/>
	Un senhal serà demandada per tots los articles que son dins una categoria protegida.
</p>
<p>
	<b>Mèfi :</b> Los articles que son dins una categoria protegida, lor cal èsser que dins una categoria.
</p>
<p>&nbsp;</p>
<h3>Utilizacion dins las paginas estaticas</h3>
<p>Dins la pagina d'&eacute;dicion de vòstras paginas estaticas, i a un nòu camps "Senhal".<br/>
	Indicatz i un senhal.<br/>
	Un senhal serà demandat per l'afichatge de la pagina estatica.
</p>
<h4>Afichatge del cadenat.</h4>
<p>Modificatz lo modèl "home.php" de vòstre tèma. Ajutatz lo còdi seguent ont volètz veire lo cadenat :</p>
<pre>
	&lt;?php eval($plxShow->callHook('showIconIfLock')); ?&gt;
</pre>
<p>Aquesta fonccion aficha un cadenat se l'article ten un senhal.</p>
<p>Vos caldrà benlèu modificar tanben los fichièrs : archives.php, categorie.php, tags.php</p>
