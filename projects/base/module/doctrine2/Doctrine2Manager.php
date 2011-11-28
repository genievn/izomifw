<?php
use Doctrine\Common\Cache\ArrayCache,
    DoctrineExtensions\Versionable\VersionManager,
    Entity\Base\IContentManagable,
    DoctrineExtensions\Paginate\Paginate,
    Entity\Base\ContentItem,
    Entity\Base\ContentItemTranslation;

define('CONTENTITEM_ENTITY', 'Entity\Base\ContentItem');
define('CONTENTITEM_TRANSLATION_ENTITY', 'Entity\Base\ContentItemTranslation');
define('TRANSLATION_ENTITY','Entity\Base\Translation');

class Doctrine2Manager extends Object
{
    public function create($entity)
    {
        //$em = $this->getReader()->getEntityManager();
        $instance = new $entity;
        return $instance;
    }
    public function save($instance)
    {
        $em = $this->getWriter()->getEntityManager();
        $em->persist($instance);
    }

	public function remove($instance)
	{
		$em = $this->getWriter()->getEntityManager();
		$associations = $this->getAssociationMappings(get_class($instance));
		# remove all the associations of this instance
		foreach(array_keys($associations) as $a)
		{
			$instance->assign($a, null);
			$em->persist($instance);
			$em->flush();
		}
		# remove the instance itself;
		$em->remove($instance);
	}

    public function saveAssociation($instance)
    {

    }

    public function flush()
    {
        $em = $this->getWriter()->getEntityManager();
        $em->flush();
    }

    public function getConnection()
    {
        return $this->getReader()->getEntityManager()->getConnection();
    }
    
    public function getEntityManager()
    {
        return $this->getReader()->getEntityManager();
    }

    public function beginTransaction()
    {
        $em = $this->getWriter()->getEntityManager();
        $con = $em->getConnection();
        $con->beginTransaction();
    }

    public function commit()
    {
        $em = $this->getWriter()->getEntityManager();
        $con = $em->getConnection();
        $con->commit();
    }

    public function rollback()
    {
        $em = $this->getWriter()->getEntityManager();
        $con = $em->getConnection();
        $con->rollback();
    }

    public function update()
    {


    }
    /**
     * delete function.
     * delete records from entity
     * 
     * @access public
     * @param String $entity
     * @param Array $ids
     * @return Boolean
     */
    public function delete($entity, $ids)
    {
        $em = $this->getWriter()->getEntityManager();
        $records = $this->getRecordsByIds($entity, $ids);
        
        if (count($records)>0)
        {
            $em->beginTransaction();
            foreach($records as $record)
            {
                $em->remove($record);
            }
            $em->flush();
            $em->commit();
        }
        return true;
    }

