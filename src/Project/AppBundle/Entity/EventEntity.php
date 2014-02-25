<?php

namespace Project\AppBundle\Entity;

use ADesigns\CalendarBundle\Entity\EventEntity as BaseEvent;
/**
 * Class for holding a calendar event's details.
 */
class EventEntity extends BaseEvent
{
    
    protected $speaker;
    
    protected $module;
    
    public function __construct($title, \DateTime $startDatetime, \DateTime $endDatetime, $id, $speaker, $module)
    {
        $this->title = $title;
        $this->startDatetime = $startDatetime;
        $this->setId($id);        
        $this->endDatetime = $endDatetime;
        $this->speaker = $speaker;
        $this->module = $module;
    }
    
    
    public function toArray()
    {
        $event = parent::toArray();
        
        $event['speaker'] = $this->speaker;
        $event['module']  = $this->module;

        return $event;
    }
  
    public function getSpeaker() {
        return $this->speaker;
    }

    public function setSpeaker($speaker) {
        $this->speaker = $speaker;
    }

    public function getModule() {
        return $this->module;
    }

    public function setModule($module) {
        $this->module = $module;
    }

}
