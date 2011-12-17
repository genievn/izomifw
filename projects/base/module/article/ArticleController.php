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
	public function createArticle($lang = null)
	{
		$render = $this->getTemplate('create_article');
		// include resources
		$this->getManager( 'html' )->addJs( config('root.url') . 'libs/extjs/ux/treecombo/treecombo.js', true);
		return $render;
	}
	/**
	 * undocumented function
	 *
	 * @return void
	 * @author Thanh Nguyen
	 **/
	public function listArticles()
	{
		$render = $this->getTemplate('list_articles');
		return $render;
	}
	/**
	 * undocumented function
	 *
	 * @return void
	 * @author Thanh Nguyen
	 **/
	public function articleJsonData($categoryId = null)
	{
		$limit = @$_REQUEST['limit'];
		$start = @$_REQUEST['start'];
		
		$render = $this->getTemplate('json');
		$em = $this->getManager('doctrine2')->getEntityManager();
		$query = $em->createQuery('SELECT COUNT(a.id) FROM Entity\Cms\Article a');;
		$count = $query->getSingleScalarResult();
		
		$query = $em->createQuery('SELECT a.id,a.title,a.description FROM Entity\Cms\Article a ORDER BY a.id DESC');
		$query->setMaxResults($limit);
		$query->setFirstResult($start);
		$result = $query->getArrayResult();
		$render->setTotalCount($count);
		$render->setArticles($result);
		return $render;		
	}
	public function saveArticle()
	{
		$title = @$_REQUEST['title'];
		$content = @$_REQUEST['content'];
		$image = @$_REQUEST['image'];
		$sub_title = @$_REQUEST['sub_title'];
		$published_date = @$_REQUEST['published_date'];
		$published_time = @$_REQUEST['published_time'];
		$expired_date = @$_REQUEST['expired_date'];
		$expired_time = @$_REQUEST['expired_time'];
		$author = @$_REQUEST['author'];
		$is_sticky = @$_REQUEST['is_sticky'];
		$is_hot = @$_REQUEST['is_hot'];
		$allow_comments = @$_REQUEST['allow_comments'];
		$show_comments = @$_REQUEST['show_comments'];
		
		$published_on = $expired_on = false;
		
		if(!empty($published_date))
		{
			if (!empty($published_time))
				$published_on = $published_date . " " . $published_time;
			else
				$published_on = $published_date;
		}
		
		$errors = array();
		
		if (empty($errors))
		{
			// there is no errors, save the article
			$a = new Entity\Cms\Article();
			$a->setTitle($title);
			$a->setContent($content);
			$a->setImage($image);
			$a->setSubTitle($sub_title);
			$a->setAuthor($author);
			
			if($published_on = new \DateTime($published_on) && $published_on instanceof \DateTime) $a->setPublishedOn($published_on);
			if($expired_on = new \DateTime($expired_on) && $expired_on instanceof \DateTime) $a->setExpiredOn($expired_on);
			
			if($is_hot == "1") $a->setHot(true); else $a->setHot(false);
			if($is_sticky == "1") $a->setSticky(true); else $a->setSticky(false);
			if($allow_comments == "1") $a->setAllowComments(true); else $a->setAllowComments(false);
			if($show_comments == "1") $a->setShowComments(true); else $a->setShowComments(false);
			
			$em = $this->getManager('doctrine2')->getEntityManager();
			$em->persist($a);
			$em->flush();
		}
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
		$root_url = config('root.url');
		// include resources
		$this->getManager( 'html' )->addJs( $root_url . 'libs/extjs/ux/treecombo/treecombo.js', true);
		
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
			case 'before':
			case 'after':
				// move the				
				try {
					if ($position == 'after')
						$repo->persistAsNextSiblingOf($source, $target);				
					else 
						$repo->persistAsPrevSiblingOf($source, $target);
					//$em->persist($source);
					$em->flush();
					
					$render->setSuccess(true);
					if ($target)
						$render->setMessage('Category ('.$source->getTitle().') has been moved '.$position.' ('.$target->getTitle().')');
					return $render;
				} catch (Exception $e) {
					$render->setSuccess(false);
					$render->setMessage('Error while moving ('.$source->getTitle().') '.$position.' ('.$target->getTitle().'):'.$e->toString());
					return $render;
				} 
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
    /*
    public function createArticle()
    {
        $render = $this->getTemplate('create_article');
        $type = $this->getManager('tree')->existTreeType(array('codename'=>CATEGORY_CODENAME));
        
        $render->setTreeType($type);
        return $render;
    }
	*/

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