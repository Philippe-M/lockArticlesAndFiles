<?php
/**
 * Plugin lockArticlesAndFiles
 *
 * @package	PLX
 * @version	1.0
 * @date	12/04/2021
 * @author	Rockyhorror, Flipflip
 **/


if(!defined('PLX_ROOT')) exit;
	# Control du token du formulaire
	plxToken::validateFormToken($_POST);

	if(!empty($_POST)) {
		$plxPlugin->setParam('hide_l_categories', isset($_POST['hide_l_categories'])?1:0, 'numeric');
		$plxPlugin->setParam('keyurl', $_POST['keyurl'], 'cdata');
		$plxPlugin->saveParams();
		header('Location: parametres_plugin.php?p=lockArticlesAndFiles');
		exit;
	}
?>

<form class="inline-form" action="parametres_plugin.php?p=lockArticlesAndFiles" method="post">
	<fieldset>
		<p>
			<label for="hide_l_categories"><?php echo $plxPlugin->getLang('L_HIDE_LOCKED_CATEGORIES') ?> :</label>
			<input type="checkbox" name="hide_l_categories" value="true" <?php if($plxPlugin->getParam('hide_l_categories')) { echo 'checked="true"'; }?>/>
		</p>
		<p>
			<label for="keyurl">Clé de cryptage utilisée pour la création des url des fichiers à télécharger</label>
			<?php plxUtils::printInput('keyurl', $plxPlugin->getParam('keyurl'), 'text', '20-20'); ?>
		</p>
		<p class="in-action-bar">
			<?php echo plxToken::getTokenPostMethod() ?>
			<input type="submit" name="submit" value="<?php echo $plxPlugin->getLang('L_SAVE') ?>" />
		</p>
	</fieldset>
</form>
