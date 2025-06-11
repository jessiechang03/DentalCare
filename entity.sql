CREATE TABLE IF NOT EXISTS User(
userID VARCHAR(10) PRIMARY KEY,
name VARCHAR(50) NOT NULL,
gender VARCHAR(6) NOT NULL,
contactNo VARCHAR(13) NOT NULL,
email VARCHAR(50)
);

CREATE TABLE IF NOT EXISTS Login(
username varchar(20) NOT NULL,
password VARCHAR(255) NOT NULL,
userlevel INT NOT NULL,
fk_userid VARCHAR(10) NOT NULL,
status VARCHAR(10) NOT NULL DEFAULT 'Active',
FOREIGN KEY (fk_userid) REFERENCES User(userID)
ON DELETE CASCADE
ON UPDATE CASCADE
);

CREATE TABLE IF NOT EXISTS Patient(
patientID VARCHAR(10) PRIMARY KEY,
dateOfBirth DATE DEFAULT CURRENT_DATE,
address VARCHAR(80),
userID VARCHAR(10),
FOREIGN KEY(userID) REFERENCES User(userID)
);

CREATE TABLE IF NOT EXISTS Dentist(
dentistID VARCHAR(10) PRIMARY KEY,
qualification VARCHAR(10) NOT NULL,
specialization VARCHAR(20) NOT NULL,
userID VARCHAR(10),
FOREIGN KEY (userID) REFERENCES User(userID)
);

CREATE TABLE IF NOT EXISTS Admin(
adminID VARCHAR(10) PRIMARY KEY,
qualification VARCHAR(25) NOT NULL,
userID VARCHAR(10),
FOREIGN KEY (userID) REFERENCES User(userID)
);

CREATE TABLE IF NOT EXISTS Appointment(
appointmentID VARCHAR(10) PRIMARY KEY,
appointmentTime DATETIME,
dentalServiceType VARCHAR(20) NOT NULL, -- Treatment / Check Up
serviceName VARCHAR(30) NOT NULL,
servicePrice FLOAT NOT NULL,
lengthOfTime VARCHAR(10) NOT NULL,
patientID VARCHAR(10),
dentistID VARCHAR(10),
appointmentStatus VARCHAR(20) NOT NULL DEFAULT 'Pending',
FOREIGN KEY(patientID) REFERENCES Patient(patientID),
FOREIGN KEY(dentistID) REFERENCES Dentist(dentistID)
);

CREATE TABLE IF NOT EXISTS DentalRecord(
recordID VARCHAR(10) PRIMARY KEY,
recordDate TIMESTAMP,
symptoms VARCHAR(20),
treatment VARCHAR(20),
history VARCHAR(50),
patientID VARCHAR(10),
dentistID VARCHAR(10),
appointmentID VARCHAR(10),
FOREIGN KEY (patientID) REFERENCES Patient(patientID),
FOREIGN KEY (dentistID) REFERENCES Dentist(dentistID),
FOREIGN KEY (appointmentID) REFERENCES Appointment(appointmentID)
);

CREATE TABLE IF NOT EXISTS Payment(
invoiceNo VARCHAR(10) PRIMARY KEY,
paymentDate TIMESTAMP,
paymentFees FLOAT(8) NOT NULL,
paymentStatus VARCHAR(20) NOT NULL,
paymentMethod VARCHAR(20) NOT NULL, -- Cash / Online Transfer
recordID VARCHAR(10),
appointmentID VARCHAR(10),
FOREIGN KEY (appointmentID) REFERENCES Appointment(appointmentID)
);




-- User (Patient)
INSERT INTO User
VALUES('U007', 'Denies Wong', 'Female', '01139762466', 'denies0516@gmail.com');
INSERT INTO User
VALUES('U008', 'Joyce Lee', 'Female', '0167710851', 'joyce031019@yahoo.com');
INSERT INTO User
VALUES('U009', 'Titanic Tan', 'Female', '01111413231', 'ttf0406@yahoo.com');
INSERT INTO User
VALUES('U010', 'Nancy Lim', 'Female', '01137707837', 'enting0601@yahoo.com');
INSERT INTO User
VALUES('U011', 'Christina Tang', 'Female', '0127376115', 'zyiibusytoeat@gmail.com');
INSERT INTO User
VALUES('U012', 'Koh Jing Yi', 'Male', '01110741160', 'jy030531@gmail.com');

