<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Otentikasi Berhasil</title>
    <script>
        if (window.opener && !window.opener.closed) {
            window.opener.location.reload();
            window.close();
        } else {
            window.location.href = '/';
        }
    </script>
</head>
<body>
    <p style="font-family: sans-serif; text-align: center; margin-top: 50px; color: #64748b;">
        Otentikasi berhasil, mengalihkan...
    </p>
</body>
</html>
