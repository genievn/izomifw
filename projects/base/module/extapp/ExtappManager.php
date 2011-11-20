<?php
/*
 * class ExtappManager
 */

class ExtappManager extends Object{

    public function includeResources($method = null)
    {
        # extjs base 
        $this->getManager('izojs')->addLibExtJS($version='3.2',$footer=false);
        $this->getManager('izojs')->extjsBasex($extVersion='3.0', $footer = false);
        # debugging tool (for IE without Firebug)
        # $this->getManager( 'html' )->addJs( locale( 'jslibs/fauxconsole/fauxconsole.js', true ), false );
        # $this->getManager( 'html' )->addCss( locale( 'jslibs/fauxconsole/fauxconsole.css', true ));
        # extjs components
        $this->getManager( 'html' )->addJs( locale( 'jslibs/extjs/3-0/src/locale/ext-lang-'.config('root.current_lang').'.js', true ), false );
		$this->getManager( 'html' )->addJs( locale( 'jslibs/izojs/extjs/3-0/datetime/datetime.js', true ), true );
		$this->getManager( 'html' )->addJs( locale( 'jslibs/izojs/extjs/3-0/errormsgfield/errormsgfield.js', true ), true );
		$this->getManager( 'html' )->addJs( locale( 'jslibs/izojs/extjs/3-0/md5/md5.js', true ), true );
		$this->getManager( 'html' )->addJs( locale( 'jslibs/izojs/extjs/3-0/ajaxcache/ajaxcache.js', true ), true );
        $this->getManager( 'html' )->addJs( locale( 'jslibs/izojs/extjs/2-2/jsloader/jsloader.js', true ), true );
        $this->getManager( 'html' )->addJs( locale( 'jslibs/izojs/extjs/3-0/superboxselect/superboxselect.js', true ), false );
        $this->getManager( 'html' )->addCss( locale( 'jslibs/izojs/extjs/3-0/superboxselect/superboxselect.css', true ));
        $this->getManager( 'html' )->addJs( locale( 'jslibs/extjs/3-0/examples/ux/statusbar/StatusBar.js', true ), false );
        $this->getManager( 'html' )->addCss( locale( 'jslibs/extjs/3-0/examples/ux/statusbar/css/statusbar.css', true ));
        $this->getManager( 'html' )->addJs( locale( 'jslibs/izojs/extjs/3-0/tabscroller/TabScrollerMenu.js', true ), false );
        
        $this->getManager( 'html' )->addCss( locale( 'jslibs/izojs/extjs/3-0/tabscroller/tab-scroller-menu.css', true ));
        $this->getManager( 'html' )->addCss( locale( 'jslibs/izojs/extjs/3-0/alertbox/css/alertbox.css', true ));
        $this->getManager( 'html' )->addJs( locale( 'jslibs/izojs/extjs/3-0/alertbox/js/alertbox.js', true ), false );
        $this->getManager( 'html' )->addCss( locale( 'jslibs/izojs/extjs/3-0/treegrid/css/TreeGrid.css', true ));
        $this->getManager( 'html' )->addJs( locale( 'jslibs/izojs/extjs/3-0/treegrid/TreeGrid.js', true ), false );
        $this->getManager( 'html' )->addJs( locale( 'jslibs/izojs/extjs/3-0/rowcontext/rowcontext.js', true ), false );
        # add row expander
        $this->getManager( 'html' )->addJs( locale( 'jslibs/izojs/extjs/3-0/rowpanelexpander/rowpanelexpander.js', true ), false );
        $this->getManager( 'html' )->addJs( locale( 'jslibs/izojs/extjs/3-0/rowexpander/rowexpander.js', true ), false );
        
        $this->getManager( 'html' )->addJs( locale( 'jslibs/izojs/extjs/2-2/drawer/drawer.js', true ), false );
 		//$this->getManager( 'html' )->addJs( locale( 'jslibs/izojs/extjs/izomi-extjs.js', true ), false );
        $this->getManager('izojs')->extjsGridFilter();
        $this->getManager('izojs')->extjsExporter();
        $this->getManager('izojs')->extjsSwfUploadButton();
    }
}
?>
