<?php
namespace Entity\Cms;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Translatable\Translatable;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * @ORM\Entity
 * @ORM\Table(name="cms_articles")
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
     * @ORM\Column(name="status", type="integer", nullable=true)
     */
    private $status;
	/**
     * @ORM\Column(name="num_views", type="integer", nullable=true)
     */
    private $num_views;
	/**
     * @ORM\Column(name="allow_comment", type="boolean", nullable=false)
     */
    private $allow_comment;
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
     * @var datetime $created
     *
     * @Gedmo\Timestampable(on="create")
     * @ORM\Column(type="datetime")
     */
    private $created;

    /**
     * @var datetime $updated
     *
     * @ORM\Column(type="datetime")
     * @Gedmo\Timestampable(on="update")
     */
    private $updated;
	
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
