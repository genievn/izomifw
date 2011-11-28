<?php
define('_YUI_VERSION_', '2.5.2');

class IzojsManager extends Object
{
	public function addLib($name = 'dojo')
	{
		switch ($name) {
			case 'extjs':
				# code...
				$this->addLibExtJS();
				break;
			case 'dojo':
				$this->addLibDojo();
				break;
			case 'jquery':
				$this->addLibJquery();
			default:
				# code...
				break;
		}
	}
	// =====================
	// = EXTJS JAVASCRIPTS =
	// =====================
	/**
	 * undocumented function
	 *
	 * @return void
	 * @author user
	 **/
	public function addLibExtJS($version = '4',$footer = false)
	{
		$this->getManager( 'html' )->addJs( locale( 'jslibs/extjs/ext-all.js', true ), $footer );
		$this->getManager( 'html' )->addCss( locale( 'jslibs/extjs/resources/css/ext-all.css', true ));
		/**
		 * Setting this if using different themes
		 */
		//$this->getManager( 'html' )->addCss( locale( 'jslibs/extjs/resources/css/ext-all-notheme.css', true ));
		//$this->getManager( 'html' )->addCss( locale( 'jslibs/extjs/resources/css/xtheme-blue.css', true ), "ext-theme");

	}
	public function extjsLogin($footer = false)
	{
		$this->getManager( 'html' )->addJs( locale( 'jslibs/izojs/extjs/login.js', true ), $footer );
	}
	public function extjsGridFilter($extVersion = '3.0', $footer=false)
	{
		switch($extVersion){
			case '3.0':
				$this->getManager( 'html' )->addJs( locale( 'jslibs/izojs/extjs/3-0/gridfilter/ux/menu/EditableItem.js', true ), $footer );
				$this->getManager( 'html' )->addJs( locale( 'jslibs/izojs/extjs/3-0/gridfilter/ux/menu/RangeMenu.js', true ), $footer );
				#$this->getManager( 'html' )->addJs( locale( 'jslibs/izojs/extjs/3-0/gridfilter/ux/menu/ListMenu.js', true ), $footer );
				$this->getManager( 'html' )->addJs( locale( 'jslibs/izojs/extjs/3-0/gridfilter/ux/grid/GridFilters.js', true ), $footer );
				$this->getManager( 'html' )->addJs( locale( 'jslibs/izojs/extjs/3-0/gridfilter/ux/grid/filter/Filter.js', true ), $footer );
				$this->getManager( 'html' )->addJs( locale( 'jslibs/izojs/extjs/3-0/gridfilter/ux/grid/filter/StringFilter.js', true ), $footer );
				$this->getManager( 'html' )->addJs( locale( 'jslibs/izojs/extjs/3-0/gridfilter/ux/grid/filter/DateFilter.js', true ), $footer );
				$this->getManager( 'html' )->addJs( locale( 'jslibs/izojs/extjs/3-0/gridfilter/ux/grid/filter/ListFilter.js', true ), $footer );
				$this->getManager( 'html' )->addJs( locale( 'jslibs/izojs/extjs/3-0/gridfilter/ux/grid/filter/NumericFilter.js', true ), $footer );
				$this->getManager( 'html' )->addJs( locale( 'jslibs/izojs/extjs/3-0/gridfilter/ux/grid/filter/BooleanFilter.js', true ), $footer );
				$this->getManager( 'html' )->addCss( locale( 'jslibs/izojs/extjs/3-0/gridfilter/resources/style.css', true ));
				break;
			default:
				$this->getManager( 'html' )->addJs( locale( 'jslibs/izojs/extjs/2-2/gridfilter/ux/menu/EditableItem.js', true ), $footer );
				$this->getManager( 'html' )->addJs( locale( 'jslibs/izojs/extjs/2-2/gridfilter/ux/menu/RangeMenu.js', true ), $footer );
				$this->getManager( 'html' )->addJs( locale( 'jslibs/izojs/extjs/2-2/gridfilter/ux/menu/ListMenu.js', true ), $footer );
				$this->getManager( 'html' )->addJs( locale( 'jslibs/izojs/extjs/2-2/gridfilter/ux/grid/GridFilters.js', true ), $footer );
				$this->getManager( 'html' )->addJs( locale( 'jslibs/izojs/extjs/2-2/gridfilter/ux/grid/filter/Filter.js', true ), $footer );
				$this->getManager( 'html' )->addJs( locale( 'jslibs/izojs/extjs/2-2/gridfilter/ux/grid/filter/StringFilter.js', true ), $footer );
				$this->getManager( 'html' )->addJs( locale( 'jslibs/izojs/extjs/2-2/gridfilter/ux/grid/filter/DateFilter.js', true ), $footer );
				$this->getManager( 'html' )->addJs( locale( 'jslibs/izojs/extjs/2-2/gridfilter/ux/grid/filter/ListFilter.js', true ), $footer );
				$this->getManager( 'html' )->addJs( locale( 'jslibs/izojs/extjs/2-2/gridfilter/ux/grid/filter/NumericFilter.js', true ), $footer );
				$this->getManager( 'html' )->addJs( locale( 'jslibs/izojs/extjs/2-2/gridfilter/ux/grid/filter/BooleanFilter.js', true ), $footer );
				$this->getManager( 'html' )->addCss( locale( 'jslibs/izojs/extjs/2-2/gridfilter/css/gridfilter.css', true ));
				break;
		}

	}

