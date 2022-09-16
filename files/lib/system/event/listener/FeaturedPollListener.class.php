<?php
namespace wcf\system\event\listener;
use wcf\data\poll\featured\FeaturedPoll;
use wcf\data\poll\featured\FeaturedPollAction;

/**
 * Listen to Poll actions for Featured Poll
 * 
 * @author		2016-2022 Zaydowicz
 * @license		GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package		com.uz.wcf.poll.featured
 */
class FeaturedPollListener implements IParameterizedEventListener {
	/**
	 * @inheritDoc
	 */
	public function execute($eventObj, $className, $eventName, array &$parameters) {
		// only if configured
		if (!MODULE_FEATUREDPOLL) return;
		
		// only action create
		$action = $eventObj->getActionName();
		if ($action != 'create') return;
		
		// get poll
		$returnValues = $eventObj->getReturnValues();
		$pollID = $returnValues['returnValues']->pollID;
		
		// get featured and add poll, if configured
		$featured = FeaturedPoll::getFeaturedRow();
		if (!$featured->autoAdd) return;
		
		$featuredPollIDs = unserialize($featured->pollIDs);
		$featuredPollIDs[] = $pollID;
		
		// save and set to new poll
		$objectAction = new FeaturedPollAction([$featured->featuredID], 'update', [
				'data' => [
						'pollIDs' => serialize($featuredPollIDs),
						'actualPollID' => $pollID
				]
		]);
		$objectAction->executeAction();
	}
}
