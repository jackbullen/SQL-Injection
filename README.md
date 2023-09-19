# SQL Injection Demonstration

A simple PHP login/register application to demonstrate SQL injection vulnerabilities.

## Setup

1. Clone the repository
2. Setup MySQL user demouser
3. Setup MySQL database demo_db with password demopassword
4. Run the SQL scripts in the root of the repository to create the users table and insert some test data.

## Usage

1. Run the PHP application using the PHP built-in web server: `php -S localhost:8000`
2. Navigate to http://localhost:8000 in your browser
3. Login with the email `' UNION SELECT id, name, email, password, sin FROM users -- -`
4. You should now be logged in as the first user in the database, John Doe, and you should see their profile page.

## Explanation

The login form is vulnerable to SQL injection because it does not sanitize the input from the email field. 

The SQL query that is executed as a result of a normal login form submission is:

```sql
SELECT * FROM users WHERE email = '$email' AND password = '$password'
```

with `email` and `password` coming from the form.

The SQL query that is executed as a result of the login form submission is:

```sql
SELECT * FROM users WHERE email = '$email' UNION SELECT id, name, email, password, sin FROM users -- -' AND password = '$password'
```

Since the password is no longer a part of the query, the query will return all users in the database.

Then when the below PHP code is executed, fetch_assoc() grabs the first row, and logs in as that user.

```php
if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    header("Location: profile.php?id=" . $row['id']);
    exit;
} else {
    echo "Login failed!";
}
```

## Prevention

The best way to prevent SQL injection is to use prepared statements. Prepared statements are a feature of the database that allow you to specify the query and the parameters separately. The database will then ensure that the parameters are properly escaped before executing the query.
 
Use prepared statements in PHP:

```php
$stmt = $pdo->prepare("SELECT * FROM users WHERE email=? AND password=?");
$result->execute([$email, $password]);
```

## Reality

This is not typically how these attacks take place. 

First of all the attacker is assumed to know information about the database like the table name and it's column names. Usually the attacker will try to get this information by using similar emails to the one provided above.

For example if the site had a search functionality that echoed data back to the user and the attacker knew there were 5 columns in the actual queried table, they could use:

```sql
' UNION SELECT table_name, NULL, NULL, NULL, NULL FROM information_schema.tables WHERE table_schema = DATABASE() -- 
```

Resulting in the query:

```sql
SELECT * FROM users WHERE email='$email' UNION SELECT table_name, NULL, NULL, NULL, NULL FROM information_schema.tables WHERE table_schema = DATABASE() -- -' AND password='$password'

```

If the site echoed back the table name: users, they could then search for:

```sql
' UNION SELECT column_name, NULL, NULL, NULL, NULL FROM information_schema.columns WHERE table_name = 'users' -- -
```

Resulting in the query:

```sql
SELECT * FROM users WHERE email='$email' UNION SELECT column_name, NULL, NULL, NULL, NULL FROM information_schema.columns WHERE table_name = 'users' -- -' AND password='$password'
```

and now they would have the column names: id, name, email, password, sin.

If the search was poorly designed and echoed back the data from the table, they could then search for the original query and get the profile data for all users. Hence why it is important to not only prevent SQL injection, but also to store sensitive data in as hashes and not plain text.

