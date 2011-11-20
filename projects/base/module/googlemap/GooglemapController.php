<?php
class GooglemapController extends Object
{
    public function loadMapping()
    {
        $this->getManager( 'googlemap' )->useMapping();
    }
}
?>