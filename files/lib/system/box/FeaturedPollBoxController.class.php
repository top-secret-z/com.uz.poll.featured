<?php
namespace wcf\system\box;
use wcf\data\poll\Poll;
use wcf\data\poll\featured\FeaturedPoll;
use wcf\system\exception\SystemException;
use wcf\system\poll\PollManager;
use wcf\system\WCF;

/**
 * Shows featured polls.
 *
 * @author		2016-2022 Zaydowicz
 * @license		GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package		com.uz.wcf.poll.featured
 */
class FeaturedPollBoxController extends AbstractBoxController {
	/**
	 * link to poll
	 */
	protected $link = null;
	
	/**
	 * supported box positions
	 */
	protected static $supportedPositions = ['headerBoxes', 'footerBoxes', 'sidebarLeft', 'sidebarRight'];
	
	/**
	 * template name
	 */
	protected $templateName = 'boxFeaturedPoll';
	
	/**
	 * @inheritDoc
	 */
	public function getLink() {
		if ($this->link === null) {
			$featured = FeaturedPoll::getFeaturedRow();
			if (!$featured->actualPollID) return '';
			$poll = new Poll($featured->actualPollID);
			if (!$poll->pollID || $poll->isFinished()) return '';
		}
		try {
			$object = PollManager::getInstance()->getRelatedObject($poll);
			if ($object !== null) {
				$this->link = $object->getLink();
			}
		}
		catch (SystemException $e) {
			return '';
		}
		if ($this->link) return $this->link;
		return '';
	}
	
	/**
	 * @inheritDoc
	 */
	public function hasLink() {
		return true;
	}
	
	/**
	 * @inheritDoc
	 */
	protected function loadContent() {
		// get featured poll
		$featured = FeaturedPoll::getFeaturedRow();
		if (!$featured->actualPollID) return;
		
		$poll = new Poll($featured->actualPollID);
		if (!$poll->pollID || $poll->isFinished()) return;
		
		// check permission - poll might have been deleted or ended or user can't vote anymore
		$foundPoll = null;
		
		$participated = $poll->isParticipant();
		if (!$participated || $poll->isChangeable) {
			try {
				$object = PollManager::getInstance()->getRelatedObject($poll);
				if ($object !== null) {
					$canVote = $object->canVote();
					$link = $object->getLink(); // test
				}
			}
			catch (SystemException $e) {
				$object = null;
			}
			
			if ($object !== null && $canVote) {
				$foundPoll = $poll;
			}
		}
		
		// no poll, no box
		if ($foundPoll === null) return;
		
		// load box, set width iaw position
		$width = $featured->width;
		if ($this->getBox()->position == 'footerBoxes' || $this->getBox()->position == 'headerBoxes') {
			$width = 99;
		}
		$this->content = WCF::getTPL()->fetch($this->templateName, 'wcf', [
				'poll' => $foundPoll,
				'width' => $width
		]);
	}
}
