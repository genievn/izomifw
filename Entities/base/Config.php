<?php

namespace Entities\Base;
/**
 * @Entity
 * @HasLifecycleCallbacks
 * @Table(name="base_configs")
 */
class Config
{
    /**
     * @Id
     * @Column(type="integer")
     * @GeneratedValue(strategy="AUTO")
     */
    protected $id;   
    /**
     * @Column(type="string", length=255)
     */
    protected $name;
    /**
     * @Column(type="string", length=255)
     */
    protected $value;
     

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

}

