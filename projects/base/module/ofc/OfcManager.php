<?php
class OfcManager extends Object
{
    public function importOfcLib()
    {

        import('tools.ofc.lib.OFC.JSON_Format');

        import('tools.ofc.lib.OFC.Elements.OFC_Elements_Base');
        import('tools.ofc.lib.OFC.Elements.OFC_Elements_Axis');
        import('tools.ofc.lib.OFC.Elements.Axis.OFC_Elements_Axis_X');
        import('tools.ofc.lib.OFC.Elements.Axis.OFC_Elements_Axis_X_Label_Set');
        import('tools.ofc.lib.OFC.Elements.Axis.OFC_Elements_Axis_X_Label');
        import('tools.ofc.lib.OFC.Elements.Axis.OFC_Elements_Axis_Y');
        import('tools.ofc.lib.OFC.Elements.Axis.OFC_Elements_Axis_Y_Right');
        import('tools.ofc.lib.OFC.Elements.Legend.*');
        import('tools.ofc.lib.OFC.Elements.OFC_Elements_Title');
        import('tools.ofc.lib.OFC.Charts.OFC_Charts_Base');
		import('tools.ofc.lib.OFC.Charts.*');
        import('tools.ofc.lib.OFC.Charts.Area.*');
        //import('tools.ofc.lib.OFC.Charts.Bar.*');
        import('tools.ofc.lib.OFC.Charts.Bar.OFC_Charts_Bar_Filled');
        import('tools.ofc.lib.OFC.Charts.Bar.OFC_Charts_Bar_3d');
        //import('tools.ofc.lib.OFC.Charts.Bar.*');
        //import('tools.ofc.lib.OFC.Charts.Bar.*');
        //import('tools.ofc.lib.OFC.Charts.Line.*');
        //import('tools.ofc.lib.OFC.Charts.Scatter.*');

        import('tools.ofc.lib.OFC.OFC_Chart');

    }
}
?>