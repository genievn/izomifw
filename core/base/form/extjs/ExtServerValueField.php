<?php
class ExtServerValueField extends Object
{
    public function doHtml($options = null)
    {
        $attrs = $this->getAttributes();
        $context = $this->getContext();
        $module = @$attrs["module"];
        $method = @$attrs["method"];
        $params = @$attrs["params"];
        
        $manager = $context->getManager($module);
        
        
        if ($manager & method_exists($manager, $method)){
            $value = call_user_func_array(array($manager,$method), ((is_array($params)) ? $params : array($params)));
        }else{
            $value = '';
        } 
        
        $html = "
            {
                xtype: 'hidden'
                , name: '{$this->getName()}'
                , id: '{$this->getId()}'
                , value: '{$value}'
            }
        ";
        
        return $html;
    }
}
###
?>
