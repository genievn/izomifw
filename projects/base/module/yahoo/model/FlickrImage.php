<?php
class FlickrImage extends Object
{
    public function getUrlSquare()
    {
        return 'http://farm'.$this->getFarm().'.static.flickr.com/'.$this->getServer().'/'.$this->getId().'_'.$this->getSecret().'_s.jpg';
    }
    
    public function getUrlFull()
    {
        return 'http://farm'.$this->getFarm().'.static.flickr.com/'.$this->getServer().'/'.$this->getId().'_'.$this->getSecret().'.jpg';
    }
}
?>