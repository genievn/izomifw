<?php
namespace Entity\Cms;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Translatable\Translatable;
use Gedmo\Loggable\Loggable;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * @ORM\Entity
 * @ORM\Table(name="cms_articles")
 * @Gedmo\Loggable(logEntryClass="Entity\Cms\Log\Article")
 * @Gedmo\TranslationEntity(class="Entity\Cms\ArticleTranslation")
 */
class Article
{
    /**
     * @ORM\Id 
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;
	/**
	 * @Gedmo\Translatable
	 * @Gedmo\Versioned
	 * @ORM\Column(name="title", type="string", length=128)
	 */
	private $title;
	/**
	 * @Gedmo\Translatable
	 * @ORM\Column(name="sub_title", type="string", length=128, nullable=true)
	 */
	private $sub_title;
	/**
	 * @Gedmo\Translatable
	 * @ORM\Column(name="description", type="text", nullable=true)
	 */
	private $description;
	/**
	 * @Gedmo\Translatable
	 * @ORM\Column(name="content", type="text")
	 */
	private $content;
	/**
	 * @ORM\Column(name="author", type="string", length=128, nullable=true)
	 */
	private $author;
	/**
	 * @ORM\Column(name="image", type="string", length=128, nullable=true)
	 */
	private $image;
	/**
     * @ORM\Column(name="status", type="integer", nullable=true)
     */
    private $status;
	/**
     * @ORM\Column(name="num_views", type="integer", nullable=true)
     */
    private $num_views;
	/**
     * @ORM\Column(name="allow_comments", type="boolean", nullable=false)
     */
    private $allow_comments;
	/**
     * @ORM\Column(name="num_comments", type="integer", nullable=true)
     */
    private $num_comments;
	/**
     * @ORM\Column(name="is_sticky", type="boolean", nullable=false)
     */
    private $is_sticky;
	/**
     * @ORM\Column(name="is_hot", type="boolean", nullable=false)
     */
    private $is_hot;
	/**
     * @ORM\Column(name="show_comments", type="boolean", nullable=false)
     */
    private $show_comments;
	/**
     * @Gedmo\Translatable
     * @Gedmo\Slug(fields={"title"})
     * @ORM\Column(name="slug", type="string", length=128, unique=true)
     */
    private $slug;
	/**
     * @var datetime $created
     *
     * @Gedmo\Timestampable(on="create")
     * @ORM\Column(type="datetime")
     */
    private $created_on;
    /**
     * @var datetime $updated
     *
     * @ORM\Column(type="datetime")
     * @Gedmo\Timestampable(on="update")
     */
    private $updated_on;
	/**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $published_on;
	/**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $expired_on;
	/**
     * @ORM\ManyToOne(targetEntity="ArticleCategory", inversedBy="children")
     * @ORM\JoinColumn(name="category_id", referencedColumnName="id", onDelete="SET NULL")
     */
    private $category;
	/**
     * @Gedmo\Locale
     * Used locale to override Translation listener`s locale
     * this is not a mapped field of entity metadata, just a simple property
     */
    private $locale;

	public function setTitle($title)
	{
		$this->title = $title;
	}
	public function setContent($content)
	{
		$this->content = $content;
	}
	public function setSubTitle($sub_title)
	{
		$this->sub_title = $sub_title;
	}
	public function setImage($image)
	{
		$this->image = $image;
	}
	public function setShowComments($show_comments)
	{
		$this->show_comments = $show_comments;
	}
	public function setSticky($is_sticky)
	{
		$this->is_sticky = $is_sticky;
	}
	public function setHot($is_hot)
	{
		$this->is_hot = $is_hot;
	}
	public function setAllowComments($allow_comments)
	{
		$this->allow_comments = $allow_comments;
	}
	public function setAuthor($author)
	{
		$this->author = $author;
	}
	public function setStatus($status)
	{
		$this->status = $status;
	}
	public function setPublishedOn($published_on)
	{
		$this->published_on = $published_on;
	}	
	public function setExpiredOn($expired_on)
	{
		$this->expired_on = $expired_on;
	}
	public function setTranslatableLocale($locale)
    {
        $this->locale = $locale;
    }
	public function setCategory(ArticleCategory $category)
	{
		$this->category = $category;
	}
/*
	private $author;
	private $image_avatar;
	private $status;
	private $num_views;
	private $allow_comment;
	private $num_comments;
	private $article_type;
	private $is_sticky;
	private $published_on;
	private $expired_on;
	private $created_on;
	private $last_updated_on;
	private $created_by;
	private $last_updated_by;
	private $created_username;
	*/
}

final class ArticleStatus {
	const DRAFT = 0;
	const PUBLISHED = 100;
}
