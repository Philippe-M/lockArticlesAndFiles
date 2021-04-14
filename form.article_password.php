<?php if(!defined('PLX_ROOT')) exit; ?>
<?php
$plxMotor = plxMotor::getInstance();
$plxPlugin=$plxMotor->plxPlugins->getInstance('lockArticlesAndFiles');
if($plxMotor->mode == 'article_password') {
	$action = $plxMotor->urlRewrite('?article'.intval($plxMotor->plxRecord_arts->f('numero')).'/'.$plxMotor->plxRecord_arts->f('url'));
}
elseif($plxMotor->mode == 'categorie_password') {
	$action = $plxMotor->urlRewrite('?article'.intval($plxMotor->plxRecord_arts->f('numero')).'/'.$plxMotor->plxRecord_arts->f('url'));
}
elseif($plxMotor->mode == 'categories_password') {
	$action = $plxMotor->urlRewrite('?categorie'.intval($plxMotor->idCat).'/'.$plxMotor->aCats[$plxMotor->idCat]['url']);
}
elseif($plxMotor->mode == 'static_password') {
	$action = $plxMotor->urlRewrite('?static'.intval($plxMotor->idStat).'/'.$plxMotor->aStats[$plxMotor->idStat]['url']);
}

?>

	<div class="grid-1 txtcenter mbl" id="login">
		<h1><b><?php $plxPlugin->lang('L_PASSWORD_FIELD_LABEL') ?></b></h1>
	</div>

	<div class="grid-1 center bggrey-2 ptl prl pbl pll">
		<form action="<?php echo $action; ?>" method="post">
			<div class="grid-1 mbs has-gutter center">
				<div class="center mbl"><input type="password" name="password" size="12" maxlength="72" required></div>
				<div class="center"><input type="submit" value="Connexion" /></div>
				<input type="hidden" name="lockArticlesAndFiles">
			</div>
		</form>
		<?php
			if(isset($_SESSION['lockArticlesAndFiles']['error'])) {
				plxUtils::showMsg($_SESSION['lockArticlesAndFiles']['error'], 'alert--warning');
				unset($_SESSION['lockArticlesAndFiles']['error']);
			}
		?>
	</div>
