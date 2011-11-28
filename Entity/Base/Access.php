<?php
namespace Entity\Base;
/*
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
*/
use Doctrine\ORM\Mapping as ORM;
/**
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 * @ORM\Table(name="base_accesses")
 */
class Access
{
    /**
     * @ORM\Id 
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;
    /**
     * @ORM\ManyToOne(targetEntity="Rule", inversedBy="accesses")
     * @ORM\JoinColumn(name="rule_id", referencedColumnName="id", onDelete="CASCADE")
     */
    protected $rule;
    /**
     * @ORM\ManyToOne(targetEntity="Action", inversedBy="accesses")
     * @ORM\JoinColumn(name="action_id", referencedColumnName="id", onDelete="CASCADE")
     */
    protected $action;
    /**
     * @ORM\ManyToOne(targetEntity="Role", inversedBy="accesses")
     * @ORM\JoinColumn(name="role_id", referencedColumnName="id", onDelete="CASCADE")
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

