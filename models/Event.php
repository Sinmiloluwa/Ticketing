<?php

class Event
{
    private $conn;
    private $table = 'events';

    // Event properties
    public $id;
    public $name;
    public $venue;
    public $date;
    public $ticket;
    public $ticketName;
    public $created_at;
    public $category_ame;

    // constructor
    public function __construct($db)
    {
        $this->conn = $db;
    }

    // Get Events
    public function read()
    {
        $sql = "SELECT 
        *
        FROM
        events
        ORDER BY events.created_at DESC";

        // prepare statements
        $stmt = $this->conn->prepare($sql);

        // Execute the statement
        $stmt->execute();

        $num = $stmt->rowCount();

        // Check for events
        if ($num > 0) {
            $events = array();
            $events['data'] = array();
        
            while($row = $stmt->fetch(PDO::FETCH_ASSOC))
            {   
                extract($row);
                $tickets = unserialize($tickets);
        
                $event_list = [
                    'id' => $id,
                    'ticket' => $tickets,
                    'name' => $name,
                    'venue' => $venue,
                    'price' => $price,
                    'date' => $date,
                ];
        
                // push to data
                array_push($events['data'], $event_list);
            }
        
            echo json_encode($events);
        } else {
            // No events
            echo json_encode(['message' => 'No events found']);
        }
    }

    // Get single Event
    public function show()
    {
        $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $uri = explode( '/', $uri );
        // Get Id
        $this->id = (isset($uri[2])) ? $uri[2] : die();


        $sql = "SELECT * FROM events WHERE id = :id LIMIT 0,1";
        $stmt = $this->conn->prepare($sql);

        // Bind id
        $stmt->bindParam(':id', $this->id);

        $stmt->execute();

        // Get row count
        $num = $stmt->rowCount();

        // Check for events
        if ($num > 0) {
            $events = array();
            $events['data'] = array();

            while($row = $stmt->fetch(PDO::FETCH_ASSOC))
            {   
                extract($row);
                $tickets = unserialize($tickets);

                $event_list = [
                    'id' => $id,
                    'ticket' => $tickets,
                    'name' => $name,
                    'venue' => $venue,
                    'price' => $price,
                    'date' => $date,
                ];

                // push to data
                array_push($events['data'], $event_list);
            }

            echo json_encode($events);
        } else {
            // No events
            echo json_encode(['message' => 'No events found']);
        }
    }

    public function create()
    {
            $data = json_decode(file_get_contents("php://input"));

            $this->name = $data->name;
            $this->venue = $data->venue;
            $this->date = $data->date;
            $this->ticket = $data->ticket;

            $sql = 'INSERT INTO events (name, date, tickets, venue)  VALUES (:name, :date, :ticket, :venue)';
            $stmt = $this->conn->prepare($sql);
            
            // clean post data
            $this->name = htmlspecialchars(strip_tags($this->name));
            $this->venue = htmlspecialchars(strip_tags($this->venue));
            $this->date = htmlspecialchars(strip_tags($this->date));
            $this->ticket = json_encode($this->ticket);
    
            
            // Bind data
            $stmt->bindParam(':name', $this->name);
            $stmt->bindParam(':venue', $this->venue);
            $stmt->bindParam(':date', $this->date);
            $stmt->bindParam(':ticket', $this->ticket);

            // Execute query
            if ($stmt->execute()) {
                echo json_encode(["message" => 'Event Created']);
            }
            else {
                echo json_encode(["message" => "Could not create Event"]);
            }
    }

    public function pay()
    {
        $data = json_decode(file_get_contents("php://input"));

            $this->amount = $data->amount;
            $this->theTicket = $data->theTicket;
        $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $uri = explode( '/', $uri );
        // Get Id
        $this->id = (isset($uri[2])) ? $uri[2] : die();
        $sql = "SELECT * FROM events WHERE id = :id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':id', $this->id);
        $stmt->execute();
        while ($row = $stmt->fetchAll(PDO::FETCH_ASSOC)) {
           $eventName = $row[0]['name'];
           $tickets = $row[0]['tickets'];
           $tick = json_decode($tickets, true);
           $regular = $tick[0]['name'];
           $vip = $tick[1]['name'];
           $vvip = $tick[2]['name'];
           $regularPrice = $tick[0]['price'];
           $vipPrice = $tick[1]['price'];
           $vvipPrice = $tick[2]['price'];
           
        }

        if ($stmt->rowCount() > 0) {
            session_start();
            if (isset($_SESSION['user_id']) == true) {
                $sql = "SELECT email FROM users WHERE id =:id";
                $stmt = $this->conn->prepare($sql);
                $stmt->bindParam(':id', $_SESSION['user_id']);
                $stmt->execute();
                while($row = $stmt->fetch(PDO::FETCH_ASSOC))
                {
                    $emailUser = $row['email'];
                }
                $curl = curl_init();

                $email = $emailUser;
                $amount = $this->amount;  //the amount in kobo. This value is actually NGN 300

                // url to go to after payment
                $callback_url = 'localhost/pay/callback.php';  

                curl_setopt_array($curl, array(
                CURLOPT_URL => "https://api.paystack.co/transaction/initialize",
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_CUSTOMREQUEST => "POST",
                CURLOPT_POSTFIELDS => json_encode([
                    'amount'=>$amount,
                    'email'=>$email,
                    'callback_url' => $callback_url
                ]),
                CURLOPT_HTTPHEADER => [
                    "authorization: Bearer sk_test_1501b0094dd6f4c67a173de67250289e417ad3c7", //replace this with your own test key
                    "content-type: application/json",
                    "cache-control: no-cache"
                ],
                ));

                $response = curl_exec($curl);
                $err = curl_error($curl);

                if($err){
                // there was an error contacting the Paystack API
                die('Curl returned error: ' . $err);
                }

                $tranx = json_decode($response, true);
                $sql = "INSERT INTO payments (ticket, email, price, event) VALUES(:ticket, :email, :price, :event)";
                $stmt = $this->conn->prepare($sql);
                $stmt->bindParam(':ticket', $this->theTicket);
                $stmt->bindParam(':email', $emailUser);
                $stmt->bindParam(':price', $this->amount);
                $stmt->bindParam(':event', $eventName);
                if($stmt->execute()) {
                    echo json_encode(["message"=>"Successfully Updated"]);
                }

                if(!$tranx['status']){
                // there was an error from the API
                print_r('API returned error: ' . $tranx['message']);
                }

                // // comment out this line if you want to redirect the user to the payment page
                print_r($tranx);
                // redirect to page so User can pay
                // uncomment this line to allow the user redirect to the payment page
                header('Location: ' . $tranx['data']['authorization_url']);

            }
        }
    }
}