	public function extjsExporter($extVersion = '3.0', $footer = false)
	{
		switch($extVersion){
			case '3.0':
				$this->getManager( 'html' )->addJs( locale( 'jslibs/izojs/extjs/3-0/exporter/Exporter-all.js', true ), $footer );
				break;
		}
	}

	public function extjsSwfUploadButton($extVersion = '3.0', $footer = false)
	{
		switch($extVersion){
			case '3.0':
				$this->getManager( 'html' )->addJs( locale( 'jslibs/swfupload/2-5/swfupload.js', true ), $footer );
				$this->getManager( 'html' )->addJs( locale( 'jslibs/izojs/extjs/3-0/swfuploadbtn/swfbtn.js', true ), $footer );
				$this->getManager( 'html' )->addJs( locale( 'jslibs/swfupload/2-5/plugins/swfupload-queue.js', true ), $footer );
				break;
		}
	}

	public function extjsSwfUploadPanel($extVersion = '3.0', $footer = false)
	{
		switch($extVersion){
			case '3.0':
				$this->getManager( 'html' )->addJs( locale( 'jslibs/swfupload/2-5/swfupload.js', true ), $footer );
				$this->getManager( 'html' )->addJs( locale( 'jslibs/izojs/extjs/3-0/swfuploadpanel/UploadPanel.js', true ), $footer );
				break;
		}
	}

	public function extjsBasex($extVersion = '3.0', $footer = false)
	{
		switch($extVersion){
			case '3.0':
				$this->getManager( 'html' )->addJs( locale( 'jslibs/izojs/extjs/3-0/basex4jit/ext-basex.js', true ), $footer );
				$this->getManager( 'html' )->addJs( locale( 'jslibs/izojs/extjs/3-0/basex4jit/jit.js', true ), $footer );
				break;
		}
	}
	/**
	 * undocumented function
	 *
	 * @return void
	 * @author user
	 **/
	public function extjsDesktop($version = '1.0', $footer = false)
	{
		switch($version){
			case '1.0':
				$this->getManager( 'html' )->addJs( locale( 'jslibs/izojs/extjs/2-2/desktop/system/dialogs/colorpicker/ColorPicker.js', true ), $footer );
				$this->getManager( 'html' )->addJs( locale( 'jslibs/izojs/extjs/2-2/desktop/system/core/App.js', true ), $footer );
				$this->getManager( 'html' )->addJs( locale( 'jslibs/izojs/extjs/2-2/desktop/system/core/Desktop.js', true ), $footer );
				$this->getManager( 'html' )->addJs( locale( 'jslibs/izojs/extjs/2-2/desktop/system/core/HexField.js', true ), $footer );
				$this->getManager( 'html' )->addJs( locale( 'jslibs/izojs/extjs/2-2/desktop/system/core/Module.js', true ), $footer );
				$this->getManager( 'html' )->addJs( locale( 'jslibs/izojs/extjs/2-2/desktop/system/core/Notification.js', true ), $footer );
				$this->getManager( 'html' )->addJs( locale( 'jslibs/izojs/extjs/2-2/desktop/system/core/Shortcut.js', true ), $footer );
				$this->getManager( 'html' )->addJs( locale( 'jslibs/izojs/extjs/2-2/desktop/system/core/StartMenu.js', true ), $footer );
				$this->getManager( 'html' )->addJs( locale( 'jslibs/izojs/extjs/2-2/desktop/system/core/TaskBar.js', true ), $footer );

				$this->getManager( 'html' )->addCss( locale( 'jslibs/izojs/extjs/2-2/desktop/resources/css/desktop.css', true ));
				$this->getManager( 'html' )->addCss( locale( 'jslibs/izojs/extjs/2-2/desktop/system/dialogs/colorpicker/colorpicker.css', true ));
				$this->getManager( 'html' )->addCss( locale( 'jslibs/izojs/extjs/2-2/desktop/system/modules/qo-preferences/qo-preferences.css', true ));
				$this->getManager( 'html' )->addCss( locale( 'jslibs/izojs/extjs/2-2/desktop/system/modules/grid-win/grid-win.css', true ));
				$this->getManager( 'html' )->addCss( locale( 'jslibs/izojs/extjs/2-2/desktop/system/modules/tab-win/tab-win.css', true ));
				$this->getManager( 'html' )->addCss( locale( 'jslibs/izojs/extjs/2-2/desktop/system/modules/acc-win/acc-win.css', true ));
				$this->getManager( 'html' )->addCss( locale( 'jslibs/izojs/extjs/2-2/desktop/system/modules/layout-win/layout-win.css', true ));
				$this->getManager( 'html' )->addCss( locale( 'jslibs/izojs/extjs/2-2/desktop/system/modules/bogus/bogus-win/bogus-win.css', true ));
				break;
			default: break;
		}
	}

