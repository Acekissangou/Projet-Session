const scanner = new Html5Qrcode("reader");

scanner.start(
    { facingMode: "environment" },
    {
        fps: 10,
        qrbox: 250
    },
    (decodedText) => {
        console.log("QR détecté :", decodedText);

        fetch("qr_scan_trait.php", {
            method: "POST",
            headers: {
                "Content-Type": "application/x-www-form-urlencoded"
            },
            body: "token=" + encodeURIComponent(decodedText)
        })
        .then(res => res.text())
        .then(data => {
            document.getElementById("result").innerHTML = data;
            scanner.stop();
        });
    },
    (error) => {}
);
