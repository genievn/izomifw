<?php
use Doctrine\Common\ClassLoader,
    Doctrine\ORM\Configuration,
    Doctrine\ORM\EntityManager,
    Doctrine\Common\Cache\ApcCache;
/**
 * APPLICATION + MODULE + MODEL BUILDER
 *
 * @package default
 * @author Thanh H. Nguyen
 */
class BuildController extends Object
{
	public function defaultCall()
	{
		return $this->home();
	}
	/**
	 * Control Panel of the Builder
	 *
	 * @return Render
	 * @author Thanh H. Nguyen
	 */
	public function home()
	{
		$this->getManager('build')->includeResources('home');
		$render = $this->getTemplate('build_home');
		$buildType = @$_REQUEST["type"];
		if (!$buildType) $buildType = "module";
		$render->setBuildType($buildType);
		return $render;
	}
	/**
	 * Load Extjs component based on the button
	 *
	 * @param string $button 
	 * @return Render
	 * @author Thanh H. Nguyen
	 */
	public function jsLoadButtonAction($button)
	{		
		switch ($button) {
			case 'app':
				$render = $this->getTemplate('js_app_comp');
				
				break;
			case 'module':
				if( !config( 'root.development' ) ) Event::fire( 'httpPage', 404 );
				
				$render = $this->getTemplate('js_module_comp');
				$render->setApp_folders(config('root.app_folders'));
				break;
			case 'model-config':
				$render = $this->getTemplate('js_model_config_comp');
				$render->setHostModules($this->getAllModules());
				$render->setAllModels($this->getAllModels());				
				break;
			default:
				# code...
				break;
		}
		$render->setCmpSuffixID(time());
		
		return $render;
	}
	/**
	 * undocumented function
	 *
	 * @return void
	 * @author user
	 **/
	public function project( $project=null, $error=false )
	{
		if( !config( 'root.development' ) ) Event::fire( 'httpPage', 404 );
		
		$validate = true;
		
		if( !$project )
		{
			$project = $this->getProjectFormValues();
			$validate = false;
		}
		
		$render = $this->getTemplate( 'project_form' );
		$render->setProject( $project );
		$render->setValidate( $validate );
		$render->setError( $error );
		
		return $render;
	}
	
	/**
	 * 
	 *
	 * @return void
	 * @author Thanh H. Nguyen
	 **/
	public function module($module=null,$error=false)
	{
		if( !config( 'root.development' ) ) Event::fire( 'httpPage', 404 );   
		$render = $this->getTemplate('module_form');
		$render->setApp_folders(config('root.app_folders'));
		return $render;
	}
	
