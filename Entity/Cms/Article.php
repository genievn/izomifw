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
    protected $id;
	/**
	 * @Gedmo\Translatable
	 * @ORM\Column(name="title", type="string", length=128)
	 */
	protected $title;
	/**
	 * @Gedmo\Translatable
	 * @ORM\Column(name="sub_title", type="string", length=128)
	 */
	protected $sub_title;
	/**
	 * @Gedmo\Translatable
	 * @ORM\Column(name="description", type="text")
	 */
	protected $description;
	
	/**
	 * @Gedmo\Translatable
	 * @ORM\Column(name="content", type="text")
	 */
	protected $content;
	

/*
	protected $author;
	protected $image_avatar;
	protected $status;
	protected $num_views;
	protected $allow_comment;
	protected $num_comments;
	protected $article_type;
	protected $is_sticky;
	protected $published_on;
	protected $expired_on;
	protected $created_on;
	protected $last_updated_on;
	protected $created_by;
	protected $last_updated_by;
	protected $created_username;
	*/
}

