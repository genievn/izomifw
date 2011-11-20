<?php

namespace Entities\Cms;
use Doctrine\Common\Collections\ArrayCollection,
    Entities\Base\ITranslatable,
    Doctrine\ORM\EntityRepository;
/**
 * @Entity
 * @Table(name="cms_albums")
 */
class Album implements ITranslatable
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
     * @Column(type="string", length=255, nullable=true)
     */
    protected $description;
    /**
     * @Column(type="text")
     */
    protected $photos;
    /**
     * @Column(type="datetime", nullable=true)
     */
    protected $created_on;
    /**
     * @Column(type="datetime", nullable=true)
     */
    protected $updated_on;
    /**
     * @Column(type="string", length=10, nullable=false)
     */
    protected $default_lang;
    /**
     * @ManyToOne(targetEntity="Entities\Base\TreeNode")
     * @JoinColumn(name="node_id", referencedColumnName="id", onDelete="CASCADE")
     */
    protected $node;
    
    
    public function __construct()
    {
        #$this->contentitem = new ArrayCollection;
    }
    /**
     * getI18nColumns function.
     * implements the IContentTranslatable interface
     * 
     * @access public
     * @static
     * @return void
     */
    public static function getI18nColumns()
    {
        return array('title','description');
    }
    
    public function assign($association, $collection)
	{
		if (property_exists($this,$association))
			$this->$association = $collection;
	}
	
	public function __set($var, $val){
        if (property_exists($this,$var))
            $this->$var = $val;
    }

    public function __get($var){
		if (property_exists($this,$var))
        	return $this->$var;
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
