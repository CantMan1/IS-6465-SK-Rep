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