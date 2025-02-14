<?php 

function OutputClients($clients) {
    foreach ($clients as $key => $client) {
        $id = $client['id'];
        $name = $client['name'];
        $email = $client['email'];
        $phone = $client['phone'];
        $birthday = $client['birthday'];
        $created_at = $client['created_at'];
        echo "
            <tr>
                <td>$id</td>
                <td>$name</td>
                <td>$email</td>
                <td>$phone</td>
                <td>$birthday</td>
                <td>$created_at</td>
                <td onclick=\"MicroModal.show('history-modal')\"><i class='fa fa-history'></i></td>
                <td onclick=\"MicroModal.show('edit-modal')\"><i class='fa fa-pencil'></i></td>
                <td><a href='api/clients/ClientsDelete.php?id=$id'><i class='fa fa-trash'></i></a></td>
            </tr>";
    }
}

?>