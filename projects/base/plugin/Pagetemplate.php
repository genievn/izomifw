<?php
class PagetemplatePlugin extends Object
{
    public function postSite($action)
    {
        $this->getManager('page')->setPageTemplate($action);
        return $action;
    }
}
?>