<?php

interface UserI{
    public function createUser(array $data):? string;
    public function updateUser(int $id, array $data):bool;
    public function deleteUser(int $id):bool;
    public function getUsers():? array;
    public function searchUsers(string $text):? array;
}


class strategyUser implements UserI{
    private $db;
    public function __construct($host, $username, $password, $database) {
        $this->db = new mysqli($host, $username, $password, $database);
        if($this->db->connect_error) {
            die("connection failed". $this->db->connect_error);
        }
    }

    public function createUser(array $data): ? string {
        try{

            $name  = $this->db->real_escape_string($data['employee_name']);
            $salary = $data['salary'];
            $stmt = $this->db->prepare("INSERT INTO EMPLOYEES (employee_name, salary) VALUES(?, ?)");
            if(!$stmt) {
                throw new Exception("Prepare statement error". $this->db->error);
            }

            $stmt->bind_param('ss' ,$name, $salary);
            $stmt->execute();
            if($stmt->affected_rows === -1 ) {
                throw new Exception("Error excecuting the statements". $stmt->error);
            }
            $stmt->close();
            $data['id'] = $this->db->insert_id;
            return json_encode($data);
        } catch ( Exception $e){
            return null;
        }
    }

    public function updateUser(int $id, array $data): bool {
        try {
            $name = $this->db->real_escape_string($data['employee_name']);
            $salary = $data['salary'];
            $stmt = $this->db->prepare("UPDATE EMPLOYEES SET employee_name = ? , salary = ? WHERE id = ?");
            if(!$stmt) {
                throw new Exception("Error preparing statement". $this->db->error);
            }
            $stmt->bind_param('ssi', $name, $salary, $id);
            $stmt->execute();
            $stmt->close();
            return true;
        } catch (Exception $e) {
            return false;
        }
    }

    public function deleteUser(int $id): bool {
        try {
            $stmt = $this->db->prepare("DELETE FROM EMPLOYEES WHERE id = ?");
            $stmt->bind_param('i', $id);
            if(!$stmt) {
                throw new Exception("Error preparing the statement". $this->db->error);
            }
            $stmt->execute();
            if($stmt->affected_rows === -1) {
                throw new Exception("Error Executing the statement". $this->stmt->error);
            }
            return true;
        } catch(Exception $e) {
            echo  $e->getMessage();
        }
    }

    public function getUsers():? array {
        try {
            $stmt = $this->db->prepare("SELECT id, employee_name, salary FROM EMPLOYEES order by id desc");
            if(!$stmt) {
                throw new Exception("Error preparing the statement". $this->db->error);
            }
            $stmt->execute();
            $result = $stmt->get_result();
            if(!$result) {
                throw new Exception("Error executing statement". $stmt->error);
            }
            $result_array = [];
            while($row = $result->fetch_assoc()) {
                $result_array[] = $row;
            }
            $stmt->close();
            return $result_array ?? [];

        } catch(Exception $e) {
            return null;
        }
    }


    public function searchUsers($text):? array {
        try {
            $query = "%".$text."%";
            $stmt = $this->db->prepare("SELECT id, employee_name, salary FROM EMPLOYEES WHERE emloyee_name like ? or salary like ?  order by id desc");
            $stmt->bind_param('ss', $query, $query);
            if(!$stmt) {
                throw new Exception("Error preparing the statement". $this->db->error);
            }
            $stmt->execute();
            $result = $stmt->get_result();
            if(!$result) {
                throw new Exception("Error executing statement". $stmt->error);
            }
            $result_array = [];
            while($row = $result->fetch_assoc()) {
                $result_array[] = $row;
            }
            $stmt->close();
            return $result_array ?? [];

        } catch(Exception $e) {
            return null;
        }
    }
}


class UserContext {
    private $strategy;

    public function __construct(UserI $strategy) {
        $this->strategy = $strategy;
    }


    public function createRecord(array $data): ? string {
        return $this->strategy->createUser($data);
    }

    public function updateRecord(int $id, array $data):bool {
        return $this->strategy->updateUser($id, $data);
    }

    public function deleteRecord(int $id):bool {
        return $this->strategy->deleteUser($id);
    }

    public function getRecords():?array {
        return $this->strategy->getUsers();
    }

}

function getUserRequest() {
    try {

        $userStrategy = new UserContext(new strategyUser('localhost', 'root', '', 'employee_app'));
        if(isset($_GET['action'])) {

            if($_GET['action']!=='create' && $_GET['action']!=='update' && $_GET['action']!=='delete' && $_GET['action']!=='find' && $_GET['action']!=='all' ) {
                throw new Exception("Invalid request1");
            }

            // if(($_GET['action'] == 'update' || $_GET['action'] == 'create') && $_SERVER['REQUEST_METHOD']!=='POST') {
            //     throw new Exception("Invalid request2");
            // }

            if($_GET['action'] == 'update' || $_GET['action'] == 'create') {
                $data = [
                    'employee_name' => $_POST['employee_name'],
                    'salary' => $_POST['salary']
                ];
            }

            
            $success = json_encode(['success' => true]);
            switch($_GET['action']) {
                case  'create':
                    echo $userStrategy->createRecord($data);
                    break;
                
                case 'update':
                    $userStrategy->updateRecord($_POST['id'], $data);
                break;
                case 'delete':
                    echo $userStrategy->deleteRecord($_GET['id']) ? $success : false;
                break;
                
                default:
                   echo  $results = json_encode(['data' => $userStrategy->getRecords()]);
                    //echo "<pre>", print_r( $results), "</pre>";
                break;
            }

        }

    } catch (Exception $e) {
        echo $e->getMessage();
    }
}

getUserRequest();