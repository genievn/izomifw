<?php
use Entities\Base\Audit,
    Entities\Base\IContentManagable;

class AuditManager extends Object
{
    public function log($instance)
    {
        
        $em = $this->getWriter()->getEntityManager();
        
        $account = $this->getManager('auth')->getAccount();
        
        $audit = new Audit();
        $audit->entity = get_class($instance);
        $audit->entity_id = $instance->id;
        $audit->account_id = $account->id;
        $audit->account_username = $account->username;
        $audit->entity_version = $instance->version;
        
        if ($instance instanceof IContentManagable)
        {
            izlog('[audit/manager/auditManager] Audited instance is instance of IContentManagable');
            $audit->contentitem_id = $instance->contentitem->id;
        }
        $em->beginTransaction('AUDIT');
        try {
            $em->persist($audit);
            $em->flush();
            $em->commit('AUDIT');
        }catch(Exception $e)
        {
            $em->rollback('AUDIT');
        }
        
        
    }
}
?>