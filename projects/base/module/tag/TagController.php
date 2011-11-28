<?php
class TagController extends Object
{

    public function tag()
    {
        $this->getManager('tag')->tag('Entity\Base\ContentItem',1,array('pink','hot','coolair','warm'));
    }
}
?>