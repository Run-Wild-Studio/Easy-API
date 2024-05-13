<?php

namespace runwildstudio\easyapi\elements;

use Cake\Utility\Hash;
use Carbon\Carbon;
use Craft;
use craft\base\ElementInterface;
use craft\elements\User as UserElement;
use craft\errors\ElementNotFoundException;
use runwildstudio\easyapi\base\Element;
use runwildstudio\easyapi\events\ApiProcessEvent;
use runwildstudio\easyapi\EasyApi;
use runwildstudio\easyapi\services\Process;
use craft\helpers\Json;
use Exception;
use RRule\RfcParser;
use Solspace\Calendar\Calendar;
use Solspace\Calendar\Elements\Event as EventElement;
use Solspace\Calendar\Library\DateHelper;
use Throwable;
use yii\base\Event;

/**
 *
 * @property-read string $mappingTemplate
 * @property-read mixed $groups
 * @property-write mixed $model
 * @property-read string $groupsTemplate
 * @property-read string $columnTemplate
 */
class CalenderEvent extends Element
{
    public const RRULE_MAP = [
        'BYMONTH' => 'byMonth',
        'BYYEARDAY' => 'byYearDay',
        'BYMONTHDAY' => 'byMonthDay',
        'BYDAY' => 'byDay',
        'UNTIL' => 'until',
        'INTERVAL' => 'interval',
        'FREQ' => 'freq',
        'COUNT' => 'count',
    ];

    // Properties
    // =========================================================================

    /**
     * @var string
     */
    public static string $name = 'Calendar Event';

    /**
     * @var string
     */
    public static string $class = 'Solspace\Calendar\Elements\Event';

    /**
     * @var array
     */
    private array $rruleInfo = [];

    /**
     * @var array
     */
    private array $selectDates = [];


    // Templates
    // =========================================================================

    /**
     * @inheritDoc
     */
    public function getGroupsTemplate(): string
    {
        return 'easyapi/_includes/elements/calendar-events/groups';
    }

    /**
     * @inheritDoc
     */
    public function getColumnTemplate(): string
    {
        return 'easyapi/_includes/elements/calendar-events/column';
    }

    /**
     * @inheritDoc
     */
    public function getMappingTemplate(): string
    {
        return 'easyapi/_includes/elements/calendar-events/map';
    }

    // Public Methods
    // =========================================================================

    /**
     * @inheritDoc
     */
    public function getGroups(): array
    {
        if (Calendar::getInstance()) {
            return Calendar::getInstance()->calendars->getAllAllowedCalendars();
        }
        
        return [];
    }

    /**
     * @inheritDoc
     */
    public function getQuery($settings, array $params = []): mixed
    {
        $query = EventElement::find()
            ->status(null)
            ->setCalendarId($settings['elementGroup'][EventElement::class])
            ->siteId(Hash::get($settings, 'siteId') ?: Craft::$app->getSites()->getPrimarySite()->id);
        Craft::configure($query, $params);
        return $query;
    }

    /**
     * @inheritDoc
     */
    public function setModel($settings): ElementInterface
    {
        $siteId = (int)Hash::get($settings, 'siteId');
        $calendarId = $settings['elementGroup'][EventElement::class];

        $this->element = EventElement::create($siteId, $calendarId);

        return $this->element;
    }
}
