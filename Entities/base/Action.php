<?php

namespace Entities\Base;
use Entities\Base\ITranslatable,
    Doctrine\Common\Collections\ArrayCollection,
    Doctrine\ORM\EntityRepository;
/**
 * @Entity(repositoryClass="Entities\Base\ActionRepository")
 * @HasLifecycleCallbacks
 * @Table(name="base_actions")
 */
class Action
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
    protected $params;
    /**
     * @Column(type="integer", nullable = true)
     */
    protected $position;
    /**
     * @ManyToOne(targetEntity="ActionDefinition", inversedBy="actions")
     * @JoinColumn(name="action_definition_id", referencedColumnName="id", onDelete="CASCADE")
     */
    protected $action_definition;
    /**
     * @OneToMany(targetEntity="Access", mappedBy="action")
     */
    protected $accesses;
    /**
     * @OneToMany(targetEntity="Widget", mappedBy="action")
     */
    protected $widgets;
    /**
     * @OneToMany(targetEntity="NavigationMenuItem", mappedBy="action")
     */
    protected $navigation_menu_items;
    /**
     * @ManyToOne(targetEntity="PageTemplate", inversedBy="actions")
     * @JoinColumn(name="page_template_id", referencedColumnName="id", onDelete="CASCADE")
     */
    protected $page_template;
    
    public function __construct()
    {
    
    }
    
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
	
	public function addActionDefinition($ad)
	{
        $this->action_definition = $ad;
	}
	public function addPageTemplate($pt)
	{
        $this->page_template = $pt;
	}
	public function getActionDefinition()
	{
        return $this->action_definition;
	}
	
	public function getPageTemplate()
	{
        return $this->page_template;
	}
}

class ActionRepository extends EntityRepository
{
    public function findByParams($action_definition_id, $params)
    {
        if (empty($params)) $params = '*';
        $q = $this->_em->createQuery('SELECT u FROM Entities\Base\Action u JOIN u.action_definition a WHERE a.id = ?1 AND u.params = ?2');
        $q->setParameter(1, $action_definition_id);
        $q->setParameter(2, $params);
        return $q->getSingleResult();
    }
}