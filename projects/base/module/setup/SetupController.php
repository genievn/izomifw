<?php
config('.setup.entity.accounts', array(
    array('username'=>'admin', 'password'=>'admin', 'role'=>'administrators')
    ,array('username'=>'dev', 'password'=>'dev')
));

config('.setup.entity.roles', array(
    array('name'=>'administrators')
    ,array('name'=>'developers')
));

config('.setup.entity.rules', array(
    array('codename'=>'true', 'rule'=>true),
    array('codename'=>'false', 'rule'=>false),
    array('codename'=>'login', 'rule'=>array('status'=>'login'))
));

config('.setup.entity.treetypes', array(
    array('codename'=>'module.category'),
    array('codename'=>'article.category')
));

config('.setup.entity.treenodes', array(
    array('codename'=>'system', 'title'=>'System', 'treetype'=>'module.category', 'sequence'=> 1),
    array('codename'=>'cms', 'title'=>'CMS', 'treetype'=>'module.category', 'sequence'=> 2),
    array('codename'=>'dev', 'title'=>'Development', 'treetype'=>'module.category', 'sequence'=> 3)    
));

config('.setup.entity.modules', array(
    array('codename'=>'*', 'title'=>'All modules')
));

config('.setup.entity.actiondefinitions', array(
    array('module'=>'*', 'codename'=>'*', 'title'=>'All methods')
));

class SetupController extends Object
{
    public function setupData()
    {
        // resetting the data
        $this->resetData();
    
        $em = $this->getManager('doctrine2')->getEntityManager();
        $em->beginTransaction();
        
        // setting up Role data
        $roles = config('setup.entity.roles',null);
        if(!empty($roles))
        {
            foreach($roles as $r)
            {
                $role = new Entities\Base\Role();
                $role->name = @$r["name"];
                $em->persist($role);
            }
        }
        $em->flush();
        $em->commit();
        
        $em->beginTransaction();
        // setting up Account data
        $accounts = config('setup.entity.accounts', null);
        if(!empty($accounts))
        {
            foreach($accounts as $a)
            {
                $role = null;
                $account = new Entities\Base\Account();
                $account->username = @$a["username"];
                $account->password = @$a["password"];
                $account->email = @$a["email"];
                if (!empty($a["role"]) && $role = $em->getRepository('Entities\Base\Role')->findOneBy(array('name' => $a["role"])))
                {
                    $account->addRole($role);
                }
                $em->persist($account);
            }
        }
        // setting up TreeType data
        $treetypes = config('setup.entity.treetypes', null);
        if(!empty($treetypes))
        {
            foreach($treetypes as $a)
            {
                $treetype = new Entities\Base\TreeType();
                $treetype->codename = @$a["codename"];
                $treetype->uuid = new_uuid();
                $em->persist($treetype);
            }
        }
        
        // setting up TreeNode data
        $treenodes = config('setup.entity.treenodes', null);
        if(!empty($treenodes))
        {
            foreach($treenodes as $a)
            {
                $type = null;
                $treenode = new Entities\Base\TreeNode();
                $treenode->uuid = new_uuid();
                $treenode->title = @$a["title"];
                $treenode->codename = @$a["codename"];
                $treenode->default_lang = config('root.default_lang');
                $treenode->sequence = @$a["sequence"];
                if (!empty($a["treetype"]) && $type = $em->getRepository('Entities\Base\TreeType')->findOneBy(array('codename' => $a["treetype"])))
                {
                    $treenode->addTreeType($type);
                }
                $em->persist($treenode);
            }
        }
        $em->flush();
        $em->commit();
        
        $em->beginTransaction();
        // setting up Rule data
        $rules = config('setup.entity.rules', null);
        if(!empty($rules))
        {
            foreach($rules as $r)
            {
                $rule = new Entities\Base\Rule();
                $rule->codename = @$r["codename"];
                $rule->rule = serialize(@$r["rule"]);
                $em->persist($rule);
            }
        }
        
        $em->flush();
        $em->commit();
    }
    
    public function resetData()
    {
        $em = $this->getManager('doctrine2')->getEntityManager();
        $em->beginTransaction();
        $roles = $em->getRepository('Entities\Base\Role')->findAll();
        $accounts = $em->getRepository('Entities\Base\Account')->findAll();
        $rules = $em->getRepository('Entities\Base\Rule')->findAll();
        $treetypes = $em->getRepository('Entities\Base\TreeType')->findAll();
        $treenodes = $em->getRepository('Entities\Base\TreeNode')->findAll();
        foreach ($roles as $role) $em->remove($role);
        foreach ($accounts as $account) $em->remove($account);
        foreach ($rules as $rule) $em->remove($rule);
        foreach ($treetypes as $treetype) $em->remove($treetype);
        foreach ($treenodes as $treenode) $em->remove($treenode);
        $em->flush();
        $em->commit();
    }
    
    public function getData()
    {
        $em = $this->getManager('doctrine2')->getEntityManager();
        
        $rules = $em->getRepository('Entities\Base\Rule')->findAll();
        foreach ($rules as $rule)
        {
            var_dump($rule->codename);
        }
    }
}
?>