	public function find($entity, $id)
	{
		$em = $this->getWriter()->getEntityManager();
		$instance = $em->find($entity, $id);
		return $instance;
	}
	public function findAll($entity)
	{
		$em = $this->getWriter()->getEntityManager();
		$collection = $em->getRepository($entity)->findAll();
		return $collection;
	}
	public function executeDql($dql)
	{
		$em = $this->getWriter()->getEntityManager();
		return $em->createQuery($dql)->getArrayResult();
	}
	public function select($options)
	{
		$em = $this->getReader()->getEntityManager();
		$where = $this->selectFilterToWhere($options['filter']);
		
		$select = $options['select'];
		
		$qb = $em->createQueryBuilder();
		$qb = $qb->select($select);
		$qb = $qb->add('where', $where);
		if (!empty($options["from"]))
		{
            $from = $options["from"];
            //$qb = $qb->from($options["from"]);
            $qb = $qb->add('from', $from);
            
		}
		if (!empty($options['innerJoin']))
		{
            $innerJoin = $options['innerJoin'];
            foreach ($innerJoin as $ij)
            {
                $qb = $qb->innerJoin($ij);
            }            
		}
		
		if (!empty($options['leftJoin']))
		{
            $leftJoin = $options['leftJoin'];
            foreach ($leftJoin as $lj)
            {
                $qb = $qb->leftJoin($lj["join"], $lf["alias"], $lf["conditionType"], $lf["condition"]);
            }
		}
		
		if (!empty($options["where"]))
		{
            $where = $options["where"];
            foreach ($where as $w)
            {
                if ($w[0] == "and")
                {
                    //$qb = $qb->andWhere($w[1]);
                }else{
                    //$qb = $qb->orWhere($w[1]);
                }                
            }
		}
		
		if (!empty($options["groupBy"]))
		{
            $qb = $qb->addGroupBy($options["groupBy"]);
		}		
		
		
		
		$query = $qb->getQuery();
		var_dump($query->getDql());
		
		$record = $query->setFirstResult((int)$options['start'])
						->setMaxResults((int)$options['limit'])
						->getArrayResult();

		
		$count = $em->createQuery($dql)
					->getSingleScalarResult();
		return array($records, $count);	
		
	}
	/**
	 * retrieve records, optional with filters 
	 *
	 * @param string $entity 
	 * @param string $options 
	 * @return void
	 * @author Nguyen Huu Thanh
	 */
	/*
	public function retrieve($entity, $options)
	{
		$em = $this->getReader()->getEntityManager();
		$where = $this->selectFilterToWhere($options['filter']);
		$dql = "SELECT e FROM $entity e WHERE $where";
		$dummyObject = new $entity;
		if ($dummyObject instanceof ITranslatable)
		{
            $translationEntity = $entity.'Translation';
            $dql = "SELECT e,t FROM $entity e JOIN e.translations t WHERE e.default_lang = t.lang AND $where";
		}
		
		if ($dummyObject instanceof IContentManagable)
		{
            $dql = "SELECT e,c FROM $entity e INNER JOIN e.contentitem c WHERE $where";
		}
			
		$query = $em->createQuery($dql)						
						->setFirstResult((int)$options['start'])
						->setMaxResults((int)$options['limit']);
        $records = $query->getArrayResult();
		# count the records;for paging
		$count = Paginate::count($query);
		$dummyObject = null;
		return array($records, $count);	
		
	}
	*/
	public function retrieve($entity, $options)
	{
		$em = $this->getReader()->getEntityManager();
		$where = $this->selectFilterToWhere($options['filter']);
		$dql = "SELECT e FROM $entity e WHERE $where";
		$query = $em->createQuery($dql)						
						->setFirstResult((int)$options['start'])
						->setMaxResults((int)$options['limit']);
        $records = $query->getArrayResult();
		# count the records;for paging
		$count = Paginate::count($query);
		$dummyObject = null;
		return array($records, $count);	
		
	}
	
