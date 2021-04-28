<?php
/**
 * Plugin lockArticlesAndFiles
 *
 * @version	1.3
 * @date	28/04/2021
 * @author	Rockyhorror, Flipflip
 **/

require("PasswordHash.php");

class lockArticlesAndFiles extends plxPlugin {

	public $path = '';
	public $dir = '';
	public $activeLockdir = '';
	public $img_supported = array('.png', '.gif', '.jpg', '.jpeg', '.bmp', '.webp'); # images formats supported
	public $img_exts = '/\.(jpe?g|png|gif|bmp|webp)$/i';
	public $doc_exts = '/\.(7z|aiff|asf|avi|csv|docx?|epub|fla|flv|gpx|gz|gzip|m4a|m4v|mid|mov|mp3|mp4|mpc|mpe?g|ods|odt|odp|ogg|pdf|pptx?|ppt|pxd|qt|ram|rar|rm|rmi|rmvb|rtf|svg|swf|sxc|sxw|tar|tgz|txt|vtt|wav|webm|wma|wmv|xcf|xlsx?|zip)$/i';

	/**
	 * Constructeur de la classe lockArticles
	 *
	 * @param	default_lang	langue par défaut utilisée par PluXml
	 * @return	null
	 * @author	Rockyhorror
	 **/
	public function __construct($default_lang) {

		# Appel du constructeur de la classe plxPlugin (obligatoire)
		parent::__construct($default_lang);

		# Autorisation d'acces à la configuration du plugins
		$this-> setConfigProfil(PROFIL_ADMIN);

		$this->path = $path;
		$this->dir = $dir;

		# Déclarations des hooks
		$this->addHook('plxMotorPreChauffageEnd', 'plxMotorPreChauffageEnd');
		$this->addHook('plxMotorDemarrageEnd', 'plxMotorDemarrageEnd');
		$this->addHook('plxFeedConstructLoadPlugins', 'plxFeedConstructLoadPlugins');
		$this->addHook('AdminArticleSidebar', 'AdminArticleSidebar');
		$this->addHook('plxAdminEditArticleXml','plxAdminEditArticleXml');
		$this->addHook('plxMotorParseArticle','plxMotorParseArticle');
		$this->addHook('plxShowConstruct', 'plxShowConstruct');
		$this->addHook('showIconIfLock','showIconIfLock');
		/*$this->addHook('AdminIndexTop', 'AdminIndexTop');
		$this->addHook('AdminIndexFoot', 'AdminIndexFoot');*/
		$this->addHook('plxFeedPreChauffageEnd','plxFeedPreChauffageEnd');
		$this->addHook('plxFeedDemarrageEnd','plxFeedDemarrageEnd');
		/*$this->addHook('AdminCategory','AdminCategory');
		$this->addHook('plxAdminEditCategoriesUpdate','plxAdminEditCategoriesUpdate');
		$this->addHook('plxAdminEditCategoriesNew','plxAdminEditCategoriesNew');
		$this->addHook('plxAdminEditCategoriesXml','plxAdminEditCategoriesXml');
		$this->addHook('plxAdminEditCategorie','plxAdminEditCategorie');
		$this->addHook('plxMotorGetCategories','plxMotorGetCategories');
		$this->addHook('AdminCategoriesTop','AdminCategoriesTop');
		$this->addHook('AdminCategoriesFoot','AdminCategoriesFoot');*/
		$this->addHook('AdminArticlePostData', 'AdminArticlePostData');
		$this->addHook('AdminArticleParseData', 'AdminArticleParseData');
		$this->addHook('AdminArticleInitData', 'AdminArticleInitData');
		/*$this->addHook('AdminStatic', 'AdminStatic');
		$this->addHook('plxAdminEditStatiquesXml', 'plxAdminEditStatiquesXml');
		$this->addHook('plxAdminEditStatiquesUpdate', 'plxAdminEditStatiquesUpdate');
		$this->addHook('plxAdminEditStatique', 'plxAdminEditStatique');
		$this->addHook('plxMotorGetStatiques', 'plxMotorGetStatiques');
		$this->addHook('AdminStaticsTop', 'AdminStaticsTop');
		$this->addHook('AdminStaticsFoot', 'AdminStaticsFoot');*/
		$this->addHook('displayLockdir', 'displayLockdir');

		$this->hasher = new PasswordHash(8, false);
	}

	public function plxFeedConstructLoadPlugins(){
		$this->FeedMode = true;
	}

