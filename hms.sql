-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Dec 03, 2025 at 07:49 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `hms`
--

-- --------------------------------------------------------

--
-- Table structure for table `admission`
--

CREATE TABLE `admission` (
  `admission_id` int(11) NOT NULL,
  `patient_id` int(11) NOT NULL,
  `room_number` int(11) NOT NULL,
  `admission_date` date NOT NULL,
  `discharge_date` date DEFAULT NULL,
  `diagnosis` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admission`
--

INSERT INTO `admission` (`admission_id`, `patient_id`, `room_number`, `admission_date`, `discharge_date`, `diagnosis`) VALUES
(1, 5, 104, '2025-11-01', '2025-11-07', 'Appendicitis'),
(2, 12, 102, '2025-11-03', NULL, 'Fracture'),
(3, 19, 203, '2025-11-05', NULL, 'Pneumonia'),
(4, 8, 107, '2025-11-07', '2025-11-10', 'Gastroenteritis'),
(5, 21, 101, '2025-11-08', NULL, 'Cardiac Arrest'),
(6, 3, 105, '2025-11-10', '2025-11-15', 'Migraine'),
(7, 17, 202, '2025-11-12', NULL, 'Allergic Reaction'),
(8, 25, 110, '2025-11-14', NULL, 'Diabetes Complication'),
(9, 9, 108, '2025-11-15', '2025-11-20', 'Asthma Attack'),
(10, 14, 104, '2025-11-17', NULL, 'Kidney Infection');

-- --------------------------------------------------------

--
-- Table structure for table `appointment`
--

CREATE TABLE `appointment` (
  `appointment_id` int(11) NOT NULL,
  `patient_id` int(11) NOT NULL,
  `doctor_id` int(11) NOT NULL,
  `appointment_datetime` datetime NOT NULL,
  `reason` varchar(100) DEFAULT NULL,
  `status` enum('Scheduled','Completed','Cancelled') DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `appointment`
--

INSERT INTO `appointment` (`appointment_id`, `patient_id`, `doctor_id`, `appointment_datetime`, `reason`, `status`) VALUES
(1, 5, 2, '2025-12-03 09:00:00', 'Appendicitis follow-up', 'Scheduled'),
(2, 12, 4, '2025-12-03 10:00:00', 'Fracture check', 'Scheduled'),
(3, 19, 5, '2025-12-04 11:30:00', 'Pneumonia treatment', 'Scheduled'),
(4, 8, 3, '2025-12-04 09:45:00', 'Gastroenteritis follow-up', 'Scheduled'),
(5, 21, 1, '2025-12-05 14:00:00', 'Cardiac arrest recovery', 'Scheduled'),
(6, 3, 6, '2025-12-05 15:30:00', 'Migraine treatment', 'Scheduled'),
(7, 17, 7, '2025-12-06 10:15:00', 'Allergic reaction treatment', 'Scheduled'),
(8, 25, 8, '2025-12-06 13:00:00', 'Diabetes complication review', 'Scheduled'),
(9, 9, 9, '2025-12-07 09:00:00', 'Asthma attack treatment', 'Scheduled'),
(10, 14, 10, '2025-12-07 11:00:00', 'Kidney infection follow-up', 'Scheduled');

-- --------------------------------------------------------

--
-- Table structure for table `department`
--

CREATE TABLE `department` (
  `department_id` int(11) NOT NULL,
  `department_name` varchar(20) NOT NULL,
  `location` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `department`
--

INSERT INTO `department` (`department_id`, `department_name`, `location`) VALUES
(1, 'Cardiology', '5th Floor, Block A'),
(2, 'Neurology', '6th Floor, Block B'),
(3, 'Orthopedics', '4th Floor, Block C'),
(4, 'Pediatrics', '3rd Floor, Block D'),
(5, 'ENT', '2nd Floor, Block E'),
(6, 'Gynecology', '3rd Floor, Block F'),
(7, 'Dermatology', '2nd Floor, Block G'),
(8, 'Ophthalmology', '1st Floor, Block H'),
(9, 'Psychiatry', '2nd Floor, Block I'),
(10, 'General Surgery', '5th Floor, Block J');

-- --------------------------------------------------------

--
-- Table structure for table `doctor`
--

CREATE TABLE `doctor` (
  `doctor_id` int(11) NOT NULL,
  `first_name` varchar(20) NOT NULL,
  `last_name` varchar(20) NOT NULL,
  `gender` enum('M','F','Other') NOT NULL,
  `dob` date NOT NULL,
  `phone` varchar(15) DEFAULT NULL,
  `email` varchar(30) DEFAULT NULL,
  `address` varchar(50) DEFAULT NULL,
  `specialty` varchar(20) NOT NULL,
  `department_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `doctor`
--

INSERT INTO `doctor` (`doctor_id`, `first_name`, `last_name`, `gender`, `dob`, `phone`, `email`, `address`, `specialty`, `department_id`) VALUES
(1, 'Arif', 'Rahman', 'M', '1980-04-12', '01750001111', 'arif.rahman@example.com', 'Dhaka, Banani', 'Cardiology', 7),
(2, 'Nabila', 'Haque', 'F', '1985-07-20', '01966002222', 'nabila.haque@example.com', 'Chittagong, Agrabad', 'Neurology', 3),
(3, 'Imran', 'Sayeed', 'M', '1978-11-05', '01777003333', 'imran.sayeed@example.com', 'Sylhet, Zindabazar', 'Orthopedics', 10),
(4, 'Rumana', 'Shikha', 'F', '1982-03-15', '01888004444', 'rumana.shikha@example.com', 'Rajshahi, Motihar', 'Pediatrics', 2),
(5, 'Tariq', 'Alam', 'M', '1975-12-22', '01799005555', 'tariq.alam@example.com', 'Khulna, Sonadanga', 'ENT', 5),
(6, 'Laila', 'Karim', 'F', '1988-06-18', '01911006666', 'laila.karim@example.com', 'Dhaka, Mirpur', 'Gynecology', 1),
(7, 'Fahim', 'Ali', 'M', '1986-07-25', '01766770000', 'fahim.ali@example.com', 'Dhaka, Uttara', 'Dermatology', 8),
(8, 'Sadia', 'Chowdhury', 'F', '1990-03-30', '01833007777', 'sadia.chowdhury@example.com', 'Khulna, Sonadanga', 'Ophthalmology', 4),
(9, 'Imtiaz', 'Khan', 'M', '1975-12-05', '01711223344', 'imtiaz.khan@example.com', 'Gazipur', 'Psychiatry', 9),
(10, 'Rita', 'Das', 'F', '1988-06-17', '01833445566', 'rita.das@example.com', 'Dhaka, Banani', 'General Surgery', 6);

-- --------------------------------------------------------

--
-- Table structure for table `invoice`
--

CREATE TABLE `invoice` (
  `invoice_id` int(11) NOT NULL,
  `admission_id` int(11) NOT NULL,
  `patient_id` int(11) NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `issued_date` date NOT NULL,
  `status` enum('Paid','Unpaid','Pending') DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `invoice`
--

INSERT INTO `invoice` (`invoice_id`, `admission_id`, `patient_id`, `amount`, `issued_date`, `status`) VALUES
(1, 1, 5, 1500.00, '2025-11-07', 'Paid'),
(2, 2, 12, 800.00, '2025-11-10', 'Unpaid'),
(3, 3, 19, 1200.00, '2025-11-15', 'Pending'),
(4, 4, 8, 500.00, '2025-11-10', 'Paid'),
(5, 5, 21, 2500.00, '2025-11-12', 'Unpaid'),
(6, 6, 3, 400.00, '2025-11-15', 'Paid'),
(7, 7, 17, 600.00, '2025-11-18', 'Pending'),
(8, 8, 25, 1800.00, '2025-11-20', 'Unpaid'),
(9, 9, 9, 700.00, '2025-11-20', 'Paid'),
(10, 10, 14, 1100.00, '2025-11-22', 'Pending');

-- --------------------------------------------------------

--
-- Table structure for table `lab_report`
--

CREATE TABLE `lab_report` (
  `report_id` int(11) NOT NULL,
  `patient_id` int(11) NOT NULL,
  `admission_id` int(11) NOT NULL,
  `test_id` int(11) NOT NULL,
  `result` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `lab_report`
--

INSERT INTO `lab_report` (`report_id`, `patient_id`, `admission_id`, `test_id`, `result`) VALUES
(1, 5, 1, 1, 'Normal'),
(2, 12, 2, 2, 'High Sugar'),
(3, 19, 3, 1, 'Elevated WBC'),
(4, 8, 4, 4, 'Mild Liver Issue'),
(5, 21, 5, 5, 'Normal ECG'),
(6, 3, 6, 3, 'Normal'),
(7, 17, 7, 2, 'Allergy Detected'),
(8, 25, 8, 2, 'High Sugar'),
(9, 9, 9, 6, 'Abnormal ECG'),
(10, 14, 10, 4, 'Kidney function impaired');

-- --------------------------------------------------------

--
-- Table structure for table `lab_test`
--

CREATE TABLE `lab_test` (
  `test_id` int(11) NOT NULL,
  `test_name` varchar(100) NOT NULL,
  `price` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `lab_test`
--

INSERT INTO `lab_test` (`test_id`, `test_name`, `price`) VALUES
(1, 'Complete Blood Count', 200.00),
(2, 'Blood Sugar Test', 150.00),
(3, 'Urine Test', 100.00),
(4, 'Liver Function Test', 300.00),
(5, 'X-Ray', 500.00),
(6, 'ECG', 400.00);

-- --------------------------------------------------------

--
-- Table structure for table `medicine`
--

CREATE TABLE `medicine` (
  `medicine_id` int(11) NOT NULL,
  `medicine_name` varchar(255) DEFAULT NULL,
  `description` varchar(100) DEFAULT NULL,
  `price` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `medicine`
--

INSERT INTO `medicine` (`medicine_id`, `medicine_name`, `description`, `price`) VALUES
(1, 'Paracetamol', 'Pain reliever and fever reducer', 15.00),
(2, 'Amoxicillin', 'Antibiotic for bacterial infections', 45.50),
(3, 'Omeprazole', 'Reduces stomach acid', 60.00),
(4, 'Cough Syrup', 'Relieves cough and cold symptoms', 35.75),
(5, 'Ibuprofen', 'Anti-inflammatory painkiller', 25.00),
(6, 'Metformin', 'Used to treat type 2 diabetes', 50.00),
(7, 'Aspirin', 'Blood thinner and pain reliever', 20.00),
(8, 'Cetirizine', 'Antihistamine for allergies', 18.50),
(9, 'Salbutamol Inhaler', 'Bronchodilator for asthma', 120.00),
(10, 'Vitamin C', 'Boosts immunity', 12.00),
(11, 'Azithromycin', 'Antibiotic for infections', 55.00),
(12, 'Loratadine', 'Antihistamine for allergies', 22.00),
(13, 'Diclofenac', 'Pain relief and anti-inflammatory', 28.50),
(14, 'Ranitidine', 'Reduces stomach acid', 40.00),
(15, 'Hydrocortisone Cream', 'Topical steroid for skin conditions', 35.00),
(16, 'Metoprolol', 'Used for blood pressure and heart issues', 65.00),
(17, 'Insulin', 'Controls blood sugar in diabetes', 150.00),
(18, 'Saline Solution', 'For hydration and IV use', 10.00),
(19, 'Fexofenadine', 'Antihistamine for allergy relief', 25.00),
(20, 'Multivitamin', 'Daily vitamin supplement', 30.00);

-- --------------------------------------------------------

--
-- Table structure for table `patient`
--

CREATE TABLE `patient` (
  `patient_id` int(11) NOT NULL,
  `first_name` varchar(20) NOT NULL,
  `last_name` varchar(20) NOT NULL,
  `date_of_birth` date NOT NULL,
  `gender` enum('M','F','Other') NOT NULL,
  `blood_group` enum('A+','A-','B+','B-','O+','O-','AB+','AB-') NOT NULL,
  `phone` varchar(15) NOT NULL,
  `email` varchar(50) NOT NULL,
  `address` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `patient`
--

INSERT INTO `patient` (`patient_id`, `first_name`, `last_name`, `date_of_birth`, `gender`, `blood_group`, `phone`, `email`, `address`) VALUES
(1, 'Ayesha', 'Rahman', '1998-03-12', 'F', 'O+', '01711000111', 'ayesha.rahman@example.com', 'Dhaka, Uttara'),
(2, 'Imran', 'Hossain', '1985-11-25', 'M', 'A+', '01822000555', 'imran.hossain@example.com', 'Chittagong, Agrabad'),
(3, 'Ritu', 'Das', '2001-07-09', 'F', 'B+', '01933000888', 'ritu.das@example.com', 'Dhaka, Mirpur'),
(4, 'Hasan', 'Ali', '1990-02-14', 'M', 'AB+', '01755000999', 'hasan.ali@example.com', 'Sylhet, Zindabazar'),
(5, 'Minu', 'Khatun', '1975-12-05', 'F', 'O-', '01888000123', 'minu.khatun@example.com', 'Barishal Sadar'),
(6, 'Arnab', 'Chowdhury', '1996-07-19', 'M', 'A-', '01999000555', 'arnab.chowdhury@example.com', 'Dhaka, Banani'),
(7, 'Liza', 'Karim', '2003-04-22', 'F', 'B-', '01722000888', 'liza.karim@example.com', 'Rajshahi, Motihar'),
(8, 'Farhan', 'Ahmed', '1999-11-08', 'M', 'O+', '01833000991', 'farhan.ahmed@example.com', 'Khulna, Sonadanga'),
(9, 'Priya', 'Sen', '1987-03-01', 'F', 'A+', '01977000444', 'priya.sen@example.com', 'Dhaka, Dhanmondi'),
(10, 'Masud', 'Rana', '1979-10-30', 'M', 'B+', '01788000321', 'masud.rana@example.com', 'Rangpur Town'),
(11, 'Niloy', 'Saha', '1995-06-15', 'M', 'O+', '01711044321', 'niloy.saha@example.com', 'Dhaka, Bashundhara'),
(12, 'Orpa', 'Islam', '1998-05-18', 'F', 'AB-', '01855774432', 'orpa.islam@example.com', 'Mymensingh'),
(13, 'Tanim', 'Mahmud', '2000-01-17', 'M', 'A+', '01799887755', 'tanim.mahmud@example.com', 'Dhaka, Tejgaon'),
(14, 'Ria', 'Akter', '1993-09-03', 'F', 'O-', '01766774455', 'ria.akter@example.com', 'Narayanganj'),
(15, 'Tanvir', 'Hasan', '1988-12-11', 'M', 'B+', '01988775544', 'tanvir.hasan@example.com', 'Comilla'),
(16, 'Rohan', 'Khan', '2002-02-09', 'M', 'A-', '01812345678', 'rohan.khan@example.com', 'Dhaka, Farmgate'),
(17, 'Sima', 'Dey', '1982-08-21', 'F', 'O+', '01799966442', 'sima.dey@example.com', 'Sylhet'),
(18, 'Jayed', 'Islam', '1997-12-29', 'M', 'B-', '01911000432', 'jayed.islam@example.com', 'Khulna'),
(19, 'Ananya', 'Paul', '2004-07-26', 'F', 'AB+', '01711223456', 'ananya.paul@example.com', 'Dhaka, Mirpur-10'),
(20, 'Mahir', 'Chowdhury', '1991-04-11', 'M', 'O+', '01866554433', 'mahir.chowdhury@example.com', 'Gazipur'),
(21, 'Samuel', 'Hart', '1991-02-14', 'M', 'A+', '01722009911', 'samuel.hart@example.com', 'Dhaka, Dhanmondi'),
(22, 'Emily', 'Carson', '1996-08-23', 'F', 'B-', '01833004499', 'emily.carson@example.com', 'Chittagong, Halishahar'),
(23, 'Ayaan', 'Shah', '2000-12-05', 'M', 'O+', '01788002244', 'ayaan.shah@example.com', 'Sylhet, Amberkhana'),
(24, 'Mia', 'Jenkins', '1987-11-17', 'F', 'AB+', '01999001234', 'mia.jenkins@example.com', 'Khulna, Khalishpur'),
(25, 'Zara', 'Hussain', '1994-05-30', 'F', 'A-', '01755007733', 'zara.hussain@example.com', 'Rajshahi, Shaheb Bazar'),
(26, 'Ethan', 'Brooks', '1990-07-12', 'M', 'B+', '01844006622', 'ethan.brooks@example.com', 'Dhaka, Gulshan 2'),
(27, 'Aisha', 'Rahim', '2001-01-27', 'F', 'O-', '01766005577', 'aisha.rahim@example.com', 'Barishal, Sadar Road'),
(28, 'Liam', 'Foster', '1985-03-19', 'M', 'AB-', '01977001122', 'liam.foster@example.com', 'Dhaka, Uttara Sector 4'),
(29, 'Noor', 'Chowdhury', '1993-04-08', 'F', 'A+', '01890003355', 'noor.chowdhury@example.com', 'Comilla, Kandirpar'),
(30, 'Caleb', 'Morgan', '1997-09-14', 'M', 'B+', '01733007788', 'caleb.morgan@example.com', 'Dhaka, Mirpur 11');

-- --------------------------------------------------------

--
-- Table structure for table `prescription`
--

CREATE TABLE `prescription` (
  `prescription_id` int(11) NOT NULL,
  `treatment_id` int(11) NOT NULL,
  `medicine_id` int(11) NOT NULL,
  `dosage` varchar(50) NOT NULL,
  `frequency` varchar(50) NOT NULL,
  `duration` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `prescription`
--

INSERT INTO `prescription` (`prescription_id`, `treatment_id`, `medicine_id`, `dosage`, `frequency`, `duration`) VALUES
(1, 1, 2, '500mg', '3 times a day', '5 days'),
(2, 2, 5, '200mg', 'Once a day', '10 days'),
(3, 3, 2, '500mg', '2 times a day', '7 days'),
(4, 4, 3, '10ml', '2 times a day', '3 days'),
(5, 5, 7, '75mg', 'Once a day', '30 days'),
(6, 6, 5, '200mg', 'Once a day', '5 days'),
(7, 7, 8, '10mg', 'Once a day', '7 days'),
(8, 8, 6, '500mg', '2 times a day', '10 days'),
(9, 9, 9, '2 puffs', '2 times a day', '7 days'),
(10, 10, 2, '500mg', '3 times a day', '7 days');

-- --------------------------------------------------------

--
-- Table structure for table `room`
--

CREATE TABLE `room` (
  `room_number` int(11) NOT NULL,
  `room_type` enum('ICU','Emergency','General','Private') NOT NULL,
  `department_id` int(11) NOT NULL,
  `availability` varchar(10) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `room`
--

INSERT INTO `room` (`room_number`, `room_type`, `department_id`, `availability`) VALUES
(101, 'ICU', 1, '0'),
(102, 'ICU', 1, '0'),
(103, 'Emergency', 2, '0'),
(104, 'Emergency', 2, '0'),
(105, 'General', 3, '1'),
(106, 'General', 3, '1'),
(107, 'Private', 4, '1'),
(108, 'Private', 4, '1'),
(109, 'General', 5, '1'),
(110, 'ICU', 6, '0'),
(201, 'General', 7, '1'),
(202, 'Private', 7, '0'),
(203, 'Emergency', 8, '0'),
(204, 'ICU', 9, '0'),
(205, 'Private', 10, '1');

-- --------------------------------------------------------

--
-- Table structure for table `staff`
--

CREATE TABLE `staff` (
  `staff_id` int(11) NOT NULL,
  `first_name` varchar(50) NOT NULL,
  `last_name` varchar(50) NOT NULL,
  `gender` enum('M','F','Other') NOT NULL,
  `dob` date NOT NULL,
  `phone` varchar(15) NOT NULL,
  `email` varchar(100) NOT NULL,
  `address` varchar(255) NOT NULL,
  `category` enum('Nurse','Ward Boy','Cleaner','Technician','Receptionist','Pharmacist','Accountant','Security','Other') NOT NULL,
  `salary` decimal(10,2) NOT NULL,
  `joined_date` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `staff`
--

INSERT INTO `staff` (`staff_id`, `first_name`, `last_name`, `gender`, `dob`, `phone`, `email`, `address`, `category`, `salary`, `joined_date`) VALUES
(1, 'Shila', 'Begum', 'F', '1989-05-12', '01720000111', 'shila.begum@example.com', 'Dhaka, Dhanmondi', 'Nurse', 35000.00, '2007-04-21'),
(2, 'Jamal', 'Khan', 'M', '1985-02-10', '01830000222', 'jamal.khan@example.com', 'Dhaka, Mirpur', 'Ward Boy', 18000.00, '2016-09-02'),
(3, 'Razia', 'Sultana', 'F', '1992-09-25', '01790000333', 'razia.sultana@example.com', 'Chittagong, OR Nizam Rd', 'Nurse', 36000.00, '2016-06-17'),
(4, 'Kamrul', 'Islam', 'M', '1990-11-11', '01911000444', 'kamrul.islam@example.com', 'Sylhet, Ambarkhana', 'Cleaner', 12000.00, '2025-04-13'),
(5, 'Fatema', 'Akter', 'F', '1994-03-05', '01888000555', 'fatema.akter@example.com', 'Dhaka, Uttara', 'Receptionist', 25000.00, '2013-01-13'),
(6, 'Bashir', 'Ahmed', 'M', '1982-08-19', '01955000666', 'bashir.ahmed@example.com', 'Khulna, Khalishpur', 'Security', 17000.00, '2020-04-28'),
(7, 'Rifat', 'Rahman', 'M', '1993-07-13', '01766000777', 'rifat.rahman@example.com', 'Dhaka, Banani', 'Technician', 30000.00, '2017-07-08'),
(8, 'Sharmin', 'Jahan', 'F', '1988-12-04', '01833000888', 'sharmin.jahan@example.com', 'Rajshahi, Shaheb Bazar', 'Nurse', 34500.00, '2019-08-15'),
(9, 'Tarek', 'Hossain', 'M', '1991-01-22', '01755000999', 'tarek.hossain@example.com', 'Barishal Sadar', 'Pharmacist', 28000.00, '2019-07-19'),
(10, 'Mitu', 'Khanam', 'F', '1995-10-18', '01788000123', 'mitu.khanam@example.com', 'Dhaka, Shyamoli', 'Cleaner', 13000.00, '2012-11-15'),
(11, 'Sohag', 'Ali', 'M', '1984-06-02', '01844000234', 'sohag.ali@example.com', 'Gazipur', 'Ward Boy', 17500.00, '2017-09-24'),
(12, 'Nargis', 'Akter', 'F', '1990-04-27', '01966000321', 'nargis.akter@example.com', 'Comilla', 'Accountant', 40000.00, '2024-01-12'),
(13, 'Juthi', 'Rahman', 'F', '1996-02-03', '01811000567', 'juthi.rahman@example.com', 'Dhaka, Mohammadpur', 'Receptionist', 24500.00, '2021-12-20'),
(14, 'Mamun', 'Chowdhury', 'M', '1992-09-09', '01977000678', 'mamun.chowdhury@example.com', 'Sylhet, Zindabazar', 'Technician', 31000.00, '2011-10-03'),
(15, 'Habib', 'Uddin', 'M', '1987-03-14', '01799000456', 'habib.uddin@example.com', 'Mymensingh', 'Security', 16000.00, '2023-11-12');

-- --------------------------------------------------------

--
-- Table structure for table `treatment`
--

CREATE TABLE `treatment` (
  `treatment_id` int(11) NOT NULL,
  `doctor_id` int(11) NOT NULL,
  `admission_id` int(11) NOT NULL,
  `details` varchar(100) DEFAULT NULL,
  `treating_date` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `treatment`
--

INSERT INTO `treatment` (`treatment_id`, `doctor_id`, `admission_id`, `details`, `treating_date`) VALUES
(1, 2, 1, 'Appendectomy surgery', '2025-11-02'),
(2, 4, 2, 'Cast and fracture management', '2025-11-04'),
(3, 5, 3, 'Antibiotic therapy for pneumonia', '2025-11-06'),
(4, 3, 4, 'IV fluids and medication for gastroenteritis', '2025-11-08'),
(5, 1, 5, 'Cardiac monitoring and medication', '2025-11-09'),
(6, 6, 6, 'Migraine management with medication', '2025-11-11'),
(7, 7, 7, 'Antihistamines and allergy treatment', '2025-11-13'),
(8, 8, 8, 'Insulin adjustment for diabetes', '2025-11-15'),
(9, 9, 9, 'Asthma management with inhalers', '2025-11-16'),
(10, 10, 10, 'Kidney infection treatment with antibiotics', '2025-11-18');

-- --------------------------------------------------------

--
-- Table structure for table `ward`
--

CREATE TABLE `ward` (
  `ward_id` int(11) NOT NULL,
  `ward_name` varchar(50) DEFAULT NULL,
  `department_id` int(11) DEFAULT NULL,
  `total_beds` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `ward`
--

INSERT INTO `ward` (`ward_id`, `ward_name`, `department_id`, `total_beds`) VALUES
(1, 'Cardiac Care Unit', 1, 20),
(2, 'Neuro Ward', 2, 18),
(3, 'Orthopedic Ward', 3, 25),
(4, 'Pediatric General Ward', 4, 30),
(5, 'ENT Recovery Ward', 5, 15),
(6, 'Gynecology Ward', 6, 22),
(7, 'Dermatology Observation', 7, 12),
(8, 'Ophthalmology Ward', 8, 16),
(9, 'Psychiatry Unit', 9, 14),
(10, 'General Surgery Ward', 10, 28);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admission`
--
ALTER TABLE `admission`
  ADD PRIMARY KEY (`admission_id`),
  ADD KEY `patient_id` (`patient_id`),
  ADD KEY `room_number` (`room_number`);

--
-- Indexes for table `appointment`
--
ALTER TABLE `appointment`
  ADD PRIMARY KEY (`appointment_id`),
  ADD KEY `patient_id` (`patient_id`),
  ADD KEY `doctor_id` (`doctor_id`);

--
-- Indexes for table `department`
--
ALTER TABLE `department`
  ADD PRIMARY KEY (`department_id`);

--
-- Indexes for table `doctor`
--
ALTER TABLE `doctor`
  ADD PRIMARY KEY (`doctor_id`),
  ADD KEY `department_id` (`department_id`);

--
-- Indexes for table `invoice`
--
ALTER TABLE `invoice`
  ADD PRIMARY KEY (`invoice_id`),
  ADD KEY `admission_id` (`admission_id`),
  ADD KEY `patient_id` (`patient_id`);

--
-- Indexes for table `lab_report`
--
ALTER TABLE `lab_report`
  ADD PRIMARY KEY (`report_id`),
  ADD KEY `patient_id` (`patient_id`),
  ADD KEY `admission_id` (`admission_id`),
  ADD KEY `test_id` (`test_id`);

--
-- Indexes for table `lab_test`
--
ALTER TABLE `lab_test`
  ADD PRIMARY KEY (`test_id`);

--
-- Indexes for table `medicine`
--
ALTER TABLE `medicine`
  ADD PRIMARY KEY (`medicine_id`);

--
-- Indexes for table `patient`
--
ALTER TABLE `patient`
  ADD PRIMARY KEY (`patient_id`),
  ADD UNIQUE KEY `phone` (`phone`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `prescription`
--
ALTER TABLE `prescription`
  ADD PRIMARY KEY (`prescription_id`),
  ADD KEY `treatment_id` (`treatment_id`),
  ADD KEY `medicine_id` (`medicine_id`);

--
-- Indexes for table `room`
--
ALTER TABLE `room`
  ADD PRIMARY KEY (`room_number`),
  ADD KEY `department_id` (`department_id`);

--
-- Indexes for table `staff`
--
ALTER TABLE `staff`
  ADD PRIMARY KEY (`staff_id`);

--
-- Indexes for table `treatment`
--
ALTER TABLE `treatment`
  ADD PRIMARY KEY (`treatment_id`),
  ADD KEY `doctor_id` (`doctor_id`),
  ADD KEY `admission_id` (`admission_id`);

--
-- Indexes for table `ward`
--
ALTER TABLE `ward`
  ADD PRIMARY KEY (`ward_id`),
  ADD KEY `department_id` (`department_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admission`
--
ALTER TABLE `admission`
  MODIFY `admission_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `appointment`
--
ALTER TABLE `appointment`
  MODIFY `appointment_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `department`
--
ALTER TABLE `department`
  MODIFY `department_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `doctor`
--
ALTER TABLE `doctor`
  MODIFY `doctor_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `invoice`
--
ALTER TABLE `invoice`
  MODIFY `invoice_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `lab_report`
--
ALTER TABLE `lab_report`
  MODIFY `report_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `lab_test`
--
ALTER TABLE `lab_test`
  MODIFY `test_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `medicine`
--
ALTER TABLE `medicine`
  MODIFY `medicine_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT for table `patient`
--
ALTER TABLE `patient`
  MODIFY `patient_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=35;

--
-- AUTO_INCREMENT for table `prescription`
--
ALTER TABLE `prescription`
  MODIFY `prescription_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `room`
--
ALTER TABLE `room`
  MODIFY `room_number` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=206;

--
-- AUTO_INCREMENT for table `staff`
--
ALTER TABLE `staff`
  MODIFY `staff_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=42;

--
-- AUTO_INCREMENT for table `treatment`
--
ALTER TABLE `treatment`
  MODIFY `treatment_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `ward`
--
ALTER TABLE `ward`
  MODIFY `ward_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `admission`
--
ALTER TABLE `admission`
  ADD CONSTRAINT `admission_ibfk_1` FOREIGN KEY (`patient_id`) REFERENCES `patient` (`patient_id`),
  ADD CONSTRAINT `admission_ibfk_2` FOREIGN KEY (`room_number`) REFERENCES `room` (`room_number`);

--
-- Constraints for table `appointment`
--
ALTER TABLE `appointment`
  ADD CONSTRAINT `appointment_ibfk_1` FOREIGN KEY (`patient_id`) REFERENCES `patient` (`patient_id`),
  ADD CONSTRAINT `appointment_ibfk_2` FOREIGN KEY (`doctor_id`) REFERENCES `doctor` (`doctor_id`);

--
-- Constraints for table `doctor`
--
ALTER TABLE `doctor`
  ADD CONSTRAINT `doctor_ibfk_1` FOREIGN KEY (`department_id`) REFERENCES `department` (`department_id`);

--
-- Constraints for table `invoice`
--
ALTER TABLE `invoice`
  ADD CONSTRAINT `invoice_ibfk_1` FOREIGN KEY (`admission_id`) REFERENCES `admission` (`admission_id`),
  ADD CONSTRAINT `invoice_ibfk_2` FOREIGN KEY (`patient_id`) REFERENCES `patient` (`patient_id`);

--
-- Constraints for table `lab_report`
--
ALTER TABLE `lab_report`
  ADD CONSTRAINT `lab_report_ibfk_1` FOREIGN KEY (`patient_id`) REFERENCES `patient` (`patient_id`),
  ADD CONSTRAINT `lab_report_ibfk_2` FOREIGN KEY (`admission_id`) REFERENCES `admission` (`admission_id`),
  ADD CONSTRAINT `lab_report_ibfk_3` FOREIGN KEY (`test_id`) REFERENCES `lab_test` (`test_id`);

--
-- Constraints for table `prescription`
--
ALTER TABLE `prescription`
  ADD CONSTRAINT `prescription_ibfk_1` FOREIGN KEY (`treatment_id`) REFERENCES `treatment` (`treatment_id`),
  ADD CONSTRAINT `prescription_ibfk_2` FOREIGN KEY (`medicine_id`) REFERENCES `medicine` (`medicine_id`);

--
-- Constraints for table `room`
--
ALTER TABLE `room`
  ADD CONSTRAINT `room_ibfk_1` FOREIGN KEY (`department_id`) REFERENCES `department` (`department_id`);

--
-- Constraints for table `treatment`
--
ALTER TABLE `treatment`
  ADD CONSTRAINT `treatment_ibfk_1` FOREIGN KEY (`doctor_id`) REFERENCES `doctor` (`doctor_id`),
  ADD CONSTRAINT `treatment_ibfk_2` FOREIGN KEY (`admission_id`) REFERENCES `admission` (`admission_id`);

--
-- Constraints for table `ward`
--
ALTER TABLE `ward`
  ADD CONSTRAINT `ward_ibfk_1` FOREIGN KEY (`department_id`) REFERENCES `department` (`department_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