	/**
	 * Get the module name and build the module structure
	 *
	 * @return void
	 * @author user
	 **/
	public function buildModule()
	{
		$module = $this->getModuleFormValues();
		$render = $this->getTemplate('json');
		$error = object('errors');
		
		if($module->isValid()){
			//check if the module name existed in the application
			if (!$this->isModuleExisted($module->getName())){
				
				$module_name = $module->getName();
				
				$fs = $this->getManager( 'files' );
				
				//module is not existed, create it
				$app_path = config('root.abs').$module->getModule_app().DIRECTORY_SEPARATOR;
				$lang_path = $app_path."lang".DIRECTORY_SEPARATOR;
				$locale_path = $app_path."locale".DIRECTORY_SEPARATOR;
				$module_path = $app_path."module".DIRECTORY_SEPARATOR;
				
				//create module folder
				mkdir($mpath = $module_path.$module_name, 0755, true );
				mkdir($mpath_model = $module_path.$module_name.DIRECTORY_SEPARATOR."model",0755,true);
				mkdir($mpath_admin = $module_path.$module_name.DIRECTORY_SEPARATOR."admin",0755,true);
				mkdir($mpath_admin = $module_path.$module_name.DIRECTORY_SEPARATOR."public",0755,true);
				//create locale folder
				
				mkdir($mlocale = $locale_path."all".DIRECTORY_SEPARATOR.$module_name, 0755, true);
				mkdir($mlocale.DIRECTORY_SEPARATOR."js", 0755, true);
				mkdir($mlocale.DIRECTORY_SEPARATOR."css", 0755, true);
				mkdir($mlocale.DIRECTORY_SEPARATOR."images", 0755, true);
				
				//create lang folder
				//mkdir($mlang_eng = $lang_path."eng".DIRECTORY_SEPARATOR.$module_name, 0755, true);
				mkdir($mlang_vie = $lang_path."vie".DIRECTORY_SEPARATOR.$module_name, 0755, true);
				
				
				//create module files
				$uc_module_name = ucfirst($module_name);
				$controller_content = "<?php
/**
 * {$uc_module_name} Controller
 *
 * @package {$uc_module_name}Controller
 * @author Thanh H. Nguyen
 */
class {$uc_module_name}Controller extends Object
{
	// ============================
	// = AUTO GENERATED FUNCTIONS =
	// ============================
	public function defaultCall(){
		\$render = \$this->getTemplate('default');
		return \$render;
	}
	public function admin(\$model = null){
		\$render = \$this->getTemplate('admin');
		\$render->setModel(\$model);
		return \$render;
	}
	
	// =============================
	// = YOUR OTHER FUNCTIONS HERE =
	// =============================
}
?>
";
				$fs->write( $mpath.DIRECTORY_SEPARATOR."{$uc_module_name}Controller.php", $controller_content );
				
				$manager_content = "<?php
/**
 * {$uc_module_name} Manager
 *
 * @package {$uc_module_name}Manager
 * @author Thanh H. Nguyen
 */
class {$uc_module_name}Manager extends Object
{
	// =============================
	// = AUTO GENERATED PROPERTIES =
	// =============================
	private \$objectValidation = null;
	
	// ============================
	// = AUTO GENERATED FUNCTIONS =
	// ============================
	/**
	 * Import the Model Configuration depends on mode (public|admin)
	 *
	 * @param string \$model 
	 * @param string \$mode 
	 * @return void
	 * @author Thanh H. Nguyen
	 */
	public function importConfig(\$model=null,\$mode=\"admin\"){
		switch (\$mode) {
			case 'public':
				import('".implode(".",explode(DIRECTORY_SEPARATOR, $module->getModule_app())).".module.$module_name.public.*');
				break;			
			default:
				import('".implode(".",explode(DIRECTORY_SEPARATOR, $module->getModule_app())).".module.$module_name.admin.*');
				break;
		}		
	}
	/**
	 * Create an empty object of the model
	 *
	 * @param string \$model 
	 * @return void
	 * @author Thanh H. Nguyen
	 */
	public function newEmptyObject(\$model='{$uc_module_name}Model'){
		\$object = object(\$model);
		return \$this->addObjectValidation(\$object,\$model);			
	}
	/**
	 * Add validation to object model
	 *
	 * @param string \$object 
	 * @param string \$model 
	 * @return Object
	 * @author Thanh H. Nguyen
	 */
	public function addObjectValidation(\$object, \$model){
		if(!\$this->objectValidation){
			\$this->objectValidation = object('Validate');
			switch (\$model) {
				case '{$uc_module_name}Model':
					# \$this->objectValidation->insertValidateRule('column_name', 'string', false, 200, 1);
					break;
				
				default:
					# code...
					break;
			}
		}
		return \$object->prototype(\$this->objectValidation);
	}
	// =============================
	// = YOUR OTHER FUNCTIONS HERE =
	// =============================
}
?>
";
				$fs->write( $mpath.DIRECTORY_SEPARATOR."{$uc_module_name}Manager.php", $manager_content );
				//create locale files
				$created_info = "
					<hr/>
					<br/>Module <strong>{$module_name}</strong> has been generated!!!
						<br/>Application: {$module->getModule_app()}
						<br/>Created:
						<br/><ul>
							<li><strong>Lang folder:</strong>{$mlang_vie}</li>
							<li><strong>Locale folder:</strong>{$mlocale}</li>
							<li><strong>Module folder:</strong>{$mpath}</li>
						</ul>
					";
					
				$admin_tpl_content = "
<iz:insert:module module=\"crud\" method=\"admin\" params=\"$module_name,<?php echo \$this->getModel();?>\"/>
				";	
				$fs->write( $mlocale.DIRECTORY_SEPARATOR."default.html","<iz:lang id=\"{$module_name}.hello\">Hello from {$module_name}</iz:lang><br/>{$created_info}");
				$fs->write( $mlocale.DIRECTORY_SEPARATOR."admin.html", $admin_tpl_content);
				//create lang files
				$fs->write( $mlang_vie.DIRECTORY_SEPARATOR.'lang.php', "<?php\nconfig( '.lang.{$module_name}.hello', 'Module {$uc_module_name} khởi tạo thành công' );\n?>" );
				
				
				$render->setSuccess(true);
				$data = object('Data');
				$data->setModule_name($module_name);
				$render->setData($data);
				//$error->setReason($module_path.$module->getName());
				//$render->setError($error);
				return $render;
			}else{
				$render->setSuccess(false);
				$error->setReason("Module is already existed, please choose another name");
				$render->setError($error);
				return $render;
			}
			
		}else{
			$error->setReason("The module you entered is not valid, please check it again");
			$render->setSuccess(false);
			$render->setError($error);
			return $render;
		}
	}
	public function buildProject()
	{
		$error = object('errors');
		$project = $this->getProjectFormValues();
		$render = $this->getTemplate('json');
		if( $project->isValid() )
		{
			if( $this->build( $project->getName(), $project->getDomain(), $project->getType() ) ){
				//Event::fire( 'redirect', config( 'root.url' ).'@'.$domain.'/');
				$render->setSuccess(true);
				$data = object('Data');
				$data->setUri(config('root.url').'@admin.'.$project->getDomain().'/');
				$data->setApp_name($project->getName());
				$render->setData($data);
				return $render;
			}
			else{
				$error->setReason("Application or Config existed ... please check again");
				$render->setSuccess(false);
				$render->setError($error);
				return $render;
			}
		}        
		$error = object('errors');
		$error->setReason("There is problem with application creation");
		$render->setSuccess(false);
		return $render;
		//return $this->project( $project, $error );
	}
	/**
	 * Build Public Model Configuration
	 *
	 * @return void
	 * @author Thanh H. Nguyen
	 */
	public function buildModelConfigPublic()
	{
		$error = object('errors');
		$modelConfig = $this->getModelConfigFormValues();
		$render = $this->getTemplate('json');
		if ($modelConfig->isValid()){
			$module = $modelConfig->getHost_module();
			$modelConfigName = $modelConfig->getModel_config_name();
			
			$moduleName = pathinfo($module);
			$moduleName = $moduleName["filename"];
			
			$file = $module.DIRECTORY_SEPARATOR."public".DIRECTORY_SEPARATOR.$modelConfigName."Config.php";
			
			if (!is_file($file)){
				# model config doesn't exist
				$fs = $this->getManager( 'files' );
				
				$content = "<?php
/**
 * AUTO MODEL PUBLIC CONFIGURATION
 *
 * @author Thanh H. Nguyen
 */
// ==========================
// = GENERAL CONFIGURATIONS =
// ==========================
config('.public.$moduleName.$modelConfigName.import.packages',array());
// ==========================
// = RETRIEVE CONFIGURATION =
// ==========================
config('.public.$moduleName.$modelConfigName.retrieve.pageSize',30);
config('.public.$moduleName.$modelConfigName.retrieve.url',array('module'=>'crudpublic','method'=>'retrieve','params'=>'$moduleName/$modelConfigName'));
config('.public.$moduleName.$modelConfigName.retrieve.columns',array(
	'id'=>array('type'=>'integer','title'=>'Id')
	#,'title'=>array('type'=>'string','title'=>'Title'))
	# Fields for display
	));
config('.public.$moduleName.$modelConfigName.retrieve.sortInfo',array('field'=>'id','dir'=>'ASC'));
?>";
				$fs->write($file, $content);
				$render->setSuccess(true);
				$render->setError(null);
				return $render;
			}else{
				$render->setSuccess(false);
				$render->setMessage('Public Config file existed');
				$render->setError($error);
				return $render;
			}
		}
		
	}
	
