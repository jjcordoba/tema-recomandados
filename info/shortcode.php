<!DOCTYPE html>
<html>
<head>
    <style>
        table {
            border-collapse: collapse;
            width: 100%;
        }

        th, td {
            border: 1px solid #dddddd;
            text-align: left;
            padding: 8px;
            font-size: 17px;
        }

        tr:nth-child(even) {
            background-color: #f2f2f2;
        }

        h2 {
            text-align: center;
            font-size: 23px;
        }

        p {
            text-align: center;
            font-size: 20px;
        }

        .shortcode-cell:hover {
            background-color: #eaf1ff;
            cursor: pointer;
        }
    </style>
</head>
<body>
    <h2>Shortcodes a usar</h2>
    <p>Haga clic en el shortcode que desea usar y luego péguelo donde lo desea usar.</p>

    <table id="shortcode-table">
        <tr>
            <th>Shortcode</th>
            <th>Descripción</th>
        </tr>
        <tr>
            <td data-shortcode="[productos_oferta ids='1,2,3']" class="shortcode-cell">[productos_oferta ids="1,2,3"]</td>
            <td>Muestra productos en oferta con los IDs especificados.</td>
        </tr>
        <tr>
            <td data-shortcode="[productos_oferta categories='ropa,zapatos']" class="shortcode-cell">[productos_oferta categories="ropa,zapatos"]</td>
            <td>Muestra productos en oferta de las categorías especificadas.</td>
        </tr>
        <tr>
            <td data-shortcode="[productos_oferta limit='40']" class="shortcode-cell">[productos_oferta limit="40"]</td>
            <td>Muestra productos en oferta con un límite de 40.</td>
        </tr>
        <tr>
            <td data-shortcode="[productos_oferta ids='1,2,3' limit='40']" class="shortcode-cell">[productos_oferta ids="1,2,3" limit="40"]</td>
            <td>Muestra productos en oferta con los IDs especificados y un límite de 40.</td>
        </tr>
        <tr>
            <td data-shortcode="[productos_oferta categories='ropa,zapatos' limit='40']" class="shortcode-cell">[productos_oferta categories="ropa,zapatos" limit="40"]</td>
            <td>Muestra productos en oferta de las categorías especificadas y un límite de 40.</td>
        </tr>
    </table>

    <script>
        const shortcodeCells = document.getElementsByClassName('shortcode-cell');
        for (let i = 0; i < shortcodeCells.length; i++) {
            shortcodeCells[i].addEventListener('click', function() {
                const shortcode = this.dataset.shortcode;
                copiarShortcode(shortcode);
            });
        }

        function copiarShortcode(shortcode) {
            const textarea = document.createElement('textarea');
            textarea.value = shortcode;
            document.body.appendChild(textarea);
            textarea.select();
            document.execCommand('copy');
            document.body.removeChild(textarea);
            alert('Shortcode copiado al portapapeles: ' + shortcode);
        }
    </script>
</body>
</html>