	/**
	 * Méthode qui ajoute le champs 'mot de passe' dans les options des catégories
	 *
	 * @return	stdio
	 * @author	Rockyhorror
	 **/
	public function AdminCategory() {
		echo '<div class="grid">
				<div class="col sml-12">
					<label for="id_password">'.$this->getlang('L_PASSWORD_FIELD_LABEL').'&nbsp;:</label>
					<?php $image = "<img src=\"".PLX_PLUGINS."lockArticlesAndFiles/locker.png\" alt=\"\" />";
						if(!empty($plxAdmin->aCats[$id]["password"])){ echo $image; }
						plxUtils::printInput("password","","text","27-72"); ?>
					<label for="id_resetpassword">'.$this->getlang('L_RESETPASSWORD_FIELD').'&nbsp;<input type="checkbox" name="resetpassword" /></label>
					<input type="hidden" name="passwordhash" value="<?php echo plxUtils::strCheck($plxAdmin->aCats[$id]["password"]); ?>" />
				</div>
			</div>';
	}

	public function plxAdminEditCategoriesNew() {
		echo "<?php \$this->aCats[\$cat_id]['password'] = ''; ?>";
	}

	public function plxAdminEditCategoriesUpdate() {
		echo "<?php \$this->aCats[\$cat_id]['password']=(isset(\$this->aCats[\$cat_id]['password'])?\$this->aCats[\$cat_id]['password']:'') ?>";
	}

	public function plxAdminEditCategoriesXml() {
		echo "<?php \$xml .= '<password><![CDATA['.plxUtils::cdataCheck(\$cat['password']).']]></password>'; ?>";
	}

	public function plxAdminEditCategorie() {
		echo '<?php
			if(isset($content["resetpassword"])) {
				$this->aCats[$content["id"]]["password"] = "";
			}
			elseif(!empty($content["password"])) {
				$password = trim($content["password"]);
				if(strlen($password) > 72) { return plxMsg::Error("'.$this->getlang('L_TOO_LONG').'"); }
				$hash = $this->plxPlugins->aPlugins["lockArticlesAndFiles"]->hasher->HashPassword($password);
				if(strlen($hash) >= 20){
					$this->aCats[$content["id"]]["password"] = $hash;
				}
				else {
					return plxMsg::Error("'.$this->getlang('L_HASH_FAIL').'");
				}
			}
			else {
				if(!empty($content["passwordhash"])) {
					if(strlen($content["passwordhash"]) >= 20) {
						$this->aCats[$content["id"]]["password"] = $content["passwordhash"];
					}
					else {
						return plxMsg::Error("'.$this->getlang('L_HASH_FAIL').'");
					}
				}
			}
			?>';
	}

