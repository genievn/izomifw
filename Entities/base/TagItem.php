<?php

namespace Entities\Base;
use Entities\Base\ITranslatable,
    Doctrine\Common\Collections\ArrayCollection;
/**
 * @Entity
 * @HasLifecycleCallbacks
 * @Table(name="base_tagitems")
 */
class TagItem
{
    /**
     * @Id
     * @Column(type="integer")
     * @GeneratedValue(strategy="AUTO")
     */
    protected $id;
    /**
     * @ManyToOne(targetEntity="Account")
     * @JoinColumn(name="acccount_id", referencedColumnName="id", onDelete="CASCADE")
     */
    protected $account;
    /**
     * @ManyToOne(targetEntity="Tag")
     * @JoinColumn(name="tag_id", referencedColumnName="id", onDelete="CASCADE")
     */
    protected $tag;
    
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

