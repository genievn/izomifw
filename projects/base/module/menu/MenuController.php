<?php

class MenuController extends Object
{
    /**
     * createMenu function.
     * 
     * @access public
     * @return void
     */
    public function createMenu()
    {
        $render = $this->getTemplate('create_menu');
        
        return $render;
    }
    
    public function saveMenu()
    {
        $render = $this->getTemplate('json');
        $em = $this->getManager('doctrine2')->getEntityManager();
        
        $m = new Entity\Base\NavigationMenu();
        $m->uuid = new_uuid();
        $m->title = $_REQUEST['title'];
        $m->codename = $_REQUEST['codename'];
        
        # get the parent menu
        $p = $em->find('Entity\Base\NavigationMenu', $_REQUEST['parent_id']);
        
        if ($p)
        {
            $m->addParent($p);
        }
        
        try {
            $em->beginTransaction();
            $em->persist($m);
            $em->flush();
            $em->commit();
            $render->setSuccess(true);
        }catch(Exception $e){
            $em->rollback();
            $render->setSuccess(false);
        }
        return $render;        
    }
    
    public function listMenu()
    {
        $render = $this->getTemplate('list_menu');
        
        return $render;
    }
    public function getMenu()
    {
        $render = $this->getTemplate('json');
        
        $em = $this->getManager('doctrine2')->getEntityManager();
        $dql = "SELECT u FROM Entity\Base\NavigationMenu u";
        $menus = $em->createQuery($dql)->getArrayResult();

        $render->setMenus($menus);
        
        return $render;
        
    }
    
    public function listMenuItemForMenuId($menu_id)
    {
        $render = $this->getTemplate('list_menu_item');
        $em = $this->getManager('doctrine2')->getEntityManager();
        $menu = $em->find('Entity\Base\NavigationMenu', $menu_id);
        $render->setMenu($menu);
        return $render;
    }
    
    public function saveMenuItem($id = null)
    {
        $em = $this->getManager('doctrine2')->getEntityManager();
        $render = $this->getTemplate('json');
        $em->beginTransaction();
        $m = $em->find('Entity\Base\NavigationMenu', (int)$_REQUEST['menu_id']);
        
        if (!$m)
        {
            $render->setSuccess(false);
            return $render;
        }
        
        # find parent
        $p = $em->find('Entity\Base\NavigationMenuItem', (int)$_REQUEST['parent_id']);
        
        if ($id) $i = $em->find('Entity\Base\NavigationMenuItem', (int)$id);
        else $i = new Entity\Base\NavigationMenuItem();
        
        $i->title = $_REQUEST['title'];
        $i->sequence = (int)$_REQUEST['sequence'];
        $i->link = $_REQUEST['link'];
        # if it's INSERT operation (id is null), we add the parent and menu
        if (!$id)
        {
            if ($m) $i->addNavigationMenu($m);
            if ($p) $i->addParent($p);
        }
        
        # find action definition
        $d = $em->find('Entity\Base\ActionDefinition', (int)$_REQUEST['action_definition_id']);
        # find action
        $dql = 'SELECT u FROM Entity\Base\Action u JOIN u.action_definition a WHERE a.id = ?1 AND u.params = ?2';
        $q = $em->createQuery($dql);
        $q->setParameter(1, (int)$_REQUEST['action_definition_id']);
        $q->setParameter(2, $_REQUEST['params']);
        $a = $q->getResult();
        # if more than one action found, get the first one
        if (count($a) >= 1) $a = $a[0]; else $a = null;
        # if there is action definition but no action found, create a new action
        if (is_null($a) && $d)
        {
            # create new action
            $a = new Entity\Base\Action();
            $a->params = $_REQUEST['params'];
            $a->addActionDefinition($d);
            $em->persist($a);
        }
        # associate the action
        if ($a) $i->addAction($a);
        
        try {
            
            $em->persist($i);
            $em->flush();
            $em->commit();
            $render->setSuccess(true);
            $render->setRedirect('menu/listMenuItemForMenuId/'.$m->id);
        }catch(Exception $e)
        {
            $em->rollback();
            $render->setSuccess(false);
        }
        return $render;
    }
    /*
     * Because the TreePanel only take an JSON array, we need to provide a custom
     * template instead of using the JSON plugin
     */
    public function getMenuItemByMenuCodename($codename = null)
    {
        $render = $this->getTemplate('json_menu_item');
        $em = $this->getManager('doctrine2')->getEntityManager();
        
        
        $dql = 'SELECT u,p.id as parent_id FROM Entity\Base\NavigationMenuItem u JOIN u.navigation_menu n LEFT JOIN u.parent p WHERE n.codename = ?1';
        $q = $em->createQuery($dql)->setParameter(1, $codename);
        
        $items = $q->getArrayResult();
        $render->setMenuItems($items);
        return $render;
    }
    
