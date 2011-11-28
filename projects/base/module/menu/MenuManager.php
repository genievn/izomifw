<?php
class MenuManager extends Object
{
    public function registerTreeType($codename = 'base.tree.menu', $parent = null)
    {
        $em = $this->getWriter()->getEntityManager();
        # find the tree type menu
        $t = $em->getRepository('Entity\Base\TreeType')->findOneBy(array('codename'=>$codename));
        
        # if it doesn't exist create a new tree type
        if (!$t) {
            $t = new Entity\Base\TreeType();
            $t->title = 'Menu';
            $t->codename = $codename;
            $t->uuid = new_uuid();
        }
        
        if ($parent)
        {
            #find the parent
            $p = $em->getRepository('Entity\Base\TreeType')->findOneBy(array('codename'=>$parent));
            
            if ($p){
                $t->addParent($p);
            }
        }
        
        $em->persist($t);
        $em->flush();
        
        return $t;
    }
}
?>