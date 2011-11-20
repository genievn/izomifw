<?php

namespace Entities\Base;
use Doctrine\Common\Collections\ArrayCollection,
    Entities\Base\ITranslatable,
    Doctrine\ORM\EntityRepository;
/**
 * @Entity
 * @Table(name="base_navigation_menus")
 */
class NavigationMenu
{
    /**
     * @Id
     * @Column(type="integer")
     * @GeneratedValue(strategy="AUTO")
     */
    protected $id;
    /**
     * @Column(type="string", length=36, nullable = false)
     */
    protected $uuid;
    /**
     * @Column(type="string", length=50, nullable = false)
     */
    protected $codename;
    /**
     * @Column(type="string", length=255)
     */
    protected $title;
    /**
     * @Column(type="string", length=255, nullable=true)
     */
    protected $description;
    /**
     * @OneToOne(targetEntity="NavigationMenu")
     * @JoinColumn(name="parent_id", referencedColumnName="id", onDelete="CASCADE")
     */
    protected $parent;
    /**
     * @OneToMany(targetEntity="NavigationMenuItem", mappedBy="navigation_menu")
     */
    protected $navigation_menu_items;
    
    public function __construct()
    {
        
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
    
    public function addParent($parent)
    {
        $this->parent = $parent;
    }
}