    public function createMenuItemForMenuId($id, $parentId = null)
    {
        $render = $this->getTemplate('create_menu_item');
        $em = $this->getManager('doctrine2')->getEntityManager();
        $m = $em->find('Entity\Base\NavigationMenu', $id);
        
        # find the parent;
        if ($parentId) $parent = $em->find('Entity\Base\NavigationMenuItem', $parentId);
        
        $modules = $this->getManager('registry')->getModules();
        $render->setMenu($m);
        $render->setParent($parent);
        $render->setModules($modules);
        return $render;
    }
    
    public function editMenuItem($id)
    {
        $render = $this->getTemplate('create_menu_item');
        $em = $this->getManager('doctrine2')->getEntityManager();
        # find the menu item
        $i = $em->find('Entity\Base\NavigationMenuItem', $id);
        # find the menu
        $m = $i->getMenu();        
        # find the parent;
        $p = $i->getParent();
        # find the action associate with this menu item
        $a = $i->getAction();
        if ($a)
        {
            # find the action definition
            $d = $a->getActionDefinition();
            # find the module
            $mo = $d->getModule();
        }
        # get all the available modules
        $modules = $this->getManager('registry')->getModules();
        
        $render->setMenu($m);
        $render->setParent($p);
        $render->setModules($modules);
        $render->setAction($a);
        $render->setActionDefinition($d);
        $render->setActionModule($mo);
        $render->setMenuItem($i);
        $render->setEdit(true);
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
        

        $em = $this->getManager('doctrine2')->getEntityManager();
        $em->beginTransaction();
        // find the node
        $node = $em->getRepository('Entity\Base\NavigationMenuItem')->findOneBy(array('id'=>$id));
        // find all the children of the parent node
        if ($node->parent) $parentId = $node->parent->id; else $parentId = null;
        
        if ($parentId)
            $children = $em->createQuery('SELECT u FROM Entity\Base\NavigationMenuItem u JOIN u.parent p WHERE p.id = '.$parentId.' ORDER BY u.sequence')->getResult();
        else
            $children = $em->createQuery('SELECT u FROM Entity\Base\NavigationMenuItem u JOIN u.parent p WHERE p.id = NULL ORDER BY u.sequence')->getResult();

        $sequence = 0;
        
        
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
        try
        {
            $em->commit();
            $render->setSuccess(true);
        }catch(Exception $e){
            $em->rollback();
            $render->setSuccess(false);
        }
        return $render;
    }
    
    public function reparentNode()
    {
        $r = $_REQUEST;
        $render = $this->getTemplate('dummy');
        $id = intval($r['node']);
        $parentId = intval($r['parent']);
        $position = intval($r['position']);
        
        
        $em = $this->getManager('doctrine2')->getEntityManager();
        
        # find the current node
        $node = $em->getRepository('Entity\Base\NavigationMenuItem')->findOneBy(array('id'=>$id));
        
        # find the parent node
        $parent = $em->getRepository('Entity\Base\NavigationMenuItem')->findOneBy(array('id'=>$parentId));
        
        if ($node && $parent)
        {
            $node->addParent($parent);
            $node->sequence = $position;
            $em->beginTransaction();
            $em->persist($node);
            $em->flush();
            # select all the children of parent node, except the current node
            $children = $em->createQuery('SELECT u FROM Entity\Base\NavigationMenuItem u JOIN u.parent p WHERE p.id = '.$parentId.' AND u.id != '.$id.' ORDER BY u.sequence')->getResult();
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
            $node->removeParent();
            $em->persist($node);
            $render->setSuccess(true);
        }else{
            $render->setSuccess(false);
        }
        return $render;
    
    }
    
    public function htmlHelp()
    {
    
    }
}
?>