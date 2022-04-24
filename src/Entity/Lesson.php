<?php

namespace App\Entity;

class Lesson
{
    public int $id;
    public int $meetingPointId;
    public int $instructorId;
    public \DateTime $start_time;
    public \DateTime $end_time;

    public function __construct(int $id, int $meetingPointId, int $instructorId, \DateTime $start_time, \DateTime  $end_time)
    {
        $this->id = $id;
        $this->meetingPointId = $meetingPointId;
        $this->instructorId = $instructorId;
        $this->start_time = $start_time;
        $this->end_time = $end_time;
    }

    public function renderHtml(): string
    {
        return '<p>' . $this->id . '</p>';
    }

    public function renderText(): string
    {
        return (string) $this->id;
    }
}