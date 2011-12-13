<?php
define('CATEGORY_CODENAME','base.article.category');
use Entity\Cms\ArticleCategory;
use Gedmo\Tree\Entity\Repository\NestedTreeRepository;

class ArticleController extends Object {
	/**
	 * Get the Submodules of this Module
	 *
	 * @return void
	 * @author Thanh Nguyen
	 **/
	public function getSubmodules()
	{
		$render = $this->getTemplate('submodules');
		return $render;
	}
	/**
	 * undocumented function
	 *
	 * @return void
	 * @author Thanh Nguyen
	 **/
	public function categoryTree()
	{
		$render = $this->getTemplate('category_tree');
		$this->getManager( 'html' )->addCss( locale( 'jslibs/dojo/dojox/grid/resources/claroGrid.css', true ));
		$this->getManager( 'html' )->addCss( locale( 'jslibs/dojo/dojox/grid/resources/Grid.css', true ));
		
		$em = $this->getManager('doctrine2')->getEntityManager();
		$repo = $em->getRepository('Entity\Cms\ArticleCategory');
		$tree = $repo->childrenHierarchy(null, $direct=true,$html=true, $options=array(
			
		));
		return $render;
	}
	/**
	 * undocumented function
	 *
	 * @return void
	 * @author Thanh Nguyen
	 **/
	public function categoryGrid()
	{
		
	}
	/**
	 * undocumented function
	 *
	 * @return void
	 * @author Thanh Nguyen
	 **/
	public function categoryTreeJsonData($lang = null)
	{
		if (!$lang) $lang = config('root.current_lang');
		$render = $this->getTemplate('json');
		$em = $this->getManager('doctrine2')->getEntityManager();
		$repo = $em->getRepository('Entity\Cms\ArticleCategory');
		
		$tree = $repo->childrenHierarchy();
		$render->setText('.');
		$render->setChildren($tree);
		return $render;
	}
	/**
	 * Form to create a category
	 *
	 * @return void
	 * @author Thanh Nguyen
	 **/
	public function createCategory()
	{
		$render = $this->getTemplate('create_category');
		// include resources
		$this->getManager( 'html' )->addJs( locale( 'jslibs/extjs/ux/treecombo/treecombo.js', true ), true);
		
		$em = $this->getManager('doctrine2')->getEntityManager();
		$repo = $em->getRepository('Entity\Cms\ArticleCategory');
		$categories = $repo->children();
		$render->setCategories($categories);
		return $render;
	}
	/**
	 * Save a new category
	 *
	 * @return void
	 * @author Thanh Nguyen
	 **/
	public function saveCategory()
	{
		$render = $this->getTemplate('json');
		$em = $this->getManager('doctrine2')->getEntityManager();
		
		$title = $_REQUEST["title"];
		$parentId = $_REQUEST["parentId"];
		// find the parent category;
		if ($parentId)
		{
			$parent = $em->find('Entity\Cms\ArticleCategory', $parentId);
			// loop check
			
			if ($parent && $parent->getTitle() == $title) {
				$render->setSuccess(false);
				$render->setMessage('Category ('.$title.' existed)');
				return $render;
			}			
		}
		
		$category = new ArticleCategory();
		$category->setTitle($title);
		// if a parent is specified
		if ($parent) $category->setParent($parent);
		
		try {
			$em->persist($category);
			$em->flush();
			$render->setSuccess(true);
			$render->setMessage('Category ('.$title.') saved');
		}catch(Exception $e){
			$render->setSuccess(false);
			$render->setMessage('Error while saving category ('.$title.'): ' . $e->getMessage());
		}
		return $render;
	}
	/**
	 * Reorder the category, should be called to return json
	 *
	 * @return void
	 * @author Thanh Nguyen
	 **/
	public function reorderCategory()
	{
		$sourceId = $_REQUEST["sourceId"];
		$targetId = $_REQUEST["targetId"];
		$position = $_REQUEST["position"];
		$render = $this->getTemplate('json');
		$em = $this->getManager('doctrine2')->getEntityManager();
		$repo = $em->getRepository('Entity\Cms\ArticleCategory');
		if ($sourceId) $source = $em->find('Entity\Cms\ArticleCategory', $sourceId);
		if ($targetId) $target = $em->find('Entity\Cms\ArticleCategory', $targetId);
		switch ($position) {
			case 'append':
				// make target to be source's parent
				if ($target) $source->setParent($target); else $source->setParent(null);
				try {					
					$em->persist($source);
					$em->flush();
					
					$render->setSuccess(true);
					if ($target)
						$render->setMessage('Category ('.$source->getTitle().') has new parent ('.$target->getTitle().')');
					else
						$render->setMessage('Category ('.$source->getTitle().') has new parent (root)');
					return $render;
				} catch (Exception $e) {
					$render->setSuccess(false);
					$render->setMessage('Error while setting new parent ('.$target->getTitle().') for category ('.$source->getTitle().')');
					return $render;
				}
				break;
			case 'after':
				// move the
				 
				break;
			default:
				# code...
				break;
		}
	}
	/**
	 * undocumented function
	 *
	 * @return void
	 * @author Thanh Nguyen
	 **/
	public function editCategory($categoryId)
	{
		if (!$categoryId) return false;
		$render = $this->getTemplate('edit_category');
		$em = $this->getManager('doctrine2')->getEntityManager();
		$category = $em->find('Entity\Cms\ArticleCategory', $categoryId);
		if(!$category) return false;
		
		$render->setCategory($category);
		return $render;
	}	
    public function createArticleForNodeId($id)
    {
        $render = $this->getTemplate('create_article');
        $type = $this->getManager('tree')->existTreeType(array('codename'=>CATEGORY_CODENAME));
        
        $render->setTreeType($type);
        return $render;
    }
    
