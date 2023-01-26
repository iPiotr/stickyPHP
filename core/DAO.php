<?php
/* It's a class that handles all the database interactions */
class DAO
{
    private $pdo;
    private $servername;
    private $username;
    private $password;

    /**
     * It creates a new PDO object and stores it in the  property of the class.
     */
    function __construct()
    {
        $servername = "localhost";
        $username = "root";
        $password = "";
        $this->pdo = new PDO('mysql:dbname=sticky;host=' . $servername, $username, $password);
    }

    /**
     * It checks if the username is already taken
     * 
     */
    public function isUsernameFree($pUsername)
    {
        require_once('Util.php');
        if (IsNullOrEmptyString($pUsername))
            return false;

        $query = 'SELECT username FROM user WHERE username = ?';
        try {
            $stmt = $this->pdo->prepare($query);

            $stmt->bindParam(1, $pUsername);

            if ($stmt->execute()) {
                if ($stmt->fetch()) {
                    return false;
                } else {
                    return true;
                }
            } else
                throw new PDOException('SELECT FAILED');

        } catch (PDOException $e) {
            //Comment this out later
            echo $e->getMessage();
        }
    }

    /**
     * It takes a username and password, hashes the password, and inserts the username and hashed
     * password into the database
     */
    public function insertUser($pUsername, $pPassword)
    {
        require_once('Util.php');
        if (IsNullOrEmptyString($pUsername) || IsNullOrEmptyString($pPassword))
            throw new InvalidArgumentException('Parameters not proper strings');

        $query = 'INSERT INTO user (username, password) VALUES (?,?)';
        try {
            $stmt = $this->pdo->prepare($query);
            $hashedPassword = getHashedString($pPassword);

            $stmt->bindParam(1, $pUsername);
            $stmt->bindParam(2, $hashedPassword);

            if (!($stmt->execute()))
                throw new PDOException('INSERT FAILED');

            return $this->pdo->lastInsertId();

        } catch (PDOException $e) {
            //Comment this out later
            echo $e->getMessage();
        }
    }

    /**
     * It checks if the username and password are valid, if they are, it returns the user object, if
     * not, it returns the number of attempts left
     * 
     */
    public function isValidUser($pUsername, $pPassword)
    {
        $totalAttempts = 5;

        require_once('Util.php');
        if (IsNullOrEmptyString($pUsername) || IsNullOrEmptyString($pPassword))
            throw new InvalidArgumentException('Parameters not proper strings');

        $query = 'SELECT id, username, password, attempts FROM user WHERE username = ?';
        try {
            $stmt = $this->pdo->prepare($query);

            $stmt->bindParam(1, $pUsername);

            if ($stmt->execute()) {
                if ($row = $stmt->fetch()) {
                    //do nothing
                } else //username doesn't exist
                    return $totalAttempts;
            } else
                throw new PDOException('SELECT FAILED');

            $id = $row[0];
            $username = $row[1];
            $password = $row[2];
            $attempts = $row[3];

            if ($attempts > $totalAttempts) //if locked out, return 0
                return 0;

            //If the hashed passwords are the same set attempts to 0 and return the user object
            if (password_verify($pPassword, $password)) {

                $query = 'UPDATE user SET attempts = 0 WHERE id = ?';
                try {
                    $stmt = $this->pdo->prepare($query);

                    $stmt->bindParam(1, $id);

                    if (!($stmt->execute()))
                        throw new PDOException('UPDATE FAILED');
                } catch (PDOException $e) {
                }

                $user = new User($id, $username, $password);
                return $user;
            }

            //Wrong password, increment attempts in db
            $query = 'UPDATE user SET attempts = attempts + 1 WHERE id = ?';
            try {
                $stmt = $this->pdo->prepare($query);

                $stmt->bindParam(1, $id);

                if (!($stmt->execute()))
                    throw new PDOException('UPDATE FAILED');
            } catch (PDOException $e) {
            }

            return $totalAttempts - $attempts;

        } catch (PDOException $e) {
            //Comment this out later
            echo $e->getMessage();
        }
    }

    /**
     * It takes a note object and a user id and inserts the note into the database
     */
    public function insertNote($pNote, $pUserId)
    {
        require_once('Util.php');
        if (!($pNote instanceof Note))
            throw new InvalidArgumentException('Parameter not of type Note');

        $query = 'INSERT INTO note (user_id, text, x, y) VALUES (?,?,?,?)';
        try {
            $stmt = $this->pdo->prepare($query);

            $stmt->bindParam(1, $pUserId);
            $stmt->bindValue(2, $pNote->getText());
            $stmt->bindValue(3, $pNote->getX());
            $stmt->bindValue(4, $pNote->getY());

            if (!($stmt->execute()))
                throw new PDOException('INSERT FAILED');

            return $this->pdo->lastInsertId();

        } catch (PDOException $e) {
            //Comment this out later
            echo $e->getMessage();
        }
    }

    /**
     * It updates the x and y coordinates of a note in the database
     */
    public function updateNote($pNote)
    {
        require_once('Util.php');
        if (!($pNote instanceof Note))
            throw new InvalidArgumentException('Parameter not of type Note');

        $query = 'UPDATE note SET x = ?, y = ? WHERE id = ?';
        try {
            $stmt = $this->pdo->prepare($query);

            $stmt->bindValue(1, $pNote->getX());
            $stmt->bindValue(2, $pNote->getY());
            $stmt->bindValue(3, $pNote->getId());

            if (!($stmt->execute()))
                throw new PDOException('UPDATE FAILED');

        } catch (PDOException $e) {
            //Comment this out later
            echo $e->getMessage();
        }
    }

    /**
     * It gets all the notes from the database and returns them as a JSON string
     */
    public function getAllNotes($pUserId)
    {
        require_once('Note.php');
        $query = 'SELECT id, text, x, y FROM note WHERE user_id = ?';
        try {
            $stmt = $this->pdo->prepare($query);
            $stmt->bindParam(1, $pUserId);
            $stmt->setFetchMode(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE, 'Note');

            if ($stmt->execute()) {
                $notes = $stmt->fetchAll();
            } else
                throw new PDOException('SELECT FAILED');
            return json_encode($notes);

        } catch (PDOException $e) {
            //Comment this out later
            echo $e->getMessage();
        }
    }

    /**
     * It deletes a note from the database
     */
    public function deleteNote($id)
    {
        require_once('Util.php');
        if ($id < 1)
            throw new InvalidArgumentException('Bad id');

        $query = 'DELETE from note WHERE id = ?';
        try {
            $stmt = $this->pdo->prepare($query);

            $stmt->bindParam(1, $id);

            if (!($stmt->execute()))
                throw new PDOException('DELETE FAILED');

        } catch (PDOException $e) {
            //Comment this out later
            echo $e->getMessage();
        }
    }
}