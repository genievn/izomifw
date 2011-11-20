<?php
class TreeManager extends Object
{
    public function saveTreeType($instance)
    {
        $em = $this->getWriter()->getEntityManager();
        
        try
        {
            $em->persist($instance);
            $em->flush();
            
            return $instance;
            
        }catch(Exception $e){
            Event::fire('logException', $e);
            return false;
        }     

    }
    
    public function existTreeType($filterArray)
    {
        if (empty($filterArray)) return false;
        
        $em = $this->getWriter()->getEntityManager();
        
        $i = $em->getRepository('Entities\Base\TreeType')->findOneBy($filterArray);
        
        if ($i) return $i; else return false;
    }
    
    
    
    public function getTreeType()
    {
        $em = $this->getWriter()->getEntityManager();
        
        $r = $em->createQuery("SELECT t FROM Entities\Base\TreeType t")->getArrayResult();
                
        return $r;
    }
        
    public function saveTreeNode($instance)
    {
        $em = $this->getWriter()->getEntityManager();
        
        try
        {
            $em->persist($instance);
            $em->flush();
            
            return $instance;
            
        }catch(Exception $e){
            Event::fire('logException', $e);
            return false;
        }     

    }
    
    public function existTreeNode($filterArray)
    {
        if (empty($filterArray)) return false;
        
        $em = $this->getWriter()->getEntityManager();
        
        $i = $em->getRepository('Entities\Base\TreeNode')->findOneBy($filterArray);
        
        if ($i) return $i; else return false;
    }
    
    public function getNodeForTypeId($id)
    {
        $em = $this->getWriter()->getEntityManager();
        
        if ($id)
            $r = $em->createQuery('SELECT u FROM Entities\Base\TreeNode u JOIN u.tree_type t WHERE t.id = '.$id.' ORDER BY u.sequence')->getResult();
        else
            $r = null;
        
        return $r;
    }
    
    public function getChildNode($id)
    {
        $em = $this->getWriter()->getEntityManager();
        
        if ($id)
            $r = $em->createQuery('SELECT u FROM Entities\Base\TreeNode u JOIN u.parent p WHERE p.id = '.$id.' ORDER BY u.sequence')->getResult();
        else
            $r = $em->createQuery('SELECT u FROM Entities\Base\TreeNode u JOIN u.parent p WHERE p.id = NULL ORDER BY u.sequence')->getResult();
        
        return $r;
    }
}
?>