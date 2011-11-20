<?php
class IzojsController extends Object
{
	// ====================
	// = JSON JAVASCRIPTS =
	// ====================

	public function addLibJson()
	{
		$this->getManager('izojs')->addLibJson();
	}
	// =====================
	// = EXTJS JAVASCRIPTS =
	// =====================
	public function addExtHelpers()
	{
		# site-context-dependent JS support file for Extjs
		$render = $this->getTemplate('izomi_extjs_helpers');
		return $render;
	}
	public function addLibExtJS($version = '3.0', $footer = false)
	{
		$this->getManager('izojs')->addLibExtJS($version, $footer);
	}
	/**
	 * undocumented function
	 *
	 * @return void
	 * @author user
	 **/
	public function extjsDesktop($version = '1.0', $footer = false)
	{
		$this->getManager('izojs')->extjsDesktop($version, $footer);
	}
	/**
	 * undocumented function
	 *
	 * @return void
	 * @author user
	 **/
	public function extjsLogin($footer = false)
	{
		$this->getManager('izojs')->extjsLogin($footer);
	}
	public function extjsGridFilter($version = '3.0', $footer = false)
	{
		# code...
		$this->getManager('izojs')->extjsGridFilter($version, $footer);
	}
	// ===================
	// = YUI JAVASCRIPTS =
	// ===================
    public function addLibYui($version = '2.5.2', $footer=false)
	{
	    $this->getManager('izojs')->addLibYui($version, $footer);
	}


    public function helpController()
    {
        $this->getManager( 'izojs' )->helpController();
    }

    public function formSubmit($footer = false)
    {
        $this->getManager( 'izojs' )->formSubmit($footer);
    }

    /**
     * undocumented function
     *
     * @return void
     * @author user
     **/
    public function yuiDataTable($footer = false)
    {
    	$this->getManager('izojs')->yuiDataTable($footer);
    }
    /**
     * undocumented function
     *
     * @return void
     * @author user
     **/
    public function yuiTreeView($footer = false)
    {
    	$this->getManager('izojs')->yuiTreeView($footer);
    }
	// ======================
	// = JQUERY JAVASCRIPTS =
	// ======================
    public function addLibJquery($version = '1.2', $footer = false)
    {
    	$this->getManager('izojs')->addLibJquery($version, $footer);
    }

    public function jqueryFlow($version = '1.2', $footer = false)
    {
    	$this->getManager('izojs')->jqueryFlow($version, $footer);
    }

    public function jqueryForm($version = '2.4', $footer = false)
    {
    	$this->getManager('izojs')->jqueryForm($version, $footer);
    }

    // ======================================
    // = PURE JAVASCRIPTS - TEMPLATE ENGINE =
    // ======================================
    public function addLibPure($version = '', $footer = false)
    {
    	$this->getManager('izojs')->addLibPure($version, $footer);
    }
    // =========================
    // = SHADOWBOX JAVASCRIPTS =
    // =========================
    public function addLibShadowbox($version = '2.0', $footer = false)
    {
    	$this->getManager('izojs')->addLibShadowbox($version, $footer);
    }
	// ========================
	// = MOOTOOLS JAVASCRIPTS =
	// ========================
    public function datePicker()
    {
    	$this->getManager( 'izojs')->datePicker();
    }


    /**
     * undocumented function
     *
     * @return void
     * @author
     **/
    public function addLibMootools($version = '1.1')
    {
    	$this->getManager( 'izojs' )->addLibMootools($version);
    }
    /**
     * undocumented function
     *
     * @return void
     * @author
     **/
    public function tinymce()
    {
    	$this->getManager( 'izojs' )->tinymce();
    }
    /**
     * undocumented function
     *
     * @return void
     * @author
     **/
    public function comboBoo()
    {
    	$this->getManager('izojs')->comboBoo();
    }
    /**
     * undocumented function
     *
     * @return void
     * @author
     **/
    public function mooflow()
    {
    	$this->getManager('izojs')->mooflow();
    }
    /**
     * undocumented function
     *
     * @return void
     * @author
     **/
    public function mooslider()
    {
    	$this->getManager('izojs')->mooslider();
    }

    public function moosplitter()
    {
    	$this->getManager('izojs')->moosplitter();
    }
    public function moogrowl()
    {
    	$this->getManager('izojs')->moogrowl();
    }
    public function mooquee()
    {
    	$this->getManager('izojs')->mooquee();
    }
    public function mooiscroll()
    {
    	$this->getManager('izojs')->mooiscroll();
    }
    public function mooreflection(){
    	$this->getManager('izojs')->mooreflection();
    }
    public function mooslimbox(){
    	$this->getManager('izojs')->mooslimbox();
    }
    public function moomultibox(){
    	$this->getManager('izojs')->moomultibox();
    }
    public function moosidebar(){
    	$this->getManager('izojs')->moosidebar();
    }
    public function mooslidex(){
    	$this->getManager('izojs')->mooslidex();
    }
    public function mooqscroller(){
    	$this->getManager('izojs')->mooqscroller();
    }
    public function jquery_flexigrid(){
    	//http://webplicity.net/flexigrid/
    	$this->getManager('izojs')->jquery_flexigrid();
    }
}
?>