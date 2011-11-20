<?php
class TagController extends Object
{

    public function tag()
    {
        $this->getManager('tag')->tag('Entities\Base\ContentItem',1,array('pink','hot','coolair','warm'));
    }
}
?>