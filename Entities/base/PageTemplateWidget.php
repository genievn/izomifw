<?php
namespace Entities\Base;
use Doctrine\Common\Collections\ArrayCollection,
    Doctrine\ORM\EntityRepository;
/**
 * Description of Page
 *
 * @author Thanh H. Nguyen
 * @Entity(repositoryClass="Entities\Base\PageTemplateWidgetRepository")
 * @Table(name="base_page_template_widgets")
 */
class PageTemplateWidget
{
    /**
     * @Id
     * @Column(type="integer")
     * @GeneratedValue(strategy="AUTO")
     */
    protected $id;
    /**
     * @Column(type="string", length=30, nullable = false)
     */
    protected $region;
    /**
     * @Column(type="integer", nullable = false)
     */
    protected $position;
    /**
     * @ManyToOne(targetEntity="PageTemplate", inversedBy="page_template_widgets")
     * @JoinColumn(name="page_template_id", referencedColumnName="id", onDelete="CASCADE")
     */
    protected $page_template;
    /**
     * @ManyToOne(targetEntity="Widget", inversedBy="page_template_widgets")
     * @JoinColumn(name="widget_id", referencedColumnName="id", onDelete="CASCADE")
     */
    protected $widget;
    

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
	
	public function addWidget($widget)
	{
        $this->widget = $widget;
	}
	
	public function addPageTemplate($pageTemplate)
	{
        $this->page_template = $pageTemplate;
	}
}

class PageTemplateWidgetRepository extends EntityRepository
{
    
    public function findAllPageTemplateWidgets($pageTemplateId)
    {
        # find out all the widgets corresponding to this page template;
        $dql = 'SELECT m.codename as module, d.method as method, a.params as params,w.title as title, w.settings as settings,u.region as region,u.position as position FROM Entities\Base\PageTemplateWidget u JOIN u.page_template p JOIN u.widget w JOIN w.action a JOIN a.action_definition d JOIN d.module m WHERE p.id = ?1';
        $q = $this->_em->createQuery($dql);
        $q->setParameter(1, $pageTemplateId);
        return $q->getResult();
    }
}
