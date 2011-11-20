<?php

namespace Entities\Base;
use Entities\Base\ITranslatable,
    Doctrine\Common\Collections\ArrayCollection;
/**
 * @Entity
 * @HasLifecycleCallbacks
 * @Table(name="base_accesses")
 */
class Access
{
    /**
     * @Id 
     * @Column(type="integer")
     * @GeneratedValue(strategy="AUTO")
     */
    protected $id;
    /**
     * @ManyToOne(targetEntity="Rule", inversedBy="accesses")
     * @JoinColumn(name="rule_id", referencedColumnName="id", onDelete="CASCADE")
     */
    protected $rule;
    /**
     * @ManyToOne(targetEntity="Action", inversedBy="accesses")
     * @JoinColumn(name="action_id", referencedColumnName="id", onDelete="CASCADE")
     */
    protected $action;
    /**
     * @ManyToOne(targetEntity="Role", inversedBy="accesses")
     * @JoinColumn(name="role_id", referencedColumnName="id", onDelete="CASCADE")
     */
    protected $role;
    
    public function __construct()
    {
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
	
	public function addRule($rule)
	{
	   $this->rule = $rule;
	}
	
	public function addRole($role)
	{
	   $this->role = $role;
	}
	
	public function addAction($action)
	{
	   $this->action = $action;
	}
}

