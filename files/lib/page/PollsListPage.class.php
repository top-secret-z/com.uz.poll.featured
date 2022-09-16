<?php
namespace wcf\page;
use wcf\data\poll\PollViewableList;
use wcf\system\exception\PermissionDeniedException;
use wcf\system\WCF;

/**
 * Page with available polls.
 * 
 * @author		2016-2022 Zaydowicz
 * @license		GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package		com.uz.wcf.poll.featured
 */
class PollsListPage extends SortablePage {
	/**
	 * @inheritDoc
	 */
	public $itemsPerPage = 20;
	
	/**
	 * @inheritDoc
	 */
	public $defaultSortField = 'time';
	
	/**
	 * @inheritDoc
	 */
	public $defaultSortOrder = 'DESC';
	
	/**
	 * @inheritDoc
	 */
	public $neededModules = ['MODULE_FEATUREDPOLL'];
	
	/**
	 * @inheritDoc
	 */
	public $neededPermissions = ['user.profile.poll.canSeePollPage'];
	
	/**
	 * @inheritDoc
	 */
	public $objectListClassName = PollViewableList::class;
	
	/**
	 * @inheritDoc
	 */
	public $validSortFields = ['time', 'votes', 'question'];
	
	/**
	 * @inheritDoc
	 */
	public function readParameters() {
		parent::readParameters();
		
		if (!WCF::getSession()->getPermission('user.profile.poll.canSeePollPage')) {
			throw new PermissionDeniedException();
		}
	}
}
