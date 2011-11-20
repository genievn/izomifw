<?php
class Validate extends Object
{
    private $validate = array();
    
    public function insertValidateRule( $key, $type='string', $canBeNull=true, $maxLength=0, $minLength=0 )
    {
        if( $type instanceof ValidateRule )
            $this->validate[$key] = $type;
        else
            $this->validate[$key] = new ValidateRule( $type, $canBeNull, $maxLength, $minLength );
    }
    
    public function removeValidateRule( $key )
    {
        if( isset( $this->validate[$key] ) )
            unset( $this->validate[$key] );
    }
    
    public function getValidateRule( $key )
    {
        if( isset( $this->validate[$key] ) )
            return $this->validate[$key];
        
        return null;
    }
    
    public function isValid( $key=null )
    {
        if( $key )
            if( isset( $this->validate[$key] ) )
                return $this->validate[$key]->validate( $this->__get( $key ) );
        
        if( empty( $key ) )
            foreach( $this->validate as $key => $rule )
                if( !$rule->validate( $this->__get( $key ) ) )
                    return false;
        
        return true;
    }
}

class ValidateRule
{
    private $type = null;
    private $canBeNull = null;
    private $maxLength = null;
    private $minLength = null;
    
    public function __construct( $type='string', $canBeNull=true, $maxLength=0, $minLength=0 )
    {
        $this->setType( $type );
        $this->setCanBeNull( $canBeNull );
        $this->setMaxLength( $maxLength );
        $this->setMinLength( $minLength );
    }
    
    public function validate( $value )
    {
        if( empty( $value ) && $this->getCanBeNull() ) return true;
        
        if( empty( $value ) && !$this->getCanBeNull() ) return false;
        
        $type = strtoupper( $this->getType() );
        
        switch( $type )
        {
            case 'INT':
			case 'INTEGER':
				$result = strcmp( $value, intval( $value ) ) === 0  ? true : false;
				break;

			case 'FLOAT':
			case 'DOUBLE':
				$result = strcmp( $value, floatval( $value ) ) === 0  ? true : false;
				break;

			case 'BOOL':
			case 'BOOLEAN':
				$result = strcmp( strtolower( $value ), 'true' ) === 0 || strcmp( strtolower( $value ), 'false' ) === 0 || $value == 1 || $value == 0 ? true : false;
				break;

			case 'ARRAY':
				$result = is_array( $value );
				break;

			case 'STRING':
				$result = is_string( $value );
				break;
				
		    case 'CHARS':
				preg_match_all( '/[A-Z0-9_-]/i', $value, $match );
				$match = implode( '', $match[0] );
				$result = strcmp( $match, $value ) == 0 ? true : false;
				break;
		    
			case 'DOMAIN':
				preg_match_all( '/[A-Z0-9._-]/i', $value, $match );
				$match = implode( '', $match[0] );
				$result = strcmp( $match, $value ) == 0 ? true : false;
				break;
				
			case 'EMAIL':
			    preg_match_all( '/^[A-Z0-9._-]+@[A-Z0-9][A-Z0-9.-]{0,61}[A-Z0-9]\.[A-Z.]{2,6}$/i', $value, $match );
				$match = implode( '', $match[0] );
				$result = strcmp( $match, $value ) == 0 ? true : false;
				break;
		      
			case 'OBJECT':
				$result = is_object( $value );
				break;
			case 'CAPTCHA':	
				session_start();			
				$result = strcmp(@$_SESSION['captcha'], $value) == 0 ? true : false;
				break;
			default:
				$result = true;
				break;
        }
        
        if( $result === false ) return false;
        
        if( $this->getMaxLength() > 0 && strlen( $value ) > $this->getMaxLength() ) return false;
        
        if( $this->getMinLength() > 0 && strlen( $value ) < $this->getMinLength() ) return false;
        
        return true;
    }
    
    public function setType( $type ){ $this->type = $type; }
    public function setCanBeNull( $canBeNull ){ $this->canBeNull = $canBeNull; }
    public function setMaxLength( $maxLength ){ $this->maxLength = $maxLength; }
    public function setMinLength( $minLength ){ $this->minLength = $minLength; }
    
    public function getType(){ return $this->type; }
    public function getCanBeNull(){ return $this->canBeNull; }
    public function getMaxLength(){ return $this->maxLength; }
    public function getMinLength(){ return $this->minLength; }
}
?>