	public function retrieveAssociation($entity, $association, $id)
	{
        $em = $this->getReader()->getEntityManager();
        $cm = $em->getClassMetadata($entity);
        $am = $cm->getAssociationMappings();
        
        
        
        if (array_key_exists($association, $am))
        {
            $dql = "SELECT e,a FROM ".$entity." e JOIN e.{$association} a WHERE e.id = ?1";
            $query = $em->createQuery($dql);
            $query->setParameter(1, $id);
            $records = $query->getArrayResult();
            
            if (count($records) == 1)
            {
                $associationRecords = $records[0][$association];
                return $associationRecords;
            }
            
        }
        
        return null;        
	}
	/**
	 * retrieveContentItemTranslation function.
	 * retrieve a translation of a contentitem
	 * 
	 * @access public
	 * @param integer $contentitem_id
	 * @param string $lang
	 * @return ArrayCollection
	 */
	public function retrieveTranslation($id, $lang)
	{
        $em = $this->getReader()->getEntityManager();
        
        $dql = "SELECT e FROM ".TRANSLATION_ENTITY." e WHERE e.entity_id=?1 AND e.lang=?2";
        
        $query = $em->createQuery($dql);
        $query->setParameter(1, $id);
        $query->setParameter(2, $lang);
        
        $records = $query->getResult();
        return $records;
	}
	/**
	 * deleteContentItemTranslations function.
	 * 
	 * @access public
	 * @param integer $contentitem_id
	 * @param array $langArray
	 * @return void
	 */
	public function deleteContentItemTranslations($entity, $contentitem_id, $langArray)
	{
        $em = $this->getWriter()->getEntityManager();
        $em->beginTransaction('DELETE-TRANSLATION');
        $contentitem = $em->find(CONTENTITEM_ENTITY, $contentitem_id);
        foreach($contentitem->translations as $t)
        {
            $em->remove($t);
        }
        $em->flush();
        $em->commit('DELETE-TRANSLATION');
        return true;        
	}
	/**
	 * getTranslations function.
	 * get all translations for a contentitem
	 * 
	 * @access public
	 * @param string $entity
	 * @param integer $content_id
	 * @param array $options
	 * @return void
	 */
	public function getTranslations($entity, $content_id, $options)
	{
        $em = $this->getWriter()->getEntityManager();
        
        $dql = "SELECT e FROM ".CONTENTITEM_TRANSLATION_ENTITY." e INNER JOIN e.contentitem c WHERE c.id=?1";
        $query = $em->createQuery($dql);
        $query->setParameter(1,$content_id);
        if (!empty($options["start"]) && !empty($options["limit"]))
        {
            $query = $query->setFirstResult((int)$options['start'])
						   ->setMaxResults((int)$options['limit']);
        }
        $records = $query->getResult();
        $i18nColumns = $entity::getI18nColumns();
        $i18nRecords = array();
        $r = array();
        
        foreach($records as $record)
        {
            # build the record            
            $lang = $record->lang;
            
            if (!is_array($r[$lang])) $r[$lang] = array();
            
            $field = $record->field;
            $translation = $record->translation;
            
            $r[$lang][$record->field] = $record->translation;
            $r[$lang]["contentitem_id"] = $record->contentitem->id;
            $r[$lang]["version"] = $record->version;
            //$r[$lang]["contentitem_id"] = $record->contentitem->id;
        }
		# convert to table row format
		$rows = array();
		foreach ($r as $key=>$value)
		{
            # $key is lang
            $value["lang"] = $key;
            $rows[] = $value;
		}
		//var_dump($rows);
		$records = null;
		# count the records;for paging
		# $count = Paginate::count($query);
		$count = count($rows);
		return array($rows, $count);
	}
	public function retrieveTranslations($entity, $id, $options)
	{
        $em = $this->getWriter()->getEntityManager();
        
        $dql = "SELECT e FROM ".TRANSLATION_ENTITY." e WHERE e.entity=?1 AND e.entity_id=?2";
        $query = $em->createQuery($dql);
        $query->setParameter(1,$entity);
        $query->setParameter(2,$id);
        if (!empty($options["start"]) && !empty($options["limit"]))
        {
            $query = $query->setFirstResult((int)$options['start'])
						   ->setMaxResults((int)$options['limit']);
        }
        $records = $query->getResult();
        $i18nColumns = $entity::getI18nColumns();
        $i18nRecords = array();
        $r = array();
        
        foreach($records as $record)
        {
            # build the record            
            $lang = $record->lang;
            
            if (!is_array($r[$lang])) $r[$lang] = array();
            
            $field = $record->field;
            $translation = $record->translation;
            
            $r[$lang][$record->field] = $record->translation;
            $r[$lang]["version"] = $record->version;
            $r[$lang]["id"] = $record->id;
            $r[$lang]["entity_id"] = $record->entity_id;
        }
		# convert to table row format
		$rows = array();
		foreach ($r as $key=>$value)
		{
            # $key is lang
            $value["lang"] = $key;
            $rows[] = $value;
		}
		//var_dump($rows);
		$records = null;
		# count the records;for paging
		# $count = Paginate::count($query);
		$count = count($rows);
		return array($rows, $count);
	}
	/**
	 * getTree function.
	 * get the tree data for grid
	 * 
	 * @access public
	 * @param string $entity
	 * @param integer $anode. (default: null)
	 * @param array $options
	 * @return array
	 */
	public function getContentItemTree($entity, $anode = null, $options)
	{
        $em = $this->getWriter()->getEntityManager();
        
        if ($anode)
        {
            # select all children of given contentitem_id
            $dql = "SELECT e,c,p FROM ".$entity." e JOIN e.contentitem c LEFT JOIN c.owner p WHERE p.id=?1";    
            $query = $em->createQuery($dql);
            $query->setParameter(1, $anode);
            
        }else{
            # select all root node
            $dql = "SELECT e,c,p FROM ".$entity." e JOIN e.contentitem c LEFT JOIN c.owner p";
            $query = $em->createQuery($dql);
        }
        
        
        #$query->setParameter(1,$entity);
        if ($options["start"] !== false && $options["limit"] !== false)
        {
            izlog("setting paging");
            $query = $query->setFirstResult((int)$options['start'])
						   ->setMaxResults((int)$options['limit']);
        }
               
        $records = $query->getArrayResult();
        izlog($query->getSql());
        foreach($records as $key=>$value)
        {
            # assign the owner to null for the grid to work
            if (empty($value["contentitem"]["owner"])) $records[$key]["contentitem"]["owner"] = array('id'=>null);
        }
        
        #print_r($records);
        $count = Paginate::count($query);
		return array($records, $count);
	}
	