	/*
	 * Méthode qui récupère le mot de passe des catégories dans le fichier XML, enlève les articles
	 * des catégories avec mot de passe de la home page
	 *
	 * @return stdio
	 * @author Rockyhorror
	 *
	 */
	public function plxMotorGetCategories() {
		echo "<?php \$this->aCats[\$number]['password']=isset(\$iTags['password'])?plxUtils::getValue(\$values[\$iTags['password'][\$i]]['value']):''; ?>";
		if(($this->getParam('hide_l_categories') || isset($this->FeedMode)) && !defined('PLX_ADMIN')){
			echo "<?php if(!empty(\$this->aCats[\$number]['password']) && !isset(\$_SESSION['lockArticlesAndFiles']['categorie'][\$number])){
				foreach(\$arts as \$filename){
					\$artId = substr(\$filename, 0, 4);
					unset (\$this->plxGlob_arts->aFiles[\$artId]);
				}
			} ?>";
		}
	}

	/*
	 * Méthode qui démarrage la bufferisation des categories
	 *
	 * @return stdio
	 * @author Rockyhorror
	 *
	 */
	public function AdminCategoriesTop() {
		echo '<?php ob_start(); ?>';
	}

	/*
	 * Méthode qui ajoute le cadenas dans l'administration des categories
	 *
	 * @return stdio
	 * @author Rockyhorror
	 *
	 */
	public function AdminCategoriesFoot() {
		echo '<?php
				$content=ob_get_clean();
				if(preg_match_all("#<td>([0-9]{3})</td>#", $content, $capture)) {
					$image = "<img src=\"".PLX_PLUGINS."lockArticles/locker.png\" alt=\"\" />";
					foreach($capture[1] as $idCat) {
						if(!empty($plxAdmin->aCats[$idCat]["password"])) {
							$str = "<td>".$idCat;
							$content = str_replace($str, $str."&nbsp;".$image, $content);
						}
					}
				}
				echo $content;
			?>';
	}

	public function AdminArticlePostData () {
		echo '<?php $password = $_POST["password"]; ?>';
		echo '<?php $lockdir = $_POST["lockdir"]; ?>';
	}

	public function AdminArticleParseData () {
		echo '<?php $password = $result["password"]; ?>';
		echo '<?php $lockdir = $result["lockdir"]; ?>';
	}

	public function AdminArticleInitData () {
		echo '<?php $password = ""; ?>';
		echo '<?php $lockdir = ""; ?>';
	}

	private function myGetAllDirs($dir, $level=0) {
		# Initialisation
		$folders = array();
		$alldirs = scandir($dir);
		natsort($alldirs);

		foreach($alldirs as $folder) {
			if($folder[0] != '.') {
				if(is_dir(($dir!=''?$dir.'/':$dir).$folder)) {
					$dir = (substr($dir, -1)!='/' AND $dir!='') ? $dir.'/' : $dir;
					$path = str_replace($this->path, '',$dir.$folder);
					$folders[] = array(
							'level' => $level,
							'name' => $folder,
							'path' => $path
						);

					$folders = array_merge($folders, $this->myGetAllDirs($dir.$folder, $level+1) );
				}
			}
		}
		return $folders;
	}

	/**
	 * Méthode qui retourne la liste des fichiers d'un répertoire
	 *
	 * @param	dir		répertoire de lecture
	 * @return	array	tableau contenant la liste de tous les fichiers d'un dossier
	 * @author	Stephane F
	 **/
	private function _getDirFiles($dir) {

		$matches = '';

		$src = $this->path.$dir;
		if(!is_dir($src)) return array();

		$defaultSample = PLX_CORE.'admin/theme/images/file.png';
		$offset = strlen($this->path);
		$files = array();
		foreach(array_filter(
			glob($src.'*'),
			function($item) { return !preg_match('@\.tb\.\w+$@', $item); } # On rejette les miniatures
			) as $filename) {
				if(is_dir($filename)) { continue; }

				$thumbInfos = false;
				if(preg_match($this->img_exts, $filename, $matches)) {
					$thumbName = plxUtils::thumbName($filename);
					if(file_exists($thumbName)) {
						$thumbInfos = array(
							'infos' 	=> getimagesize($thumbName),
							'filesize'	=> filesize($thumbName)
						);
					}
					$sample = $this->path. '.thumbs/' .substr($filename, $offset);
					$sampleOk = (
						file_exists($sample) or
						plxUtils::makeThumb(
							$filename,
							$sample
							)
						);
					$imgSize = getimagesize($filename);
				} else {
					$imgSize = false;
				}
				$stats = stat($filename);
				$files[basename($filename)] = array(
					'.thumb'	=> (!empty($sampleOk)) ? $sample : $defaultSample,
					'name' 		=> basename($filename),
					'path' 		=> $filename,
					'date' 		=> $stats['mtime'],
					'filesize' 	=> $stats['size'],
					'extension'	=> '.' . strtolower(pathinfo($filename, PATHINFO_EXTENSION)),
					'infos' 	=> $imgSize,
					'thumb' 	=> $thumbInfos
				);
				$sample = '';
				$sampleOk = "";
			}

			ksort($files);
			return $files;
	}

	/**
	 * Liste le contenu du répertoire
	 */
	public function contentFolder() {
		$plxAdmin = plxAdmin::getInstance();

		if($plxAdmin->aConf['userfolders'] AND $_SESSION['profil']==PROFIL_WRITER) {
			$this->path = PLX_ROOT.$_SESSION['user'].'/';
		} else {
			$this->path = PLX_ROOT.$plxAdmin->aConf['medias'].'/';
		}
		$this->aDirs = (is_dir($this->path)?$this->myGetAllDirs($this->path):"");

		$str  = "\n".'<select class="no-margin" id="id_lockdir" size="1" name="lockdir">'."\n";
		$selected = (empty($this->$activeLockdir)?'selected="selected" ':'');
		$str .= '<option '.$selected.'value="">|. ('.L_PLXMEDIAS_ROOT.') &nbsp; </option>'."\n";
		# Dir non vide
		if(!empty($this->aDirs)) {
			foreach($this->aDirs as $k => $v) {
				$prefixe = '|&nbsp;';
				$i = 0;
				while($i < $v['level']) {
					$prefixe .= '_&nbsp;';
					$i++;
				}
				$selected = ($v['path']==$this->$activeLockdir?'selected="selected" ':'');
				$str .= '<option '.$selected.'value="'.$v['path'].'">'.$prefixe.$v['name'].'</option>'."\n";
			}
		}
		$str  .= '</select>'."\n";

		# On retourne la chaine
		return $str;
	}

	/**
	 * Méthode qui ajoute le champs 'Répertoire à lister' dans l'edition de l'article
	 *
	 * @return	stdio
	 * @author	Rockyhorror
	 **/
	public function AdminArticleSidebar() {
		echo '<div class="grid">
					<div class="col sml-12">
						<label for="id_password">'.$this->getlang('L_PASSWORD_FIELD_LABEL').'&nbsp;:</label>
						<?php $image = "<img src=\"".PLX_PLUGINS."lockArticlesAndFiles/locker.png\" alt=\"\" />";
						if(!empty($password)) { echo $image; } plxUtils::printInput("password","","text","27-72"); ?>
						<label for="id_resetpassword">'.$this->getlang('L_RESETPASSWORD_FIELD').'&nbsp;<input type="checkbox" name="resetpassword" />
						<input type="hidden" name="passwordhash" value="<?php echo $password; ?>" />
					</div>
					<div class="col sml-12">
						<?php $plxAdmin->plxPlugins->aPlugins["lockArticlesAndFiles"]->$activeLockdir = $lockdir; ?>
						<label for="dir_crypt">'.$this->getlang('L_DIR_CRYPT').'&nbsp;:</label>
						<?php echo $plxAdmin->plxPlugins->aPlugins["lockArticlesAndFiles"]->contentFolder(); ?>
					</div>
				</div>';
  }

	/*
	 * Méthode qui enregistre le mot de passe dans le fichier XML de l'article
	 *
	 * @return null
	 * @author Rockyhorror
	 *
	 */
	public function plxAdminEditArticleXml(){
		echo '<?php
		if(isset($content["resetpassword"])) {
			$xml .= "\t"."<password><![CDATA[]]></password>"."\n";
		}
		elseif(!empty($content["password"])) {
			$password = plxUtils::cdataCheck(trim($content["password"]));
			if(strlen($password) > 72) { return plxMsg::Error("'.$this->getlang('L_TOO_LONG').'"); }
			$hash = $this->plxPlugins->aPlugins["lockArticlesAndFiles"]->hasher->HashPassword($password);
			if(strlen($hash) >= 20){
				$xml .= "\t"."<password><![CDATA[".$hash."]]></password>"."\n";
			}
			else {
				return plxMsg::Error("'.$this->getlang('L_HASH_FAIL').'");
			}
		}
		else {
			if(!empty($content["passwordhash"])) {
				if(strlen($content["passwordhash"]) >= 20) {
					$xml .= "\t"."<password><![CDATA[".$content["passwordhash"]."]]></password>"."\n";
				}
				else {
					return plxMsg::Error("'.$this->getlang('L_HASH_FAIL').'");
				}
			}
		}

		if(!empty($content["lockdir"])) {
			$xml .= "\t"."<lockdir><![CDATA[".$content["lockdir"]."]]></lockdir>"."\n";
		} else {
			$xml .= "\t"."<lockdir><![CDATA[]]></lockdir>"."\n";
		}
		?>';
	}

	/*
	 * Méthode qui démarre la bufferisation de la liste des articles
	 *
	 * @return null
	 * @author Rockyhorror
	 *
	 */
	public function AdminIndexTop (){
		echo "<?php ob_start(); ?>";
	}

	/*
	 * Méthode qui ajoute le cadenas dans l'administration des articles
	 *
	 * @return stdio
	 * @authro Rockyhorror
	 *
	 */
	public function AdminIndexFoot () {
		echo '<?php
				$content=ob_get_clean();
				$image = "<img src=\"".PLX_PLUGINS."lockArticlesAndFiles/locker.png\" alt=\"\">";
				while($plxAdmin->plxRecord_arts->loop()) {
					$passwordhash = $plxAdmin->plxRecord_arts->f("password");
					if(!empty($passwordhash)) {
						$artId = $plxAdmin->plxRecord_arts->f("numero");
						$str = "<td>".$idArt;
						$content = str_replace($str, $str."&nbsp;".$image, $content);
					}
				}
				echo $content;
			?>';

	}

	/*
	 * Méthode qui récupère les informations de mot de passe dans le fichier XML de l'article
	 *
	 * @return null
	 * @author Rockyhorror
	 *
	 */
	public function plxMotorParseArticle(){
		echo "<?php    \$art['password'] = (isset(\$iTags['password']))?trim(\$values[ \$iTags['password'][0] ]['value']):''; ?>";
		echo "<?php    \$art['lockdir'] = (isset(\$iTags['lockdir']))?trim(\$values[ \$iTags['lockdir'][0] ]['value']):''; ?>";
	}

	/*
	 * Fonction qui ajoute le champs password dans l'administration des pages statiques
	 *
	 * @return stdio
	 * @author Rockyhorror
	 *
	 */
	public function AdminStatic() {

		echo '<div class="grid">
				<div class="col sml-12">
					<label for="id_password">'.$this->getlang('L_PASSWORD_FIELD_LABEL').'&nbsp;:</label>
					<?php $image = "<img src=\"".PLX_PLUGINS."lockArticles/locker.png\" alt=\"\" />";
						$password = $plxAdmin->aStats[$id][\'password\'];
						if(!empty($password)) { echo $image; } plxUtils::printInput("password","","text","27-72"); ?>
					<label for="id_resetpassword">'.$this->getlang('L_RESETPASSWORD_FIELD').'&nbsp;<input type="checkbox" name="resetpassword" /></label>
					<input type="hidden" name="passwordhash" value="<?php echo $password; ?>" />
				</div>
			</div>';
	}

	/*
	 * Méthode qui enregistre le mot de passe dans le fichier XML statiques.xml
	 *
	 * @return null
	 * @author Rockyhorror
	 *
	 */
	public function plxAdminEditStatiquesXml() {
		echo "<?php \$xml .= '<password><![CDATA['.plxUtils::cdataCheck(\$static['password']).']]></password>'; ?>";
	}

	/*
	 * Fonction qui gère la mise à jour de la liste des pages statiques.
	 *
	 * @return null
	 * @author Rockyhorror
	 *
	 */
	public function plxAdminEditStatiquesUpdate() {
		echo "<?php \$this->aStats[\$static_id]['password'] = (isset(\$this->aStats[\$static_id]['password'])?\$this->aStats[\$static_id]['password']:''); ?>";
	}

	/*
	 * Méthode qui prépare l'enregistrement du mot de passe de la page statique
	 *
	 * @return null
	 * @author Rockyhorror
	 *
	 */
	public function plxAdminEditStatique() {

		echo '<?php
				if(isset($content["resetpassword"])) {
					$this->aStats[$content["id"]]["password"] = "";
				}
				elseif(!empty($content["password"])) {
					$password = trim($content["password"]);
					if(strlen($password) > 72) { return plxMsg::Error("'.$this->getlang('L_TOO_LONG').'"); }
					$hash = $this->plxPlugins->aPlugins["lockArticlesAndFiles"]->hasher->HashPassword($password);
					if(strlen($hash) >= 20){
						$this->aStats[$content["id"]]["password"] = $hash;
					}
					else {
						return plxMsg::Error("'.$this->getlang('L_HASH_FAIL').'");
					}
				}
				else {
					if(!empty($content["passwordhash"])) {
						if(strlen($content["passwordhash"]) >= 20) {
							$this->aStats[$content["id"]]["password"] = $content["passwordhash"];
						}
						else {
							return plxMsg::Error("'.$this->getlang('L_HASH_FAIL').'");
						}
					}
				}
			?>';
	}

	/**
	 * Méthode qui permet de démarrer la bufférisation de sortie sur la page admin/statiques.php
	 *
	 * @return	stdio
	 * @author	Stephane F
	 **/
  public function AdminStaticsTop() {
		echo '<?php ob_start(); ?>';
  }

	/**
	 * Méthode qui affiche l'image du cadenas si la page est protégée par un mot de passe
	 *
	 * @return	stdio
	 * @author	Stephane F
	 **/
  public function AdminStaticsFoot() {
		echo '<?php
		$content=ob_get_clean();
		if(preg_match_all("#<td>([0-9]{3})</td>#", $content, $capture)) {
			$image = "<img src=\"".PLX_PLUGINS."lockArticles/locker.png\" alt=\"\" />";
			foreach($capture[1] as $idStat) {
				if(!empty($plxAdmin->aStats[$idStat]["password"])) {
					$str = "<td>".$idStat;
					$content = str_replace($str, $str."&nbsp;".$image, $content);
				}
			}
		}
		echo $content;
		?>';
  }

	/*
	 * Méthode qui récupère les informations de mot de passe dans le fichier statiques.xml
	 *
	 * @return null
	 * @author Rockyhorror
	 *
	 */
	public function plxMotorGetStatiques() {
		echo "<?php \$this->aStats[\$number]['password']=isset(\$iTags['password'])?plxUtils::getValue(\$values[\$iTags['password'][\$i]]['value']):''; ?>";
	}

	/*
	 * Méthode qui affiche un cadenas si l'article a un mot de passe
	 *
	 * @return	stdio
	 * @author	Rockyhorror
	 *
	 */
	public function showIconIfLock() {
		$plxMotor = plxMotor::getInstance();

		$passwordhash = $plxMotor->plxRecord_arts->f('password');
		if(!empty($passwordhash)){
			echo '<img src="'.PLX_PLUGINS.'lockArticlesAndFiles/locker.png" alt="locker icon" />';
		}
	}

	/**
	 * Méthode qui redefinie le mode de l'article
	 * et gère le téléchargement des fichiers
	 *
	 * @return	stdio
	 * @author	Rockyhorror, Flipflip
	 *
	 **/
  public function plxMotorPreChauffageEnd() {
		$plxMotor = plxMotor::getInstance();

		if($plxMotor->mode=='article') {
			// Téléchargement du fichier
			preg_match('/^article([0-9]+)\/([a-z0-9-]+)\/(dl)\/(.*)/', plxUtils::getGets(), $capture);
			// L'article est vérouillé par un mot de passe
			if($plxMotor->plxRecord_arts->f('password') == true) {
				if($_SESSION['lockArticlesAndFiles']['articles'][$plxMotor->cible] == true) {
					if(!empty($capture[3]) && $capture[3] === 'dl') {
						$file = plxEncrypt::decryptId($capture[4], $this->getParam('keyurl'));
						if(file_exists($file)) {
							header('Content-Description: File Transfer');
							header('Content-Type: application/force-download');
							header('Content-Type: application/octet-stream');
							header('Content-Type: application/download');
							header('Content-Disposition: attachment; filename='.basename($file));
							header('Content-Transfer-Encoding: binary');
							header('Expires: 0');
							header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
							header('Pragma: no-cache');
							header('Content-Length: '.filesize($file));
							readfile($file);
							exit();
						}
					}
				}
			} else {
				// L'article n'est pas vérouillé par un mot de passe
				if(!empty($capture[3]) && $capture[3] === 'dl') {
					$file = $capture[4];
					if(file_exists($file)) {
						header('Content-Description: File Transfer');
						header('Content-Type: application/force-download');
						header('Content-Type: application/octet-stream');
						header('Content-Type: application/download');
						header('Content-Disposition: attachment; filename='.basename($file));
						header('Content-Transfer-Encoding: binary');
						header('Expires: 0');
						header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
						header('Pragma: no-cache');
						header('Content-Length: '.filesize($file));
						readfile($file);
						exit();
					}
				}
			}

			$password = (($a = $plxMotor->plxRecord_arts->f('password')) == false )? "": plxUtils::getValue($a);
			if(!empty($password)) {
				if(!isset($_SESSION['lockArticlesAndFiles']['articles'][$plxMotor->cible])) {
					$plxMotor->mode = 'article_password';
				}
			}
			else {
				$cat_id = $plxMotor->plxRecord_arts->f('categorie');
				if(!empty($plxMotor->aCats[$cat_id]['password'])) {
					if(!isset($_SESSION['lockArticlesAndFiles']['categorie'][$cat_id])) {
						$plxMotor->mode = 'categorie_password';
					}
				}
			}
		}
		elseif($plxMotor->mode == 'categorie') {
			if(!empty($plxMotor->aCats[$plxMotor->cible]['password'])) {
				if(!isset($_SESSION['lockArticlesAndFiles']['categorie'][$plxMotor->cible])) {
					$plxMotor->mode = 'categories_password';
					$plxMotor->idCat = $plxMotor->cible;
				}
			}
		}
		elseif($plxMotor->mode == 'static') {
			if(!empty($plxMotor->aStats[$plxMotor->cible]['password'])) {
				if(!isset($_SESSION['lockArticlesAndFiles']['static'][$plxMotor->cible])){
					$plxMotor->mode = 'static_password';
					$plxMotor->idStat = $plxMotor->cible;
				}
			}
		}
	}

	/**
	 * Méthode qui valide le mot de passe
	 *
	 * @return	stdio
	 * @author	Rockyhorror
	 **/
	public function plxMotorDemarrageEnd() {
		$plxMotor = plxMotor::getInstance();

		$showForm = false;
		// Timer pour gérer la durée de vie d'une session
		if($plxMotor->mode == 'article' && !empty($plxMotor->plxRecord_arts->f('password'))) {
			if(!empty($_SESSION['lockArticlesAndFiles']['timer'])) {
				if((time() - $_SESSION['lockArticlesAndFiles']['timer']) > 300) {
					unset($_SESSION['lockArticlesAndFiles']);
					$_SESSION['lockArticlesAndFiles']['timer'] = time();
					$url = $plxMotor->urlRewrite('?article'.intval($plxMotor->plxRecord_arts->f('numero')).'/'.$plxMotor->plxRecord_arts->f('url'));
					header('Location: '.$url);
					exit;
				}
			} else {
				$_SESSION['lockArticlesAndFiles']['timer'] = time();
			}
		}
		if($plxMotor->mode == 'article_password') {
			$_SESSION['lockArticlesAndFiles']['timer'] = time();
			if(isset($_POST['lockArticlesAndFiles']) && isset($_POST['password'])) {
				$passwordhash = (($a = $plxMotor->plxRecord_arts->f('password')) == false )? "": plxUtils::getValue($a);
				$pw = strip_tags(substr($_POST['password'],0,72));
				if($this->hasher->CheckPassword($pw, $passwordhash)) {
					$_SESSION['lockArticlesAndFiles']['articles'][$plxMotor->cible] = True;
					$url = $plxMotor->urlRewrite('?article'.intval($plxMotor->plxRecord_arts->f('numero')).'/'.$plxMotor->plxRecord_arts->f('url'));
					header('Location: '.$url);
					exit;
				}
				else {
					$_SESSION['lockArticlesAndFiles']['error'] = $this->getlang('L_PLUGIN_BAD_PASSWORD');
				}
			}
			$showForm = true;
		}
			elseif($plxMotor->mode == 'categorie_password') {
				if(isset($_POST['lockArticlesAndFiles']) && isset($_POST['password'])) {
					$cat_id = $plxMotor->plxRecord_arts->f('categorie');
					$passwordhash = $plxMotor->aCats[$cat_id]['password'];
					$pw = strip_tags(substr($_POST['password'],0,72));
					if($this->hasher->CheckPassword($pw, $passwordhash)){
						$_SESSION['lockArticlesAndFiles']['categorie'][$cat_id] = true;
						$url = $plxMotor->urlRewrite('?article'.intval($plxMotor->plxRecord_arts->f('numero')).'/'.$plxMotor->plxRecord_arts->f('url'));
						header('Location: '.$url);
						exit;
					}
					else {
						$_SESSION['lockArticlesAndFiles']['error'] = $this->getlang('L_PLUGIN_BAD_PASSWORD');
					}
				}
				$showForm = true;

			}
			elseif($plxMotor->mode == 'categories_password') {
				if(isset($_POST['lockArticlesAndFiles']) && isset($_POST['password'])) {
					$passwordhash = $plxMotor->aCats[$plxMotor->cible]['password'];
					$pw = strip_tags(substr($_POST['password'],0,72));

					if ($this->hasher->CheckPassword($pw, $passwordhash)) {
						$_SESSION['lockArticlesAndFiles']['categorie'][$plxMotor->cible] = true;
						$url = $plxMotor->urlRewrite('?categorie'.intval($plxMotor->cible).'/'.$plxMotor->aCats[$plxMotor->cible]['url']);
						header('Location: '.$url);
						exit;
					}
					else {
						$_SESSION['lockArticlesAndFiles']['error'] = $this->getlang('L_PLUGIN_BAD_PASSWORD');
					}
				}
				$showForm = true;
			}
			elseif($plxMotor->mode == 'static_password') {
				if(isset($_POST['lockArticlesAndFiles']) && isset($_POST['password'])) {
					$passwordhash = $plxMotor->aStats[$plxMotor->cible]['password'];
					$pw = strip_tags(substr($_POST['password'],0,72));
					if($this->hasher->Checkpassword($pw, $passwordhash)) {
						$_SESSION['lockArticlesAndFiles']['static'][$plxMotor->cible] = true;
						$url = $plxMotor->urlRewrite('?static'.intval($plxMotor->cible).'/'.$plxMotor->aStats[$plxMotor->cible]['url']);
						header('Location: '.$url);
						exit;
					}
					else {
						$_SESSION['lockArticlesAndFiles']['error'] = $this->getlang('L_PLUGIN_BAD_PASSWORD');
					}
				}
				$showForm = true;
			}
			if($showForm) {
				$plxMotor->cible = '../../'.PLX_PLUGINS.'lockArticlesAndFiles/form';
				$plxMotor->template = 'static.php';
			}
	}

	/*
	 * Method qui affiche le formulaire
	 *
	 * @return stdio
	 * @author	Rockyhorror
	 */
	public function plxShowConstruct() {
		# infos sur la page statique
		$string  = "if(\$this->plxMotor->mode=='article_password' or \$this->plxMotor->mode=='categorie_password') {";
		$string .= "	\$array = array();";
		$string .= "	\$array[\$this->plxMotor->cible] = array(
			'name'		=>	\$this->plxMotor->plxRecord_arts->f('title'),
			'menu'		=> '',
			'url'		=> 'article_password',
			'readable'	=> 1,
			'active'	=> 1,
			'group'		=> ''
		);";
		$string .= "	\$this->plxMotor->aStats = array_merge(\$this->plxMotor->aStats, \$array);";
		$string .= "}";
		$string .= "elseif(\$this->plxMotor->mode=='categories_password') {";
		$string .= "	\$array = array();";
		$string .= "	\$array[\$this->plxMotor->cible] = array(
			'name'		=>	\$this->plxMotor->aCats[\$this->plxMotor->idCat]['name'],
			'menu'		=> '',
			'url'		=> 'article_password',
			'readable'	=> 1,
			'active'	=> 1,
			'group'		=> ''
		);";
		$string .= "	\$this->plxMotor->aStats = array_merge(\$this->plxMotor->aStats, \$array);";
		$string .= "}";
		$string .= "elseif(\$this->plxMotor->mode=='static_password') {";
		$string .= "	\$array = array();";
		$string .= "	\$array[\$this->plxMotor->cible] = array(
			'name'		=>	\$this->plxMotor->aStats[\$this->plxMotor->idStat]['name'],
			'menu'		=> '',
			'url'		=> 'article_password',
			'readable'	=> 1,
			'active'	=> 1,
			'group'		=> ''
		);";
		$string .= "	\$this->plxMotor->aStats = array_merge(\$this->plxMotor->aStats, \$array);";
		$string .= "}";
		echo "<?php ".$string." ?>";
	}

	/*
	 * Méthode qui enlève les liens RSS des articles des catégories avec mot de passe
	 *
	 * @return null
	 * @author Rockyhorror
	 *
	 */
	public function plxFeedPreChauffageEnd() {

		$plxFeed = plxFeed::getInstance();

		if ($plxFeed->mode == 'article') {
			$plxFeed->mode = 'article_password';
		}
	}

	/*
	 * Méthode qui enlève les liens RSS des articles avec mot de passe
	 *
	 * @return null
	 * @author Rockyhorror
	 *
	 */
	public function plxFeedDemarrageEnd() {
		$plxFeed = plxFeed::getInstance();

		if ($plxFeed->mode != 'article_password')
			return;

		if($plxFeed->plxRecord_arts) {
			$i = 0;
			while($plxFeed->plxRecord_arts->loop()) {
				$password = $plxFeed->plxRecord_arts->f('password');
				if(!empty($password)) {
					unset($plxFeed->plxRecord_arts->result[$plxFeed->plxRecord_arts->i]);
					$i++;
				}
			}
			$plxFeed->plxRecord_arts->size -= $i;
			$plxFeed->plxRecord_arts->result = array_values($plxFeed->plxRecord_arts->result);
		}
		echo '<?php $this->getRssArticles(); ?>';
	}

	/*
	 * Affiche la liste des fichiers contenu dans un répertoire
	 *
	 * @param $lockdir string : chemin d'accès au fichier
	 * @return $str string
	 * @author flipflip
	 */
	public function displayLockdir($lockdir) {
		global $plxMotor;

		if(!empty($lockdir)) {
			$id = intval($plxMotor->plxRecord_arts->f('numero'));
			$url = $plxMotor->urlRewrite('?article'.$id.'/'.$plxMotor->plxRecord_arts->f('url'));
			$files = $this->_getDirFiles($plxMotor->aConf['medias'].$lockdir.'/');
			$str = '<table class="table--zebra">
					<thead>
						<tr>
							<th>&nbsp;</th>
							<th>Fichier</th>
							<th>Taille</th>
							<th>Action</th>
						</tr>
					</thead>
					<tbody>';

					foreach ($files as $key => $value) {
						$str .= '<tr>';
						$isImage = in_array(strtolower($files[$key]['extension']), $this->img_supported);
						if($isImage) {
							$str .= '<td><img src="'.plxUtils::thumbName($files[$key]['path']).'"></td>';
						} else {
							$str .= '<td><img src="'.$files[$key]['.thumb'].'"></td>';
						}
						$str .= '<td>'.$files[$key]['name'].'</td>';
						if(!empty($plxMotor->plxRecord_arts->f('password'))) {
							$encrypt = plxEncrypt::encryptId($files[$key]['path'], $this->getParam('keyurl'));
						} else {
							$encrypt = $files[$key]['path'];
						}
						$str .= '<td>'.plxUtils::formatFilesize($files[$key]['filesize']).'</td>';
						$str .= '<td><a class="txtgrey" title="'.$files[$key]['name'].'" href="'.$url.'/dl/'.$encrypt.'"><i class="fas fa-download"></i></a></td>';
						$str .= '</tr>';
					}
			$str .= '</tbody></table>';

			echo $str;
		}
	}
}
?>
