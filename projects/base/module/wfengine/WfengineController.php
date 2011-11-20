<?php
/**
 * Wfengine Controller
 *
 * @package WfengineController
 * @author Thanh H. Nguyen
 * @email nguyenhuuthanh@gmail.com
 */
class WfengineController extends Object
{
	// ============================
	// = AUTO GENERATED FUNCTIONS =
	// ============================
	public function defaultCall(){
		$render = $this->getTemplate('default');
		return $render;
	}
	public function admin($model = null){
		$render = $this->getTemplate('admin');
		$render->setModel($model);
		return $render;
	}

	// =============================
	// = YOUR OTHER FUNCTIONS HERE =
	// =============================
	public function test(){
		$doctrinecp = $this->getManager('doctrinecp');
		$doctrinecp->importAllPackages();


		# find all the transitions begins from this place (via INWARDS ARC)
		$p = array(
            "model" => "WfArc"
            //, "groupBy" => "m.pvn_catalog_id"
            , "leftJoin" => array("a"=>"WfTransition")
            , "where" => array(
                "where" => "wf_id = 1 AND place_id = 1 AND direction = 0"
            )
            , "select" => "m.wf_id, m.transition_id, place_id, direction, arc_type, a.task_id, a.role_id, a.transition_trigger, a.time_limit"
        );
        $records = $doctrinecp->select($p);
        print_r($records);
	}

	public function workflowDefinition($workflow_id)
	{
		$doctrinecp = $this->getManager('doctrinecp');
		$doctrinecp->importAllPackages();


		# find all the transitions begins from this place (via INWARDS ARC)
		$p = array(
            "model" => "WfWorkflow"
            //, "groupBy" => "m.pvn_catalog_id"
            //, "leftJoin" => array("a"=>"WfTransition")
            , "where" => array(
                "where" => "id = $workflow_id"
            )
            , "select" => "m.*"
        );
        $records = $doctrinecp->select($p);

        if (!$records) return;

        $workflow_name = $records[0]['wf_name'];

        # getting all places
        $p = array(
            "model" => "WfPlace"
            //, "groupBy" => "m.pvn_catalog_id"
            //, "leftJoin" => array("a"=>"WfTransition")
            , "where" => array(
                "where" => "wf_id = $workflow_id"
            )
            , "select" => "m.*"
        );
        $records = $doctrinecp->select($p);

        $places = array();
        foreach ($records as $record)
        {
        	$place_id = $record['id'];
        	$place_name = $record['place_name'];
        	$places[$place_id] = $place_name;
        }

        # getting all transitions
        $p = array(
            "model" => "WfTransition"
            //, "groupBy" => "m.pvn_catalog_id"
            //, "leftJoin" => array("a"=>"WfTransition")
            , "where" => array(
                "where" => "wf_id = $workflow_id"
            )
            , "select" => "m.*"
        );
        $records = $doctrinecp->select($p);

        $transitions = array();
        foreach ($records as $record)
        {
        	$transition_id = $record['id'];
        	$transition_name = $record['transition_name'];
        	$task_id = $record['id'];

        	$transitions[$transition_id] = $transition_name;
        }
        # getting all the arcs
        $p = array(
            "model" => "WfArc"
            //, "groupBy" => "m.pvn_catalog_id"
            //, "leftJoin" => array("a"=>"WfTransition")
            , "where" => array(
                "where" => "wf_id = $workflow_id"
            )
            , "select" => "m.*"
        );
        $records = $doctrinecp->select($p);

        $arcs = array();
        foreach ($records as $record)
        {
        	$transition_id = $record['transition_id'];
        	$place_id = $record['place_id'];
        	$arc_type = $record['arc_type'];
        	$direction = $record['direction'];
        	$pre_condition = $record['pre_condition'];
        	$arcs[] = array($transition_id, $place_id, $direction, $pre_condition);
        }

        return array(
        	"name"=>$workflow_name,
        	"places"=>$places,
        	"transitions"=>$transitions,
        	"arcs"=>$arcs
        );
	}

	public function workflowAsDot($workflow_id)
	{
		$workflow = $this->workflowDefinition($workflow_id);

		$workflow_name = $workflow['name'];
		$places = $workflow['places'];
		$transitions = $workflow['transitions'];
		$arcs = $workflow['arcs'];

        # enough information to generate DOT format
        $G = "rankdir=LR;\n";

        foreach ($arcs as $arc)
        {
        	if ($arc[3]) $arc_label = $pre_condition;
        	else $arc_label = "";

        	if ((int)$arc[2] == 0)
        	{
        		# INWARDS arc: from Place to Transition
        		$G .= "P".$arc[1]." -> T".$arc[0]." [label=\"$arc_label\"];\n";
        	}else{
        		# OUTWARDS arc: from Transition to Place
        		$G .= "T".$arc[0]." -> P".$arc[1]." [label=\"$arc_label\"];\n";
        	}
        }
        foreach ($places as $place_id => $place_name)
        {
        	$G .= "P".$place_id . " [label=\"$place_name\",style=\"filled\",color=\"#00ff00\"]\n";
        }
        foreach ($transitions as $transition_id => $transition_name)
        {
        	$G .= "T".$transition_id . " [shape=rectangle, label=\"$transition_name\"];\n";
        }

        $G .= "overlap=false\n;label=\"$workflow_name\";\n";

        $dot = "digraph G {\n $G }";

        return $dot;
	}

