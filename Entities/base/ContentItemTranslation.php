<?php
namespace Entities\Base;
use DoctrineExtensions\Versionable\IVersionable;

/**
 * @Entity
 * @Table(name="base_contentitems_translations")
 */
class ContentItemTranslation implements IVersionable
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
    protected $field;
    /**
     * @Column(type="text", nullable=true)
     */
    protected $translation;
    /**
     * @Column(type="string", length=15)
     */
    protected $lang;
    /**
     * @Column(type="integer")
     * @version
     */
    protected $version;
    /**
     * @ManyToOne(targetEntity="ContentItem", inversedBy="translations")
     * @JoinColumn(name="contentitem_id", referencedColumnName="id", onDelete="CASCADE")
     */
    protected $contentitem;
    
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
}
?>