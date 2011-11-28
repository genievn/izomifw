<?php
class TreeController extends Object
{
    public function getTreeType()
    {
        $render = $this->getTemplate('dummy');
        
        $m = $this->getManager('tree');
        
        $r = $m->getTreeType();
        
        $render->setTreeType($r);
        
        return $render;
    }
    
    public function createTreeType()
    {
        $render = $this->getTemplate('create_tree_type');
        
        return $render;
    }
    
    public function saveTreeType()
    {
        $r = $_REQUEST;
        $render = $this->getTemplate('dummy');
        $m = $this->getManager('tree');
        
        # check if the TreeType with codename has already existed
        if ($m->existTreeType(array('codename' => $r["codename"])))
        {
            $render->setSuccess(false);
            $render->setMsg('TreeType with codename already existed');
            return $render;
        }
        # otherwise, saving new instance
        $i = new Entity\Base\TreeType();
        $i->title = $r["title"];
        $i->codename = $r["codename"];
        $i->description = $r["description"];
        $i->uuid = new_uuid();
        
        $ret = $m->saveTreeType($i);
        
        if ($ret)
        {
            $render->setSuccess(true);
            $render->setMsg('TreeType saved successfully');
            $render->setRedirect('tree/createNodeForTypeId/'.$ret->id.'/');
            
        }else{
            $render->setSuccess(false);
            $render->setMsg('Error while saving, please check log file');
            
        }
        return $render;
    }
    
    
    
    public function createTreeNodeForTypeId($id)
    {
        $render = $this->getTemplate('create_tree_node');
        $m = $this->getManager('tree');
        
        $t = $m->existTreeType(array('id' => $id));
        $render->setTreeType($t);
        return $render;
    }
    
    
    public function saveTreeNode()
    {
        $render = $this->getTemplate('dummy');
        
        $r = $_REQUEST;
        
        
        $m = $this->getManager('tree');
        $em = $m->getWriter()->getEntityManager();
        
        # check if the TreeNode with codename has already existed
        if ($r["codename"] && $m->existTreeNode(array('codename' => $r["codename"])))
        {
            $render->setSuccess(false);
            $render->setMsg('TreeNode with codename already existed');
            return $render;
        }
        # find the TreeType
        if ((int)$r["tree_type_id"] > 0)
            $t = $m->existTreeType(array('id'=>$r["tree_type_id"]));
        
        # find the parent TreeNode
        if ((int)$r["parent_id"] > 0)
            $p = $m->existTreeNode(array('id'=>$r["parent_id"]));
        
        # otherwise, saving new instance
        $i = new Entity\Base\TreeNode();
        $i->title = $r["title"];
        if ($t) $i->codename = $t->codename;
        $i->description = $r["description"];
        $i->sequence = $r["sequence"];
        $i->uuid = new_uuid();
        if ($p) $i->parent_uuid = $m->uuid;
        $i->default_lang = config('root.current_lang');
        
        $ret = $m->saveTreeNode($i);
        
        if ($ret)
        {
            $em->beginTransaction();
            # associate TreeType with the TreeNode
            if ($t) {
                $ret->addTreeType($t);
                $em->persist($ret);
                $em->flush();
            }
            
            # associate parent with the TreeNode
            if ($p) {
                $ret->addParentNode($p);
                $em->persist($ret);
                $em->flush();
            }            
            $em->commit();
            $render->setSuccess(true);
            $render->setMsg('TreeNode saved successfully');
            $render->setRedirect('tree/listNodeForTypeId/'.$ret->id.'/');
            
        }else{
            $render->setSuccess(false);
            $render->setMsg('Error while saving, please check log file');
            
        }
        
        return $render;
    }
    
    public function viewTreeNodeByCodename($codename = null)
    {
        $render = $this->getTemplate('view_tree_node');
        $m = $this->getManager('tree');
        # find the TreeType
        $type = $m->existTreeType(array('codename'=>$codename));
        
        $render->setTreeType($type);
        
        return $render;
    }
    