	public function getTree($entity, $anode = null, $options)
	{
        $em = $this->getWriter()->getEntityManager();
        $parent = $em->find($entity, $anode);
        
        if ($parent)
        {
            # select all children of given contentitem_id
            $dql = "SELECT e,p FROM ".$entity." e JOIN e.parent p WHERE e.parent=?1";    
            $query = $em->createQuery($dql);
            $query->setParameter(1, $parent);
            
        }else{
            # select all root node
            $dql = "SELECT e,p FROM ".$entity." e LEFT JOIN e.parent p";
            $query = $em->createQuery($dql);
        }
        
        
        #$query->setParameter(1,$entity);
        if ($options["start"] !== false && $options["limit"] !== false)
        {
            izlog("setting paging");
            $query = $query->setFirstResult((int)$options['start'])
						   ->setMaxResults((int)$options['limit']);
        }
               
        $records = $query->getArrayResult();
        foreach($records as $key=>$value)
        {
            # assign the owner to null for the grid to work
            if (empty($value["parent"])) $records[$key]["parent"] = array('id'=>null);
        }
        $count = Paginate::count($query);
		return array($records, $count);
	}
	/**
	 * Retrieves a list of key/value pair from an entity table, possibly with filter data
	 *
	 * @param string $entity 
	 * @param string $displayField 
	 * @param string $valueField 
	 * @param string $filter 
	 * @return void
	 * @author Nguyen Huu Thanh
	 */
    public function retrieveLOV($entity, $displayField, $valueField, $filter)
    {
		$em = $this->getReader()->getEntityManager();
		$where = $this->selectFilterToWhere($filter);
		
		$dql = "SELECT e.{$valueField},e.{$displayField} from $entity e  where $where";
		
		if (object($entity) instanceof IContentManagable)
		{
            $dql = "SELECT c.{$valueField},e.{$displayField} from $entity e INNER JOIN e.contentitem c where $where";
		}
		$q = $em->createQuery($dql);
		$results = $q->getResult();
		return $results;
    }
	/**
	 * Get entity records from an array of ID
	 *
	 * @param string $entity 
	 * @param string $ids 
	 * @return void
	 * @author Nguyen Huu Thanh
	 */
	public function getRecordsByIds($entity, $ids = array())
	{
		$em = $this->getReader()->getEntityManager();
		$where = "e.id in (".implode(",", $ids).")";
		$q = $em->createQuery("SELECT e FROM $entity e WHERE $where");
		$results = $q->getResult();
		return $results;
	}
	
	public function getRecordById($entity, $id)
	{
		$em = $this->getReader()->getEntityManager();
		$instance = $em->find($entity, $id);
		return $instance;		
	}

