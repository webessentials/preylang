<html>
<body style="
        font-family: 'Open Sans', sans-serif;
        color: rgb(79, 95, 111);
        background-color: rgb(240, 243, 246);
    ">

    <div style="
            background-color: white;
            max-width: 60%;
            margin: 70px auto;
            padding: 30px;
            text-align: center;
        ">
        <img src="{{ $message->embed(public_path('/images/logo/logo.png')) }}" alt="Logo">

        @yield('message')
    </div>
</body>
</html>