    public function viewTreeNodeByTypeId($id = null)
    {
        $render = $this->getTemplate('view_tree_node');
        $m = $this->getManager('tree');
        # find the TreeType
        $type = $m->existTreeType(array('id'=>$id));
        
        $render->setTreeType($type);
        
        return $render;
    }
    /*
     * Because the TreePanel only take an JSON array, we need to provide a custom
     * template instead of using the JSON plugin
     */
    public function getTreeNodeByCodename($codename = null)
    {
        $render = $this->getTemplate('json_tree_node');
        
        
        $m = $this->getManager('tree');
        # find the TreeType
        $type = $m->existTreeType(array('codename'=>$codename));
        # get TreeNode for the TreeType
        if ($type) $nodes = $m->getNodeForTypeId($type->id);
        else $nodes = null;
        $render->setTreeNode($nodes);
        return $render;
    }
    
    public function reorderNode()
    {
        $render = $this->getTemplate('dummy');
        $r = $_REQUEST;
        $id = intval($r['node']);
        $delta = intval($r['delta']);
        $position = intval($r['position']);
        $oldPosition = intval($r['oldPosition']);
        
        $m = $this->getManager('tree');
        $em = $m->getWriter()->getEntityManager();
        // find the node
        $node = $m->existTreeNode(array('id'=>$id));
        // find all the children of the node
        if ($node->parent) $parentId = $node->parent->id; else $parentId = null;
        $children = $m->getChildNode($parentId);

        $sequence = 0;
        $em->beginTransaction();
        
        if ($delta < 0)
        {
            # move node up
            
            foreach ($children as $c)
            {
                if ($sequence >= $position && $sequence < $oldPosition)
                {
                    $c->sequence = $sequence + 1;
                    $em->persist($c);
                    $em->flush();
                }
                $sequence = $sequence + 1;
            }
            $node->sequence = $position;
            $em->persist($node);
            $em->flush();
        } elseif ($delta > 0) {
            foreach ($children as $c)
            {
                if ($sequence > $oldPostion && $sequence <= $position)
                {
                    $c->sequence = $sequence - 1;
                    $em->persist($c);
                    $em->flush();
                }
                $sequence = $sequence + 1;
            }
            $node->sequence = $position;
            $em->persist($node);
            $em->flush();
        }
        $em->commit();
        $render->setSuccess(true);
        return $render;
    }
    
    public function reparentNode()
    {
        $r = $_REQUEST;
        $render = $this->getTemplate('dummy');
        $id = intval($r['node']);
        $parentId = intval($r['parent']);
        $position = intval($r['position']);
        
        $m = $this->getManager('tree');
        $em = $m->getWriter()->getEntityManager();
        
        # find the current node
        $node = $m->existTreeNode(array('id'=>$id));
        
        # find the parent node
        $parent = $m->existTreeNode(array('id'=>$parentId));
        
        if ($node && $parent)
        {
            $node->addParentNode($parent);
            $node->sequence = $position;
            $em->beginTransaction();
            $em->persist($node);
            $em->flush();
            # select all the children of parent node, except the current node
            $children = $em->createQuery('SELECT u FROM Entity\Base\TreeNode u JOIN u.parent p WHERE p.id = '.$parentId.' AND u.id != '.$id.' ORDER BY u.sequence')->getResult();
            $sequence = 0;
            if (!empty($children))
            {
                foreach($children as $c)
                {
                    if ($sequence == $position) $sequence = $sequence + 1;
                    $c->sequence = $sequence;
                    $sequence = $sequence + 1;
                    $em->persist($c);
                    $em->flush();
                }
            }
            $em->commit();
            $render->setSuccess(true);            
        }elseif ($node && $parentId == 0 ){
            # it becomes the root node
            $node->removeParentNode();
            $m->saveTreeNode($node);
            $render->setSuccess(true);
        }else{
            $render->setSuccess(false);
        }
        return $render;
    
    }
}
?>