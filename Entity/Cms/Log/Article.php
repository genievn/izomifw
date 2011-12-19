<?php
namespace Entity\Cms\Log;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Loggable\Entity\AbstractLogEntry;
/**
 * @ORM\Table(name="cms_articles_log")
 * @ORM\Entity(repositoryClass="Gedmo\Loggable\Entity\Repository\LogEntryRepository")
 */
class Article extends AbstractLogEntry
{

}
?>