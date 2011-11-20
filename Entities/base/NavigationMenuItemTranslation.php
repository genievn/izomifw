<?php
namespace Entities\Base;

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