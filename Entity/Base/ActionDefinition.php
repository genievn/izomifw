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

use Doctrine\Common\Collections\ArrayCollection;
/**
 * @Entity
 * @HasLifecycleCallbacks
 * @Table(name="base_action_definitions")
 */
class ActionDefinition
{
    /**
     * @Id
     * @Column(type="integer")
     * @GeneratedValue(strategy="AUTO")
     */
    protected $id;
    /**
     * @Column(type="string", length=255, nullable = true)
     */
    protected $title;
    /**
     * @Column(type="string", length=255)
     */
    protected $method;
    /**
     * @Column(type="string", nullable = true)
     */
    protected $setting;
    /**
     * @Column(type="boolean")
     */
    protected $is_widget;
    /**
     * @Column(type="integer", nullable=true)
     */
    protected $cached_duration;
    /**
     * @ManyToOne(targetEntity="Module", inversedBy="action_definitions")
     * @JoinColumn(name="module_id", referencedColumnName="id", onDelete="CASCADE")
     */
    protected $module;
    /**
     * @OneToMany(targetEntity="Action", mappedBy="action_definition", cascade={"persist", "remove"})
     */
    protected $actions;
    
    
    public function __construct()
    {
        $this->is_widget = false;
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
	
	public function addModule($module)
	{
        $this->module = $module;
	}
	
	public function getActions()
	{
        return $this->actions;
	}
	
	public function getModule()
	{
        return $this->module;
	}
}

