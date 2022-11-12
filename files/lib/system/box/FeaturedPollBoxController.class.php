<?php

/*
 * Copyright by Udo Zaydowicz.
 * Modified by SoftCreatR.dev.
 *
 * License: http://opensource.org/licenses/lgpl-license.php
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation; either
 * version 3 of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
 * Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with this program; if not, write to the Free Software Foundation,
 * Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 */
namespace wcf\system\box;

use wcf\data\poll\featured\FeaturedPoll;
use wcf\data\poll\Poll;
use wcf\system\exception\SystemException;
use wcf\system\poll\PollManager;
use wcf\system\WCF;

/**
 * Shows featured polls.
 */
class FeaturedPollBoxController extends AbstractBoxController
{
    /**
     * link to poll
     */
    protected $link;

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
    public function getLink()
    {
        if ($this->link === null) {
            $featured = FeaturedPoll::getFeaturedRow();
            if (!$featured->actualPollID) {
                return '';
            }
            $poll = new Poll($featured->actualPollID);
            if (!$poll->pollID || $poll->isFinished()) {
                return '';
            }
        }
        try {
            $object = PollManager::getInstance()->getRelatedObject($poll);
            if ($object !== null) {
                $this->link = $object->getLink();
            }
        } catch (SystemException $e) {
            return '';
        }
        if ($this->link) {
            return $this->link;
        }

        return '';
    }

    /**
     * @inheritDoc
     */
    public function hasLink()
    {
        return true;
    }

    /**
     * @inheritDoc
     */
    protected function loadContent()
    {
        // get featured poll
        $featured = FeaturedPoll::getFeaturedRow();
        if (!$featured->actualPollID) {
            return;
        }

        $poll = new Poll($featured->actualPollID);
        if (!$poll->pollID || $poll->isFinished()) {
            return;
        }

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
            } catch (SystemException $e) {
                $object = null;
            }

            if ($object !== null && $canVote) {
                $foundPoll = $poll;
            }
        }

        // no poll, no box
        if ($foundPoll === null) {
            return;
        }

        // load box, set width iaw position
        $width = $featured->width;
        if ($this->getBox()->position == 'footerBoxes' || $this->getBox()->position == 'headerBoxes') {
            $width = 99;
        }
        $this->content = WCF::getTPL()->fetch($this->templateName, 'wcf', [
            'poll' => $foundPoll,
            'width' => $width,
        ]);
    }
}
