<?php
class izManager extends Object {
	protected $reader = null;
	protected $writer = null;
	
	public function __call($method, $args){
		switch (substr($method, 0, 6)){
			case 'select':
				return call_user_func_array(array($this->getReader(),'selectRecords'),$args);
			case 'counts':
				return call_user_func_array( array( $this->getReader(), 'countsRecords' ), $args );
			case 'choose':
				return call_user_func_array( array( $this->getReader(), 'chooseRecord' ), $args );
			case 'insert':
				return call_user_func_array( array( $this->getWriter(), 'insertRecord' ), $args );
			case 'update':
				return call_user_func_array( array( $this->getWriter(), 'updateRecord' ), $args );
			case 'delete':
				return call_user_func_array( array( $this->getWriter(), 'deleteRecord' ), $args );
		}
		return parent::__call($method, $args);
	}
	
	public function getReader(){
		if ($this->reader==null){
			if (class_exists($this->toString().'Reader')){
				$class = $this->toString().'Reader';				
			}else{
				$class = config("{$this->toString()}.data_reader",config('root.data_reader'));
			}
			$string = config("{$this->toString()}.data_reader_string",config('root.data_reader_string'));			
			if (!$class) trigger_error("Cannot load Reader for {$this->toString()}",E_USER_ERROR);
			$this->reader = $this->loadDataSource($class,$string);
		}
		return $this->reader;
	}
	public function getWriter(){
		if ($this->writer == null){
			if (class_exists($this->toString().'Writer')){
				$class = $this->toString().'Writer';
			}else{
				$class = config("{$this->toString()}.data_writer",config('root.data_writer'));
			}
			$string = config( "{$this->toString()}.data_writer_string", config( 'root.data_writer_string' ) );
			if( !$class ) trigger_error( "Cannot load Writer for {$this->toString()}", E_USER_ERROR );
			$this->writer = $this->loadDataSource( $class, $string );
			
		}
		return $this->writer;
	}
	/*
	
	*/
	private function loadDataSource($class, $string){
		$object = object($class);
		$object->init($string);
		return $object;
	}
}
?>