	public function addLibDraw2d($version = '0.9.14', $footer = false)
	{
		switch($version)
		{
			case '0.9.14':
				$this->getManager( 'html' )->addJs( locale( 'jslibs/draw2d/0-9-14/wz_jsgraphics.js', true ), $footer );
				$this->getManager( 'html' )->addJs( locale( 'jslibs/draw2d/0-9-14/mootools.js', true ), $footer );
				$this->getManager( 'html' )->addJs( locale( 'jslibs/draw2d/0-9-14/moocanvas.js', true ), $footer );
				$this->getManager( 'html' )->addJs( locale( 'jslibs/draw2d/0-9-14/draw2d.js', true ), $footer );
				break;
			default:
				break;
		}
	}

	public function addLibCanviz($version = '', $footer = false)
	{
		switch($version)
		{
			default:
				$this->getManager( 'html' )->addJs( locale( 'jslibs/canviz/prototype/prototype.js', true ), $footer );
				$this->getManager( 'html' )->addJs( locale( 'jslibs/canviz/path/path.js', true ), $footer );
				$this->getManager( 'html' )->addJs( locale( 'jslibs/canviz/canviz.js', true ), $footer );
				$this->getManager( 'html' )->addCss( locale( 'jslibs/canviz/canviz.css', true ));
				break;
		}
	}
	
	// ===================
	// = YUI JAVASCRIPTS =
	// ===================

	public function addLibYui($version = '2.5.2', $footer = false)
	{
		switch($version){
			case '2.5.2':
				$this->getManager( 'html' )->addJs( locale( 'jslibs/yui/2.5.2/yahoo/yahoo-min.js', true ), $footer );
				$this->getManager( 'html' )->addJs( locale( 'jslibs/yui/2.5.2/event/event-min.js', true ), $footer );
				$this->getManager( 'html' )->addJs( locale( 'jslibs/yui/2.5.2/json/json-min.js', true ), $footer );
				$this->getManager( 'html' )->addJs( locale( 'jslibs/yui/2.5.2/connection/connection-min.js', true ), $footer );
				$this->getManager( 'html' )->addJs( locale( 'jslibs/yui/2.5.2/dom/dom-min.js', true ), $footer );
				$this->getManager( 'html' )->addJs( locale( 'jslibs/yui/2.5.2/yuiloader/yuiloader-beta-min.js', true ), $footer );
				$this->getManager( 'html' )->addJs( locale( 'jslibs/yui/2.5.2/selector/selector-beta-min.js', true ), $footer );
				$this->getManager( 'html' )->addJs( locale( 'jslibs/yui/2.5.2/container/container-min.js', true ), $footer );

				//$this->getManager( 'html' )->addJs( locale( 'jslibs/misc/json2.js', true ), $footer );
				//$this->getManager( 'html' )->addCss( locale( 'jslibs/yui/2.5.2/assets/skins/sam/skin.css', true ));
				$this->getManager( 'html' )->addCss( locale( 'jslibs/yui/2.5.2/container/assets/skins/sam/container.css', true ));
				break;
			default: break;
		}
	}