	public function workflowCaseAsDot($case_id)
	{
		$doctrinecp = $this->getManager('doctrinecp');
		$doctrinecp->importAllPackages();

		$p = array(
            "model" => "WfCase"
            //, "groupBy" => "m.pvn_catalog_id"
            //, "leftJoin" => array("a"=>"WfTransition")
            , "where" => array(
                "where" => "id = $case_id"
            )
            , "select" => "m.*"
        );
        $records = $doctrinecp->select($p);

        if (count($records) !== 1) return;

        # get first record;
        $case = $records[0];
        $wf_id = $case['wf_id'];

        # get workflow definitions
        $workflow = $this->workflowDefinition($wf_id);

        $workflow_name = $workflow['name'];
		$places = $workflow['places'];
		$transitions = $workflow['transitions'];
		$arcs = $workflow['arcs'];

        # get current tokens
        $p = array(
            "model" => "WfToken"
            //, "groupBy" => "m.pvn_catalog_id"
            //, "leftJoin" => array("a"=>"WfTransition")
            , "where" => array(
                "where" => "case_id = $case_id"
            )
            , "select" => "m.*"
        );
        $records = $doctrinecp->select($p);
        $tokens = array();
        foreach ($records as $token)
        {
        	$place_id = $token['place_id'];
        	$tokens[$place_id] = array($token['token_status']);
        }


        #get current workitem
        $p = array(
            "model" => "WfWorkitem"
            //, "groupBy" => "m.pvn_catalog_id"
            //, "leftJoin" => array("a"=>"WfTransition")
            , "where" => array(
                "where" => "case_id = $case_id"
            )
            , "select" => "m.*"
        );
        $records = $doctrinecp->select($p);
        $workitems = array();
        foreach ($records as $workitem)
        {
        	$transition_id = $workitem['transition_id'];
        	$workitems[$transition_id] = array($workitem['workitem_status']);
        }

        # enough information to generate DOT format
        $G = "rankdir=LR;\n";

        foreach ($arcs as $arc)
        {
        	if ($arc[3]) $arc_label = $pre_condition;
        	else $arc_label = "";

        	if ((int)$arc[2] == 0)
        	{
        		$G .= "P".$arc[1]." -> T".$arc[0]." [label=\"$arc_label\"];\n";
        	}else{

        		$G .= "T".$arc[0]." -> P".$arc[1]." [label=\"$arc_label\"];\n";
        	}
        }


        foreach ($places as $place_id => $place_name)
        {
        	if (array_key_exists($place_id, $tokens))
        	{
        		$token_status = (int)$tokens[$place_id][0];
        	}else{
        		$token_status = -1;
        	}

        	switch ($token_status)
        	{
        		case 0:
        			# Token is FREE, mark as GREEN
        			$G .= "P".$place_id . " [label=\"$place_name\",style=\"filled\",color=\"#00ff00\"]\n";
        			break;
        		default:
        			# place has not been reached
        			$G .= "P".$place_id . " [label=\"$place_name\",style=\"filled\",color=\"#888888\"]\n";
        	}

        }

        foreach ($transitions as $transition_id => $transition_name)
        {

        	if (array_key_exists($transition_id, $workitems))
        	{
        		$workitem_status = (int)$workitems[$transition_id][0];
        	}else{
        		$workitem_status = -1;
        	}

        	switch ($workitem_status)
        	{
        		case 0:
        			# transition is ENABLED
        			$G .= "T".$transition_id . " [shape=rectangle,label=\"$transition_name\",style=\"filled\",color=\"#ffff00\"];\n";
        			break;
        		case 1:
        			# transition is in progress
        			break;
        		case 2:
        			# transition is cancelled
        			break;
        		case 3:
        			# transition is finished
        			break;
        		default:
        			$G .= "T".$transition_id . " [shape=rectangle,label=\"$transition_name\",style=\"filled\",color=\"#888888\"];\n";
        	}
        }

        $G .= "overlap=false\n;label=\"$workflow_name\";\n";

        $dot = "digraph G {\n $G }";

        return $dot;
	}

	public function workflowAsXdot($workflow_id)
	{
		$dot = $this->workflowAsDot($workflow_id);
		return $this->xdot($dot);
	}

	public function workflowAsSvg($workflow_id)
	{
		$dot = $this->workflowAsDot($workflow_id);
		return $this->svg($dot);
	}

	public function workflowCaseAsXdot($case_id)
	{
		$dot = $this->workflowCaseAsDot($case_id);
		return $this->xdot($dot);
	}

	public function svg($dot)
	{
		$svglines = array();

        exec('echo \''.$dot.'\' | dot -Tsvg', $svglines, $ret);

		if ($ret == 0) $svg = implode("\n", $svglines);
		else $svg = "Error when parsing dot file";

		header('Content-Type: image/svg+xml');
		echo $svg;
		die();

        //echo $dot;


        # $render = $this->getTemplate('xdot');
        # $render->setXdot($xdot);

        # return $render;
	}

	public function xdot($dot)
	# render the workflow as dot format
	{

        $xdotlines = array();

        exec('echo \''.$dot.'\' | dot -Txdot', $xdotlines, $ret);

		if ($ret == 0) $xdot = implode("\n", $xdotlines);
		else $xdot = "Error when parsing dot file";

        //echo $dot;


        $render = $this->getTemplate('xdot');
        $render->setXdot($xdot);

        return $render;
	}

	public function visualizeWorkflow($workflow_id)
	{
		$this->getManager('izojs')->addLibCanviz();
		config('.layout.template','xhtml-transitional');

	    $render = $this->getTemplate('workflow_visualize_2d');
	    $render->setWorkflowId($workflow_id);


	    return $render;
	}

	public function visualizeWorkflowCase($case_id)
	{
		$this->getManager('izojs')->addLibCanviz();
		config('.layout.template','xhtml-transitional');

	    $render = $this->getTemplate('workflow_case_visualize_2d');
	    $render->setCaseId($case_id);


	    return $render;
	}
}
?>