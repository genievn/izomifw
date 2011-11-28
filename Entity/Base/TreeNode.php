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
use Doctrine\ORM\Mapping\ManyToOne;
use Doctrine\ORM\Mapping\ManyToMany;
use Doctrine\ORM\Mapping\OneToOne;
use Doctrine\ORM\Mapping\OneToMany;
use Doctrine\ORM\Mapping\JoinColumn;
use Doctrine\ORM\Mapping\JoinTable;
use Doctrine\ORM\Mapping\PrePersist;
use Doctrine\ORM\Mapping\PreUpdate;
/**
 * @Entity
 * @HasLifecycleCallbacks
 * @Table(name="base_tree_nodes")
 */
class TreeNode
{
    /**
     * @Id
     * @Column(type="integer")
     * @GeneratedValue(strategy="AUTO")
     */
    protected $id;
    /**
     * @Column(type="string", length=36)
     */
    protected $uuid;
    /**
     * @Column(type="string", length=50, nullable=true)
     */
    protected $codename;
    /**
     * @Column(type="string", length=255)
     */
    protected $title;
    /**
     * @Column(type="string", length=255, nullable=true)
     */
    protected $description;
    /**
     * @Column(type="string", length=255, nullable=true)
     */
    protected $avatar;
    /**
     * @Column(type="datetime", nullable=true)
     */
    protected $created_on;
    /**
     * @Column(type="datetime", nullable=true)
     */
    protected $updated_on;
    /**
     * @Column(type="integer")
     */
    protected $sequence;
    /**
     * @Column(type="integer", nullable=true)
     */
    protected $lft;
    /**
     * @Column(type="integer", nullable=true)
     */
    protected $rgt;
    /**
     * @Column(type="string", length=10, nullable=false)
     */
    protected $default_lang;
    /**
     * @Column(type="string", length=36, nullable=true)
     */
    protected $parent_uuid;
    /**
     * @ManyToOne(targetEntity="TreeNode", inversedBy="children")
     * @JoinColumn(name="parent_id", referencedColumnName="id", onDelete="CASCADE")
     */
    protected $parent;
    /**
     * @OneToMany(targetEntity="TreeNode", mappedBy="parent")
     */
    protected $children;
    /**
     * @ManyToOne(targetEntity="TreeType", inversedBy="nodes")
     * @JoinColumn(name="tree_type_id", referencedColumnName="id", onDelete="CASCADE")
     */
    protected $tree_type;
    
    
    public function __construct()
    {
        $this->children = new ArrayCollection;
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
    
    public function addTreeType($type)
    {
        $this->tree_type = $type;
    }
    public function addParentNode($node)
    {
        $this->parent = $node;
    }
    public function removeParentNode()
    {
        $this->parent = null;
    }
    
    public function getParent()
    {
        return $this->parent;
    }
    
    public function isLeaf()
    {
        return (count($this->children) == 0);
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
}
