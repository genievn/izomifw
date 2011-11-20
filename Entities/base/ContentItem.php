<?php

namespace Entities\Base;
use Entities\Base\ITranslatable,
    Doctrine\Common\Collections\ArrayCollection;
/**
 * @Entity
 * @HasLifecycleCallbacks
 * @Table(name="base_contentitems")
 */
class ContentItem
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
    protected $remote_id;
    /**
     * @Column(type="integer", length=1)
     */
    protected $visibility;
    /**
     * @Column(type="boolean")
     */
    protected $visibility_by_parent;
    /**
     * @Column(type="datetime", nullable=true)
     */
    protected $created_on;
    /**
     * @Column(type="datetime", nullable=true)
     */
    protected $updated_on;
    /**
     * @Column(type="string", length="255")
     */
    protected $entity;
    /**
     * @Column(type="integer")
     */
    protected $status;
    /**
     * @OneToMany(targetEntity="ContentItemTranslation", mappedBy="contentitem")
     */
    protected $translations;
    /**
     * @ManyToOne(targetEntity="ContentItem", inversedBy="children")
     * @JoinColumn(name="owner_id", referencedColumnName="id", onDelete="CASCADE")
     */
    protected $owner;
    /**
     * @OneToMany(targetEntity="ContentItem", mappedBy="owner")
     */
    protected $children;
    /**
     * @OneToMany(targetEntity="TagItem", mappedBy="contentitem")
     */
    protected $tagitems;
    /**
     * @OneToMany(targetEntity="Comment", mappedBy="contentitem")
     */
    protected $comments;
    /**
     * @ManyToMany(targetEntity="ContentItem")
     * @JoinTable(name="base_contentitems_parents",
     *      joinColumns={@JoinColumn(name="contentitem_child_id", referencedColumnName="id")},
     *      inverseJoinColumns={@JoinColumn(name="contentitem_parent_id", referencedColumnName="id")}
     *      )
     */
    protected $parents;
    /**
     * @ManyToMany(targetEntity="ContentItem")
     * @JoinTable(name="base_contentitems_relatives",
     *      joinColumns={@JoinColumn(name="contentitem_id", referencedColumnName="id")},
     *      inverseJoinColumns={@JoinColumn(name="contentitem_related_id", referencedColumnName="id")}
     *      )
     */
    protected $relatives;

    public function __construct()
    {
        $this->created_on = $this->updated_on = new \DateTime("now");
        $this->tagitems = new ArrayCollection();
        $this->owner = new ArrayCollection();
        $this->translations = new ArrayCollection();
        $this->parents = new ArrayCollection();
        $this->relatives = new ArrayCollection();
        # hidden: 2 - visible: 4 - hidden by parent (if visibility_by_parent) visibility & parent_visibility
        $this->visibility = 4;
        $this->visibility_by_parent = true;
        $this->status = ContentItemStatus::ENABLED;
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
	
	public function getTagItems()
	{
        return $this->tagitems;
	}
}

class ContentItemStatus
{
    const ENABLED = 0;
    const DISABLED = 1;
    const DELETED = 2;
}

