<?php

namespace Entities\Cms;

use Entities\Base\IValidatable,
    Entities\Base\ITranslatable,
    Entities\Base\IAuditable,
    Doctrine\Common\Collections\ArrayCollection,
    DoctrineExtensions\Versionable\IVersionable;

/**
 * @Entity
 * @HasLifecycleCallbacks
 * @Table(name="cms_articles")
 */
class Article implements IVersionable, IAuditable, ITranslatable
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
    protected $slug;
    /**
     * @Column(type="string", length=255, nullable=true)
     */
    protected $subtitle;
    /**
     * @Column(type="string", length=255, nullable=true)
     */
    protected $supertitle;
    /**
     * @Column(type="string", length=255, nullable=true)
     */
    protected $source;
    /**
     * @Column(type="text", nullable=true)
     */
    protected $summary;
    /**
     * @Column(type="text")
     */
    protected $body;
    /**
     * @Column(type="string", length=255, nullable=true)
     */
    protected $avatar;
    /**
     * @Column(type="integer")
     */
    protected $priority;
    /**
     * @Column(type="boolean", nullable=true)
     */
    protected $published;    
    /**
     * @Column(type="boolean", nullable=true)
     */
    protected $multipart;
    /**
     * @Column(type="datetime", nullable=true)
     */
    protected $created_on;
    /**
     * @Column(type="datetime", nullable=true)
     */
    protected $updated_on;
    /**
     * @Column(type="datetime", nullable=true)
     */
    protected $published_on;
    /**
     * @Column(type="datetime", nullable=true)
     */
    protected $expired_on;
    /**
     * @Column(type="integer")
     * @version
     */
    protected $version;
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
        // constructor is never called by Doctrine
        $this->created_on = $this->updated_on = new \DateTime("now");
        $this->published_on = $this->expired_on = null;
        $this->priority = 0;
        $this->multipart = false;
    }
    /**
	 * getI18nColumns function.
	 * implements IContentTranslatable interface
	 * 
	 * @access public
	 * @static
	 * @return void
	 */
	public static function getI18nColumns()
	{
        return array('title','subtitle','supertitle','summary','body');
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
		else
			return parent::__get($var);
    }
    
    public function getAssociation($association)
    {
        return $this->$association;
    }
    
    public function setAuthor(SimpleCmsUser $author) {
        $this->user = $author;
    }
    /** @PrePersist */
    public function prePersist()
    {
        $this->created_on = new \DateTime("now");
    }
    /** @PreUpdate */
    public function updated_on()
    {
        $this->updated_on = new \DateTime("now");
    }
    
    /** @PrePersist @PreUpdate */
    public function doSlug()
    {
        $this->slug = \Strings::slugize($this->title);
    }
}
