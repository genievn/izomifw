<?php
namespace Entities\Base;

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

