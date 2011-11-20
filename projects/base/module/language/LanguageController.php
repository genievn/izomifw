<?php
class LanguageController extends Object {
	public function defaultCall(){		
		$render = object('izRender');
		//$render->setCountries($this->showCountry());
		$render->setLanguages($this->showLanguage());
		return $render;
	}
	
	public function showCountry(){
		$render = $this->getTemplate('country');
		$render->setCountries(config('locale.code'));
		$render->setCurrent(config('root.current_locale'));
		return $render;
	}
	
	public function showLanguage(){
		$render = $this->getTemplate( 'language' );
		$render->setLanguages( config( 'lang.code' ) );
		$render->setLocale( config( 'root.current_locale' ) );
		$render->setCurrent( config( 'root.current_lang' ) );		
		return $render;
	}
	
	public function country($code){
		$this->getManager('language')->updateLocaleLanguage($code,config('locale.lang.'.$code));
		Event::fire('redirect',config('root.uri')."language/");
	}
	
	public function language($code, $locale=null){		
		if(!$locale) $locale = config('root.default_locale');
		$this->getManager('language')->updateLocaleLanguage($locale,$code);
		Event::fire('redirect', config('root.uri')."language/");
	}
}
?>