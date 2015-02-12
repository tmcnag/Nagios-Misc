<?php

$oldpass = $argv[1];
$newpass = $argv[2];

try {
        $conn = new PDO('mysql:host=localhost;dbname=nagiosql', 'root', 'nagiosxi');
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $stmt = $conn->prepare("SELECT id,service_description,check_command FROM tbl_service WHERE check_command LIKE \"%$oldpass%\"");
        $stmt->execute();

        $result = $stmt->fetchAll();

        if ( count($result) ) {
                foreach ($result as $row) {
                        $newargs = str_replace($oldpass, $newpass, $row['check_command']);
                        echo "Service name:     " . $row['service_description'] . "\n";
                        echo "Command args:     " . $row['check_command']       . "\n";
                        echo "NEW Command args: " . $newargs                    . "\n\n";
                        $stmt = $conn->prepare("UPDATE tbl_service SET check_command=\"$newargs\" WHERE id=:id");
                        $stmt->execute(array('id' => $row['id']));
                }
        } else {
                echo "No rows returned.\n";
        }

} catch (PDOException $e) {
        echo 'ERROR: ' . $e->getMessage();
}

?>
