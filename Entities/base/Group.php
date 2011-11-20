<?php

namespace Entities\Base;

/**
 * @Entity
 * @InheritanceType("JOINED")
 * @DiscriminatorColumn(name="discr", type="string")
 * @DiscriminatorMap({
 *  "group" = "Group"
 * })
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