    /**
     * Convert the filter POST parameter to WHERE SQL clause for filtering
     *
     * @param string $filter filter object for look-up
     * @param string $prefix prefix attached to fields name, in order to make DQL of Doctrine works
     * @return void
     * @author Nguyen Huu Thanh
     */
	public function selectFilterToWhere($filter = null, $prefix = "e.")
	{
		$where = " 0 = 0 ";
		if (is_array($filter)) {
			for ($i=0;$i<count($filter);$i++){
				switch($filter[$i]['data']['type']){
					case 'string' : $qs .= " AND {$prefix}".$filter[$i]['field']." LIKE '%".$filter[$i]['data']['value']."%'"; break;
					case 'list' :
						if (strstr($filter[$i]['data']['value'],',')){
							$fi = explode(',',$filter[$i]['data']['value']);
							for ($q=0;$q<count($fi);$q++){
								$fi[$q] = "'".$fi[$q]."'";
							}
							$filter[$i]['data']['value'] = implode(',',$fi);
							$qs .= " AND {$prefix}".$filter[$i]['field']." IN (".$filter[$i]['data']['value'].")";
						}else{
							$qs .= " AND {$prefix}".$filter[$i]['field']." = '".$filter[$i]['data']['value']."'";
						}
					break;
					case 'boolean' : $qs .= " AND {$prefix}".$filter[$i]['field']." = ".($filter[$i]['data']['value']); break;
					case 'numeric' :
						switch ($filter[$i]['data']['comparison']) {
							case 'eq' : $qs .= " AND {$prefix}".$filter[$i]['field']." = ".$filter[$i]['data']['value']; break;
							case 'neq' : $qs .= " AND {$prefix}".$filter[$i]['field']." <> ".$filter[$i]['data']['value']; break;
							case 'lt' : $qs .= " AND {$prefix}".$filter[$i]['field']." < ".$filter[$i]['data']['value']; break;
							case 'gt' : $qs .= " AND {$prefix}".$filter[$i]['field']." > ".$filter[$i]['data']['value']; break;
						}
					break;
					case 'date' :
						switch ($filter[$i]['data']['comparison']) {
							case 'eq' : $qs .= " AND {$prefix}".$filter[$i]['field']." = '".date('Y-m-d',strtotime($filter[$i]['data']['value']))."'"; break;
							case 'lt' : $qs .= " AND {$prefix}".$filter[$i]['field']." < '".date('Y-m-d',strtotime($filter[$i]['data']['value']))."'"; break;
							case 'gt' : $qs .= " AND {$prefix}".$filter[$i]['field']." > '".date('Y-m-d',strtotime($filter[$i]['data']['value']))."'"; break;
						}
					break;
				}
			}
			$where .= $qs;
		}

		return $where;
	}

	public function getColumnNames($entity = null)
	{
		#$class = 'Entity\Base\Account';
		$em = $this->getReader()->getEntityManager();
		$cm = $em->getClassMetadata($entity);
		return $cm->getColumnNames();
	}

	public function getAssociationMappings($entity = null)
	{
		$em = $this->getReader()->getEntityManager();
		$cm = $em->getClassMetadata($entity);
		return $cm->getAssociationMappings();
	}
	/**
	 * Create an entity, and fill its properties with the submitted form.
	 *
	 * @param string $entity 
	 * @return void
	 * @author Nguyen Huu Thanh
	 */
	public function createInstanceFromRequest($entity)
	{
        $instance = new $entity;
        $columns = $this->getColumnNames($entity);
        $filter = $this->getManager('filter');
        foreach ($columns as $c)
        {
            if (array_key_exists($c, $_REQUEST))
            {
                #$instance->$c = $_REQUEST[$c];
                $instance->$c = $filter->getRawRequest($c);
            }
        }
        return $instance;
	}
	/**
	 * retrieveInstanceFromRequest function.
	 * retrieve the instance with $_REQUEST["id"] and fill its properties from $_REQUEST
	 * 
	 * @access public
	 * @param string $entity
	 * @return Object
	 */
	public function retrieveInstanceFromRequest($entity)
	{
		$instance = $this->find($entity, $_REQUEST["id"]);
		if (!$instance) return null;
		$columns = $this->getColumnNames($entity);
		foreach ($columns as $c)
        {
            if (array_key_exists($c, $_REQUEST) && $c !== "id")
            {
                $instance->$c = $_REQUEST[$c];
            }
        }
		return $instance;
	}
	
