<?php

namespace App\Helpers;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class DebugHelper
{
    /**
     * Log detailed information about a service.
     *
     * @param int $serviceId
     * @return void
     */
    public static function logServiceDetails(int $serviceId): void
    {
        try {
            // Get service details
            $service = DB::table('services')->where('id', $serviceId)->first();
            if (!$service) {
                Log::error("DEBUG_HELPER: Service #{$serviceId} not found");
                return;
            }

            // Log service details
            Log::info("DEBUG_HELPER: Service #{$serviceId} details", [
                'service' => json_decode(json_encode($service), true),
            ]);

            // Get mechanics for this service
            $mechanics = DB::table('mechanic_service')
                ->where('service_id', $serviceId)
                ->get();

            // Log mechanics details
            Log::info("DEBUG_HELPER: Service #{$serviceId} mechanics", [
                'mechanics_count' => $mechanics->count(),
                'mechanics' => json_decode(json_encode($mechanics), true),
            ]);

            // Get mechanic reports for this service's mechanics
            $mechanicIds = $mechanics->pluck('mechanic_id')->toArray();
            $reports = DB::table('mechanic_reports')
                ->whereIn('mechanic_id', $mechanicIds)
                ->get();

            // Log mechanic reports
            Log::info("DEBUG_HELPER: Mechanic reports for service #{$serviceId}", [
                'reports_count' => $reports->count(),
                'reports' => json_decode(json_encode($reports), true),
            ]);
        } catch (\Exception $e) {
            Log::error("DEBUG_HELPER: Error logging service details", [
                'service_id' => $serviceId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
        }
    }

    /**
     * Log all database queries.
     *
     * @param bool $enable
     * @return void
     */
    public static function logDatabaseQueries(bool $enable = true): void
    {
        if ($enable) {
            DB::listen(function ($query) {
                $sql = $query->sql;
                $bindings = $query->bindings;
                $time = $query->time;

                // Format the query with bindings
                $sql = static::formatSqlWithBindings($sql, $bindings);

                Log::info("DEBUG_SQL: {$sql} [{$time}ms]");
            });

            Log::info("DEBUG_HELPER: Database query logging enabled");
        }
    }

    /**
     * Format SQL query with bindings.
     *
     * @param string $sql
     * @param array $bindings
     * @return string
     */
    private static function formatSqlWithBindings(string $sql, array $bindings): string
    {
        $sql = str_replace(['%', '?'], ['%%', '%s'], $sql);
        $bindings = array_map(function ($binding) {
            if (is_null($binding)) {
                return 'NULL';
            } elseif (is_bool($binding)) {
                return $binding ? 'TRUE' : 'FALSE';
            } elseif (is_string($binding)) {
                return "'" . addslashes($binding) . "'";
            } elseif (is_array($binding)) {
                return json_encode($binding);
            }
            return $binding;
        }, $bindings);

        return vsprintf($sql, $bindings);
    }

    /**
     * Log event details.
     *
     * @param object $event
     * @return void
     */
    public static function logEventDetails(object $event): void
    {
        try {
            $eventClass = get_class($event);
            $eventData = [];

            // Get public properties of the event
            $reflection = new \ReflectionClass($event);
            $properties = $reflection->getProperties(\ReflectionProperty::IS_PUBLIC);

            foreach ($properties as $property) {
                $name = $property->getName();
                $value = $property->getValue($event);

                if (is_object($value)) {
                    if (method_exists($value, 'toArray')) {
                        $eventData[$name] = $value->toArray();
                    } else {
                        $eventData[$name] = get_class($value) . ' (object)';
                    }
                } else {
                    $eventData[$name] = $value;
                }
            }

            Log::info("DEBUG_EVENT: {$eventClass}", [
                'event_data' => $eventData,
                'backtrace' => static::getBacktrace(),
            ]);
        } catch (\Exception $e) {
            Log::error("DEBUG_EVENT: Error logging event details", [
                'event_class' => get_class($event),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
        }
    }

    /**
     * Get a simplified backtrace.
     *
     * @param int $limit
     * @return array
     */
    private static function getBacktrace(int $limit = 10): array
    {
        $backtrace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, $limit + 2);
        // Remove the first two entries (this method and the calling method)
        $backtrace = array_slice($backtrace, 2, $limit);

        $result = [];
        foreach ($backtrace as $trace) {
            $result[] = [
                'file' => $trace['file'] ?? 'unknown',
                'line' => $trace['line'] ?? 'unknown',
                'function' => $trace['function'] ?? 'unknown',
                'class' => $trace['class'] ?? 'unknown',
            ];
        }

        return $result;
    }
}
