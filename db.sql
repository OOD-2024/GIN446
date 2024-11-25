CREATE DATABASE IF NOT EXISTS clinic;

USE clinic;

CREATE DATABASE IF NOT EXISTS clinic;

CREATE TABLE IF NOT EXISTS patient ( 
    ID INT NOT NULL PRIMARY KEY AUTO_INCREMENT, 
    First_Name VARCHAR(50) NOT NULL,
    Last_Name VARCHAR(50) NOT NULL, 
    Email VARCHAR(100) NOT NULL UNIQUE, 
    pwd VARCHAR(255) NOT NULL, 
    phoneNum VARCHAR(15) NOT NULL, 
    DOB DATE NOT NULL, 
    gender VARCHAR(10) NOT NULL, 
    BloodType VARCHAR(3) NOT NULL,
    Created_AT TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    Updated_AT TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP, 
    Session_ID VARCHAR(128) DEFAULT NULL,
    CONSTRAINT chk_gender CHECK (gender IN ('Male', 'Female', 'Other')),
    CONSTRAINT chk_bloodtype CHECK (BloodType IN ('A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-'))
);

CREATE TABLE IF NOT EXISTS doctor (
    ID INT NOT NULL PRIMARY KEY,
    Start_Date DATE NOT NULL,
    FOREIGN KEY (ID) REFERENCES patient(ID) ON DELETE CASCADE ON UPDATE CASCADE
);

CREATE TABLE IF NOT EXISTS specialty ( 
    DoctorID INT NOT NULL,
    Specialty_ID INT NOT NULL,
    Specialty_Name VARCHAR(50) NOT NULL,
    PRIMARY KEY (DoctorID, Specialty_ID),
    FOREIGN KEY (DoctorID) REFERENCES doctor(ID) ON DELETE CASCADE ON UPDATE CASCADE
);

CREATE TABLE IF NOT EXISTS location ( 
    ID INT PRIMARY KEY AUTO_INCREMENT, 
    Country VARCHAR(50) NOT NULL, 
    City VARCHAR(50) NOT NULL, 
    Building VARCHAR(50), 
    Street VARCHAR(100)
);

CREATE TABLE IF NOT EXISTS appointment ( 
    AppointmentID INT PRIMARY KEY AUTO_INCREMENT,
    DoctorID INT NOT NULL, 
    PatientID INT NOT NULL, 
    Appointment_Date DATE NOT NULL, 
    LocationID INT, 
    StartTime TIME NOT NULL, 
    EndTime TIME NOT NULL, 
    Note VARCHAR(255), 
    Appointment_Status VARCHAR(20) NOT NULL,
    FOREIGN KEY (DoctorID) REFERENCES doctor(ID) ON DELETE RESTRICT ON UPDATE CASCADE,
    FOREIGN KEY (PatientID) REFERENCES patient(ID) ON DELETE RESTRICT ON UPDATE CASCADE,
    FOREIGN KEY (LocationID) REFERENCES location(ID) ON DELETE SET NULL ON UPDATE CASCADE,
    CONSTRAINT chk_appointment_status CHECK (Appointment_Status IN ('Scheduled', 'Completed', 'Cancelled', 'Available')),
    CONSTRAINT chk_appointment_time CHECK (EndTime > StartTime)
);

CREATE TABLE IF NOT EXISTS medical_record ( 
    PatientID INT NOT NULL,
    Diagnosis VARCHAR(100) NOT NULL,
    DiagnosisDate DATE NOT NULL DEFAULT (CURRENT_DATE),
    Treatment VARCHAR(255),
    Notes TEXT,
    PRIMARY KEY (PatientID, Diagnosis, DiagnosisDate),
    FOREIGN KEY (PatientID) REFERENCES patient(ID) ON DELETE RESTRICT ON UPDATE CASCADE
);

-- Dumb Data to test
-- Insert sample patients
INSERT INTO patient (First_Name, Last_Name, Email, pwd, phoneNum, DOB, gender, BloodType) VALUES
('John', 'Doe', 'john.doe@email.com', 'hashedpass123', '+1234567890', '1990-05-15', 'Male', 'O+'),
('Jane', 'Smith', 'jane.smith@email.com', 'hashedpass456', '+1234567891', '1985-08-22', 'Female', 'A+'),
('Robert', 'Johnson', 'robert.j@email.com', 'hashedpass789', '+1234567892', '1978-12-03', 'Male', 'B-'),
('Maria', 'Garcia', 'maria.g@email.com', 'hashedpass101', '+1234567893', '1995-03-28', 'Female', 'AB+'),
('James', 'Wilson', 'james.w@email.com', 'hashedpass102', '+1234567894', '1982-07-14', 'Male', 'A-');

-- Insert these patients as doctors (assuming they're all doctors)
INSERT INTO doctor (ID, Start_Date) VALUES
(1, '2015-01-15'),  -- John Doe
(2, '2016-03-01'),  -- Jane Smith
(3, '2010-06-22');  -- Robert Johnson

-- Insert specialties for doctors
INSERT INTO specialty (DoctorID, Specialty_ID, Specialty_Name) VALUES
(1, 1, 'Cardiology'),
(1, 2, 'Internal Medicine'),
(2, 3, 'Pediatrics'),
(2, 4, 'Family Medicine'),
(3, 5, 'Neurology');

-- Insert locations
INSERT INTO location (ID, Country, City, Building, Street) VALUES
(1, 'USA', 'New York', 'Medical Plaza', '123 Health St'),
(2, 'USA', 'Los Angeles', 'Care Center', '456 Wellness Ave'),
(3, 'USA', 'Chicago', 'Healing Hub', '789 Medical Dr');

-- Insert appointments
INSERT INTO appointment (DoctorID, PatientID, Appointment_Date, LocationID, StartTime, EndTime, Note, Appointment_Status) VALUES
(1, 4, '2024-11-25', 1, '09:00:00', '10:00:00', 'Regular checkup', 'Scheduled'),
(2, 5, '2024-11-25', 1, '10:30:00', '11:30:00', 'Follow-up', 'Scheduled'),
(3, 4, '2024-11-26', 2, '14:00:00', '15:00:00', 'Initial consultation', 'Scheduled'),
(1, 5, '2024-11-24', 1, '11:00:00', '12:00:00', 'Annual physical', 'Completed'),
(2, 4, '2024-11-23', 3, '13:00:00', '14:00:00', 'Emergency visit', 'Completed');

-- Insert medical records
INSERT INTO medical_record (PatientID, Diagnosis, DiagnosisDate, Treatment, Notes) VALUES
(4, 'Hypertension', '2024-03-24', 'Prescribed Lisinopril', 'Blood pressure 140/90'),
(4, 'Migraine', '2024-03-20', 'Prescribed Sumatriptan', 'Frequent headaches'),
(5, 'Type 2 Diabetes', '2024-03-23', 'Metformin 500mg', 'HbA1c: 7.2%'),
(5, 'Anxiety', '2024-03-22', 'Referred to therapy', 'Experiencing work-related stress');