	public function helpController($footer = false)
	{
		$this->addLibYui(_YUI_VERSION_, $footer);
		//$this->getManager( 'html' )->addJs( locale( 'jslibs/yui/2.5.2/yahoo/yahoo-min.js', true ), true );
		//$this->getManager( 'html' )->addJs( locale( 'jslibs/yui/2.5.2/event/event-min.js', true ), true );
		//$this->getManager( 'html' )->addJs( locale( 'jslibs/yui/2.5.2/dom/dom-min.js', true ), true );
		$this->getManager( 'html' )->addJs( locale( 'jslibs/scriptaculous/lib/prototype.js', true ), true );
		$this->getManager( 'html' )->addJs( locale( 'jslibs/scriptaculous/src/scriptaculous.js', true ), true );
		$this->getManager( 'html' )->addJs( locale( 'jslibs/izojs/yui/helpcontroller.js', true ), true );
	}

	public function formSubmit($footer = false)
	{
		$this->addLibYui(_YUI_VERSION_,$footer);
		//$this->getManager( 'html' )->addJs( locale( 'jslibs/yui/2.5.2/yahoo/yahoo-min.js', true ), false );
		//$this->getManager( 'html' )->addJs( locale( 'jslibs/yui/2.5.2/event/event-min.js', true ), false );
		//$this->getManager( 'html' )->addJs( locale( 'jslibs/yui/2.5.2/json/json-min.js', true ), false );
		//$this->getManager( 'html' )->addJs( locale( 'jslibs/yui/2.5.2/connection/connection-min.js', true ), false );
		//$this->getManager( 'html' )->addJs( locale( 'jslibs/yui/2.5.2/dom/dom-min.js', true ), false );
		$this->getManager( 'html' )->addJs( locale( 'jslibs/izojs/yui/formsubmit.js', true ), $footer );
	}

	/**
	 * undocumented function
	 *
	 * @return void
	 * @author user
	 **/
	public function yuiDataTable($footer=false)
	{
		$this->getManager( 'html' )->addCss( locale( 'jslibs/yui/2.5.2/fonts/fonts-min.css', true ));
		//$this->getManager( 'html' )->addCss( locale( 'jslibs/yui/2.5.2/assets/skins/sam/datatable.css', true ));
		$this->getManager( 'html' )->addCss( locale( 'jslibs/yui/2.5.2/datatable/assets/datatable-core.css', true ));
		$this->getManager( 'html' )->addCss( locale( 'jslibs/yui/2.5.2/datatable/assets/skins/sam/datatable.css', true ));
		$this->getManager( 'html' )->addJs( locale( 'jslibs/yui/2.5.2/yahoo-dom-event/yahoo-dom-event.js', true ), $footer );
		$this->getManager( 'html' )->addJs( locale( 'jslibs/yui/2.5.2/element/element-beta-min.js', true ), $footer );
		$this->getManager( 'html' )->addJs( locale( 'jslibs/yui/2.5.2/datasource/datasource-beta-min.js', true ), $footer );

		$this->getManager( 'html' )->addJs( locale( 'jslibs/yui/2.5.2/datatable/datatable-beta-min.js', true ), $footer );
	}
	/**
	 * undocumented function
	 *
	 * @return void
	 * @author user
	 **/
	public function yuiTreeView($footer=false)
	{
		$this->getManager( 'html' )->addCss( locale( 'jslibs/yui/2.5.2/menu/assets/skins/sam/menu.css', true ));
		$this->getManager( 'html' )->addCss( locale( 'jslibs/yui/2.5.2/fonts/fonts-min.css', true ));
		$this->getManager( 'html' )->addCss( locale( 'jslibs/yui/2.5.2/treeview/assets/skins/sam/treeview.css', true ));

		$this->getManager( 'html' )->addJs( locale( 'jslibs/yui/2.5.2/treeview/treeview-min.js', true), $footer);
		$this->getManager( 'html' )->addJs( locale( 'jslibs/yui/2.5.2/menu/menu.js', true), $footer);


	}
	// =========================
	// = SHADOWBOX JAVASCRIPTS =
	// =========================
	public function addLibShadowbox($version = '2.0', $footer = false)
	{
		//$this->addLibYui();
		switch ($version){
			case '2.0':
				$this->getManager( 'html' )->addJs( locale( 'jslibs/yui/2.5.2/utilities/utilities.js', true ), $footer );
				$this->getManager( 'html' )->addJs( locale( 'jslibs/shadowbox/2.0/adapter/shadowbox-yui.js', true ), $footer );
				$this->getManager( 'html' )->addJs( locale( 'jslibs/shadowbox/2.0/shadowbox.js', true ), $footer );
				break;
			default:
				break;
		}
	}
	// ========================
	// = MOOTOOLS JAVASCRIPTS =
	// ========================
	/**
	 * undocumented function
	 *
	 * @return void
	 * @author
	 **/
	public function addLibMootools($version = '1.1', $footer = false)
	{
		if ($version === '1.1')
			$this->getManager( 'html' )->addJs( locale( 'jslibs/mootools/mootools.v1.11.js', true ), false );
		else
			$this->getManager( 'html' )->addJs( locale( 'jslibs/mootools/mootools-beta-1.2b2.js', true ), false );
	}
	// ======================
	// = JQUERY JAVASCRIPTS =
	// ======================
	public function addLibJquery($version = '1.2')
	{
		switch($version)
			{
				case "1.2":
					break;
				default:
					$this->getManager( 'html' )->addJs( locale( 'jslibs/jquery/1-2/jquery-1.2.6.pack.js', true ), false );
					break;
			}
	}
	public function jqueryFlow($version = '1.2', $footer = false)
	{
		switch($version){
			case "1.2":
				break;
			default:
				$this->getManager('html')->addJs(locale('jslibs/jquery/1-2/components/jflow/jquery.flow.1.2.min.js', true), false);
				break;
		}
	}

