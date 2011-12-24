<?php
namespace Entity\Base;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Gedmo\Tree\Entity\Repository\NestedTreeRepository;
/**
 * @ORM\Entity(repositoryClass="Gedmo\Tree\Entity\Repository\NestedTreeRepository")
 * @ORM\Table(name="base_menus_items")
 * @Gedmo\Tree(type="nested")
 */
class MenuItem
{
    /**
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue
     */
    private $id;

    /**
	 * @Gedmo\Translatable
     * @ORM\Column(name="title", type="string", length=255)
     */
    private $title;
	/**
     * @Gedmo\Translatable
     * @Gedmo\Slug(fields={"title"})
     * @ORM\Column(name="slug", type="string", length=128, unique=true)
     */
    private $slug;
	/**
     * @ORM\Column(name="link", type="string", length=255, nullable=true)
     */
    private $link;
	/**
     * @ORM\Column(name="module", type="string", length=30, nullable=true)
     */
    private $module;
	/**
     * @ORM\Column(name="method", type="string", length=30, nullable=true)
     */
    private $method;
	/**
     * @ORM\Column(name="params", type="string", length=255, nullable=true)
     */
    private $params;
	/**
     * @ORM\Column(name="target", type="string", length=10, nullable=true)
     */
    private $target;
    /**
     * @Gedmo\TreeLeft
     * @ORM\Column(name="lft", type="integer")
     */
    private $lft;

    /**
     * @Gedmo\TreeLevel
     * @ORM\Column(name="lvl", type="integer")
     */
    private $lvl;

    /**
     * @Gedmo\TreeRight
     * @ORM\Column(name="rgt", type="integer")
     */
    private $rgt;

    /**
     * @Gedmo\TreeRoot
     * @ORM\Column(name="root", type="integer", nullable=true)
     */
    private $root;
	/**
     * @ORM\Column(name="num_views", type="integer", nullable=true)
     */
    private $num_views;
	/**
     * @ORM\Column(name="status", type="integer", nullable=true)
     */
    private $status;
    /**
     * @Gedmo\TreeParent
     * @ORM\ManyToOne(targetEntity="MenuItem", inversedBy="children")
     * @ORM\JoinColumn(name="parent_id", referencedColumnName="id", onDelete="SET NULL")
     */
    private $parent;

    /**
     * @ORM\OneToMany(targetEntity="MenuItem", mappedBy="parent")
     * @ORM\OrderBy({"lft" = "ASC"})
     */
    private $children;
    /**
     * @ORM\ManyToOne(targetEntity="Action", inversedBy="menu_items")
     * @ORM\JoinColumn(name="action_id", referencedColumnName="id", onDelete="CASCADE")
     */
    private $action;
    /**
     * @ORM\ManyToOne(targetEntity="Menu", inversedBy="menu_items")
     * @ORM\JoinColumn(name="menu_id", referencedColumnName="id", onDelete="SET NULL")
     */
    private $menu;
    /**
     * @Gedmo\Locale
     * Used locale to override Translation listener`s locale
     * this is not a mapped field of entity metadata, just a simple property
     */
    private $locale;
    public function __construct()
    {
    }
    public function getId(){ return $this->id; }

    public function __set($var, $val){
        if (property_exists($this,$var))
            $this->$var = $val;
    }

    public function __get($var){
		if (property_exists($this,$var))
        	return $this->$var;
    }
    public function setMenu($menu)
	{
	   $this->menu = $menu;
	}
     public function setParent($parent)
    {
        $this->parent = $parent;
    }
    public function removeParent()
    {
        $this->parent = null;
    }
    
    public function getParent()
    {
        return $this->parent;
    }

	public function getTitle() { return $this->title; }
    public function setTitle($title){$this->title = $title;}
	public function setTarget($target){$this->target = $target;}
	public function getTarget(){return $this->target;}
	public function setLink($link){ $this->link = $link; }
	public function getModule() { return $this->module; }
    public function setModule($module){$this->module = $module;}
	public function getMethod() { return $this->method; }
    public function setMethod($method){$this->method = $method;}
	public function getParams() { return $this->params; }
    public function setParams($params){$this->params = $params;}

    public function isLeaf()
    {
        return (count($this->children) == 0);
    }
    
	public function setAction($action)
	{
	   $this->action = $action;
	}
	public function getAction()
	{
        return $this->action;
	}
	public function getMenu()
	{
	   return $this->menu;
	}
}

