Part1:
![alt text](image.png)

Part2:
URL opened in browser: https://redesigned-space-guide-6956qw5vv5px25xxg-8000.app.github.dev/php/request-form.php
Confirmed that request was saved and displayed.
![alt text](image-1.png)

Part3:
Test        	                             What I Did      	             Expected Result	                                        Actual Result           P/F
Submit a valid request                       Submitted valid req             A new tutoring request is created with status Open.        Same as expected        P
View all requests                            Viewed all reqs                 The new request appears in the request list.               Same as expected        P
Filter requests                              Clicked filter button           Filtering by course or status shows matching requests.     Same as expected        P
Edit an Open request                         Edited open req                 The revised values are displayed.                          Same as expected        P
Cancel an Open request                       Cancelled open req              The request status changes to Cancelled.                   Same as expected        P
Try to edit or cancel a cancelled request    Attempted to edit open req      The application does not allow the change.                 Same as expected        P


Part4:
Which file displays the tutoring request form?
Request-Form.php

Which file receives the submitted form data?
process-request.php

Which file opens the PDO connection to SQLite?
db.php

Which file coordinates validation and the database transaction?
requestService.php

Which files contain the SQL statements?
studentPDO.php & requestPDO.php

Which table stores student information?
students

Which table stores tutoring requests?
tutoring_requests

How does a tutoring request connect to the student who submitted it?
student_id as the primary key in tutoring_requests references the student_id as the foreign key in students. 

Part5:
