<?php
class ExtDateTimeField extends Object
{
    public function doHtml($options = null)
    {
        $attrs = $this->getAttributes();
		$value = $this->getValue();
		izlog($value);
        $html = "        
        {
            fieldLabel: '{$attrs["title"]}'
            , xtype: 'xdatetime'
            , name: '{$this->getName()}'
            , id: '{$this->getId()}'
			, value: '{$value}'
        }";
        
        return $html;
    }
}
?>