	public function retrieveDefaultTranslationFromRequest($instance)
	{
        
        $em = $this->getWriter()->getEntityManager();
        $translationEntity = get_class($instance)."Translation";
        $dql = "SELECT e FROM {$translationEntity} e INNER JOIN e.owner a WHERE a.id = ?1 AND e.lang = ?2";
        $query = $em->createQuery($dql);
        $query->setParameter(1, $instance->id);
        $query->setParameter(2, $instance->default_lang);
        
        $translation = $query->getSingleResult();
        
        
        if ($translation instanceof $translationEntity)
        {
            $columns = $this->getColumnNames($translationEntity);
    		foreach ($columns as $c)
            {
                if (array_key_exists($c, $_REQUEST) && $c !== "id")
                {
                    $translation->$c = $_REQUEST[$c];
                }
            }
        }
        return $translation;
	}

	public function getAssociationFromRequest($entity)
	{
        $a = $this->getAssociationMappings($entity);
        foreach(array_keys($a) as $key)
        {
            if (array_key_exists($key, $_REQUEST))
            {

            }
        }
	}
	/**
	 * getVersions function.
	 * 
	 * @access public
	 * @param mixed $instance
	 * @return void
	 */
	public function getVersions($instance)
	{
        $em = $this->getReader()->getEntityManager();
        $versionManager = new VersionManager($em);
        $versions = $versionManager->getVersions($instance);
        return $versions;   
	}
	/**
	 * revert function.
	 * 
	 * @access public
	 * @param mixed $instance
	 * @param mixed $version
	 * @return void
	 */
	public function revert($instance, $version)
	{
        $em = $this->getReader()->getEntityManager();
        $versionManager = new VersionManager($em);
        $versionManager->revert($instance, $version);
	}
	/**
	 * fill function.
	 * fill the form with the instance properties
	 * 
	 * @access public
	 * @param mixed &$form
	 * @param mixed $instance
	 * @return void
	 */
	public function fill(&$form, $instance)
	{
		$entity = get_class($instance);
		/*
		# array to hold the translation columns of the entity in case it implements the ITranslatable interface
		$translationColumns = array();
		
		# check for translation
		if ($instance instanceof ITranslatable)
		{
            # get translation columns (fields)
            $translationColumns = $this->getColumnNames($entity.'Translation');
            izlog($translationColumns);
            # get all the translations
            $translations = $instance->translations;
            # loops thru translations to get default translation
            foreach ($translations as $t)
            {
                # check for default translations
                if ($t->lang == $instance->default_lang)
                {
                    $defaultTranslation = $t;
                    break;
                }                
            }
		}
		*/
		# loops thru the elements of the form
		foreach ($form->getChildren() as $e) {
            # element's name
			$ename = $e->getName();
			# get the entity columns (fields)
			$columns = $this->getColumnNames($entity);
			# check if the element name exists in the entity fields
			if (in_array($ename, $columns)) {
				if ($instance->$ename instanceof DateTime) $e->setValue($instance->$ename->format('Y-m-d H:i:s'));
				else $e->setValue($instance->$ename);
			}
			/*
			# check if the element name exists in the default translation fields
			if (in_array($ename, $translationColumns) && $defaultTranslation)
			{
                $e->setValue($defaultTranslation->$ename);
			}
			*/			
			# get all the associations of this entity.
			$associations = $this->getAssociationMappings($entity);
			foreach ($associations as $a) {
				# get the name of the association
				$aname = $a->sourceFieldName;
				# compare with the form name to find a match.
				if ($aname == $ename && $instance->$aname) {
					# extract the record's id of the association
					$aids = array();
					if ($a->isManyToMany()) {
						# if this is a ManyToMany association, we loops thru all association records and collect their ID
						foreach ($instance->$aname as $arecord) {
							$aids[] = $arecord->id;
						}
						# set those value to the form element for rendering
						$e->setValue(implode(',',$aids));
					}else if ($a->isOneToOne()) {
						$e->setValue($instance->$aname->id);
					}
				}
			}			
			$this->fill($e, $instance);
		}
	}
}
?>