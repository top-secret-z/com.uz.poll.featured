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
namespace wcf\system\cronjob;

use wcf\data\cronjob\Cronjob;
use wcf\data\poll\featured\FeaturedPoll;
use wcf\data\poll\featured\FeaturedPollAction;
use wcf\data\poll\PollList;

/**
 * Cronjob for Featured Polls
 */
class FeaturedPollCronjob extends AbstractCronjob
{
    /**
     * @inheritDoc
     */
    public function execute(Cronjob $cronjob)
    {
        parent::execute($cronjob);

        // only if configured
        if (!MODULE_FEATUREDPOLL) {
            return;
        }

        // get configuration and polls
        $featured = FeaturedPoll::getFeaturedRow();
        $featuredPollIDs = \unserialize($featured->pollIDs);

        // abort, if no polls are configured
        if (!\count($featuredPollIDs)) {
            return;
        }

        // get and check polls
        $pollIDs = [];
        $pollList = new PollList();
        $pollList->getConditionBuilder()->add('poll.pollID IN (?)', [$featuredPollIDs]);
        $pollList->readObjects();
        $polls = $pollList->getObjects();

        if (\count($polls)) {
            foreach ($polls as $poll) {
                if ($poll->isFinished()) {
                    unset($polls[$poll->pollID]);
                } else {
                    $pollIDs[] = $poll->pollID;
                }
            }
        }

        // no pollIDs, no box, delete featured
        if (!\count($pollIDs)) {
            $this->clear($featured->featuredID);

            return;
        }

        // check remaining ids
        $remaingIDs = \array_intersect($featuredPollIDs, $pollIDs);
        if (!\count($remaingIDs)) {
            $this->clear($featured->featuredID);

            return;
        }

        // change poll, if actual not in remaining or if time has run out
        $newFeaturedID = 0;
        if (!\in_array($featured->actualPollID, $remaingIDs) || $featured->nextChange < TIME_NOW) {
            // random id
            if ($featured->isRandom) {
                $newFeaturedID = $remaingIDs[\array_rand($remaingIDs)];
            } else {
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
                    \reset($remaingIDs);
                    $newFeaturedID = $remaingIDs[\key($remaingIDs)];
                }
            }

            $nextChange = $featured->nextChange;
            if ($featured->nextChange < TIME_NOW) {
                $nextChange = TIME_NOW + $featured->frequency * 60;
            }

            // update featured
            $objectAction = new FeaturedPollAction([$featured->featuredID], 'update', [
                'data' => [
                    'pollIDs' => \serialize($remaingIDs),
                    'actualPollID' => $newFeaturedID,
                    'nextChange' => $nextChange,
                ],
            ]);
            $objectAction->executeAction();
        }
    }

    /**
     * Clears featured poll.
     */
    protected function clear($id)
    {
        $objectAction = new FeaturedPollAction([$id], 'update', [
            'data' => [
                'pollIDs' => \serialize([]),
                'actualPollID' => null,
            ],
        ]);
        $objectAction->executeAction();
    }
}
