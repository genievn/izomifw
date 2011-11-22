<?php

namespace Entities\Base;
use Entities\Base\ITranslatable,
    Doctrine\Common\Collections\ArrayCollection;
/**
 * @Entity
 * @HasLifecycleCallbacks
 * @Table(name="base_comments")
 */
class Comment
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
     * @Column(type="string", length=255, nullable=true)
     */
    protected $title;
    /**
     * @Column(type="string")
     */
    protected $body;
    /**
     * @Column(type="string", length=50, nullable=true)
     */
    protected $first_name;
    /**
     * @Column(type="string", length=50, nullable=true)
     */
    protected $last_name;
    /**
     * @Column(type="string", length=50, nullable=true)
     */
    protected $mid_name;
    /**
     * @Column(type="string", length=50, nullable=true)
     */
    protected $email;
    /**
     * @Column(type="string", length=20, nullable=true)
     */
    protected $ip_address;
    /**
     * @Column(type="datetime", nullable=true)
     */
    protected $created_on;
    /**
     * @Column(type="datetime", nullable=true)
     */
    protected $updated_on;
    /**
     * @ManyToOne(targetEntity="Account")
     * @JoinColumn(name="account_id", referencedColumnName="id", onDelete="CASCADE")
     */
    protected $account;
    
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
}