    public function createArticle()
    {
        $render = $this->getTemplate('create_article');
        $type = $this->getManager('tree')->existTreeType(array('codename'=>CATEGORY_CODENAME));
        
        $render->setTreeType($type);
        return $render;
    }


    public function viewProcess($id)
    {

        $process = $this->getManager('wfmc')->getProcess($id);
        #print_r($process);
    }

    public function start()
    {
        /*
        $account = $this->getManager('auth')->getAccount();
        if (!$account) return;
        $account_id = $account->getId();
        */
        $account_id = 1;
        
        $publication = WfArticlePublication::getDefinition();
        $context = new WfArticleContext();
        # create new process instance
        $process = $publication->getProcessForContext($context);

        #$process->definition->integration->setController($this);
        $this->getManager('wfmc')->saveProcess($process);
        #$process->definition->integration->setController($this);
        #$process->definition->integration->setWorkitemsHandler(new WorkitemDbStorage($process->definition->integration));
        
        # prepare the InputParameter for the workflow
        $args = array();
        $args[] = $account_id;

        $process->start($args);
        
        //var_dump($process);


        # now process stop at Prepare activity; it starts the Prepare application
        # display input form for getting data
    }

    public function save()
    {

        # finish Prepare activity

    }

    public function help()
    {

        $application = new ApplicationBase($str = 'hello');
        echo $application->str;
        $application->setHello('Thanh');
        echo $application->getHello();
    }

    public function tasks()
    # get tasks (workitems) for current user
    {

        $render = $this->getTemplate('user_tasks');

        $account = $this->getManager('auth')->getAccount();

        $workitems = $this->getManager('wfmc')->getWorkItemsForParticipant($account);
        var_dump($workitems);
        #$workitem = $workitems[0][0];

        #return $workitem->gui($this);

        #return $render;
    }

    public function workItemGui($id)
    {
        $workitem = $this->getManager('wfmc')->getWorkItemFromId($id);
        $workitem = $workitem[0];
        $workitem->setController($this);

        if ($workitem) return $workitem->gui();
    }

    public function workItemSubmit()
    {
        $workitem_id = $_REQUEST['workitem_id'];
        $activity_id = $_REQUEST['activity_id'];
        $process_id = $_REQUEST['process_id'];
        #$where = "workitem_id = '{$workitem_id}' AND activity_id = '$activity_id' AND process_id = '{$process_id}'";
        $process = $this->getManager('wfmc')->getProcess($process_id);
        $workitem = $process->activities[$activity_id]->workitems->get($workitem_id);
        $workitem[0]->finish();

        //$workitem->finish();
        #if ($workitem) return $workitem->submit();

    }
}
?>