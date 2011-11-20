<?php
namespace Entities\Base;
use Doctrine\Common\Collections\ArrayCollection,
    Doctrine\ORM\EntityRepository;
/**
 * Description of Policy
 *
 * @author Thanh H. Nguyen
 * @Entity(repositoryClass="Entities\Base\ModuleRepository")
 * @Table(name="base_modules")
 */
class Module
{
    /**
     * @Id
     * @Column(type="integer")
     * @GeneratedValue(strategy="AUTO")
     */
    protected $id;
    /**
     * @Column(type="string", length=50, nullable = false)
     */
    protected $codename;
    /**
     * @Column(type="string", length=50, nullable = true)
     */
    protected $title;
    /**
     * @Column(type="string", length=20, nullable = true)
     */
    protected $package;    
    /**
     * @Column(type="string", length=50, nullable = true)
     */
    protected $author;
    /**
     * @Column(type="string", length=10, nullable = true)
     */
    protected $version;
    /**
     * @Column(type="string", nullable = true)
     */
    protected $setting;
    /**
     * @Column(type="string", length=50, nullable = true)
     */
    protected $avatar;
    /**
     * @OneToMany(targetEntity="ActionDefinition", mappedBy="module", cascade={"persist", "remove"})
     */
    protected $action_definitions;
    /**
     * @ManyToOne(targetEntity="TreeNode")
     * @JoinColumn(name="treenode_id", referencedColumnName="id", onDelete="SET NULL")
     */
    protected $treenode;
    

    public function __set($var, $val){
        if (property_exists($this,$var))
            $this->$var = $val;
    }

    public function __get($var){
		if (property_exists($this,$var))
        	return $this->$var;
    }
    
    public function assign($association, $collection)
	{
		if (property_exists($this,$association))
			$this->$association = $collection;
	}
	
	public function getActionDefinitions()
	{
	    return $this->action_definitions;
	}
    public function addActionDefinition($ad)
    {
        $this->action_definitions[] = $ad;
    }
    
    public function addTreeNode($treenode)
    {
        $this->treenode = $treenode;
    }
    
    public function getTreeNode()
    {
        return $this->treenode;
    }
}

class ModuleRepository extends EntityRepository
{
    public function findAllWidgets($module_id)
    {
        $dql = 'SELECT u FROM Entities\Base\ActionDefinition u JOIN Entities\Base\Module m WHERE u.is_widget = ?1 AND m.id = ?2';
        $q = $this->_em->createQuery($dql);
        $q->setParameter(1, true);
        $q->setParameter(2, $module_id);
        return $q->getResult();
    }
}
