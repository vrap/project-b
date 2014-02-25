<?php

namespace Project\AppBundle\EventListener;

use ADesigns\CalendarBundle\Event\CalendarEvent;
use Doctrine\ORM\EntityManager;
use Project\AppBundle\Entity\EventEntity;

class CalendarEventListener
{
    private $entityManager;

    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function loadEvents(CalendarEvent $calendarEvent)
    {
        $startDate = $calendarEvent->getStartDatetime();
        $endDate = $calendarEvent->getEndDatetime();

        // The original request so you can get filters from the calendar
        // Use the filter in your query for example

        $request = $calendarEvent->getRequest();
        $filter = $request->get('filter');

        // load events using your custom logic here,
        // for instance, retrieving events from a repository
        $events = $this->entityManager->getRepository('ProjectAppBundle:Lesson')
                          ->createQueryBuilder('lesson')
                          ->where('lesson.startDate >= :startDate and lesson.endDate <= :endDate')
                          ->setParameter('startDate', $startDate->format('Y-m-d H:i:s'))
                          ->setParameter('endDate', $endDate->format('Y-m-d H:i:s'))
                          ->getQuery()->getResult();

        
        foreach($events as $event) {
            $eventEntity = new EventEntity( $event->getName(),
                                            $event->getStartDate(),
                                            $event->getEndDate(),
                                            $event->getId(),
                                            $event->getSpeaker(),
                                            $event->getModule());
            
            //finally, add the event to the CalendarEvent for displaying on the calendar
            $calendarEvent->addEvent($eventEntity);
        }
    }
}