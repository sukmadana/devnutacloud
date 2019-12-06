<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Invoice</title>
    <link href="//cdnjs.cloudflare.com/ajax/libs/meyer-reset/2.0/reset.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=Open+Sans:600&display=swap" rel="stylesheet">
    <link href="<?=base_url('css/invoice.css');?>" rel="stylesheet">
</head>
<body>
    <div class="container">
        <div class="text-right">
            <button class="btn" id="print">Print</button>
        </div>

        <div class="head">
            <img src="<?= base_url('images/logo-nuta.png');?>">
            <p>Invoice No. #123456</p>
        </div>

        <div class="subject">
            <p>Kepada</p>
            <p>
                Ruben Onsu<br>
                Geprek Bensu<br>
                Jl. Pahlawan No. 2 Jakarta Pusat<br>
                08124567896
            </p>
        </div>
        <main class="main">
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th class="text-left">Nama Jasa</th>
                            <th class="text-left">Priode Tagihan</th>
                            <th class="text-left">Jenis Langganan</th>
                            <th class="text-left">Jumlah</th>
                            <th class="text-right">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>Langganan Bulanan</td>
                            <td>1 Agustus 2019 - 30 Agustus 2019</td>
                            <td>Bulanan</td>
                            <td>1</td>
                            <td class="text-right">Rp. 250.000</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </main>

        <footer class="footer">
            <p>Bila ada pertanyaan terkait Invoice ini silahkan tanyakan ke :</p>
            <table class="table-footer">
                <tbody>
                    <tr>
                        <td width="150px">Customer Service</td>
                        <td>0813-9023-8796</td>
                    </tr>
                    <tr>
                        <td>Website</td>
                        <td><a href="https://www.nutapos.com" target="_blank">https://www.nutapos.com/</a></td>
                    </tr>
                    <tr>
                        <td>Alamat</td>
                        <td>Jogja Digital Valley, Jl. Kartini No. 7 Sagan Yogyakarta</td>
                    </tr>
                </tbody>
            </table>
        </footer>
    </div>

    <script>
        document.getElementById("print").addEventListener("click", function(){
            window.print();
        })
    </script>
</body>
</html>