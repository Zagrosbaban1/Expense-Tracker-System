How to run the Daily Expense Tracking System Project
1. Download the zip file.
2. Extract the file and copy the project folder.
3. Paste inside web root directory:
   - XAMPP: xampp/htdocs
   - WAMP: wamp/www
   - LAMP: /var/www/html
4. Open PHPMyAdmin (http://localhost/phpmyadmin).
5. Create a database named detsdb.
6. Import detsdb.sql.
7. Run http://localhost/<project-folder>

Security note:
- Do not use shared/default credentials in production.
- Set a strong database password and update includes/dbconnection.php.
- Restrict database user privileges to this application only.
