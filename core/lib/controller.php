<?php
class izController extends Object {
	public function call ($method, $args=array()){
		
		/**
		 * If the methods list has not yet populated, we check its proto object
		 */
		//print_r($this->methods); 
		if (!$this->getMethods())
		{
			$list = get_class_methods($this->__protoObject());
			//print_r($list);
			$listHidden = get_class_methods($this);
			//print_r($listHidden);
			$list = array_diff($list, $listHidden); //Remove Object methods
			$methods = array();
			
			foreach ($list as $item){
				$methods[strtolower($item)] = $item;
			}
			
			$this->setMethods($methods);
		}
		//print_r($this->methods);
		
		
		if (!$this->isMethodCallable($method)||$method == 'call'){
			if (method_exists($this->__protoObject(), 'defaultCall')){
				$method = 'defaultCall';
			}else{
				$args = $method;
				$method = 'content';				
			}
		}
		else{
			$method = $this->getMethodName($method);
		}
		$module = $this->__protoObject();
		# work for PHP < 5.3
		# return call_user_func_array(array($module,$method),$args);
		# work for PHP >= 5.3
		return call_user_func_array(array($module,$method), ((is_array($args)) ? $args : array($args)));
	}
	
	/**
	 * Method 
	 *
	 * @param unknown_type $template
	 * @return unknown
	 */
	public function content($template='default'){
		//$template is a izRender object
		$template = $this->getTemplate($template);		
		if (!$template->getIzPath() && !config('root.development')){			
			Event::fire('httpPage',404);
		}		
		return $template;
	}
	
	private function isMethodCallable($method){
		//return isset($this->methods[strtolower($method)]);
		$methods = $this->getMethods();
		return isset($methods[strtolower($method)]);
	}
	
	private function getMethodName($method){
		//return $this->methods[strtolower($method)];
		$methods = $this->getMethods();
		return $methods[strtolower($method)];
	}
	
	/**
	 * Enter description here...
	 *
	 * @param String $name
	 * @param unknown_type $module
	 * @return izRender $template
	 */
	public function getTemplate($name, $module=null){		
		$template = object('izRender',object('izLocale'));				
		$template->setIzPath($this->getTemplatePath($name,$module));
		$template->setIzLocalePath($this->getLocalePath());
		return $template;
	}
	
	public function getTemplatePath($name, $module=null){
		$name = str_replace('.',DIRECTORY_SEPARATOR,$name);
		if (!$module){
			$module = $this->getModuleName();
		}else {
			$module = strtolower($module);
		}
		//echo 'Template file:'.$module.DIRECTORY_SEPARATOR.$name.'.html';
		return locale($module.DIRECTORY_SEPARATOR.$name.'.html');
	}
	
	//Path to the locale template of the module
	public function getLocalePath(){		
		return locale( $this->getModuleName().DIRECTORY_SEPARATOR, true );
	}
}
?>