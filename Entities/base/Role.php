<?php
namespace Entities\Base;
use Doctrine\Common\Collections\ArrayCollection,
    Doctrine\ORM\EntityRepository,
    Entities\Base\ITranslatable;
/**
 * Description of Role
 *
 * @Entity(repositoryClass="Entities\Base\RoleRepository")
 * @Table(name="base_roles")
 */
class Role implements ITranslatable
{
    /**
     * @Id
     * @Column(type="integer")
     * @GeneratedValue(strategy="AUTO")
     */
    protected $id;
    /**
     * @Column(type="string", length=50)
     */
    protected $name;
    
    /**
     * @Column(type="string", length=10, nullable=true)
     */
    protected $default_lang;
    /**
     * @Column(type="datetime", nullable=true)
     */
    protected $created_on;
    /**
     * @Column(type="datetime", nullable=true)
     */
    protected $updated_on;
    /**
     * @OneToMany(targetEntity="RoleTranslation", mappedBy="owner")
     */
    protected $translations;
    /**
     * @OneToMany(targetEntity="Access", mappedBy="role")
     */
    protected $accesses;
    /**
     * @ManyToMany(targetEntity="Account", mappedBy="roles")
     */
    protected $accounts;
    /**
     * @ManyToMany(targetEntity="Group", mappedBy="roles")
     */
    protected $groups;

    public function __construct()
    {
        $this->accounts = new ArrayCollection();
        $this->actions = new ArrayCollection();
        $this->created_on = $this->updated_on = new \DateTime("now");
    }

    /*
     * @PrePersist
     */
    public function created_on()
    {
        $this->created_on = new \DateTime("now");
    }
    /*
     * @PreUpdate
     */
    public function updated_on()
    {
        $this->updated_on = new \DateTime("now");
    }
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
	
	public function unassign($association)
	{
		if (property_exists($this,$association))
			unset($this->$association);
	
	}

}

class RoleRepository extends EntityRepository
{
    public function getAllRoleOrderByName()
    {
        return $this->_em->createQuery('SELECT u FROM Entities\Base\Role u ORDER BY u.name')
                         ->getResult();
    }
}
?>