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
 * @Table(name="base_navigation_menu_item_translations")
 */
class NavigationMenuItemTranslation
{
    /**
     * @Id
     * @Column(type="integer")
     * @GeneratedValue(strategy="AUTO")
     */
    protected $id;
    /**
     * @Column(type="string", length=255, nullable = false)
     */
    protected $title;
    /**
     * @Column(type="string", length=10, nullable = false)
     */
    protected $lang;
    /**
     * @ManyToOne(targetEntity="NavigationMenuItem", inversedBy="translations")
     * @JoinColumn(name="navigation_menu_item_id", referencedColumnName="id", onDelete="CASCADE")
     */
    protected $navigation_menu_item;
    
    public function __set($var, $val){
        if (property_exists($this,$var))
            $this->$var = $val;
    }
    
    public function __get($var){
		if (property_exists($this,$var))
        	return $this->$var;
    }
    
    public function addNavigationMenuItem($item)
    {
        $this->navigation_menu_item = $item;
        return $this;
    }
}
?>