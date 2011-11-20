<?php
namespace Entities\Base;

/**
 * Description of Layout
 *
 * @author Thanh H. Nguyen
 * @Entity
 * @Table(name="base_layouts")
 */
class Layout
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
    protected $codename;
    /**
     * @Column(type="string", length=255, nullable = true)
     */
    protected $regions;
    /**
     * @OneToMany(targetEntity="PageTemplate", mappedBy="layout")
     */
    protected $page_templates;

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
}

