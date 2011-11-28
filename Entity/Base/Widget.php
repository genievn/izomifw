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
 * @HasLifecycleCallbacks
 * @Table(name="base_widgets")
 */
class Widget
{
    /**
     * @Id
     * @Column(type="integer")
     * @GeneratedValue(strategy="AUTO")
     */
    protected $id;
    /**
     * @Column(type="string", length=255, nullable=false)
     */
    protected $title;
    /**
     * @ManyToOne(targetEntity="Action", inversedBy="widgets")
     * @JoinColumn(name="action_id", referencedColumnName="id", onDelete="CASCADE")
     */
    protected $action;
    /**
     * @Column(type="string", nullable=true)
     */
    protected $settings;
    /**
     * @OneToMany(targetEntity="PageTemplateWidget", mappedBy="widget")
     */
    protected $page_template_widgets;
    
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
	
	public function addAction($action)
	{
        $this->action = $action;
	}
}

