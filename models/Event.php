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
            $this->ticket = serialize($this->ticket);
    
            
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
}