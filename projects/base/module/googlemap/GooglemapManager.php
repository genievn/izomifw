<?php
class GooglemapManager extends Object
{
    public function useMapping()
    {
        $this->getManager( 'html' )->addJs( locale( 'jslibs/yui/2.5.2/yahoo/yahoo-min.js', true ), true );
        $this->getManager( 'html' )->addJs( locale( 'jslibs/yui/2.5.2/event/event-min.js', true ), true );
        $this->getManager( 'html' )->addJs( 'http://maps.google.com/maps?file=api&amp;v=2&amp;key='.config( 'google.mapping.key' ), true );
        $this->getManager( 'html' )->addJs( locale( 'google/mapping.js', true ), true );
    }
}
?>