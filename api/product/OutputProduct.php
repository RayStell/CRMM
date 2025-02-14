<?php 

function OutputProducts($products) {
    foreach ($products as $key => $product) {
        $id = $product['id'];
        $name = $product['name'];
        $description = $product['description'];
        $price = $product['price'];
        $stock = $product['stock'];
        echo "
            <tr>
                <td>$id</td>
                <td>$name</td>
                <td>$description</td>
                <td>$price</td>
                <td>$stock</td>
                <td onclick=\"MicroModal.show('qr-modal')\"><i class='fa fa-qrcode'></i></td>
                <td onclick=\"MicroModal.show('edit-modal')\"><i class='fa fa-pencil'></i></td>
                <td><a href='api/product/ProductDelete.php?id=$id'><i class='fa fa-trash'></i></a></td>
            </tr>";
    }
}

?>