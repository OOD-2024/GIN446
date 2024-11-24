create table patient ( 
	ID int not null primary key auto_increment, 
    First_Name varchar(20)  not null,
    Last_name varchar(20) not null , 
    Email varchar(30)  not null, 
    pwd varchar(30) not null , 
    phoneNum varchar(15) not null, 
    DOB  date not null , 
    gender varchar(10)  not null, 
    BloodType varchar(3) not null, 
    Created_AT timestamp default current_timestamp,
    updated_at timestamp default current_timestamp, 
    session_id varchar(50) default null 
    
)
;
create table doctor (
	ID int  not null primary key auto_increment,
    Start_Date date ,
	constraint PK_ID primary key (id) ,
    constraint FK_ID foreign key(id) references patient(id) 
);

create table sepciality ( 
DoctorID int , 
Speciality_ID int,
Speciality_Name VARCHAR(30) ,
constraint PK primary key(DoctorID , Speciality_ID) ,
constraint FK foreign Key (DoctorID) references doctor(ID) 

);

create table location ( 
	ID int primary key , 
    country varchar(20) , 
    city varchar(20) , 
    building varchar(20) , 
    Street varchar(20) 
) ; 

create table appointment ( 
	DoctorID int, 
    PatientID int , 
    AppointmentId char(6) ,
    Appointment_date Date , 
    location int  , 
    StartTime time , 
    Endtime time , 
    Note varchar(50) , 
    constraint PK primary key (doctorID , patientID , AppointmentId) ,
    constraint FK_DOC_ID foreign key (doctorid ) references doctor(id) , 
    constraint FK_PAT_ID foreign key (patientID ) references patient(id), 
    constraint FK_LOC_ID foreign key (location) references location(id)
    
);

create table medical_record ( 
    patient_id int NOT null ,
	diagnosis varchar(20)  not null, 
	CONSTRAINT pk_id PRIMARY KEY (patient_id, diagnosis) ,
    CONSTRAINT fk_PID FOREIGN KEY (patient_id) REFERENCES patient(id)
) ;