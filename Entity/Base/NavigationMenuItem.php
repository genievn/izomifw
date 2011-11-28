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
 * @Table(name="base_navigation_menu_items")
 */
class NavigationMenuItem
{
    /**
     * @Id 
     * @Column(type="integer")
     * @GeneratedValue(strategy="AUTO")
     */
    protected $id;
    /**
     * @Column(type="string", length=50, nullable = false)
     */
    protected $title;
    /**
     * @Column(type="string", length=10, nullable = true)
     */
    protected $default_lang;
    /**
     * @Column(type="string", length=255, nullable=true)
     */
    protected $avatar;
    /**
     * @Column(type="string", length=255, nullable=true)
     */
    protected $link;
    /**
     * @Column(type="datetime", nullable=true)
     */
    protected $created_on;
    /**
     * @Column(type="datetime", nullable=true)
     */
    protected $updated_on;
    /**
     * @Column(type="integer")
     */
    protected $sequence;
    /**
     * @Column(type="integer", nullable=true)
     */
    protected $lft;
    /**
     * @Column(type="integer", nullable=true)
     */
    protected $rgt;
    /**
     * @ManyToOne(targetEntity="NavigationMenuItem", inversedBy="children")
     * @JoinColumn(name="parent_id", referencedColumnName="id", onDelete="CASCADE")
     */
    protected $parent;
    /**
     * @OneToMany(targetEntity="NavigationMenuItem", mappedBy="parent")
     */
    protected $children;
    /**
     * @ManyToOne(targetEntity="Action", inversedBy="navigation_menu_items")
     * @JoinColumn(name="action_id", referencedColumnName="id", onDelete="CASCADE")
     */
    protected $action;
    /**
     * @ManyToOne(targetEntity="NavigationMenu", inversedBy="navigation_menu_items")
     * @JoinColumn(name="navigation_menu_id", referencedColumnName="id", onDelete="CASCADE")
     */
    protected $navigation_menu;
    /**
     * @OneToMany(targetEntity="NavigationMenuItemTranslation", mappedBy="navigation_menu_item")
     */
    protected $translations;
    
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
    public function addNavigationMenu($menu)
	{
	   $this->navigation_menu = $menu;
	}
     public function addParent($item)
    {
        $this->parent = $item;
    }
    public function removeParent()
    {
        $this->parent = null;
    }
    
    public function getParent()
    {
        return $this->parent;
    }
    
    public function isLeaf()
    {
        return (count($this->children) == 0);
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
    
    public function assign($association, $collection)
	{
		if (property_exists($this,$association))
			$this->$association = $collection;
	}
		
	public function addAction($action)
	{
	   $this->action = $action;
	}
	public function getAction()
	{
        return $this->action;
	}
	public function getMenu()
	{
	   return $this->navigation_menu;
	}
}

