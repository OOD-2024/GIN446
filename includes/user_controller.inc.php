<?php
function getAppointmentEvents($pdo, $id)
{
    try {
        $appointments = getAppointments($pdo, $id);
        if (empty($appointments)) {
            return [];
        }

        $events = [];
        foreach ($appointments as $apt) {
            $events[] = [
                'name' => $apt['doctor_name'],
                'days' => [getDayOfWeek($apt['Appointment_Date'])],
                'startTime' => formatTime($apt['StartTime']),
                'endTime' => formatTime($apt['EndTime']),
                'location' => $apt['location'] ?? '',
                'note' => $apt['Note'] ?? ''
            ];
        }

        return $events;
    } catch (Exception $e) {
        error_log("Error in getAppointmentEvents: " . $e->getMessage());
        throw new RuntimeException("Failed to process appointment events");
    }
}

function formatTime($time)
{
    return date('H:i', strtotime($time));
}

function getDayOfWeek($date)
{
    return date('w', strtotime($date));
}
function getrecords($pdo, $id)
{
    $records = get_medical_records($pdo, $id);
    return $records;
    // if (empty($records)) {
    //     return [];
    // } else {
    //     $rec = [];
    //     foreach ($records as $r) {
    //         $rec[] = [
    //             'diagnosis' => $r['diagnosis']
    //         ];
    //     }
    //     return $rec;
    // }
}