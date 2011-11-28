<?php

namespace Entity\Wfmc;
use Doctrine\ORM\Mapping\Table;
use Doctrine\ORM\Mapping\Index;
use Doctrine\ORM\Mapping\UniqueConstraint;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\GeneratedValue;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\MappedSuperclass;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\HasLifecycleCallbacks;

/**
 * Description of WfmcWorkItem
 *
 * @author Thanh H. Nguyen
 * @Entity
 * @Table(name="wfmc_workitems")
 */
class WfmcWorkItem
{
    /**
     * @Id
     * @Column(type="integer")
     * @GeneratedValue(strategy="AUTO")
     */
    protected $id;
    /**
     * @Column(type="string", length=50)
     */
    protected $workitem_id;
    /**
     * @Column(type="string", length=255)
     */
    protected $process_id;
    /**
     * @Column(type="string", length=255)
     */
    protected $process_definition_id;
    /**
     * @Column(type="string", length=50)
     */
    protected $activity_id;
    /**
     * @Column(type="string", length=255)
     */
    protected $activity_definition_id;
    /**
     * @Column(type="string")
     */
    protected $instance;
    /**
     * @Column(type="string", length=255)
     */
    protected $role;
    /**
     * @Column(type="string", length=20)
     */
    protected $status;    
    
    public function __set($var, $val){
        if (property_exists($this,$var))
            $this->$var = $val;
    }

    public function __get($var){
		if (property_exists($this,$var))
        	return $this->$var;
		else
			return parent::__get($var);
    }
}

