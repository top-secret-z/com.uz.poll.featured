<?php
namespace wcf\data\poll\featured;
use wcf\data\DatabaseObject;
use wcf\system\WCF;

/**
 * Represents an featured poll.
 *  
 * @author		2016-2022 Zaydowicz
 * @license		GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package		com.uz.wcf.poll.featured
 */
class FeaturedPoll extends DatabaseObject {
	/**
	 * @inheritDoc
	 */
	protected static $databaseTableIndexName = 'featuredID';
	
	/**
	 * @inheritDoc
	 */
	protected static $databaseTableName = 'poll_featured';
	
	/**
	 * returns the featured entry
	 */
	public static function getFeaturedRow() {
		$sql = "SELECT		featuredID
				FROM		wcf".WCF_N."_poll_featured
				ORDER BY	featuredID ASC";
		$statement = WCF::getDB()->prepareStatement($sql);
		$statement->execute();
		if (!$id = $statement->fetchColumn()) {
			$objectAction = new FeaturedPollAction([], 'create', [
					'data' => [
							'pollIDs' => serialize([])
					]]);
			$objectAction->executeAction();
			$returnValues = $objectAction->getReturnValues();
			$id = $returnValues['returnValues']->featuredID;
		}
		
		return new FeaturedPoll($id);
	}
}
