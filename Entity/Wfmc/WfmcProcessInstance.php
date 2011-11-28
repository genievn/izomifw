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
 * Description of WfmcProcessInstance
 *
 * @author Thanh H. Nguyen
 * @Entity
 * @Table(name="wfmc_process_instances")
 */
class WfmcProcessInstance
{
    /**
     * @Id
     * @Column(type="integer")
     * @GeneratedValue(strategy="AUTO")
     */
    protected $id;
    /**
     * @Column(type="string", length=36)
     */
    protected $uuid;
    /**
     * @Column(type="text")
     */
    protected $instance;
       
    
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

