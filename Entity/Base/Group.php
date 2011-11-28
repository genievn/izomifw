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
use Doctrine\ORM\Mapping\PreUpdate;
/**
 * @Entity
 * @Table(name="base_groups")
 */
class Group
{
    /**
     * @Id
     * @Column(type="integer")
     * @GeneratedValue
     */
    protected $id;
    /**
     * @Column(length=255)
     */
    protected $name;
    /**
     * @Column(length=255)
     */
    protected $description;
    /**
     * @ManyToMany(targetEntity="Role", inversedBy="groups", cascade={"persist"})
     * @JoinTable(name="base_groups_roles",
     *      joinColumns={@JoinColumn(name="group_id", referencedColumnName="id")},
     *      inverseJoinColumns={@JoinColumn(name="role_id", referencedColumnName="id")}
     *      )
     */
    protected $roles;
    /**
     * @ManyToMany(targetEntity="Account", mappedBy="groups")
     */
    protected $accounts;
    /**
     * @OneToMany(targetEntity="Group", mappedBy="parent")
     */
    protected $children;

    /**
     * @ManyToOne(targetEntity="Group", inversedBy="children")
     * @JoinColumn(name="parent_id", referencedColumnName="id")
     */
    protected $parent;
    
    public function __construct() {
        $this->roles = new ArrayCollection;
    }
    
    public function assign($association, $collection)
	{
		if (property_exists($this,$association))
			$this->$association = $collection;
	}
	
	public function __set($var, $val){
        if (property_exists($this,$var))
            $this->$var = $val;
    }

    public function __get($var){
		if (property_exists($this,$var))
        	return $this->$var;
    }
}