	public function buildModelConfig()
	{
		$modelConfig = $this->getModelConfigFormValues();
		
		if ($modelConfig->getIs_admin_model_config() == "on"){
			return $this->buildModelConfigAdmin();
		}
		if ($modelConfig->getIs_public_model_config() == "on"){
			return $this->buildModelConfigPublic();
		}
		
		$error = object('errors');
		$render = $this->getTemplate('json');
		$render->setSuccess(true);
		$render->setError(null);
		return $render;
	}
	/**
	 * Build Admin Model Configuration
	 *
	 * @return void
	 * @author Thanh H. Nguyen
	 */
	public function buildModelConfigAdmin()
	{
		$error = object('errors');
		$modelConfig = $this->getModelConfigFormValues();
		$render = $this->getTemplate('json');
		
		if ($modelConfig->isValid()){
			$module = $modelConfig->getHost_module();
			$modelConfigName = $modelConfig->getModel_config_name();
			
			$moduleName = pathinfo($module);
			$moduleName = $moduleName["filename"];
			
			$file = $module.DIRECTORY_SEPARATOR."admin".DIRECTORY_SEPARATOR.$modelConfigName."Config.php";
			
			if (!is_file($file)){
				# model config doesn't exist
				
				$fs = $this->getManager( 'files' );
				
				$content = "<?php
/**
 * AUTO MODEL ADMIN CONFIGURATION
 *
 * @author Thanh H. Nguyen
 */
// ==========================
// = GENERAL CONFIGURATIONS =
// ==========================
config('.admin.$moduleName.$modelConfigName.import.packages', array());
config('.admin.$moduleName.$modelConfigName.allow.users','');
config('.admin.$moduleName.$modelConfigName.allow.groups','');
config('.admin.$moduleName.$modelConfigName.deny.users','');
config('.admin.$moduleName.$modelConfigName.deny.groups','');
config('.admin.$moduleName.$modelConfigName.appearance.icon','');
config('.admin.$moduleName.$modelConfigName.appearance.title','<iz:lang id=\"\">$modelConfigName</iz:lang>');
// =======================
// = MODEL CONFIGURATION =
// =======================
config('.admin.$moduleName.$modelConfigName.model.behaviors.i18n', false);

// ===========================
// = RETRIEVE CONFIGURATIONS =
// ===========================
config('.admin.$moduleName.$modelConfigName.retrieve.pageSize',30);
config('.admin.$moduleName.$modelConfigName.retrieve.url',array('module'=>'crud','method'=>'retrieve','params'=>'$moduleName/$modelConfigName'));
config('.admin.$moduleName.$modelConfigName.retrieve.columns',array(
	'id'=>array('type'=>'integer','title'=>'Id')	
	#,'title'=>array('type'=>'string','title'=>'Title'))
	# Fields for display in grid
	));
config('.admin.$moduleName.$modelConfigName.retrieve.sortInfo',array('field'=>'id','dir'=>'ASC'));
config(
	'.admin.$moduleName.$modelConfigName.retrieve.filter',
	array(
		'id'=>array(
			'type'=>'numeric'
			)
		/*
		more filter here
		,'title'=>array(
			'type'=>'string'
			)
		*/	
		)
);
// ==================
// = TOOLBAR BUTTON =
// ==================
config(
	'.admin.$moduleName.$modelConfigName.retrieve.tbar',
	array(
		'add'=>array(
			'selectionMode'=>'none',
			'type'=>'one',
			'title'=>'<iz:lang id=\"common.add\">New</iz:lang>',
			'useCrud'=>true
			),
		'edit'=>array(
			'selectionMode'=>'single',
			'type'=>'one',
			'title'=>'<iz:lang id=\"common.edit\">Edit</iz:lang>',
			'useCrud'=>true
			),
		'delete'=>array(
			'selectionMode'=>'single|multiple',
			'type'=>'one',
			'title'=>'<iz:lang id=\"common.delete\">Delete</iz:lang>',
			'useCrud'=>true
			)
		/*	
		,'lang'=>array(
			'align'=>'->',
			'selectionMode'=>'none',
			'type'=>'lang',
			'title'=>'<iz:lang id=\"common.lang\">Languages</iz:lang>',
			'useCrud'=>false,
			'lang'=>array('en'=>'English','vi'=>'Vietnam')
			)*/
		)
	);
config(
	'.admin.$moduleName.$modelConfigName.retrieve.bbar',
	array(
		'paging'=>true,
		'status'=>true
		)
	);
// =========================
// = CREATE CONFIGURATIONS =
// =========================
config('.admin.$moduleName.$modelConfigName.create.url',array('module'=>'crud','method'=>'create','params'=>''));
config('.admin.$moduleName.$modelConfigName.create.form.validation',array('module'=>'crud','method'=>'isValidForm','params'=>'$moduleName/$modelConfigName'));
config(
	'.admin.$moduleName.$modelConfigName.create.form.columns.left.rows.1',
	array(
		'title'=>'Title of the form',
		'items'=>array(
			'id'=>array(
				'type'=>'pk',
				'form'=>array(
					'type'=>'hidden',
					'id'=>'id',
					'name'=>'id'
					)
				)
			/*	
			,'title'=>array(
				'type'=>'string',
				#'i18n'=>true,
				'form'=>array(
						'labelText'=>'Title',
						'defaultValue'=>'',
						'id'=>'title',
						'name'=>'title',
						'type'=>'textfield'
					)				
				)
			
			,'content'=>array(
				'type'=>'string',
				'i18n'=>true,
				'form'=>array(
						'labelText'=>'Content',
						'defaultValue'=>'',
						'id'=>'content',
						'name'=>'content',
						'type'=>'textfield'
					)
				)
			,'extra'=>array(
				'type'=>'string',
				'form'=>array(
						'labelText'=>'Extra',
						'defaultValue'=>'',
						'id'=>'extra',
						'name'=>'extra',
						'type'=>'combo',
						'option'=>array(
							'data'=>array(
								'0'=>'Choose a value',
								'1'=>'Paid'
								)
							)
					)
				)*/	
			)
		)
	);
 
?>";
				
				
				$fs->write($file, $content);
				$render->setSuccess(true);
				$render->setError(null);
				return $render;
				
			}else{
				$render->setSuccess(false);
				$error->setMessage('Config file existed');				
				$render->setError($error);
				return $render;
			}
		}
		
	}
	
