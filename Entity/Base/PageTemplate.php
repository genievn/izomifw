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
 * Description of Page
 *
 * @author Thanh H. Nguyen
 * @Entity
 * @Table(name="base_page_templates")
 */
class PageTemplate
{
    /**
     * @Id
     * @Column(type="integer")
     * @GeneratedValue(strategy="AUTO")
     */
    protected $id;
    /**
     * @Column(type="string", length=255, nullable = true)
     */
    protected $title;
    /**
     * @Column(type="boolean", nullable=true)
     */
    protected $is_default;
    /**
     * @ManyToOne(targetEntity="Layout", inversedBy="page_templates")
     * @JoinColumn(name="layout_id", referencedColumnName="id", onDelete="CASCADE")
     */
    protected $layout;
    /**
     * @Column(type="string", nullable = true)
     */
    protected $layout_settings;
    /**
     * @Column(type="string", length=10, nullable=true)
     */
    protected $default_lang;
    /**
     * @OneToMany(targetEntity="PageTemplateWidget", mappedBy="page_template")
     */
    protected $page_template_widgets;
    /**
     * @OneToMany(targetEntity="Action", mappedBy="page_template")
     */
    protected $actions;
    

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
	
	public function addLayout($layout)
	{
        $this->layout = $layout;
	}
	
	public function getLayout()
	{
        return $this->layout;
	}
	
	public function getPageTemplateWidgets()
	{
        return $this->page_template_widgets;
	}
}

