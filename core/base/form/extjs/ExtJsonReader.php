<?php
class ExtJsonReader extends Object
{
    public function doHtml($options = null)
    {
        $attrs = $this->getAttributes();
        $idProperty = is_null(@$attrs["idProperty"]) ? "" : "idProperty: '{$attrs["idProperty"]}'";
        $totalProperty = is_null(@$attrs["totalProperty"]) ? "" : ", totalProperty: '{$attrs["totalProperty"]}'";
        $root = is_null(@$attrs["root"]) ? "" : ", root: '{$attrs["root"]}'";

        if (@$attrs["record"]){
            $record = "
            , Ext.data.Record.create(
                {$attrs["record"]}
            )
            ";
        }else $record = "";
        $html = "
            new Ext.data.JsonReader(
                {
                    {$idProperty}
                    {$totalProperty}
                    {$root}
                }
                {$record}
            )

        ";
        return $html;
    }
}
?>