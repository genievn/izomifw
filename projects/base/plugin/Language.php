<?php
class LanguagePlugin extends Object {
	/**
	 * At this point the $action has not yet been populated
	 *
	 * @param unknown_type $action
	 */
	public function startUp($action){
        // try to load the locale language from session or cookie		
		$this->getManager('Language')->loadLocaleLanguage();
	}
	/**
	 * Capture the iz_lang parameter for changing the current locale
	 *
	 **/
	public function preSite($action)
	{
		$lang = @$_REQUEST['iz_lang'];
		if (!$lang) return $action;
		$this->getManager('Language')->updateLocaleLanguage($lang, $lang, $action);
		//$this->getManager('Language')->updateLocaleLanguage(config('root.current_locale'), $lang, $action);
	}
	/**
	 * If the virtual host is there then now it's time to apply the themes according to language
	 *
	 * @param unknown_type $action
	 */
	public function shutDown($action){
		//print_r($action);		
		$action->setContent($this->getManager('Language')->doLanguage($action));
	}
}
?>