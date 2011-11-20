<?php

namespace Entities\Base;
use Entities\Base\ITranslatable,
    Doctrine\Common\Collections\ArrayCollection;
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