	private function build( $name, $domain, $type )
	{
		$root = config( 'root.abs' ).DIRECTORY_SEPARATOR."projects".DIRECTORY_SEPARATOR.$name.DIRECTORY_SEPARATOR.'public'.DIRECTORY_SEPARATOR;
		$config = implode( DIRECTORY_SEPARATOR, array_reverse( explode( '.', $domain ) ) );
		$config = config( 'root.abs' ).DIRECTORY_SEPARATOR.config( 'root.config_folder' ).DIRECTORY_SEPARATOR.$config.DIRECTORY_SEPARATOR;
		
		if( is_dir( $root ) || is_dir( $config ) ) return false;
		
		mkdir( $lang = $root.'lang'.DIRECTORY_SEPARATOR."vie".DIRECTORY_SEPARATOR, 0777, true );
		mkdir( $locale = $root.'locale'.DIRECTORY_SEPARATOR.config( 'root.default_locale' ).DIRECTORY_SEPARATOR, 0777, true );
		mkdir( $modules = $root.'module'.DIRECTORY_SEPARATOR, 0777, true );
		mkdir( $database = $root.'database'.DIRECTORY_SEPARATOR.'orm'.DIRECTORY_SEPARATOR.'doctrine'.DIRECTORY_SEPARATOR, 0777, true);
		mkdir( $database.'fixtures'.DIRECTORY_SEPARATOR, 0777, true);
		mkdir( $database.'migrations'.DIRECTORY_SEPARATOR, 0777, true);
		mkdir( $database.'models'.DIRECTORY_SEPARATOR, 0777, true);
		mkdir( $database.'schema'.DIRECTORY_SEPARATOR, 0777, true);
		mkdir( $database.'sql'.DIRECTORY_SEPARATOR, 0777, true);
		
		mkdir( $config, 0777, true );
		mkdir( $config.'json', 0777, true );			#json output config folder
		mkdir( $config.'admin', 0777, true);			#admin region config folder
		mkdir( $config.'plain', 0777, true);			#raw output (without layout) config folder
		
		$fs = $this->getManager( 'files' );
		/*
		$fs->write( $lang.'lang.php', "<?php\nconfig( '.lang.default.hello', 'Hello from Lang' );\n?>" );
		$fs->write( $locale.'default.html', "<iz:lang id=\"default.hello\">Hello</iz:lang>" );
		$fs->write( $modules.'DefaultController.php', "<?php\nclass DefaultController extends Object\n{}\n?>" );
		*/
		$root_content = "<?php
/**
 * Auto-generated settings for {$domain}
 *
 * @author Thanh H. Nguyen
 */
// ==================================
// = COMMON SETTINGS - DON'T CHANGE =
// ==================================
config( '.root.plugins.100', '{$type}' );
config( '.root.response.json','@json.{$domain}/' );			//output to json
config( '.root.response.plain','@plain.{$domain}/' );		//output to raw template, without layout
config( '.root.app_folders.1', 'projects'.DIRECTORY_SEPARATOR.'default'.DIRECTORY_SEPARATOR.'public' );
config( '.root.app_folders.2', 'projects'.DIRECTORY_SEPARATOR.'{$name}'.DIRECTORY_SEPARATOR.'public' );
// ================================
// = DATABASE CONNECTION SETTINGS =
// ================================
config( '.root.data_reader', 'izDoctrine' );
config( '.root.data_reader_string', 'mysql://izomicmsnew:izomicmsnew@localhost/izomicmsnew' );
config( '.root.data_writer', config( 'root.data_reader'));
config( '.root.data_writer_string', config( 'root.data_reader_string'));
// ===========================
// = OTHER SPECIFIC SETTINGS =
// ===========================
config( '.layout.template','default');			//select the layout template for website
config( '.root.action.module','default');		//choose your default module for index page
config( '.root.action.method','');				//select default method for index page
config( '.root.action.params','');				//params for the default method
?>";
		$fs->write( $config.'root.php', $root_content );
		$fs->write( $config.'json'.DIRECTORY_SEPARATOR.'root.php', "<?php\nconfig( '.root.plugins.100', 'Json' );\n?>" );
		$fs->write( $config.'admin'.DIRECTORY_SEPARATOR.'root.php', "<?php\nconfig( '.layout.template','extjs-admin');\n?>" );
		$fs->write( $config.'plain'.DIRECTORY_SEPARATOR.'root.php', "<?php\nconfig( '.root.plugins.100','nolayout');\n?>" );
		
		
		return true;
	}
	/**
	 * Get submitted project value from clients
	 *
	 * @return void
	 * @author Thanh H. Nguyen
	 */
	private function getProjectFormValues()
	{
		$project = object( 'Project', object( 'Validate' ) );
		$project->setName( @$_REQUEST['app_name'] );
		$project->setDomain( @$_REQUEST['domain_name'] );
		$project->setType( 'Layout' );
		
		$project->insertValidateRule( 'name', 'chars', false, 100, 1 );
		$project->insertValidateRule( 'domain', 'domain', false, 255, 1 );
		//$project->insertValidateRule( 'type', 'chars', false, 20, 1 );
		
		return $project;
	}
	/**
	 * undocumented function
	 *
	 * @return void
	 * @author user
	 **/
	private function getModuleFormValues()
	{
		$module = object('Module',object('Validate'));
		$module->setName(strtolower(@$_REQUEST['module_name']));
		$module->setModule_app(@$_REQUEST['module_app']);
		
		$module->insertValidateRule( 'name', 'chars', false, 100, 1 );
		$module->insertValidateRule( 'module_app', 'string', false, 255, 1 );
		return $module;
	}
	