-- User (Dentist)
INSERT INTO User
VALUES('U004', 'John Doe', 'Male', '01169382348', 'john.doe@hotmail.com');
INSERT INTO User
VALUES('U005', 'Alice Smith', 'Female', '01128962522', 'alice.smith@gmail.com');
INSERT INTO User
VALUES('U006', 'Bob Johnson', 'Male', '0124141691', 'bob.johnson@yahoo.com');

-- User (Admin)
INSERT INTO User
VALUES('U001', 'Chua Ern Qi', 'Female', '01111311384', 'chloee031023@gmail.com');
INSERT INTO User
VALUES('U002', 'Eileen Yong Kai Qin', 'Female', '0143165650', 'kaiqin0206@gmail.com');
INSERT INTO User
VALUES('U003', 'Jessie Chang', 'Female', '0199322460', 'jessie1125@gmail.com');

-- Patient
INSERT INTO Patient
VALUES('P001', '2003-05-16', '20, Jalan Lawa 16, 81800, Johor', 'U007');
INSERT INTO Patient
VALUES('P002', '2003-10-19', '60, Jalan Pesona 16, 81800, Johor', 'U008');
INSERT INTO Patient
VALUES('P003', '2003-04-06', '12, Jalan Danau 27, 81800, Johor', 'U009');
INSERT INTO Patient
VALUES('P004', '2003-06-01', 'Blok L, Jalan Gaya 11, 81800, Johor', 'U010');
INSERT INTO Patient
VALUES('P005', '2003-09-05', '31, Jalan Harmonium 30/5, 81800, Johor', 'U011');
INSERT INTO Patient
VALUES('P006', '2003-05-31', '25, Jalan Cemerlang 6, 81800, Johor', 'U012');

-- Dentist
INSERT INTO Dentist
VALUES('D001', 'DDS', 'Orthodontics', 'U004');
INSERT INTO Dentist
VALUES('D002', 'DMD', 'Endodontics', 'U005');
INSERT INTO Dentist
VALUES('D003', 'BDS', 'General Dentistry', 'U006');

-- Admin
INSERT INTO Admin
VALUES('R001', 'Reception Management', 'U001');
INSERT INTO Admin
VALUES('R002', 'Office Administration', 'U002');
INSERT INTO Admin
VALUES('R003', 'Office Administration', 'U003');

-- Login (Dentist)
INSERT INTO Login
VALUES ('john', MD5('john@123'), 2, 'U004', 'Active');

INSERT INTO Login
VALUES ('alice', MD5('alice@123'), 2, 'U005', 'Active');

INSERT INTO Login
VALUES ('bob', MD5('bob@123'), 2, 'U006', 'Active');

-- Login (Patient)
INSERT INTO Login
VALUES ('denies', MD5('denies@123'), 3, 'U007', 'Active');

INSERT INTO Login
VALUES ('joyce', MD5('joyce@123'), 3, 'U008', 'Active');

INSERT INTO Login
VALUES ('titanic', MD5('titanic@123'), 3, 'U009', 'Active');

INSERT INTO Login
VALUES ('nancy', MD5('nancy@123'), 3, 'U010', 'Active');

INSERT INTO Login
VALUES ('christina', MD5('christina@123'), 3, 'U011', 'Active');

INSERT INTO Login
VALUES ('kohjy', MD5('kohjy@123'), 3, 'U012', 'Active');

-- Login (Admin)
INSERT INTO Login
VALUES ('chuaqi', MD5('chuaqi@123'), 1, 'U001', 'Active');

INSERT INTO Login
VALUES ('eileen', MD5('eileen@123'), 1, 'U002', 'Active');

INSERT INTO Login
VALUES ('jessie', MD5('jessie@123'), 1, 'U003', 'Active');

