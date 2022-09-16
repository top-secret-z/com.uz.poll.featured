<?php 
namespace wcf\system\cronjob;
use wcf\data\cronjob\Cronjob;
use wcf\data\poll\PollList;
use wcf\data\poll\featured\FeaturedPoll;
use wcf\data\poll\featured\FeaturedPollAction;

/**
 * Cronjob for Featured Polls
 *  
 * @author		2016-2022 Zaydowicz
 * @license		GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package		com.uz.wcf.poll.featured
 */
class FeaturedPollCronjob extends AbstractCronjob {
	/**
	 * @inheritDoc
	 */
	public function execute(Cronjob $cronjob) {
		parent::execute($cronjob);
		
		// only if configured
		if (!MODULE_FEATUREDPOLL) return;
		
		// get configuration and polls
		$featured = FeaturedPoll::getFeaturedRow();
		$featuredPollIDs = unserialize($featured->pollIDs);
		
		// abort, if no polls are configured
		if (!count($featuredPollIDs)) return;
		
		// get and check polls
		$pollIDs = [];
		$pollList = new PollList();
		$pollList->getConditionBuilder()->add('poll.pollID IN (?)', [$featuredPollIDs]);
		$pollList->readObjects();
		$polls = $pollList->getObjects();
		
		if (count($polls)) {
			foreach ($polls as $poll) {
				if ($poll->isFinished()) {
					unset($polls[$poll->pollID]);
				}
				else {
					$pollIDs[] = $poll->pollID;
				}
			}
		}
		
		// no pollIDs, no box, delete featured
		if (!count($pollIDs)) {
			$this->clear($featured->featuredID);
			return;
		}
		
		// check remaining ids
		$remaingIDs = array_intersect($featuredPollIDs, $pollIDs);
		if (!count($remaingIDs)) {
			$this->clear($featured->featuredID);
			return;
		}
		
		// change poll, if actual not in remaining or if time has run out
		$newFeaturedID = 0;
		if (!in_array($featured->actualPollID, $remaingIDs) || $featured->nextChange < TIME_NOW) {
			// random id
			if ($featured->isRandom) {
				$newFeaturedID = $remaingIDs[array_rand($remaingIDs)];
			}
			else {
				// next id
				$found = 0;
				foreach ($remaingIDs as $id) {
					if ($id > $featured->actualPollID) {
						$found = 1;
						$newFeaturedID = $id;
						break;
					}
				}
				if (!$found) {
					reset($remaingIDs);
					$newFeaturedID = $remaingIDs[key($remaingIDs)];
				}
			}
			
			$nextChange = $featured->nextChange;
			if ($featured->nextChange < TIME_NOW) {
				$nextChange = TIME_NOW + $featured->frequency * 60; 
			}
			
			// update featured
			$objectAction = new FeaturedPollAction([$featured->featuredID], 'update', [
					'data' => [
							'pollIDs' => serialize($remaingIDs),
							'actualPollID' => $newFeaturedID,
							'nextChange' => $nextChange
					]
			]);
			$objectAction->executeAction();
		}
	}
	
	/**
	 * Clears featured poll.
	 */
	protected function clear($id) {
		$objectAction = new FeaturedPollAction([$id], 'update', [
				'data' => [
						'pollIDs' => serialize([]),
						'actualPollID' => null
				]
		]);
		$objectAction->executeAction();
	}
}
