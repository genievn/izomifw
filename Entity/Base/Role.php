<?php
namespace Entity\Base;
use Doctrine\ORM\Mapping\Table;
use Doctrine\ORM\Mapping\Index;
use Doctrine\ORM\Mapping\UniqueConstraint;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\GeneratedValue;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\MappedSuperclass;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\HasLifecycleCallbacks;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Mapping\ManyToOne;
use Doctrine\ORM\Mapping\ManyToMany;
use Doctrine\ORM\Mapping\OneToOne;
use Doctrine\ORM\Mapping\OneToMany;
use Doctrine\ORM\Mapping\JoinColumn;
use Doctrine\ORM\Mapping\JoinTable;
use Doctrine\ORM\Mapping\PrePersist;
use Doctrine\ORM\Mapping\PreUpdate;
/**
 * Description of Role
 *
 * @Entity(repositoryClass="Entities\Base\RoleRepository")
 * @Table(name="base_roles")
 */
class Role
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