	public function jqueryForm($version = '2.4', $footer = false)
	{
		switch ($version) {
			case '2.4':
				$this->getManager('html')->addJs(locale('jslibs/jquery/1-2/components/jform/jquery.form.js', true), false);
				break;

			default:
				# code...
				break;
		}
	}
	/**
	 * undocumented function
	 *
	 * @return void
	 * @author
	 **/
	public function datePicker()
	{
		$this->getManager( 'html' )->addJs( locale( 'jslibs/mootools/DatePicker.js', true ), false );
		$this->getManager( 'html' )->addJs( locale( 'jslibs/izojs/datePicker.js', true ), false);
		$this->getManager( 'html' )->addCss( locale( 'jslibs/mootools/css/calendar.css', true ));
	}
	/**
	 * undocumented function
	 *
	 * @return void
	 * @author
	 **/
	public function tinymce()
	{
		$this->getManager( 'html' )->addJs( locale( 'jslibs/tinymce3/jscripts/tiny_mce/tiny_mce.js', true ), false );
	}
	/**
	 * undocumented function
	 *
	 * @return void
	 * @author
	 **/
	public function comboBoo()
	{
		$this->getManager( 'html' )->addJs( locale( 'jslibs/mootools/comboboo.js', true ), false);
		$this->getManager( 'html' )->addJs( locale( 'jslibs/izojs/comboBoo.js', true ), true);
		$this->getManager( 'html' )->addCss( locale( 'jslibs/mootools/css/comboboo.css', true ));
	}
	/**
	 * undocumented function
	 *
	 * @return void
	 * @author
	 **/
	public function mooflow()
	{
		//require mootools 1.12b1
		$this->getManager( 'html' )->addJs( locale( 'jslibs/mootools/mooflow.js', true ), false);
		$this->getManager( 'html' )->addJs( locale( 'jslibs/izojs/mooflow.js', true ), true);
		$this->getManager( 'html' )->addCss( locale( 'jslibs/mootools/css/mooflow.css', true ));
	}
	public function mooslider()
	{
		$this->getManager( 'html' )->addJs( locale( 'jslibs/mootools/mooslider.js', true ), false);
		$this->getManager( 'html' )->addJs( locale( 'jslibs/izojs/mooslider.js', true ), true);
		$this->getManager( 'html' )->addCss( locale( 'jslibs/mootools/css/slider.css', true ));
	}
	public function moosplitter()
	{
		//required mootools 1.11
		$this->getManager( 'html' )->addJs( locale( 'jslibs/mootools/moosplitter.js', true ), false);
		$this->getManager( 'html' )->addJs( locale( 'jslibs/izojs/moosplitter.js', true ), true);
		$this->getManager( 'html' )->addCss( locale( 'jslibs/mootools/css/splitter.css', true ));
	}
	public function moogrowl()
	{
		//required mootools 1.11
		$this->getManager( 'html' )->addJs( locale( 'jslibs/mootools/moogrowl.js', true ), false);
		$this->getManager( 'html' )->addJs( locale( 'jslibs/izojs/moogrowl.js', true ), false);
		$this->getManager( 'html' )->addCss( locale( 'jslibs/mootools/css/moogrowl.css', true ));

	}
	public function mooquee()
	{
		$this->getManager( 'html' )->addJs( locale( 'jslibs/mootools/mooquee.js', true ), false);
		$this->getManager( 'html' )->addJs( locale( 'jslibs/izojs/mooquee.js', true ), false);
		$this->getManager( 'html' )->addCss( locale( 'jslibs/mootools/css/mooquee.css', true ));
	}
	public function mooiscroll()
	{
		//current using mootools 1.11
		$this->getManager( 'html' )->addJs( locale( 'jslibs/mootools/mooiscroll.js', true ), false);
		$this->getManager( 'html' )->addJs( locale( 'jslibs/izojs/mooiscroll.js', true ), false);
		$this->getManager( 'html' )->addCss( locale( 'jslibs/mootools/css/mooiscroll.css', true ));
	}