-- Booking the appointment
-- Book Appointments with Treatment Type
INSERT INTO Appointment
VALUES ('A002', TIMESTAMP('2024-02-01 10:00:00'), 'Treatment', 'Teeth Cleaning', 100, '60 mins', 'P001', 'D002', 'Approved');
INSERT INTO Appointment
VALUES ('A003', TIMESTAMP('2024-02-01 15:00:00'), 'Treatment', 'Crown Placement',  350, '90 mins', 'P002', 'D001', 'Approved');
INSERT INTO Appointment
VALUES ('A005', TIMESTAMP('2024-02-02 14:00:00'), 'Treatment', 'Teeth Whitening',  250, '60 mins', 'P003', 'D001', 'Approved');
INSERT INTO Appointment
VALUES ('A006', TIMESTAMP('2024-02-08 10:00:00'), 'Treatment', 'Root Canal',  500, '120 mins', 'P006', 'D002', 'Approved');

-- Book Appointments with CheckUp Type
INSERT INTO Appointment
VALUES ('A001', TIMESTAMP('2024-02-01 09:00:00'), 'CheckUp', 'Routine Checkup',  80, '30 mins', 'P004', 'D003', 'Approved');
INSERT INTO Appointment
VALUES ('A004', TIMESTAMP('2024-02-02 10:00:00'), 'CheckUp', 'Comprehensive Checkup',  100, '45 mins', 'P005', 'D002', 'Approved');

-- Do dental record
INSERT INTO DentalRecord
VALUES ('DR001', TIMESTAMP('2024-02-01 12:00:00'), 'None', 'Cleaning', 'No major dental
issues', 'P001', 'D002', 'A002');
INSERT INTO DentalRecord
VALUES ('DR002', TIMESTAMP('2024-02-01 17:00:00'), 'Sensitive teeth', 'Cleaning',
'Previous cavities', 'P002', 'D001', 'A003');
INSERT INTO DentalRecord
VALUES ('DR003', TIMESTAMP('2024-02-02 11:00:00'), 'Toothache', 'Crown Placement',
'Previous root canal', 'P003', 'D001', 'A005');
INSERT INTO DentalRecord
VALUES ('DR004', TIMESTAMP('2024-02-03 10:10:00'), 'None', 'Routine exam', 'No issues',
'P004', 'D003', 'A001');
INSERT INTO DentalRecord
VALUES ('DR005', TIMESTAMP('2024-02-06 14:20:00'), 'Dull teeth', 'Teeth whitening',
'Previous whitening treatment', 'P005', 'D002', 'A004');
INSERT INTO DentalRecord
VALUES ('DR006', TIMESTAMP('2024-02-08 10:10:00'), 'Severe Toothache', 'Root Canal',
'Previous cavity', 'P006', 'D002', 'A006');

-- Make Payment
INSERT INTO Payment
VALUES ('I001', TIMESTAMP('2024-02-01 11:50:00'), 80, 'Paid', 'Cash', 'DR001', 'A002');
INSERT INTO Payment
VALUES ('I002', TIMESTAMP('2024-02-01 16:50:00'), 100, 'Paid', 'Online Transfer', 'DR002', 'A003');
INSERT INTO Payment
VALUES ('I003', TIMESTAMP('2024-02-02 10:50:00'), 350, 'Paid', 'Online Transfer', 'DR003', 'A005');
INSERT INTO Payment
VALUES ('I004', TIMESTAMP('2024-02-03 10:00:00'), 100, 'Paid', 'Cash', 'DR004', 'A001');
INSERT INTO Payment
VALUES ('I005', TIMESTAMP('2024-02-06 14:10:00'), 250, 'Paid', 'Cash', 'DR005', 'A004');
INSERT INTO Payment
VALUES ('I006', TIMESTAMP('2024-02-10 09:30:00'), 500, 'Paid', 'Online Transfer', 'DR006', 'A006');

ALTER TABLE Payment 
ADD FOREIGN KEY (recordID) REFERENCES DentalRecord(recordID);






