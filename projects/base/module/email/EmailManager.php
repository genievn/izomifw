<?php
class EmailManager extends Object
{
    public function email( $to, $from, $subject, $message, $cc=null, $bcc=null, $asHtml=false )
    {
        $headers = "";
        
        if( $asHtml ) $headers = "MIME-Version: 1.0\r\nContent-Type: text/html; charset=utf-8\r\n";
        $headers.= "From: {$this->getEmailAddresses( $from )}\r\n";
        $headers.= "Reply-To: {$this->getEmailAddresses( $from )}\r\n";
        if( $cc ) $headers.= "Cc: {$this->getEmailAddresses( $cc )}\r\n";
        if( $bcc ) $headers.= "Bcc: {$this->getEmailAddresses( $bcc )}\r\n";
        
        $message = $this->renderMessage( $message );
        
        mail( $this->getEmailAddresses( $to ), $subject, $message, $headers );
    }
    
    private function getEmailAddresses( $addresses )
    {
        if( is_string( $addresses ) ) $addresses = array( $addresses );
        
        $result = array();
        
        foreach( $addresses as $name => $address )
            if( $name )
                $result[] = "{$name} <{$address}>";
            else
                $result[] = $address;
        
        return implode( ', ', $result );
    }
    
    private function renderMessage( $render )
    {
        if( is_string( $render ) ) return $render;
        
        if( get_class( $render ) != 'izRender' ) return '';
        
        return $render->render();
    }
}
?>