<?php
namespace Entity\Cms;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Gedmo\Tree\Entity\Repository\NestedTreeRepository;
/**
 * @ORM\Entity(repositoryClass="Gedmo\Tree\Entity\Repository\NestedTreeRepository")
 * @ORM\Table(name="cms_articles_categories")
 * @Gedmo\Tree(type="nested")
 */
class ArticleCategory
{
    /**
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue
     */
    private $id;

    /**
	 * @Gedmo\Translatable
	 * @Gedmo\Sluggable
     * @ORM\Column(name="title", type="string", length=255)
     */
    private $title;

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
     * @ORM\ManyToOne(targetEntity="ArticleCategory", inversedBy="children")
     * @ORM\JoinColumn(name="parent_id", referencedColumnName="id", onDelete="SET NULL")
     */
    private $parent;

    /**
     * @ORM\OneToMany(targetEntity="ArticleCategory", mappedBy="parent")
     * @ORM\OrderBy({"lft" = "ASC"})
     */
    private $children;

    /**
     * @Gedmo\Translatable
     * @Gedmo\Slug
     * @ORM\Column(name="slug", type="string", length=128)
     */
    private $slug;

	public function getId()
    {
        return $this->id;
    }

    public function setTitle($title)
    {
        $this->title = $title;
    }

    public function getTitle()
    {
        return $this->title;
    }

    public function setParent(ArticleCategory $parent = null)
    {
        $this->parent = $parent;    
    }

    public function getParent()
    {
        return $this->parent;   
    }
	/*
	protected $created_on;
	protected $last_updated_on;
	protected $created_by;
	protected $last_updated_by;
	protected $created_username;
	protected $status;
	*/
}

