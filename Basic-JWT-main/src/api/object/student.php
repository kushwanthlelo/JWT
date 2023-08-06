<?php
    class Student {
        // db
        private $conn;
        private $table = 'student_info';

        // post props
        public $id;
        public $name;
        public $email;
        public $contact;
        public $password;

        // constructor with db
        public function __construct($db){
            $this->conn = $db;
        }

        // get post
        public function read(){
            // create query
            $query = "SELECT s.id, s.name, s.email, s.contact, s.password FROM
            " . $this->table . " AS s ORDER BY s.id DESC";
            
            // prep statement
            $stmt = $this->conn->prepare($query);
            
            // exec statement
            $stmt->execute();

            return $stmt;
        }

        //create query
        public function create(){
            // insert query
            $query = "INSERT INTO
                        " . $this->table . "
                        SET id =:id, name =:name, email =:email,
                        contact =:contact, password=:password";

            // prep query
            $stmt = $this->conn->prepare($query);

            // sanitize
            $this->name=htmlspecialchars(strip_tags($this->name));
            $this->id=htmlspecialchars(strip_tags($this->id));
            $this->email=htmlspecialchars(strip_tags($this->email));
            $this->contact=htmlspecialchars(strip_tags($this->contact));
            $this->password=htmlspecialchars(strip_tags($this->password));
            
            // bind
            $stmt->bindparam(":id",$this->id);
            $stmt->bindparam(":name",$this->name);
            $stmt->bindparam(":email",$this->email);
            $stmt->bindparam(":contact",$this->contact);
            
            // password_hashing
            $password_hash = password_hash($this->password, PASSWORD_BCRYPT);
            $stmt->bindparam(":password",$password_hash);

            // exec
            if($stmt->execute()){
                return true;
            }
            return false;
        }

        public function emailexists(){
            // query
            $query = "SELECT id, name, contact, password 
            FROM " . $this->table . "
             WHERE email = ? 
             LIMIT 0,1";

            //Prep query
            $stmt = $this->conn->prepare($query);

            // sanitize
            $this->email=htmlspecialchars(strip_tags($this->email));
            
            // bind
            $stmt->bindparam(1,$this->email);

            $stmt->execute();
            $num =$stmt->rowCount();

            // if mail exist assign values to obj & use data for session
            if($num>0){
                // get record data
                $row = $stmt->fetch(PDO:: FETCH_ASSOC);

                // assign
                $this->id = $row['id'];
                $this->name = $row['name'];
                $this->contact = $row['contact'];
                $this->password = $row['password'];

                return true;
            }
            return false;

        }

        public function update(){
            // if there is need to update password
            $password_set = !empty($this->password) ? "
            , password = :password" : "";

            // query
            $query = "UPDATE " . $this->table . " 
                    SET 
                        name = :name,
                        contact = :contact,
                        email = :email
                        {$password_set}
                        WHERE id = :id";

            // prep query
            $stmt = $this->conn->prepare($query);

            // sanitize
            $this->name = htmlspecialchars(strip_tags($this->name));
            $this->contact = htmlspecialchars(strip_tags($this->contact));
            $this->email=htmlspecialchars(strip_tags($this->email));

            // bind
            $stmt->bindparam(':name',$this->name);
            $stmt->bindparam(":contact",$this->contact);
            $stmt->bindparam(":email",$this->email);

            // hashing password before binding
            if(!empty($this->password)){
                $this->password =htmlspecialchars(strip_tags($this->password));
                $password_hash = password_hash($this->password, PASSWORD_BCRYPT);
                $stmt->bindparam(":password",$password_hash);
            }
            // editing id from db
            $stmt->bindparam(":id",$this->id);

            // exec
            if($stmt->execute()){
                return true;
            }
            return false;

        }

    }
    ?>