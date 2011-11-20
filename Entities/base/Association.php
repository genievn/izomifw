<?php
namespace Entities\Base;
use DoctrineExtensions\Versionable\IVersionable;

/**
 * @Entity
 * @Table(name="base_associations")
 */
class Association
{
    /**
     * @Id
     * @Column(type="integer")
     * @GeneratedValue(strategy="AUTO")
     */
    protected $id;
    /**
     * @Column(type="integer")
     */
    protected $source_id;
    /**
     * @Column(type="string", length=50)
     */
    protected $source_entity;
    /**
     * @Column(type="integer")
     */
    protected $destination_id;
    /**
     * @Column(type="string", length=50)
     */
    protected $destination_entity;
    /**
     * @Column(type="string", length=50)
     */
    protected $type;
    
    
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
?>