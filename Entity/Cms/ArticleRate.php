<?php
namespace Entity\Cms;
use Doctrine\ORM\Mapping as ORM;
/**
 * @ORM\Entity
 * @ORM\Table(name="cms_articles_rates")
 */
class ArticleRate
{
	/**
     * @ORM\Id 
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;
	/*
    protected article;
	protected rate;
	protected ip;
	protected created_on;
	*/
}

