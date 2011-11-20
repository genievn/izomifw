<?php
class EmailPlugin extends Object
{
    public function email( $to, $from, $subject, $message, $cc=null, $bcc=null, $asHtml=false )
    {
        $this->getManager( 'email' )->email( $to, $from, $subject, $message, $cc, $bcc, $asHtml );
    }
}
?>