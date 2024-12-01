<?php

declare(strict_types=1);

require_once 'schedule_model.inc.php';
function displayDoctorLocation($pdo, $user_id)
{

    try {
        $locations = getDoctorLocations($pdo, $user_id);

        if (empty($locations)) {
            echo "<option value=''>No locations available</option>";
            return;
        }

        foreach ($locations as $row) {
            $location_description = implode(", ", array_filter([
                $row['Building'],
                $row['Street'],
                $row['City'],
                $row['Country']
            ]));
            echo "<option value='" . htmlspecialchars($row['ID']) . "'>" .
                htmlspecialchars($location_description) . "</option>";
        }
    } catch (Exception $e) {
        error_log("Error in displayDoctorLocation: " . $e->getMessage());
        echo "<option>Error loading locations</option>";
    }
}
