<?php
/*
 * class class
 */

class BaseFormElement extends Object{
    protected $type = null;
    protected $name = null;
    protected $title = null;
    protected $parent = null;
    protected $id = null;
    protected $is_container = false;
    
    protected $elements = array();          #holds child elements
    protected $plugins = array();           #holds plugin elements

    public function addChild($element, $context = null){
        $element->setParent($this);
        $element->setContext($context);
        $this->elements[] = $element;
        return $this;
    }
    
    public function addWrapper(&$element)
    {
        $element->addChild($this);
        $this->setWrapper($element);
        return $this;
    }
    
    public function addWrapperBefore(&$element)
    {
        $this->setWrapperBefore($element);
        return $this;
    }
    
    public function addWrapperAfter(&$element)
    {
        $this->setWrapperAfter($element);
        return $this;    
    }

    /*
     * function getChildren
     * @param $arg
     */

    public function getChildren($arg = null) {
        return $this->elements;
    }

    /*
     * function getChildById
     * @param $id
     */

    function getChildById($id) {
        foreach ($this->elements as $e){
            if ($id == $e->getId()) return $e;
        }
    }

    /*
     * function getId
     *
     */

    public function getId() {
        $attrs = $this->getAttributes();
        $type = @$attrs["type"];
        $suffix = $this->getIdSuffix();
        if (is_null($suffix)) $suffix = "";
        return $type."_".$this->getName()."_".time().$suffix;
    }

    /*
     * function getElements
     *
     */

    public function getElements() {
        return $this->elements;
    }

    public function getElementsOfType($type = null)
    {
        $elements = array();
        if ($type){
            foreach ($this->elements as $e)
            {
                $attrs = $e->getAttributes();
                if ($attrs["type"] == $type){
                    $elements[] = $e;
                }
                # recursive
                $elements = array_merge($elements, $e->getElementsOfType($type));
            }
            return $elements;
        }
        return null;
    }
    
    public function sync($object, $mode = 'set') {
        if (!($object instanceof Object))
        {
            $object = object('SyncObject',$object);
        }

        $count = 0;
        foreach ($this->elements as $e){
            $properties = $object->properties();
            $attrs = $e->getAttributes();
            $ename = $e->getName();
            # if the form name exists in the properties
            # it can be attributes name or relation name
            if (array_key_exists($ename, $properties)){
                switch ($mode){
                    case "set":
                        # sync value from object to form element
                        $e->setValue($properties[$ename]);
                        break;
                    case "get":
                        # extra process for multiple control with the same name
                        switch ($attrs["type"])
                        {
                            case 'ExtRadio':
                                if ($attrs["checked"] !== 1) break;
                                $properties[$ename] = $e->getValue();                                
                                $object->properties($properties);
                            break;

                            case 'ExtCheckbox':
                                $count = $count + 1;
                                # initialize empty list of value if this is the first control in the group
                                if ($count==1) $properties[$ename] = array();

                                $retValue = $e->getValue();
                                
                                if ($retValue != null)
                                {
                                    # checkbox is selected
                                    if (!is_array($retValue)) $retValue = array($retValue);
                                    $properties[$ename] = array_merge($retValue, $properties[$ename]);                                    
                                }
                                $object->properties($properties);
                                
                            break;

                            default:
                                $properties[$ename] = $e->getValue();
                                $object->properties($properties);
                            break;
                        }
                        break;
                    default:
                        return;
                }
            }
            $e->sync($object, $mode);
        }
    }
    /*
     * function getChildrenHtml
     * @param $options
     */

    protected function getChildrenHtml($options = null) {

        $child_html = array();

        $children_count = count($this->elements);
        /**
          *  If there is no child, return empty string
          */

        if ($children_count == 0) return "";

        foreach($this->elements as $e){
            $child_html[] = $e->doHtml($options);
        }
        /**
          *  If there are more than one children, separate their html with comma
          *  Otherwise return the only child html
          */

        if ($children_count >= 2){
            return implode(",",$child_html);
        }else{
            return $child_html[0];
        }

    }

    /*
     * function hasChildren
     * @param $arg
     */

    public function hasChildren($arg = null) {
        return (count($this->elements) > 0);
    }
}

/*
 * class ExtBaseFormElement
 */

class ExtBaseFormElement extends BaseFormElement {
    /*
     * function doHtml
     *
     */

    protected function doHtml($options = null) {
        $attrs = $this->getAttributes();

        $html = "";
        if ($this->hasChildren()){
            $child_html = $this->getChildrenHtml();
        }else{
            $child_html = "";
        }
        $html = "
            new Ext.Panel({
                title: '{$attrs["title"]}'
                ,name: 'ExtBaseFormElement'
                ,items: [$child_html]
            })
        ";

        return $html;
    }
    /**
     * fill function.
     * fill the form with value of array
     * 
     * @access public
     * @param mixed $arr
     * @return void
     */
    public function fill($arr)
    {
        # loops thru the elements of the form
		foreach ($this->getChildren() as $e) {
            # element's name
			$ename = $e->getName();
			# check if the element name exists in the array
			if (in_array($ename, array_keys($arr))) {
				if ($arr[$ename] instanceof DateTime){
				    $value = $arr[$ename]->format('Y-m-d H:i:s');
				    $e->setValue($value);
				} 
				else $e->setValue($arr[$ename]);
			}else
                $e->fill($arr);
		}    
    }
}
?>