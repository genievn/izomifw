<?php
class BaseappManager extends Object
{
    private static $window = null;
    private static $status_bar = null;
    public function getWindow()
    {
        if (!$this->window)
        {
            $window_attrs = array(
                "title" => "IZOMI Framework Base Application"
                , "type" => "ExtWindow"
                , "width" => "800"
                , "height" => "600"
                , "closable" => true
                , "maximizable" => true
                , "layout" => "border"
            );

            $this->window = ExtFormFactory::createWindow($window_attrs);
        }

        return $this->window;
    }
    public function includeResources()
    {
        $this->getManager( 'html' )->addJs( locale( 'jslibs/izojs/extjs/3-0/iconmgr/iconmgr.js', true ), false );
        $this->getManager( 'html' )->addCss( locale( 'baseapp/css/baseapp.css', true ));
        return true;
    }
    public function getStatusBar()
    {

        if (!$this->status_bar)
        {

            $this->status_bar = ExtFormFactory::createToolbar();
        }
        return $this->status_bar;
    }
}
?>