	public function mooreflection()
	{
		//current using mootools 1.11
		$this->getManager( 'html' )->addJs( locale( 'jslibs/mootools/mooreflection.js', true ), false);
	}

	public function mooslimbox(){
		//current using mootools 1.11
		$this->getManager( 'html' )->addJs( locale( 'jslibs/mootools/mooslimbox.js', true ), false);
		$this->getManager( 'html' )->addJs( locale( 'jslibs/izojs/mooslimbox.js', true ), false);
		$this->getManager( 'html' )->addCss( locale( 'jslibs/mootools/css/mooslimbox.css', true ));
	}
	public function moomultibox(){
		//current using mootools 1.11
		$this->getManager( 'html' )->addJs( locale( 'jslibs/mootools/moomultibox.js', true ), false);
		$this->getManager( 'html' )->addJs( locale( 'jslibs/mootools/moooverlay.js', true ), false);
		$this->getManager( 'html' )->addJs( locale( 'jslibs/izojs/moomultibox.js', true ), false);
		$this->getManager( 'html' )->addCss( locale( 'jslibs/mootools/css/moomultibox.css', true ));
	}
	public function moosidebar(){
		//current using mootools 1.11
		$this->getManager( 'html' )->addJs( locale( 'jslibs/mootools/moosidebar.js', true ), false);
		$this->getManager( 'html' )->addJs( locale( 'jslibs/izojs/moosidebar.js', true ), false);
		$this->getManager( 'html' )->addCss( locale( 'jslibs/mootools/css/moosidebar.css', true ));
	}
	public function mooslidex(){
		//current using mootools 1.11
		$this->getManager( 'html' )->addJs( locale( 'jslibs/mootools/mooslidex.js', true ), false);
		$this->getManager( 'html' )->addJs( locale( 'jslibs/izojs/mooslidex.js', true ), false);
		$this->getManager( 'html' )->addCss( locale( 'jslibs/mootools/css/mooslidex.css', true ));
	}

	public function mooqscroller(){
		//current using mootools 1.2
		$this->getManager( 'html' )->addJs( locale( 'jslibs/mootools/mooqscroller.js', true ), false);
		$this->getManager( 'html' )->addJs( locale( 'jslibs/izojs/mooqscroller.js', true ), false);
		$this->getManager( 'html' )->addCss( locale( 'jslibs/mootools/css/mooqscroller.css', true ));
	}
	public function jquery_flexigrid(){
		//current using jquery 1.2.6
		$this->getManager( 'html' )->addJs( locale( 'jslibs/jquery/1.2/flexigrid.pack.js', true ), false);
		$this->getManager( 'html' )->addCss( locale( 'jslibs/jquery/1.2/css/flexigrid.css', true ));
	}
	// =====================================
	// = PURE JAVASCRIPT - TEMPLATE ENGINE =
	// =====================================
	public function addLibPure($version = '', $footer = false)
	{
		$this->getManager( 'html' )->addJs( locale( 'jslibs/pure/js/purePacked.js', true ), false);
	}
	// ========
	// = DOJO =
	// ========
	public function addLibDojo($version = '', $footer = false)
	{
		$this->getManager('html')->addJs(locale('jslibs/dojo/dojo/dojo.js', true), false);
		$this->getManager( 'html' )->addCss( locale( 'jslibs/dojo/dijit/themes/claro/claro.css', true ));
	}
}
?>