	/**
	 * Get form value for Model Configuration
	 *
	 * @return void
	 * @author Thanh H. Nguyen
	 */
	private function getModelConfigFormValues()
	{
		
		$modelConfig = object('ModelConfig',object('Validate'));
		$modelConfig->setHost_module(@$_REQUEST['host_module']);
		$modelConfig->setModel_config_name(@$_REQUEST['model_config_name']);
		$modelConfig->setIs_admin_model_config(@$_REQUEST['admin_model_config']);
		$modelConfig->setIs_public_model_config(@$_REQUEST['public_model_config']);
		
		
		$modelConfig->insertValidateRule('model_config_name','chars', false, 255, 1);
		return $modelConfig;
	}
	/**
	 * Check to see if the module exists in the current active app_folders
	 *
	 * @return void
	 * @author user
	 **/
	public function isModuleExisted($module_name)
	{
		foreach (config('root.app_folders') as $key => $value) {
			$app_module_folder = config('root.abs').$value;    		
			if(is_dir($app_module_folder.DIRECTORY_SEPARATOR."module".DIRECTORY_SEPARATOR.$module_name)){    			
				return true;	
			}    		
		}
		return false;
	}	
	
	public function getAllModules()
	{
		$entries = array();
		$modules = array();
		
		foreach (config('root.app_folders') as $key => $value) {
			
			$module_folder = config('root.abs').$value.DIRECTORY_SEPARATOR."module".DIRECTORY_SEPARATOR;
			
			$dir = dir($module_folder);
			while (false !== ($entry = $dir->read())) {
				$entries[] = $entry;
			}
			
			$dir->close();
			foreach ($entries as $entry) {
				$fullname = $module_folder . $entry;
				if ($entry != '.' && $entry != '.svn' && $entry != '..' && is_dir($fullname)) {
					$modules[] = $fullname;
				}
			}    		
		}
		return $modules;
	}
	
