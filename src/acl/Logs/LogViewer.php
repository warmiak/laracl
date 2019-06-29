<?php

namespace App\Logs;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\File;
use Psr\Log\LogLevel;

class LogViewer
{
    /**
     * Why? Uh... Sorry
     */
    const MAX_FILE_SIZE = 52428800;
    /**
     * @var string
     */
    private $pattern = '/\[\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}([\+-]\d{4})?\].*/';
    /**
     * @var array
     */
    private $logLevel = ['emergency', 'alert', 'critical', 'error', 'warning', 'notice', 'info', 'debug',];
    /**
     * @var array
     */
    private $levelClass = [
        'debug' => 'bg-blue-light text-white',
        'info' => 'bg-blue text-white',
        'notice' => 'bg-green text-white',
        'warning' => 'bg-orange text-white',
        'error' => 'bg-red-light text-white',
        'critical' => 'bg-red text-white',
        'alert' => 'bg-red-dark text-white',
        'emergency' => 'bg-red-darker text-white',
    ];
    /**
     * @var array
     */
    private $levelIcon = [
        'debug' => 'mdi-lifebuoy',
        'info' => 'mdi-information',
        'notice' => 'mdi-alert-circle',
        'warning' => 'mdi-alert',
        'error' => 'mdi-close-circle',
        'critical' => 'mdi-heart-pulse',
        'alert' => 'mdi-bullhorn',
        'emergency' => 'mdi-bug',
    ];

    /**
     * @param $file
     * @return array
     */
    public function getStats($file)
    {
        preg_match_all($this->pattern, $file, $headings);

        $logs = array();
        foreach ($headings as $header) {
            for ($i=0, $j = count($header); $i < $j; $i++) {
                foreach ($this->logLevel as $level) {
                    if (strpos(strtolower($header[$i]), '.' . $level) || strpos(strtolower($header[$i]), $level . ':')) {
                        preg_match('/^\[(\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}([\+-]\d{4})?)\](?:.*?(\w+)\.|.*?)' . $level . ': (.*?)( in .*?:[0-9]+)?$/i', $header[$i], $current);
                        $logs[] = array(
                            'level' => $level,
                        );
                    }
                }
            }
        }

        return array_count_values(array_column($logs, 'level'));
    }

    /**
     * @param $file
     * @return array
     */
    public function getMessage($file)
    {

        $logs = array();

        preg_match_all($this->pattern, $file, $headings);

        if (!is_array($headings)) {
            return $logs;
        }

        $log_data = preg_split($this->pattern, $file);

        if ($log_data[0] < 1) {
            array_shift($log_data);
        }

        foreach ($headings as $h) {
            for ($i=0, $j = count($h); $i < $j; $i++) {
                foreach ($this->logLevel as $level) {
                    if (strpos(strtolower($h[$i]), '.' . $level) || strpos(strtolower($h[$i]), $level . ':')) {
                        preg_match('/^\[(\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}([\+-]\d{4})?)\](?:.*?(\w+)\.|.*?)' . $level . ': (.*?)( in .*?:[0-9]+)?$/i', $h[$i], $current);
                        if (!isset($current[4])) continue;
                        $logs[] = array(
                            'context' => $current[3],
                            'level' => $level,
                            'class' => $this->levelClass[$level],
                            'icon' => $this->levelIcon[$level],
                            'date' => $current[1],
                            'message' => $current[4],
                            'in_file' => isset($current[5]) ? $current[5] : null,
                            'full_report' => preg_replace("/^\n*/", '', $log_data[$i])
                        );
                    }
                }
            }
        }

        return array_reverse($logs);
    }

    /**
     * @param $array
     * @param int $perPage
     * @param string $path
     * @return LengthAwarePaginator
     */
    public function getPagination($array, $perPage = 10, $path = 'logs')
    {
        $currentPage = LengthAwarePaginator::resolveCurrentPage();
        $col = new Collection($array);
        $currentPageSearchResults = $col->slice(($currentPage -1) * $perPage, $perPage)->all();
        $entries = new LengthAwarePaginator($currentPageSearchResults, count($col), $perPage);
        $entries->setPath($path);

        return $entries;
    }
}