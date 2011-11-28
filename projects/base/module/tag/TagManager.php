<?php
use Entity\Base\Tag,
    Entity\Base\TagItem,
    Entity\Base\ContentItem;

define ('TAG_ENTITY','Entity\Base\Tag');
define ('TAGITEM_ENTITY','Entity\Base\TagItem');
define ('CONTENTITEM_ENTITY','Entity\Base\ContentItem');

class TagManager extends Object
{
    public function getTags($entity, $id)
    {
    
    }
    /**
     * tag function.
     * associate tags to an instance of entity
     * 
     * @access public
     * @param string $entity
     * @param integer $id
     * @param array $tags. (default: array())
     * @return void
     */
    public function tag($entity, $id, $tags = array())
    {
        /**
         * Steps for tagging:
         * 1. Look for new tags
         * 2. Insert new tags to the tag table
         * 3. Get the instance (from id) of the entity
         * 4. Remove all previous association
         * 5. Assign new association
         */
         
        $em = $this->getWriter()->getEntityManager();
        $em->beginTransaction('T-TAG');
        # array to hold normalized tags
        $ntags = array();
        # array to keep the ids of the tags
        $tagIds = array();
        # build the array for where selection
        $whereTag = array();
        # loops thru input tags & normalize them
        foreach ($tags as $tag)
        {
            $ntag = $this->normalizeTag($tag);
            $ntags[$tag] = $ntag;
            $whereTag[] = "e.tag = '$ntag'";            
        }

        $whereTagOr = implode(' OR ', $whereTag);
        
        $dql = "SELECT e FROM ".TAG_ENTITY." e WHERE $whereTagOr";
        $query = $em->createQuery($dql);
        $records = $query->getResult();
        
        # array to hold new tags
        $newTags = array();
        # array to keep existing tags
        $existedTags = array();
        foreach ($records as $record)
        {
            $existedTags[] = $record->tag;
            $tagIds[] = $record->id;
        }
        
        # loop thru the normalized tags
        foreach ($ntags as $key=>$value)
        {
            # check if it's a new tag, put it in the $newTags array
            if (!in_array($value, $existedTags)) $newTags[$key] = $value;
        }

        # loop thru the new tags        
        foreach ($newTags as $key=>$value)
        {
            $tagInstance = new Tag;
            $tagInstance->tag = $value;
            $tagInstance->raw_tag = $key;
            $em->persist($tagInstance);
            $records[] = $tagInstance;
        }
        # $em->flush();
        
        

        # delete all previous
        $entityInstance = $em->find($entity, $id);
        $tagItems = $entityInstance->getTagItems();
        foreach($tagItems as $tagItem)
        {
            $em->remove($tagItem);
        }
        $em->flush();
        
        $account = $this->getManager('account')->getAccount();
        foreach ($records as $record)
        {
            $tagItem = new TagItem;
            $tagItem->assign("contentitem", $entityInstance);
            $tagItem->assign("tag", $record);
            $tagItem->assign("account", $account);
            $em->persist($tagItem);
        }
        $em->flush();
        
        $em->commit('T-TAG');
    }

    private function normalizeTag($tag)
    {
        return Vietnamese::strtolowerv($tag);    
    }


}
?>