	public function getAllModels()
	{
		$entries = array();
		$models = array();
		$dirs = $this->getManager('doctrinecp')->getDoctrineDirs();
		foreach ($dirs as $d) {
			$modelPHP = $d["dir"].'models'.DIRECTORY_SEPARATOR;
			if (is_dir($modelPHP)){
				$dir = dir($modelPHP);
				while (false !== ($entry = $dir->read())) {
					$entries[] = $entry;
				}
				
				$dir->close();
				foreach ($entries as $entry) {
					$fullname = $modelPHP.$entry;
					
					if ($entry != '.' && $entry != '.svn' && $entry != '..') {
						# if it's a file
						if ( !is_dir($fullname)) {
							$pathParts = pathinfo($fullname);
							$models[] = $pathParts['filename'];
						}else if ($entry != 'packages'){
							# if it's a directory
							$pkgdir = dir($fullname);
							$pkgentries = array();
							# read thru the package
							while (false != ($pkgentry = $pkgdir->read())) {
								$pkgentries[] = $pkgentry;
							}
							# for each entries, if it's package file model, include it
							foreach ($pkgentries as $pkgentry) {
								if ($pkgentry != '.' && $pkgentry != '.svn' && $pkgentry != '..'){
									$fullpkgname = $fullname.DIRECTORY_SEPARATOR.$pkgentry;
									if (!is_dir($fullpkgname)){
										$pathParts = pathinfo($fullpkgname);
										$models[] = $pathParts['filename'];
									}
								}
							}
						}
						
						
					}
				}				
			}
		}
		return $models;
	}

	public function mapPhpAnnotationToDb($project)
	{		
		$manager = $this->getManager('build');
		$em = $manager->getReader()->getEntityManager();
		
		$EntityClassLoader = new ClassLoader('Entity', config('root.abs'));
		$EntityClassLoader->register();
		
		$tool = new \Doctrine\ORM\Tools\SchemaTool($em);
		$classes = array(
		  	$em->getClassMetadata('Entity\Base\Account'),
		  	$em->getClassMetadata('Entity\Base\Role')
		);
		$tool->createSchema($classes);
	}
}
?>