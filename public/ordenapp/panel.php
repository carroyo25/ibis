<?php 
    require_once("acciones.php");
    $version = rand(0, 9999);

    $url = $_SERVER['SERVER_NAME'];
    $pdf = "http://".$url."/ibis/public/documentos/ordenes/emitidas/OC000001_4.pdf"

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/app.css?v<?php echo $version?>">
    <link rel="stylesheet" href="../css/all.css">
    <title>Sical</title>
</head>
<body>
    <div class="modal" id="preview">
        <div class="inside">
            <div class="preview">
            <iframe id="pdf-js-viewer" src="pdfjs/web/viewer.html?file=<?php echo $pdf ?>" title="webviewer" frameborder="0"></iframe>
            </div>
            <div class="commands">
                <button id="bntAuthorize">Aprobar</button>
                <button id="btnComment">Observar</button>
                <button id="btnClose">Cerrar</button>
            </div>
        </div>
    </div>
    <div class="modal" id="comments">
        <div class="inside__comments">
            <div class="header__inside__comment">
                <h3>Comentarios</h3>
                <div class="commands">
                    <a href="#" id="addCommment"><i class="fas fa-plus-circle"></i></a>
                    <a href="#" id="closeComment"><i class="far fa-times-circle"></i></a>
                </div>
            </div>
            <div class="body__inside__comment">
                <div class="comments">
                        <?php 
                            for ($i=0; $i < 3; $i++) { 
                        ?>
                            <div class="comment" data-grabado=1>  
                                <div class="header__comment">
                                    <span class="name__comment">Maria Tuñoque</span>
                                    <span class="date__comment">03/09/2022</span>
                                </div>
                                <div class="body__comment">
                                    <span>
                                        Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown printer took a galley
                                    </span>
                                </div>
                            </div>
                        <?php
                            }
                        ?>
                    
                </div>
            </div>
        </div>
    </div>
    <div class="modal" id="loader">
        <div class="loadingio-spinner-spinner-5ulcsi06hlf">
            <div class="ldio-fifgg00y5y">
                <div></div>
                <div></div>
                <div></div>
                <div></div>
                <div></div>
                <div></div>
                <div></div>
                <div></div>
                <div></div>
                <div></div>
                <div></div>
                <div></div>
            </div>
        </div>
    </div>
    <div class="wrap">
        <div class="header__wrap">
            <h1>Sical</h1>
        </div>
        <div class="body__wrap">
            <p>Ordenes Pendientes</p>
            <div class="documents__body__wrap">
                <ul id="ordenes">
                    <?php 
                        for ($i=0; $i < 10; $i++) { 
                    ?>
                        <li>
                            <a href="../documentos/ordenes/emitidas/OC000001_4.pdf">
                                <i class="far fa-file-pdf"></i>
                                <span>Orden <?php echo str_pad($i,4,0,STR_PAD_LEFT) ?></span>
                            </a></li>
                    <?php        
                        }
                    ?>
                </ul>
            </div>
        </div>
    </div>
    <script src="../js/jquery.js"></script>
    <script src="../js/funciones.js"></script>
    <script src="../js/panelapp.js?v<?php echo $version?>"></script>
</body>
</html>