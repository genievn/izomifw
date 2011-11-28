<?php
namespace Entity\Cms;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
/**
 * @ORM\Entity
 * @ORM\Table(name="cms_articles_categories")
 */
class ArticleCategory
{
    /**
     * @ORM\Id 
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;
	/**
	 * @Gedmo\Translatable
	 * @ORM\Column(length=64)
	 */
	protected $title;
	
	/*
	protected $num_views;
	protected $created_on;
	protected $last_updated_on;
	protected $created_by;
	protected $last_updated_by;
	protected $created_username;
	protected $status;
	*/
}

