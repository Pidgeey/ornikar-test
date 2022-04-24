<?php

namespace App;

use App\Context\ApplicationContext;
use App\Entity\Learner;
use App\Entity\Lesson;
use App\Entity\Template;
use App\Repository\InstructorRepository;
use App\Repository\MeetingPointRepository;
use Exception;

class TemplateManager
{
    /**
     * Get given template computed
     *
     * @param Template $tpl
     * @param array $data
     *
     * @return Template
     * @throws Exception
     */
    public function getTemplateComputed(Template $tpl, array $data)
    {
        // We need a Lesson instance for the computed text
        $lesson = $data['lesson'] ?? null;
        if (!$lesson instanceof Lesson) {
            throw new Exception("An instance of Lesson is required on data", 422);
        }

        // If there are no user passed in data, we took the current user
        $user = $data['user'] ?? null;
        if (!$user instanceof Learner) $data['user'] = ApplicationContext::getInstance()->getCurrentUser();

        $replaced = clone($tpl);
        $replaced->subject = $this->computeText($replaced->subject, $data);
        $replaced->content = $this->computeText($replaced->content, $data);

        return $replaced;
    }

    /**
     * Return computed text
     *
     * @param $text
     * @param array $data
     *
     * @return string
     */
    private function computeText($text, array $data)
    {
        /** @var Learner $user */
        $user = $data['user'];
        /** @var Lesson $lesson */
        $lesson = $data['lesson'];

        $meetingPoint = MeetingPointRepository::getInstance()->getById($lesson->meetingPointId);
        $instructorOfLesson = InstructorRepository::getInstance()->getById($lesson->instructorId);

        $textAttributes = [
            "lesson:instructor_link" => 'instructors/' . $instructorOfLesson->id .'-'.urlencode($instructorOfLesson->firstname),
            'lesson:summary_html' => Lesson::renderHtml($lesson),
            'lesson:summary' => Lesson::renderText($lesson),
            'lesson:instructor_name' => $instructorOfLesson->firstname,
            'lesson:meeting_point' => $meetingPoint->name,
            'lesson:start_date' => $lesson->start_time->format('d/m/Y'),
            'lesson:start_time' => $lesson->start_time->format('H:i'),
            'lesson:end_time' => $lesson->end_time->format('H:i'),

            'user:first_name' => ucfirst(strtolower($user->firstname))
        ];

        // We are looking for matches between the current text and our list of attributes
        foreach ($textAttributes as $attribute => $value) {
            $text = str_replace("[$attribute]", $value, $text);
        }

        // Then we return the text
        return $text;
    }
}
