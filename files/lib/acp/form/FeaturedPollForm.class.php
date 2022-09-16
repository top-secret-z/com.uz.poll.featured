<?php
namespace wcf\acp\form;
use wcf\data\poll\featured\FeaturedPoll;
use wcf\data\poll\featured\FeaturedPollAction;
use wcf\form\AbstractForm;
use wcf\system\WCF;
use wcf\util\ArrayUtil;

/**
 * Provides the featured poll configuration form.
 * 
 * @author		2016-2022 Zaydowicz
 * @license		GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package		com.uz.wcf.poll.featured
 */
class FeaturedPollForm extends AbstractForm {
	/**
	 * @inheritDoc
	 */
	public $activeMenuItem = 'wcf.acp.menu.link.featuredPoll.config';
	
	/**
	 * @inheritDoc
	 */
	public $neededModules = ['MODULE_FEATUREDPOLL'];
	
	/**
	 * @inheritDoc
	 */
	public $neededPermissions = ['admin.content.poll.canManageFeaturedPoll'];
	
	/**
	 * config data
	 */
	public $polls = [];
	public $pollIDs = [];
	public $autoAdd = 1;
	public $frequency = 30;
	public $isRandom = 0;
	public $width = 100;
	
	/**
	 * poll data
	 */
	public $actualPollID = null;
	public $featuredPoll = null;
	
	/**
	 * @inheritDoc
	 */
	public function readFormParameters() {
		parent::readFormParameters();
		
		$this->pollIDs = [];
		$this->isRandom = $this->autoAdd = 0;
		if (isset($_POST['isRandom'])) $this->isRandom = 1;
		if (isset($_POST['autoAdd'])) $this->autoAdd = 1;
		if (isset($_POST['frequency'])) $this->frequency = intval($_POST['frequency']);
		if (isset($_POST['width'])) $this->width = intval($_POST['width']);
		if (isset($_POST['pollIDs']) && is_array($_POST['pollIDs'])) $this->pollIDs = ArrayUtil::toIntegerArray($_POST['pollIDs']);
	}
	
	/**
	 * @inheritDoc
	 */
	public function validate() {
	parent::validate();
		
		// empty pollIDs if no polls
		if (!count($this->polls)) {
			$this->pollIDs = [];
			$this->actualPollID = null;
		}
		else {
			// get first key as actual poll
			if (!count($this->pollIDs)) {
				$this->actualPollID = null;
			}
			else {
				reset($this->pollIDs);
				$this->actualPollID = $this->pollIDs[key($this->pollIDs)];
			}
		}
	}
	
	/**
	 * @inheritDoc
	 */
	public function readParameters() {
		parent::readParameters();
		
		// get existing polls as array for template
		$sql = "SELECT		*
				FROM		wcf".WCF_N."_poll
				WHERE		endTime = ? OR endTime > ?";
		$statement = WCF::getDB()->prepareStatement($sql);
		$statement->execute([0, TIME_NOW + 600]);
		while ($row = $statement->fetchArray()) {
			$this->polls[] = $row;
		}
		
		// set data
		$this->featuredPoll = FeaturedPoll::getFeaturedRow();
		$this->isRandom = $this->featuredPoll->isRandom;
		$this->autoAdd = $this->featuredPoll->autoAdd;
		$this->frequency = $this->featuredPoll->frequency;
		$this->width = $this->featuredPoll->width;
		$this->actualPollID = $this->featuredPoll->actualPollID;
		$this->pollIDs = unserialize($this->featuredPoll->pollIDs);
	}
	
	/**
	 * @inheritDoc
	 */
	public function save() {
		parent::save();
		
		$objectAction = new FeaturedPollAction([$this->featuredPoll->featuredID], 'update', [
				'data' => [
						'isRandom' => $this->isRandom,
						'autoAdd' => $this->autoAdd,
						'frequency' => $this->frequency,
						'width' => $this->width,
						'pollIDs' => serialize($this->pollIDs),
						'actualPollID' => $this->actualPollID,
						'nextChange' => TIME_NOW + $this->frequency * 60
				]]);
		$objectAction->executeAction();
		
		$this->saved();
		
		// Show success message
		WCF::getTPL()->assign('success', true);
	}
	
	/**
	 * @inheritDoc
	 */
	public function assignVariables() {
		parent::assignVariables();
		
		WCF::getTPL()->assign([
				'frequency' => $this->frequency,
				'width' => $this->width,
				'isRandom' => $this->isRandom,
				'autoAdd' => $this->autoAdd,
				'pollIDs' => $this->pollIDs,
				'polls' => $this->polls
		]);
	}
}
