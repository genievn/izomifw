<?php
define('TREETYPE_MODULE_CATEGORY','base.module.category');

class RegistryManager extends Object
{
    public function getModule($codename)
    {
        $em = $this->getReader()->getEntityManager();
        $r = $em->getRepository('Entity\Base\Module')->findOneBy(array('codename'=>$codename));
        return $r;
    }

    public function getModules($asArray = false)
    {
        $em = $this->getReader()->getEntityManager();
        $dql = 'SELECT u FROM Entity\Base\Module u ORDER BY u.codename';
        if ($asArray)
        {
            $r = $em->createQuery($dql)->getArrayResult();
        }else{
            $r = $em->createQuery($dql)->getResult();
        }

        return $r;
    }

    public function getModuleCategories($asArray = false)
    {
        $em = $this->getReader()->getEntityManager();
        $dql = 'SELECT u FROM Entity\Base\TreeNode u JOIN u.tree_type t WHERE t.codename = ?1';
        $q = $em->createQuery($dql)->setParameter(1, TREETYPE_MODULE_CATEGORY);
        if ($asArray)
        {
            $r = $q->getArrayResult();
        }else{
            $r = $q->getResult();
        }
        return $r;
    }

    public function getActionDefinitions($asArray = false)
    {
        $em = $this->getReader()->getEntityManager();

        $dql = 'SELECT u,m FROM Entity\Base\ActionDefinition u JOIN u.module m WHERE u.is_widget = ?1 AND u.method <> ?2 AND m.codename <> ?3';

        $q = $em->createQuery($dql);
        $q->setParameter(1, false);
        $q->setParameter(2, '*');
        $q->setParameter(3, '*');

        if ($asArray)
        {
            return $q->getArrayResult();
        }else{
            return $q->getResult();
        }
    }


    public function getRules()
    {
        $em = $this->getReader()->getEntityManager();
        $dql = 'SELECT u FROM Entity\Base\Rule u ORDER BY u.codename';

        $r = $em->createQuery($dql)->getResult();
        return $r;
    }
    public function getRoles($asArray = false)
    {
        $em = $this->getReader()->getEntityManager();
        $dql = 'SELECT u FROM Entity\Base\Role u';

        $r = $em->createQuery($dql)->getResult();
        return $r;
    }
    public function getActionDefinitionForModule($codename, $asArray = false)
    {
        $em = $this->getReader()->getEntityManager();
        $dql = 'SELECT u FROM Entity\Base\ActionDefinition u JOIN u.module m WHERE m.codename = ?1';

        $q = $em->createQuery($dql);
        $q->setParameter(1, $codename);
        if ($asArray)
        {
            $r = $q->getArrayResult();
        }else{
            $r = $q->getResult();
        }

        return $r;
    }
    public function existModule($filterArray)
    {
        if (empty($filterArray)) return false;

        $em = $this->getWriter()->getEntityManager();

        $i = $em->getRepository('Entity\Base\Module')->findOneBy($filterArray);

        if ($i) return $i; else return false;
    }

}
?>