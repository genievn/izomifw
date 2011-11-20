<?php
class SystemController extends Object
{
    
    public function getModule()
    {
        $render = $this->getTemplate('dummy');
        
        $em = $this->getManager('doctrine2')->getEntityManager();
        
        
        
        $modules = array(
            array("module"=>"Quản lý nhóm người dùng", "icon"=>"article.png","group"=>"system"),
            array("module"=>"Article", "icon"=>"article.png","group"=>"system"),
            array("module"=>"Article", "icon"=>"article.png","group"=>"system"),
            array("module"=>"Article", "icon"=>"article.png","group"=>"system"),
            array("module"=>"Article", "icon"=>"article.png","group"=>"system"),
            array("module"=>"Article", "icon"=>"article.png","group"=>"system"),
            array("module"=>"Article", "icon"=>"article.png","group"=>"system"),
            array("module"=>"Article", "icon"=>"article.png","group"=>"system"),
            array("module"=>"Article", "icon"=>"article.png","group"=>"system"),
            array("module"=>"Article", "icon"=>"article.png","group"=>"system"),
            array("module"=>"Article", "icon"=>"article.png","group"=>"system"),
            array("module"=>"Article", "icon"=>"article.png","group"=>"cms"),
            array("module"=>"Article", "icon"=>"article.png","group"=>"cms"),
            array("module"=>"Article", "icon"=>"article.png","group"=>"cms"),
            array("module"=>"Article", "icon"=>"article.png","group"=>"cms"),
            array("module"=>"Article", "icon"=>"article.png","group"=>"cms"),
            array("module"=>"Article", "icon"=>"article.png","group"=>"cms"),
        );
        
        /*
        // For grouping dataview
        $modules = array( 
            array( 
                'group' => 'Group 1', 
                'list' => array( 
                    array( 
                        'img' => 'image1.jpg',
                        "icon"=>"article.png" 
                        // ... more values 
                    ), 
                    array( 
                        'img' => 'image2.jpg',
                        "icon"=>"article.png" 
                        // ... 
                    ) 
                ) 
            ), 
            array( 
                'group' => 'Group 2', 
                'list' => array( 
                    array( 
                        'img' => 'image1.jpg',
                        "icon"=>"article.png"
                    ), 
                    array( 
                        'img' => 'image2.jpg',
                        "icon"=>"article.png"
                    ) 

                ) 
            ), 
            array( 
                'group' => 'Group 2', 
                'list' => array( 
                    array( 
                        'img' => 'image1.jpg',
                        "icon"=>"article.png"
                    ), 
                    array( 
                        'img' => 'image2.jpg',
                        "icon"=>"article.png"
                    ) 

                ) 
            ), 
            array( 
                'group' => 'Group 2', 
                'list' => array( 
                    array( 
                        'img' => 'image1.jpg',
                        "icon"=>"article.png"
                    ), 
                    array( 
                        'img' => 'image2.jpg',
                        "icon"=>"article.png"
                    ) 

                ) 
            )
        );*/
        $render->setModules($modules);
        return $render;
    }
    
    public function listModule($page, $limit)
    {
        $render = $this->getTemplate('list_module');
        
        return $render;
    }
    
    public function createModule()
    {
        $render = $this->getTemplate('create_module');
        return $render;
    }
    
    public function getModuleCategory()
    {
        $render = $this->getTemplate('json_module_category');
        $role = 'administrators';
        $menuActions = $this->getManager('system')->getActionForRole($role);
        $render->setMenuActions($menuActions);
        return $render;
    }
    
    public function getActionForRole($role)
    {
        $this->getManager('system')->getActionForRole($role);
    }
}
?>