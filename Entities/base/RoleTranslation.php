<?php
namespace Entities\Base;

/**
 * @Entity
 * @Table(name="base_roles_translations")
 */
class RoleTranslation
{
    /**
     * @Id
     * @Column(type="integer")
     * @GeneratedValue(strategy="AUTO")
     */
    private $id;
    /**
     * @Column(type="string", length=255)
     */
    private $title;
    /**
     * @Column(type="text", nullable=true)
     */
    private $description;
    /**
     * @Column(type="string", length=15)
     */
    private $lang;
    /**
     * @ManyToOne(targetEntity="Role", inversedBy="translations")
     * @JoinColumn(name="role_id", referencedColumnName="id", onDelete="CASCADE")
     */
    private $owner;
    
    public function __set($var, $val){
        if (property_exists($this,$var))
            $this->$var = $val;
    }
    
    public function __get($var){
		if (property_exists($this,$var))
        	return $this->$var;
    }
    
    public function addOwner($instance)
    {
        $this->owner = $instance;
        return $this;
    }
}
?>