<!DOCTYPE html>
<html>

<head>
    <style>
        table {
            position: relative;
            width: 700px;
            background-color: #aaa;
            overflow: hidden;
            border-collapse: collapse;
        }

        /*thead*/
        thead {
            position: relative;
            display: block;
            /*seperates the header from the body allowing it to be positioned*/
            width: 700px;
            overflow: visible;
        }

        thead th {
            background-color: #99a;
            min-width: 120px;
            height: 36px;
            min-height: 36px;
            border: 1px solid #222;
        }

        thead th:nth-child(1) {
            /*first cell in the header*/
            position: relative;
            display: block;
            background-color: #88b;
        }

        tbody tr td:nth-child(2) {
            margin-left: 124px;
            display: block;
        }

</div>
