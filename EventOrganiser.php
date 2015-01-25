<?php

class EventOrganiser {

    public function sortEvents(array $events)
    {
        if (count($events) < 1) {
            throw new \MissingEventsException('No events found.');
        }

        /**
         * Assuming an array of event objects with a date
         * format of xxxx-xx-xx (2015-01-18),
         *
         * Sort the events so that a promoted event appears
         * at the top of a list of events with the same date.
         */
        return $this->prioritisePromotedEvents($events);
    }

    private function prioritisePromotedEvents(array $events)
    {
        foreach($events as $event) {
            if ($event->promoted == 'yes') {
                $date = substr($event->start_date, 0, 10);
                $filteredGroup = $this->getEventsWithTheSameDate($events, $date);
                $events = $this->removeFilteredEventsFromStack($events, $filteredGroup);
                $groupKeys = $this->copyFilteredEventKeys($filteredGroup);
                $filteredGroup = $this->movePromotedEventsToTopOfStack($filteredGroup);
                $events = $this->moveSortedGroupBackIntoEvents($events, $groupKeys, $filteredGroup);
            }
        }
        return $events;
    }

    private function getEventsWithTheSameDate(array $events, $date)
    {
        $filteredGroup = array_filter($events, function ($event) use ($date) {
            if (substr($event->start_date, 0, 10) == $date) {
                return $event;
            }
        });
        return $filteredGroup;
    }

    private function removeFilteredEventsFromStack(array $events, $filteredGroup)
    {
        $events = array_diff_key($events, $filteredGroup);
        return $events;
    }

    /**
     * A copy is made so that
     * the key integrity is
     * maintained after the sort.
     */
    private function copyFilteredEventKeys($filteredGroup)
    {
        $groupKeys = array_keys($filteredGroup);
        return $groupKeys;
    }

    private function movePromotedEventsToTopOfStack($filteredGroup)
    {
        foreach ($filteredGroup as $id => $group) {
            if ($group->promoted == 'yes') {
                $promoted = $filteredGroup[$id];
                unset($filteredGroup[$id]);
                array_unshift($filteredGroup, $promoted);
            }
        }
        return $filteredGroup;
    }

    private function rebuildGroupWithOriginalKeys($groupKeys, $filteredGroup)
    {
        $sortedEventGroup = array_combine($groupKeys, $filteredGroup);
        return $sortedEventGroup;
    }

    private function moveSortedGroupBackIntoEvents(array $events, $groupKeys, $filteredGroup)
    {
        return $events + $this->rebuildGroupWithOriginalKeys($groupKeys, $filteredGroup);
    }
}