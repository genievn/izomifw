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
use Doctrine\ORM\Mapping\ManyToOne;
use Doctrine\ORM\Mapping\ManyToMany;
use Doctrine\ORM\Mapping\OneToOne;
use Doctrine\ORM\Mapping\OneToMany;
use Doctrine\ORM\Mapping\JoinColumn;
use Doctrine\ORM\Mapping\JoinTable;
use Doctrine\ORM\Mapping\PrePersist;


use Doctrine\Common\Collections\ArrayCollection,
    Doctrine\ORM\EntityRepository;

/**
 * @Entity(repositoryClass="Entity\Base\AccountRepository")
 * @HasLifecycleCallbacks
 * @Table(name="base_accounts")
 */
class Account
{
    /**
     * @Id 
     * @Column(type="integer")
     * @GeneratedValue(strategy="AUTO")
     */
    protected $id;
    /**
     * @Column(type="string", length=50, nullable=true)
     */
    protected $status;
    /**
     * @Column(type="string", length=50, unique=true)
     */
    protected $username;
    /**
     * @Column(type="string", length=255)
     */
    protected $password;
    /**
     * @Column(type="string", length=50, nullable=true)
     */
    protected $first_name;
    /**
     * @Column(type="string", length=50, nullable=true)
     */
    protected $last_name;
    /**
     * @Column(type="string", length=50, nullable=true)
     */
    protected $mid_name;
    /**
     * @Column(type="string", length=50, nullable=true)
     */
    protected $email;
    /**
     * @Column(type="boolean", nullable=true)
     */
    protected $is_active;
    /**
     * @Column(type="datetime", nullable=true)
     */
    protected $created_on;
    /**
     * @Column(type="datetime", nullable=true)
     */
    protected $updated_on;
    /**
     * @Column(type="integer", nullable=true)
     */
    protected $type;
    /**
     * @ManyToMany(targetEntity="Role", inversedBy="accounts", cascade={"persist"})
     * @JoinTable(name="base_accounts_roles",
     *      joinColumns={@JoinColumn(name="account_id", referencedColumnName="id", onDelete="CASCADE")},
     *      inverseJoinColumns={@JoinColumn(name="role_id", referencedColumnName="id", onDelete="CASCADE")}
     *      )
     */
    protected $roles;
    /**
     * @ManyToMany(targetEntity="Group", inversedBy="accounts", cascade={"persist"})
     * @JoinTable(name="base_accounts_groups",
     *      joinColumns={@JoinColumn(name="account_id", referencedColumnName="id")},
     *      inverseJoinColumns={@JoinColumn(name="group_id", referencedColumnName="id")}
     *      )
     */
    protected $groups;
    
    


    public function __construct() {
        $this->roles = new ArrayCollection();
        $this->groups = new ArrayCollection();
        $this->created_on = $this->updated_on = new \DateTime("now");
    }


	public function getRoles()
	{
		return $this->roles;
	}
	
    public function addRole(Role $role) {
        $this->roles->add($role);
        //$role->addAccount($this);
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

    /** @PrePersist */
    public function doMd5Password()
    {
        $this->password = md5($this->password);
    }

    public function __set($var, $val){
        if (property_exists($this,$var))
            $this->$var = $val;
    }

    public function __get($var){
		if (property_exists($this,$var))
        	return $this->$var;
    }

	public function getRolesAsNameArray()
	{
		$roles = array();
		foreach ($this->roles as $role) {
			$roles[] = $role->name;
		}
		return $roles;
	}
	public function getRolesAsKeyValue()
	{
		$roles = array();
		foreach ($this->roles as $role) {
			$roles[$role->id] = $role->name;
		}
		return $roles;
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

class AccountRepository extends EntityRepository
{
    public function getAllAccountOrderByName()
    {
        return $this->_em->createQuery('SELECT u FROM Entity\Base\Account u ORDER BY u.username')
                         ->getResult();
    }
}
?>