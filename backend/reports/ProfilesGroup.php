<?php

namespace backend\reports;
use ms\loyalty\reports\contracts\base\ReportInterface;
use ms\loyalty\reports\contracts\types\GroupReportInterface;


/**
 * Class ProfilesGroup
 */
class ProfilesGroup implements ReportInterface, GroupReportInterface
{

    /**
     * @return ReportInterface[]
     */
    public function reports()
    {
        return [
            new ProfilesStat(),
            new ProfilesRegistrationChart(),
        ];
    }

    /**
     * Returns title of the report
     * @return string
     */
    public function title()
    {
        return 'Статистика участников';
    }
}