<?php
require_once './lib/phpqrcode/qrlib.php';

$data = "Edgard";

QRcode::png($data);
