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
use Doctrine\ORM\Mapping\ManyToOne;
use Doctrine\ORM\Mapping\ManyToMany;
use Doctrine\ORM\Mapping\OneToOne;
use Doctrine\ORM\Mapping\OneToMany;
use Doctrine\ORM\Mapping\JoinColumn;
use Doctrine\ORM\Mapping\JoinTable;
use Doctrine\ORM\Mapping\PrePersist;
use Doctrine\ORM\Mapping\PreUpdate;
use Doctrine\ORM\Mapping\HasLifecycleCallbacks;
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
