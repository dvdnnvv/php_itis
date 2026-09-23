<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class CalendarController extends AbstractController
{
    #[Route(
        '/calendar/{format}/{month}',
        name: 'app_calendar',
        requirements: [
            'format' => 'table|list|weekends',
            'month'  => '\d{1,2}',
        ],
        defaults: [
            'format' => 'table',
            'month'  => null,
        ]
    )]
    public function index(?string $format = 'table', ?int $month = null): Response
    {
        if ($month === null) {
            $month = (int) date('n');
        }

        if ($month < 1 || $month > 12) {
            throw $this->createNotFoundException("Месяц должен быть от 1 до 12. Получено: $month");
        }

        $year = (int) date('Y');

        $calendarData = $this->buildCalendar($year, $month);

        $monthNames = [
            1 => 'Январь', 2 => 'Февраль', 3 => 'Март',
            4 => 'Апрель', 5 => 'Май', 6 => 'Июнь',
            7 => 'Июль', 8 => 'Август', 9 => 'Сентябрь',
            10 => 'Октябрь', 11 => 'Ноябрь', 12 => 'Декабрь',
        ];

        $template = match ($format) {
            'list'     => 'calendar/list.html.twig',
            'weekends' => 'calendar/weekends.html.twig',
            default    => 'calendar/table.html.twig',
        };

        return $this->render($template, [
            'year'         => $year,
            'month'        => $month,
            'monthName'    => $monthNames[$month],
            'weeks'        => $calendarData['weeks'],
            'weekdayNames' => ['Пн', 'Вт', 'Ср', 'Чт', 'Пт', 'Сб', 'Вс'],
            'format'       => $format,
        ]);
    }

    private function buildCalendar(int $year, int $month): array
    {
        $firstDay = new \DateTimeImmutable("$year-$month-01");
        $daysInMonth = (int) $firstDay->format('t');
        $startWeekday = (int) $firstDay->format('N');
        $prevMonthDays = $startWeekday - 1;

        $days = [];

        $prevMonth = $firstDay->modify('-1 month');
        $prevMonthDaysCount = (int) $prevMonth->format('t');
        for ($i = $prevMonthDays; $i > 0; $i--) {
            $days[] = [
                'day'            => $prevMonthDaysCount - $i + 1,
                'isWeekend'      => false,
                'isCurrentMonth' => false,
            ];
        }

        for ($day = 1; $day <= $daysInMonth; $day++) {
            $date = new \DateTimeImmutable("$year-$month-$day");
            $weekday = (int) $date->format('N');
            $days[] = [
                'day'            => $day,
                'isWeekend'      => $weekday >= 6,
                'isCurrentMonth' => true,
            ];
        }

        $totalCells = (int) ceil(count($days) / 7) * 7;
        $nextDay = 1;
        while (count($days) < $totalCells) {
            $days[] = [
                'day'            => $nextDay++,
                'isWeekend'      => false,
                'isCurrentMonth' => false,
            ];
        }

        $weeks = array_chunk($days, 7);

        return ['weeks' => $weeks];
    }
}