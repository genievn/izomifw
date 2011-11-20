<?php
class TagPlugin extends Object
{
    public function tag($entity, $id, $tags)
    {
        if (empty($tags)) return;

        $this->getManager('tag')->tag($entity, $id, $tags);
    }
}
?>