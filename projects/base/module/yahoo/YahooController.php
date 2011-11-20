<?php
class YahooController extends Object
{
    public function loadContextSearch()
    {
        $this->getManager( 'yahoo' )->useContextSearch();
    }
    
    public function contextSearch( $query=null )
    {
        if( !$query ) $query = @$_REQUEST['query'];
        
        return $this->getManager( 'yahoo' )->selectContextSearch( $query );
    }
    
    public function localSearch( $query, $lat, $lon, $radius=10 )
    {
        $render = $this->getTemplate( 'local_search' );
        $render->setItems( $this->getManager( 'Yahoo' )->localSearch( $query, $lat, $lon, $radius ) );

        return $render;
    }
    
    public function flickrTags( $tags=null, $result=20 )
    {
        $render = $this->getTemplate( 'flickr_tags' );
        $render->setItems( $this->getManager( 'Yahoo' )->flickrTags( $tags, $result ) );
        
        return $render;
    }
    
    public function flickrTagsSlideShow( $tags='', $page=1 )
    {
        $render = $this->getTemplate( 'flickr_slideshow' );
        
        $render->setTags( $tags );
        $render->setImages( $this->getManager( 'Yahoo' )->flickrTags( $tags, config( 'flickr.perpage', 45 ), $page ) );
        
        return $render;
    }
    
    public function answersQuestions( $query )
    {
        $render = $this->getTemplate( 'answers_questions' );
        $render->setQuestions( $this->getManager( 'Yahoo' )->getQuestions( $query, 0, 10 ) );
        
        return $render;
    }
    
    public function webSearch( $query )
    {
        $render = $this->getTemplate( 'web_search_results' );
        $render->setResults( $this->getManager( 'Yahoo' )->getWebSearch( $query, 0, 10 ) );
        
        return $render;
    }
}
?>