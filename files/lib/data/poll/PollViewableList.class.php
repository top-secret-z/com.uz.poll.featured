<?php
namespace wcf\data\poll;
use wcf\system\poll\PollManager;
use wcf\system\WCF;

/**
 * Represents a list of viewable polls.
 * 
 * @author		2016-2022 Zaydowicz
 * @license		GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package		com.uz.wcf.poll.featured
 */
class PollViewableList extends PollList {
	/**
	 * @inheritDoc
	 */
	public $sqlOrderBy = 'poll.time DESC';
	
	/**
	 * @inheritDoc
	 */
	public function __construct() {
		parent::__construct();
		
		$this->getConditionBuilder()->add('poll.objectID <> ?', [0]);
		$this->getConditionBuilder()->add('poll.objectTypeID <> ?', [0]);
	}
	
	/**
	 * @inheritDoc
	 */
	public function countObjects() {
		$sql = "SELECT	COUNT(*)
				FROM	wcf".WCF_N."_poll poll
				".$this->getConditionBuilder();
		$statement = WCF::getDB()->prepareStatement($sql);
		$statement->execute($this->getConditionBuilder()->getParameters());
		return $statement->fetchColumn() ;
	}
	
	/**
	 * @inheritDoc
	 */
	public function readObjects() {
		$sql = "SELECT	poll.*
				FROM	wcf".WCF_N."_poll poll
				".$this->getConditionBuilder()."
				".(!empty($this->sqlOrderBy) ? "ORDER BY ".$this->sqlOrderBy : '');
		$statement = WCF::getDB()->prepareStatement($sql, $this->sqlLimit, $this->sqlOffset);
		$statement->execute($this->getConditionBuilder()->getParameters());
		$this->objects = $statement->fetchObjects(($this->objectClassName ?: $this->className));
		
		// add to objects
		$objects = [];
		foreach ($this->objects as $object) {
			$pollObject = PollManager::getInstance()->getRelatedObject($object);
			
			// simple status to display 0 = not voted, 1 = voted, 2 = forbidden
			$object->status = 2;
			
			if ($pollObject) {
				// check read permission; std in WL apps
				if (method_exists($pollObject,'canRead') && $pollObject->canRead()) {
					$object->setRelatedObject($pollObject);
					
					if ($object->isParticipant()) {
						$object->status = 1;
					}
					elseif ($object->canVote()) {
						$object->status = 0;
					}
				}
			}
			
			// get link, honor status
			$object->link = $object->question;
			$object->hasLink = 0;
			
			if ($pollObject && method_exists($pollObject,'getLink') && $object->status != 2) {
				$object->link = $pollObject->getLink();
				$object->hasLink = 1;
			}
			
			$objectID = $object->{$this->getDatabaseTableIndexName()};
			$objects[$objectID] = $object;
			
			$this->indexToObject[] = $objectID;
		}
		
		$this->objectIDs = $this->indexToObject;
		$this->objects = $objects;
	}
}
