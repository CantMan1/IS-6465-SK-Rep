Screenshot of .tables command:
![alt text](image.png)

Screenshot of .schema command:
![alt text](image-1.png)

Which column is the primary key in each table?
Students: student_id
Tutoring_Requests: request_id

Which column is the foreign key and what relationship does it represent?
student_id is the foreign key, and it represents a many-to-one relationship.

student_email is marked "unique" so that the table enforces one email address per one user. 

Insert statements:
![alt text](image-4.png)

Select * output:
![alt text](image-5.png)

Jordan has one student row but two request rows because he submitted two requests. The 'student' table correctly identified him as the same person, but the 'requests' table recorded both of his requests. 

part3:
1. ![alt text](image-2.png)
2. ![alt text](image-3.png)
3. ![alt text](image-6.png)
4. ![alt text](image-7.png)
5. ![alt text](image-8.png)

The JOIN condition connects the matching data between the students and requests tables based on student id. 
The difference between Join and Where is that Where is used to filter records from one data source based on a single condition, whereas Join is used to filter data from multiple tables based on a single condition.
The application should retrieve one request by request id instead of topic because students could enter multiple requests under the same topic. 