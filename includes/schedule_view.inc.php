<?php
require_once 'schedule_model.inc.php';
function validate_appointment_input($input)
{
    $sanitized_input = [];
    $errors = [];

    if (empty($input['appointment_date'])) {
        $errors[] = "Appointment date is required";
    } else {
        $date = filter_var($input['appointment_date'], FILTER_SANITIZE_STRING);
        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) {
            $errors[] = "Invalid date format";
        }
        $sanitized_input['appointment_date'] = $date;
    }

    foreach (['start_time', 'end_time'] as $time_field) {
        if (empty($input[$time_field])) {
            $errors[] = ucfirst(str_replace('_', ' ', $time_field)) . " is required";
        } else {
            $time = filter_var($input[$time_field], FILTER_SANITIZE_STRING);
            if (!preg_match('/^\d{2}:\d{2}$/', $time)) {
                $errors[] = "Invalid time format for " . str_replace('_', ' ', $time_field);
            }
            $sanitized_input[$time_field] = $time;
        }
    }

    $id_fields = ['location_id', 'doctor_id', 'patient_id'];
    foreach ($id_fields as $field) {
        if (empty($input[$field])) {
            $errors[] = ucfirst(str_replace('_', ' ', $field)) . " is required";
        } else {
            $id = filter_var($input[$field], FILTER_VALIDATE_INT);
            if ($id === false || $id <= 0) {
                $errors[] = "Invalid " . str_replace('_', ' ', $field);
            }
            $sanitized_input[$field] = $id;
        }
    }

    if (strtotime($sanitized_input['start_time']) >= strtotime($sanitized_input['end_time'])) {
        $errors[] = "Start time must be before end time";
    }

    return [
        'input' => $sanitized_input,
        'errors' => $errors
    ];
}

function render_appointment_errors($errors)
{
    if (empty($errors)) {
        return '';
    }

    $error_html = '<div class="error-container">';
    $error_html .= '<h3>Appointment Scheduling Errors:</h3>';
    $error_html .= '<ul class="error-list">';

    foreach ($errors as $error) {
        $error_html .= '<li>' . htmlspecialchars($error) . '</li>';
    }

    $error_html .= '</ul>';
    $error_html .= '</div>';

    return $error_html;
}

function render_appointment_success($message)
{
    if (empty($message)) {
        return '';
    }

    return '<div class="success-container">' .
        '<h3>Success</h3>' .
        '<p>' . htmlspecialchars($message) . '</p>' .
        '</div>';
}

function redirect_with_appointment_errors($errors, $redirect_url = '../user.php')
{
    $_SESSION["errors_appointment"] = $errors;
    header('Location: ' . $redirect_url);
    exit();
}

function redirect_with_appointment_success($message, $redirect_url = '../user.php')
{
    $_SESSION['success_appointment'] = $message;
    header('Location: ' . $redirect_url);
    exit();
}
