<?php

$conn = new mysqli();
$conn->connect("localhost", "azfcms", "azfcms", "azfcms");



for($i=0;$i<100;$i++){
$conn->query(<<<EOF
insert into NewsCategory (title,description,userId)
    VALUES ('My Title$i','My Description $i',0);
EOF
);
}