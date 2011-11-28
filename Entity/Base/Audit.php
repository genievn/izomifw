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
/**
 * @Entity
 * @HasLifecycleCallbacks
 * @Table(name="base_audits")
 */
class Audit
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
     * @Column(type="integer")
     */
    protected $entity_version;
    /**
     * @Column(type="integer", nullable=true)
     */
    protected $account_id;
    /**
     * @Column(type="string", length=50, nullable=true)
     */
    protected $account_username;
    /**
     * @Column(type="datetime")
     */
    protected $created_on;
    /**
     * @Column(type="datetime")
     */
    protected $updated_on;
    
    
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

