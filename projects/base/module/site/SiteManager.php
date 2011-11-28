<?php
use Doctrine\Common\ClassLoader,
    Doctrine\ORM\Configuration,
    Doctrine\ORM\EntityManager,
    Doctrine\Common\Cache\ApcCache,
    Entity\User, Entity\Address;
    
class SiteManager extends Object
{
	private	$objectValidation = null;
	//Start coding from here
	public function importConfig($model=null)
	{
		# code...
		import("apps.base.module.site.admin.*");
	}
	
	public function newEmptyObject($model="SiteModel")
	{
		$object = object($model);
		return $this->addObjectValidation($object, $model);
	}
	
	public function addObjectValidation($object, $model)
	{
		if (!$this->objectValidation){
			$this->objectValidation = object('Validate');
			switch ($model) {
				case 'SiteInfoModel':
					$this->objectValidation->insertValidateRule('site_key', 'string', false, 200, 1);
					$this->objectValidation->insertValidateRule('site_value', 'string', false, 200, 1);					
					break;				
				default:
					# code...
					break;
			}
		}
		return $object->prototype($this->objectValidation);
	}
	
	
	public function getValidationMessage($model = null)
	{
		$message = array(
				'site_key'=>'<iz:lang id="site.site_key">Key should be string</iz:lang>',
				'site_value'=>'<iz:lang id="site.site_value">Value should be string</iz:lang>'
			);
			
		return $message;
	}

	public function test()
	{
		$doctrine = $this->getReader();
		#echo config('root.abs');
		$EntityClassLoader = new ClassLoader('Entity', config('root.abs'));
		#print_r($EntityClassLoader);
		$EntityClassLoader->register();
		$em = $doctrine->getEntityManager();
		
		$this->getManager('account')->loginAccount('admin', 'admin');
/*
		$account = new \Entity\Base\Account;
		$role = new \Entity\Base\Role;
		$role->setName('anonymous');
		$em->persist($role);
		$em->flush();
		
		#print_r($account);


		$tool = new \Doctrine\ORM\Tools\SchemaTool($em);
		$classes = array(
		  $em->getClassMetadata('\Entity\Base\Account'),
		  $em->getClassMetadata('\Entity\Base\Role')
		);
		#$tool->createSchema($classes);
*/
	}
}
?>