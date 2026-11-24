CREATE DATABASE IF NOT EXISTS emirates_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE emirates_db;

CREATE TABLE IF NOT EXISTS users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  username VARCHAR(50) NOT NULL UNIQUE,
  password VARCHAR(255) NOT NULL
);

INSERT INTO users (username, password) VALUES ('admin', '1234');

CREATE TABLE IF NOT EXISTS students (
  id INT AUTO_INCREMENT PRIMARY KEY,
  student_code VARCHAR(20) NOT NULL UNIQUE,
  full_name VARCHAR(100) NOT NULL,
  national_id VARCHAR(11) NOT NULL,
  department VARCHAR(50) NOT NULL,
  level_name VARCHAR(30) NOT NULL,
  email VARCHAR(100),
  phone VARCHAR(20),
  admission_type ENUM('عام','مباشر') DEFAULT 'عام',
  score DECIMAL(5,2) NULL,
  state_name VARCHAR(50) NULL,
  pay_method VARCHAR(50) NULL,
  status ENUM('قيد المراجعة','مقبول','مرفوض') DEFAULT 'قيد المراجعة',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

INSERT INTO students (student_code, full_name, national_id, department, level_name, email, phone, admission_type, status) VALUES
('EM-2026-001', 'محمد أحمد علي', '12345678901', 'تقنية المعلومات', 'المستوى الأول', 'moh@email.com', '+249912584984', 'عام', 'قيد المراجعة'),
('EM-2026-002', 'سارة خالد محمود', '12345678902', 'إدارة الأعمال', 'المستوى الثاني', 'sara@email.com', '+249912584985', 'مباشر', 'مقبول'),
('EM-2026-003', 'علي يوسف حسن', '12345678903', 'الهندسة', 'المستوى الأول', 'ali@email.com', '+249912584986', 'عام', 'مقبول');

CREATE TABLE IF NOT EXISTS courses (
  id INT AUTO_INCREMENT PRIMARY KEY,
  course_name VARCHAR(100) NOT NULL,
  department VARCHAR(50) NOT NULL,
  hours INT DEFAULT 3
);

INSERT INTO courses (course_name, department, hours) VALUES
('برمجة 1', 'تقنية المعلومات', 3),
('محاسبة', 'إدارة الأعمال', 3);

CREATE TABLE IF NOT EXISTS payments (
  id INT AUTO_INCREMENT PRIMARY KEY,
  student_code VARCHAR(20) NOT NULL,
  amount INT NOT NULL,
  ref_number VARCHAR(50) NOT NULL,
  bank_name VARCHAR(100) DEFAULT 'بنك فيصل الإسلامي - 134014447',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);