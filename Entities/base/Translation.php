<?php

namespace Entities\Base;
use Entities\Base\ITranslatable,
    Doctrine\Common\Collections\ArrayCollection;
/**
 * @Entity
 * @HasLifecycleCallbacks
 * @Table(name="base_translations")
 */
class Translation
{
    /**
     * @Id
     * @Column(type="integer")
     * @GeneratedValue(strategy="AUTO")
     */
    protected $id;   
    /**
     * @Column(type="string", length=100)
     */
    protected $entity;
    /**
     * @Column(type="integer")
     */
    protected $entity_id;
    /**
     * @Column(type="string", length=36, nullable=true)
     */
    protected $entity_uuid;
    /**
     * @Column(type="integer", nullable=true)
     */
    protected $entity_version;
    /**
     * @Column(type="string", length=50)
     */
    protected $field;
    /**
     * @Column(type="text", nullable=true)
     */
    protected $translation;
    /**
     * @Column(type="string", length=15)
     */
    protected $lang;
    /**
     * @Column(type="integer")
     * @version
     */
    protected $version;
    /**
     * @Column(type="datetime")
     */
    protected $created_on;
    /**
     * @Column(type="datetime")
     */
    protected $updated_on;
    /**
     * @Column(type="string", length=50)
     */
    protected $status;    
    
    public function __construct()
    {
        $this->created_on = $this->updated_on = new \DateTime("now");
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

