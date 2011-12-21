<?php

class MenuController extends Object
{
	public function cpanel()
	{
		$render = $this->getTemplate('cpanel');
		return $render;
	}
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
		$name = @$_REQUEST['name'];
        $em = $this->getManager('doctrine2')->getEntityManager();
        
        $m = new Entity\Base\Menu();
        $m->setName($name);
        
        try {
            $em->beginTransaction();
            $em->persist($m);
			// create a root menu item
			$root = new Entity\Base\MenuItem();
			$root->setTitle('root');
			$root->setMenu($m);
			
			$em->persist($root);
            $em->flush();
            $em->commit();
            $render->setSuccess(true);
			$render->setMessage('Menu saved successfully');
        }catch(Exception $e){
            $em->rollback();
            $render->setSuccess(false);
			$render->setMessage('Error while saving menu');
        }
        return $render;        
    }
    
    public function listMenu()
    {
        $render = $this->getTemplate('list_menu');
        
        return $render;
    }
    public function getMenuJsonData()
    {
        $render = $this->getTemplate('json');
        
        $em = $this->getManager('doctrine2')->getEntityManager();
        $dql = "SELECT u FROM Entity\Base\Menu u";
        $menus = $em->createQuery($dql)->getArrayResult();

        $render->setMenus($menus);
        
        return $render;
        
    }
    
    public function listMenuItemForMenuId($menu_id)
    {
        $render = $this->getTemplate('list_menu_item');
        $em = $this->getManager('doctrine2')->getEntityManager();
        $menu = $em->find('Entity\Base\Menu', $menu_id);
        $render->setMenu($menu);
        return $render;
    }

    public function menuItemJsonData($menu_id = null)
	{
		if (!$menu_id) return false;
		$render = $this->getTemplate('json');
		$em = $this->getManager('doctrine2')->getEntityManager();
		$repo = $em->getRepository('Entity\Base\MenuItem');
		//find the root of the menu
		$q = $em->createQuery('SELECT u FROM Entity\Base\MenuItem u JOIN u.menu m WHERE m.id=?1');
		$q->setParameter(1, $menu_id);
		$result = $q->getResult();
		# if more than one action found, get the first one
        if (count($result) >= 1) $root = $result[0]; else $root = null;
		
		if (!$root) return false;
		
		$tree = $repo->childrenHierarchy($root);
		$tree = array('title'=>'/', 'id'=>$root->getId(), 'children'=>$tree);
		$render->setText('.');
		$render->setChildren($tree);
		//$render->setRootId($root->getId());
		return $render;
	}
    public function saveMenuItem($id = null)
    {
		$title = @$_REQUEST['title'];
		$link = @$_REQUEST['link'];
		
		$params = @$_REQUEST['params'];
		if(!$params) $params = '*';

        $em = $this->getManager('doctrine2')->getEntityManager();
        $render = $this->getTemplate('json');
        $em->beginTransaction();
        
        # find parent
        $p = $em->find('Entity\Base\MenuItem', (int)@$_REQUEST['parent_id']);

		if (!($p instanceof Entity\Base\MenuItem)){
			$render->setSuccess(false);
			$render->setMessage('A root menu item must be specified');
		}
        
        if ($id) $i = $em->find('Entity\Base\MenuItem', (int)$id);
        else $i = new Entity\Base\MenuItem();
        
        $i->setTitle($title);
        $i->setLink($link);
        # if it's INSERT operation (id is null), we add the parent and menu
        if (!$id)
        {
            if ($p) $i->setParent($p);
        }
        
        # find action definition
        $d = $em->find('Entity\Base\ActionDefinition', (int)@$_REQUEST['action_definition_id']);
        # find action
        $dql = 'SELECT u FROM Entity\Base\Action u JOIN u.action_definition a WHERE a.id = ?1 AND u.params = ?2';
        $q = $em->createQuery($dql);
        $q->setParameter(1, (int)@$_REQUEST['action_definition_id']);
        $q->setParameter(2, @$_REQUEST['params']);
        $a = $q->getResult();
        # if more than one action found, get the first one
        if (count($a) >= 1) $a = $a[0]; else $a = null;
        # if there is action definition but no action found, create a new action
        if (is_null($a) && $d)
        {
            # create new action
            $a = new Entity\Base\Action();
            $a->params = $params;
            $a->setActionDefinition($d);
            $em->persist($a);
        }
        # associate the action
        if ($a) $i->setAction($a);
        
        try {
            
            $em->persist($i);
            $em->flush();
            $em->commit();
            $render->setSuccess(true);
            $render->setMessage('Menu item created successfully!');
        }catch(Exception $e)
        {
            $em->rollback();
            $render->setSuccess(false);
			$render->setMessage('Error while saving menu item: '. $e->getMessage());
        }
        return $render;
    }
	public function reorderMenuItem()
	{
		$sourceId = $_REQUEST["sourceId"];
		$targetId = $_REQUEST["targetId"];
		$position = $_REQUEST["position"];
		$render = $this->getTemplate('json');
		$em = $this->getManager('doctrine2')->getEntityManager();
		$repo = $em->getRepository('Entity\Base\MenuItem');
		if ($sourceId) $source = $em->find('Entity\Base\MenuItem', $sourceId);
		if ($targetId) $target = $em->find('Entity\Base\MenuItem', $targetId);
		
		switch ($position) {
			case 'append':
				// make target to be source's parent
				if ($target) $source->setParent($target); else return false;
				try {					
					$em->persist($source);
					$em->flush();
					
					$render->setSuccess(true);
					if ($target)
						$render->setMessage('Menu item ('.$source->getTitle().') has new parent ('.$target->getTitle().')');
					return $render;
				} catch (Exception $e) {
					$render->setSuccess(false);
					$render->setMessage('Error while setting new parent ('.$target->getTitle().') for category ('.$source->getTitle().')');
					return $render;
				}
				break;
			case 'before':
			case 'after':
				// move the				
				try {
					if ($position == 'after')
						$repo->persistAsNextSiblingOf($source, $target);				
					else 
						$repo->persistAsPrevSiblingOf($source, $target);
					//$em->persist($source);
					$em->flush();
					
					$render->setSuccess(true);
					if ($target)
						$render->setMessage('Menu item ('.$source->getTitle().') has been moved '.$position.' ('.$target->getTitle().')');
					return $render;
				} catch (Exception $e) {
					$render->setSuccess(false);
					$render->setMessage('Error while moving ('.$source->getTitle().') '.$position.' ('.$target->getTitle().'):'.$e->toString());
					return $render;
				} 
				break;
			default:
				# code...
				break;
		}
	}
    
    public function createMenuItem($id, $parentId = null)
    {
        $render = $this->getTemplate('create_menu_item');
        $em = $this->getManager('doctrine2')->getEntityManager();
        $m = $em->find('Entity\Base\Menu', $id);
        
        # find the parent;
        if ($parentId) $parent = $em->find('Entity\Base\MenuItem', $parentId); else $parent = null;
        
        $modules = $this->getManager('registry')->getModules();
        $render->setMenu($m);
        if ($parent instanceof Entity\Base\MenuItem) $render->setParent($parent);
        $render->setModules($modules);
		$render->setMenuId($id);
		$this->getManager( 'html' )->addJs( config('root.url') . 'libs/extjs/ux/treecombo/treecombo.js', true);
        return $render;
    }

    public function editMenuItem($id)
    {
        $render = $this->getTemplate('create_menu_item');
        $em = $this->getManager('doctrine2')->getEntityManager();
        # find the menu item
        $i = $em->find('Entity\Base\MenuItem', $id);
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
    
}
?>