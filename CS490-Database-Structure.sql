CREATE DATABASE  IF NOT EXISTS `heroku_f903c676600be89` /*!40100 DEFAULT CHARACTER SET utf8 */;
USE `heroku_f903c676600be89`;
-- MySQL dump 10.13  Distrib 8.0.26, for Win64 (x86_64)
--
-- Host: us-cdbr-east-04.cleardb.com    Database: heroku_f903c676600be89
-- ------------------------------------------------------
-- Server version	5.6.50-log

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `class`
--

DROP TABLE IF EXISTS `class`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `class` (
  `classID` int(11) NOT NULL AUTO_INCREMENT,
  `teacherID` int(11) NOT NULL,
  `className` varchar(50) NOT NULL,
  PRIMARY KEY (`classID`),
  KEY `fk_teacher` (`teacherID`),
  CONSTRAINT `fk_teacher` FOREIGN KEY (`teacherID`) REFERENCES `teacher` (`teacherID`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `classenrollment`
--

DROP TABLE IF EXISTS `classenrollment`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `classenrollment` (
  `studentID` int(11) NOT NULL,
  `classID` int(11) NOT NULL,
  PRIMARY KEY (`studentID`,`classID`),
  KEY `fk_classEnrollment_class` (`classID`),
  CONSTRAINT `fk_classEnrollment_class` FOREIGN KEY (`classID`) REFERENCES `class` (`classID`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_classEnrollment_student` FOREIGN KEY (`studentID`) REFERENCES `student` (`studentID`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `exams`
--

DROP TABLE IF EXISTS `exams`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `exams` (
  `examID` int(11) NOT NULL AUTO_INCREMENT,
  `teacherID` int(11) NOT NULL,
  `examName` varchar(50) NOT NULL,
  `released` tinyint(1) NOT NULL,
  `gradedByTeacher` tinyint(1) NOT NULL,
  PRIMARY KEY (`examID`),
  KEY `fk_teacher_exams` (`teacherID`),
  CONSTRAINT `fk_teacher_exams` FOREIGN KEY (`teacherID`) REFERENCES `teacher` (`teacherID`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=335 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `login`
--

DROP TABLE IF EXISTS `login`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `login` (
  `username` varchar(20) NOT NULL,
  `pwd` varchar(255) NOT NULL,
  `isAdmin` tinyint(1) NOT NULL,
  `isTeacher` tinyint(1) NOT NULL,
  `isStudent` tinyint(1) NOT NULL,
  PRIMARY KEY (`username`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `parameters`
--

DROP TABLE IF EXISTS `parameters`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `parameters` (
  `parameterID` int(11) NOT NULL AUTO_INCREMENT,
  `testCaseID` int(11) NOT NULL,
  `parameter` varchar(20) NOT NULL,
  PRIMARY KEY (`parameterID`),
  KEY `fk_testCases_parameters` (`testCaseID`),
  CONSTRAINT `fk_testCases_parameters` FOREIGN KEY (`testCaseID`) REFERENCES `testcases` (`testCaseID`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=1315 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `questionbank`
--

DROP TABLE IF EXISTS `questionbank`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `questionbank` (
  `questionID` int(11) NOT NULL AUTO_INCREMENT,
  `teacherID` int(11) NOT NULL,
  `question` varchar(500) NOT NULL,
  `questionType` enum('forLoop','whileLoop','recursion','strings','conditionals','general') NOT NULL,
  `questionConstraint` enum('forLoop','whileLoop','recursion','none') NOT NULL,
  `difficulty` int(11) NOT NULL,
  `parameterCount` int(11) NOT NULL,
  `functionToCall` varchar(30) NOT NULL,
  PRIMARY KEY (`questionID`),
  KEY `fk_teacher_questionBank` (`teacherID`),
  CONSTRAINT `fk_teacher_questionBank` FOREIGN KEY (`teacherID`) REFERENCES `teacher` (`teacherID`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=325 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `questiongrade`
--

DROP TABLE IF EXISTS `questiongrade`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `questiongrade` (
  `gradeID` int(11) NOT NULL AUTO_INCREMENT,
  `studentID` int(11) NOT NULL,
  `examID` int(11) NOT NULL,
  `questionID` int(11) NOT NULL,
  `studentAnswer` varchar(1000) DEFAULT NULL,
  `teacherComment` varchar(1000) DEFAULT NULL,
  `achievedScore` int(11) NOT NULL,
  PRIMARY KEY (`gradeID`),
  KEY `fk_student_questionGrade` (`studentID`),
  KEY `fk_exams_questionGrade` (`examID`),
  KEY `fk_questionBank_questionGrade` (`questionID`),
  CONSTRAINT `fk_exams_questionGrade` FOREIGN KEY (`examID`) REFERENCES `exams` (`examID`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_questionBank_questionGrade` FOREIGN KEY (`questionID`) REFERENCES `questionbank` (`questionID`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_student_questionGrade` FOREIGN KEY (`studentID`) REFERENCES `student` (`studentID`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=1925 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `questionsonexam`
--

DROP TABLE IF EXISTS `questionsonexam`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `questionsonexam` (
  `questionID` int(11) NOT NULL,
  `examID` int(11) NOT NULL,
  `maxPoints` int(11) NOT NULL,
  PRIMARY KEY (`questionID`,`examID`),
  KEY `fk_exams_questionsOnExam` (`examID`),
  CONSTRAINT `fk_exams_questionsOnExam` FOREIGN KEY (`examID`) REFERENCES `exams` (`examID`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_quesitonBank_questionsOnExam` FOREIGN KEY (`questionID`) REFERENCES `questionbank` (`questionID`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `student`
--

DROP TABLE IF EXISTS `student`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `student` (
  `studentID` int(11) NOT NULL AUTO_INCREMENT,
  `teacherID` int(11) NOT NULL,
  `studentName` varchar(35) NOT NULL,
  `username` varchar(20) DEFAULT NULL,
  PRIMARY KEY (`studentID`),
  KEY `fk_login_student` (`username`),
  KEY `fk_teacher_student` (`teacherID`),
  CONSTRAINT `fk_login_student` FOREIGN KEY (`username`) REFERENCES `login` (`username`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_teacher_student` FOREIGN KEY (`teacherID`) REFERENCES `teacher` (`teacherID`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=35 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `studentexam`
--

DROP TABLE IF EXISTS `studentexam`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `studentexam` (
  `studentID` int(11) NOT NULL,
  `examID` int(11) NOT NULL,
  `completedByStudent` tinyint(1) NOT NULL,
  `studentGrade` float DEFAULT NULL,
  `teacherComment` varchar(500) DEFAULT NULL,
  PRIMARY KEY (`studentID`,`examID`),
  KEY `fk_exam_studentExam` (`examID`),
  CONSTRAINT `fk_exam_studentExam` FOREIGN KEY (`examID`) REFERENCES `exams` (`examID`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_student_studentExam` FOREIGN KEY (`studentID`) REFERENCES `student` (`studentID`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `studenttestcases`
--

DROP TABLE IF EXISTS `studenttestcases`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `studenttestcases` (
  `studentTestCaseID` int(11) NOT NULL AUTO_INCREMENT,
  `testCaseID` int(11) NOT NULL,
  `studentID` int(11) NOT NULL,
  `examID` int(11) NOT NULL,
  `maxPoints` int(11) NOT NULL,
  `autoGradeScore` int(11) NOT NULL,
  `teacherScore` int(11) NOT NULL,
  `studentOutput` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`studentTestCaseID`),
  KEY `fk_testCases_studentTestCases` (`testCaseID`),
  KEY `fk_student_studentTestCases` (`studentID`),
  KEY `fk_exams_studentTestCases` (`examID`),
  CONSTRAINT `fk_exams_studentTestCases` FOREIGN KEY (`examID`) REFERENCES `exams` (`examID`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_student_studentTestCases` FOREIGN KEY (`studentID`) REFERENCES `student` (`studentID`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_testCases_studentTestCases` FOREIGN KEY (`testCaseID`) REFERENCES `testcases` (`testCaseID`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=3595 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `teacher`
--

DROP TABLE IF EXISTS `teacher`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `teacher` (
  `teacherID` int(11) NOT NULL AUTO_INCREMENT,
  `teacherName` varchar(35) NOT NULL,
  `username` varchar(20) DEFAULT NULL,
  PRIMARY KEY (`teacherID`),
  KEY `fk_login_teacher` (`username`),
  CONSTRAINT `fk_login_teacher` FOREIGN KEY (`username`) REFERENCES `login` (`username`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `testcases`
--

DROP TABLE IF EXISTS `testcases`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `testcases` (
  `testCaseID` int(11) NOT NULL AUTO_INCREMENT,
  `questionID` int(11) NOT NULL,
  `answer` varchar(200) NOT NULL,
  PRIMARY KEY (`testCaseID`),
  KEY `fk_questionBank_testCases` (`questionID`),
  CONSTRAINT `fk_questionBank_testCases` FOREIGN KEY (`questionID`) REFERENCES `questionbank` (`questionID`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=1025 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2021-12-07 18:38:52
