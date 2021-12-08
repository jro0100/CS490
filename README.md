## CS490 - Online Grading Project:

The goal of this project was to create an autograding website for student's taking exams.

This project is currently being hosted on: https://cs490-project-website.herokuapp.com/

1) Instructor can enter questions (including test cases) into question 
bank.
2) Instructor can select questions from question bank to make exam. This 
is where points are assigned to questions.
3) Student can take exam.
4) Instructor triggers autograding. Then, can review results, tweak 
scores and add comments. The instructor can then release the results.
5) Student can review final results, including comments.

# Setup
* PHP >8.0 Required
* The following environment variables must be set in the environment you are hosting the website in
    * `DB_USERNAME` - Database username for the website to use to connect to the database
    * `DB_PASSWORD` - Password for the given database password
    * `DB_NAME` - Name of the actual website database
    * `DB_HOSTNAME` - Hostname of where the database is hosted. For example, if hosted locally, this would be "localhost"
* Modify Lines 1 and 2 of `CS490-Database-Structure.sql` and replace `heroku_f903c676600be89` with the name you would like to use for your database
* Import `CS490-Database-Structure.sql` into database to create table structure
* For now, teacher and student accounts must be manually created and entered into the database
  * Passwords for all accounts must be entered into the database as hashes returned by the php function `password_hash()` using the `PASSWORD_DEFAULT` algorithm