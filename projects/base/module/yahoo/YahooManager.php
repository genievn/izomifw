<?php
class YahooManager extends Object
{
    public function useContextSearch()
    {
        $this->getManager( 'html' )->addJs( locale( 'jslibs/yui/yahoo/yahoo-min.js', true ), true );
        $this->getManager( 'html' )->addJs( locale( 'jslibs/yui/event/event-min.js', true ), true );
        $this->getManager( 'html' )->addJs( locale( 'jslibs/yui/connection/connection-min.js', true ), true );
        $this->getManager( 'html' )->addJs( locale( 'yahoo/contextsearch.js', true ), true );
    }
    
    public function selectContextSearch( $context )
    {
    	$context = $this->stripHtml( $context );
        return $this->postHack( $context, "/ContentAnalysisService/V1/termExtraction", "search.yahooapis.com" );
    }
    
    public function geocode( $street=null, $city=null, $state=null, $zip=null, $country=null )
    {
        $street = $this->stripHtml( $street );
        $city = $this->stripHtml( $city );
        $state = $this->stripHtml( $state );
        $zip = $this->stripHtml( $zip );
        $country = $this->stripHtml( $country );
        
        return $this->getHack( "appid=YahooDemo&location={$street},+{$city},+{$state},+{$country},+{$zip}&output=php", "/MapsService/V1/geocode", "http://api.local.yahoo.com" );
    }
    
    public function localSearch( $query, $lat, $lon, $radius=10, $results=20 )
    {
        $query = $this->stripHtml( $query );
        $lat = $this->stripHtml( $lat );
        $lon = $this->stripHtml( $lon );
        $radius = $this->stripHtml( $radius );
        
        $data = $this->getHack( "appid=YahooDemo&query={$query}&latitude={$lat}&longitude={$lon}&radius={$radius}&sort=distance&results={$results}&output=php", "/LocalSearchService/V2/localSearch", "http://local.yahooapis.com" )->getResult();
        
        if( !is_array( $data ) ) return array();

        $items = array();

        if( isset( $data['Title'] ) )
        {
            $items[] = object( 'LocalItem', $data );
        }
        else
        {
            foreach( $data as $item )
            $items[] = object( 'LocalItem', $item );
        }
        
        return $items;
    }
    
    public function flickrTags( $tags='', $perPage=10, $page=1 )
    {
        $result = @file_get_contents( 'http://api.flickr.com/services/rest/?method=flickr.photos.search&api_key='.config( 'flickr.key' ).'&tags='.$tags.'&tag_mode=all&per_page='.$perPage.'&page='.$page.'&format=php_serial' );

        $resultSet = @unserialize( $result );
         
        if( !is_array( $resultSet ) ) return object( 'FlickrImages' );
        
        $images = object( 'FlickrImages' );
        $images->setPage( $resultSet['photos']['page'] );
        $images->setPages( $resultSet['photos']['pages'] );
        $images->setPerpage( $resultSet['photos']['perpage'] );
        $images->setTotal( $resultSet['photos']['total'] );
        
        $photos = array();
        
        foreach( $resultSet['photos']['photo'] as $photo )
        {
            $photos[] = object( 'FlickrImage', $photo );
        }
        
        $images->setImages( $photos );
        
        return $images;
    }
    
    public function getQuestions( $query, $start=0, $restuls=10, $type='all', $sort='date_desc' )
    {
        $result = @file_get_contents( 'http://answers.yahooapis.com/AnswersService/V1/questionSearch?output=php&appid=YahooDemo&query='.urlencode( $query ).'&type='.$type.'&sort='.$sort );
        
        $resultSet = @unserialize( $result );
         
        if( !is_array( $resultSet ) ) return array();
        
        $questions = array();
        
        foreach( $resultSet['Questions'] as $question )
        {
            $questions[] = object( 'AnswersQuestion', $question );
        }
        
        return $questions;
    }
    
