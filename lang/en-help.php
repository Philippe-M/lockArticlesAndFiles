<?php if(!defined('PLX_ROOT')) exit; ?>

<style>
h2	{ font-size: 2.0em; margin: .75em 0; color: #013162 } 
h3	{ font-size: 1.8em; margin: .83em 0; color: #13518C }
h4	{ font-size: 1.5em; margin: 1.12em 0; color: #2D7CC1 }
h5	{ font-size: 1.4em; font-style: italic; margin: 1.5em 0; color: #A4A4A4 } 
p	{ font-size: 1.2em; }
pre	{ font-size: 12px; }
</style>

<h2>Help</h2>
<p>Plugin lockArticles help file</p>

<p>&nbsp;</p>
<h3>Installation</h3>
<p>Activate the plugin.<br/>
</p>

<p>&nbsp;</p>
<h3>Usage in articles</h3>
<p>
	In the edit page of your article, there is a new field "password" in the sidebar.
	indicate your password.
</p>
<p>
	<b>Carefull:</b> Add a Chapo to your protected articles.
</p>
<p>&nbsp;</p>
<h3>Usage in categories</h3>
<p>In the edit page of your categories, there is a new field "password".<br/>
	Indicate your password.<br/>
	A password would be ask for each article that belong to a protected category.
</p>
<p>
	<b>Carefull:</b>Articles that belong to a protected category, should have only one category
</p>

<p>&nbsp;</p>
<h3>Usage in static pages</h3>
<p>In the edit page of your static pages, there is a new field "password".<br/>
	Indicate a password.<br/>
	A password would be ask to view the static page.
</p>
<p>&nbsp;</p>
<h4>Padlock display.</h4>
<p>edit your template "home.php" from your theme. Add the following code where you want to show the padlock:</p>
<pre>
	&lt;?php eval($plxShow->callHook('showIconIfLock')); ?&gt;
</pre>
<p>This function displays a padlock if your article has a password.</p>
<p>You may want to also change the following files: archives.php, categorie.php, tags.php</p>

