<?php
class Benchmark
{
  private $markers = array();
  private $memory  = array();
  
  static private $self = null;
  
  static public function getInstance()
  {
    if( self::$self ) return self::$self;

    self::$self = new Benchmark();

    return self::$self;
  }
  
  public function getAll()
  {
    return $this->markers;
  }
  
  public function setMark( $marker )
  {
    $this->markers[$marker] = microtime( true );
    $this->setMemory( $marker );
    
    return $this->markers[$marker];
  }
  
  /**
   * Returns the differance of $from, $to as seconds
   *
   * @param String $from
   * @param String $to
   * @param Int $round
   * @return float
   */
  public function getTime( $from, $to, $round=4 )
  {
    $start = isset( $this->markers[$from] ) ? $this->markers[$from] : $this->setMark( $from );
    $end   = isset( $this->markers[$to] )   ? $this->markers[$to]   : $this->setMark( $to );
    
    return round( ( $end-$start ), $round );
  }
  
  public function getMemory( $from, $to=null, $asBytes=true )
  {
    $start = isset( $this->memory[$from] ) ? $this->memory[$from] : $this->setMemory( $from );
    $end   = isset( $this->memory[$to] )   ? $this->memory[$to]   : ( is_string( $to ) ? $this->setMemory( $to ) : null );
    
    $result = $end ? $end-$start : $start;
    
    return ( is_bool( $to ) && !$to ) || !$asBytes ? $result/1048576 : $result;
  }
  
  private function setMemory( $marker )
  {
    return $this->memory[$marker] = memory_get_usage( true );
  }
}

function benchmark( $marker=null, $to=null, $round=4 )
{
  if( !$marker ) return Benchmark::getInstance();
  
  if( $to ) return Benchmark::getInstance()->getTime( $marker, $to, $round );
  
  return Benchmark::getInstance()->setMark( $marker );
}
?>