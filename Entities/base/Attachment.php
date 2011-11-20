<?php

namespace Entities\Base;
use Doctrine\Common\Collections\ArrayCollection;
/**
 * @Entity
 * @HasLifecycleCallbacks
 * @Table(name="base_attachments")
 */
class Attachment
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
    protected $title;
    /**
     * @Column(type="string", length=255)
     */
    protected $path;
    
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

