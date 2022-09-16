<?php
namespace wcf\data\poll\featured;
use wcf\data\DatabaseObjectEditor;

/**
 * Provides functions to edit featured polls.
 * 
 * @author		2016-2022 Zaydowicz
 * @license		GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package		com.uz.wcf.poll.featured
 */
class FeaturedPollEditor extends DatabaseObjectEditor {
	/**
	 * @inheritDoc
	 */
	protected static $baseClass = FeaturedPoll::class;
}
