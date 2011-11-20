<?php
use DoctrineExtensions\Versionable\IVersionable,
    Entities\Base\IAuditable;
    
class AuditPlugin extends Object
{
    public function postSave($instance)
    {
        izlog('[audit/plugin/postSave] Audit plugin called with instance of ('.get_class($instance).')');
        if ($instance instanceof IAuditable){izlog('auditable');}
        if ($instance instanceof IVersionable){izlog('versionable');}
        if (($instance instanceof IVersionable) && ($instance instanceof IAuditable))
        {
            $this->getManager('audit')->log($instance);
        }
    }
}
?>