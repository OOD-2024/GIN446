<?php

require_once 'user_model.inc.php';
function getAllAppointmentEvents($pdo, $id)
{
    try {
        $appointments = getAllAppointments($pdo, $id);

        if (empty($appointments)) {
            return [];
        }

        $events = [];
        foreach ($appointments as $apt) {
            $events[] = [
                'appointmentId' => $apt['AppointmentID'],
                'name' => $apt['doctor_name'],
                'doctor' => $apt['doctor_name'],
                'days' => [getDayOfWeek($apt['Appointment_Date'])],
                'startTime' => formatTime($apt['StartTime']),
                'endTime' => formatTime($apt['EndTime']),
                'from_time' => formatTime($apt['StartTime']),
                'to_time' => formatTime($apt['EndTime']),
                'location' => $apt['location'] ?? '',
                'note' => $apt['Note'] ?? '',
                'status' => $apt['status'] ?? 'Available',
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