    public function getWebSearch( $query, $start=0, $restuls=10 )
    {
        $result = @file_get_contents( 'http://search.yahooapis.com/WebSearchService/V1/webSearch?output=php&appid=YahooDemo&query='.urlencode( $query ) );
        
        $resultSet = @unserialize( $result );
         
        if( !is_array( $resultSet ) ) return array();
        
        $results = array();
        
        foreach( $resultSet['ResultSet']['Result'] as $item )
        {
            $results[] = object( 'SearchResult', $item );
        }
        
        return $results;
    }

    private function getHack( $data, $service, $domain )
    {
        $result = @file_get_contents( $domain.$service.'?'.$data );
        
        $resultSet = @unserialize( $result );
         
        if( is_array( $resultSet ) && isset( $resultSet['ResultSet'] ) && is_array( $resultSet['ResultSet'] ) )
            return object( 'YahooData', $resultSet['ResultSet'] );
        elseif( is_array( $resultSet ) )
            return object( 'YahooData', $resultSet );
         
        $error = object( 'YahooData' );
        $error->setError( $resultSet );
         
        return $error;
    }
    
	/**
     * This is not staying here punk!
     */
    private function postHack( $data, $service, $domain )
	{
		$request = array(array(),array());
		
		$request[0]['url']  = 'http://search.yahooapis.com/ContentAnalysisService/V1/termExtraction';
		$request[0]['post'] = array();
		$request[0]['post']['appid']   = 'YahooDemo';
		$request[0]['post']['output']  = 'php';
		$request[0]['post']['context'] = $data;
		$r = $this->multiRequest($request);
		$resultSet = @unserialize($r[0]);
		
		//$a = object('YahooData');
		//$a->setResult(is_array($resultSet));
		//return $a;
		
		//$resultSet = @unserialize( $result );
	    
	    if( is_array( $resultSet ) && isset( $resultSet['ResultSet'] ) && is_array( $resultSet['ResultSet'] ) )
	        return object( 'YahooData', $resultSet['ResultSet'] );
	    elseif( is_array( $resultSet ) )
	        return object( 'YahooData', $resultSet );
	    
	    $error = object( 'YahooData' );
	    $error->setError( $resultSet );
	    
	    return $error;
	}
	
	private function stripHtml( $data )
	{
		return utf8_encode( urlencode( strip_tags( html_entity_decode( $data ) ) ) );
	}
	
	private function multiRequest($data, $options = array()) {
	
	  // array of curl handles
	  $curly = array();
	  // data to be returned
	  $result = array();
	
	  // multi handle
	  $mh = curl_multi_init();
	
	  // loop through $data and create curl handles
	  // then add them to the multi-handle
	  foreach ($data as $id => $d) {
	
	    $curly[$id] = curl_init();
	
	    $url = (is_array($d) && !empty($d['url'])) ? $d['url'] : $d;
	    curl_setopt($curly[$id], CURLOPT_URL,            $url);
	    curl_setopt($curly[$id], CURLOPT_HEADER,         0);
	    curl_setopt($curly[$id], CURLOPT_RETURNTRANSFER, 1);
	
	    // post?
	    if (is_array($d)) {
	      if (!empty($d['post'])) {
	        curl_setopt($curly[$id], CURLOPT_POST,       1);
	        curl_setopt($curly[$id], CURLOPT_POSTFIELDS, $d['post']);
	      }
	    }
	
	    // extra options?
	    if (!empty($options)) {
	      curl_setopt_array($curly[$id], $options);
	    }
	
	    curl_multi_add_handle($mh, $curly[$id]);
	  }
	
	  // execute the handles
	  $running = null;
	  do {
	    curl_multi_exec($mh, $running);
	  } while($running > 0);
	
	  // get content and remove handles
	  foreach($curly as $id => $c) {
	    $result[$id] = curl_multi_getcontent($c);
	    curl_multi_remove_handle($mh, $c);
	  }
	
	  // all done
	  curl_multi_close($mh);
	
	  return $result;
	}
}
?>