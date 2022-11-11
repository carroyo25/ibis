<?php
    class Panelmodel extends Model{
        public function __construct(){
            parent::__construct();
        }

        public function acordeon($user){
            $salida = "";
            $opcion = $this->acordeonUL($user);
            $item = $this->acordeonLi($user);

            foreach ($opcion as $op){
                $salida .= '<li>
                                <a class="link">
                                    <i class="'.$op['cicono'].'"></i><span>'.$op['cdescripcion'].'</span><i class="fa fa-chevron-down"></i>
                                </a>
                                <ul class="submenu">';
                                foreach ($item as $it) {
                                    if($it['cclasmenu'] == $op['cclasmenu']){
                                        $salida .= '<li>
                                                        <a href="'.constant('URL').$it['cruta'].'" class="opcion">'.$it['cdescripcion'].'</a>
                                                    </li>';
                                    }
                                }
                $salida.='</ul></li>';
            }

            return $salida;
        }

        private function acordeonUl($user){
            try {
                $sql = $this->db->connect()->prepare("SELECT
                                                        sysmenu.cdescripcion,
                                                        sysmenu.cicono,
                                                        sysmenu.cruta,
                                                        sysmenu.cclasmenu 
                                                    FROM
                                                        tb_usermod
                                                        INNER JOIN sysmenu ON tb_usermod.classmenu = sysmenu.cclasmenu 
                                                    WHERE
                                                        tb_usermod.iduser = :user 
                                                        AND ISNULL( sysmenu.cruta ) 
                                                        AND sysmenu.copcion	= '00'
                                                    GROUP BY
                                                        sysmenu.cdescripcion 
                                                    ORDER BY
                                                        sysmenu.cdescripcion");
                $sql->execute(["user"=>$user]);
                $result = $sql->fetchAll();

                return $result;
            } catch (PDOException $th) {
                echo $th->getMessage();
                return false;
            }
        }

        public function acordeonLi($user){
            try {
                $sql = $this->db->connect()->prepare("SELECT
                                                        sysmenu.cdescripcion,
                                                        sysmenu.cruta,
                                                        sysmenu.cclasmenu 
                                                    FROM
                                                        tb_usermod
                                                        INNER JOIN sysmenu ON tb_usermod.ncodmod = sysmenu.ncodmenu 
                                                    WHERE
                                                        tb_usermod.iduser = :user AND flgactivo = 1 
                                                    ORDER BY
                                                        sysmenu.copcion ASC");
                $sql->execute(["user"=>$user]);
                $result = $sql->fetchAll();

                return $result;
            } catch (PDOException $th) {
                echo $th->getMessage();
                return false;
            }
        }

        //panel de pedidos
        public function listarPanelPedidos(){
            try {
                $salida ="";
                $proceso = 0;
                $consulta = 0;
                $atendido = 0;
                $aprobacion = 0;
                $aprobado = 0;
                $orden = 0;
                $firma = 0;
                $recepcion = 0;
                $despacho = 0;
                $asignado = 0;
                $anulado = 0;

                $sql = $this->db->connect()->prepare("SELECT
                                                        DATE_FORMAT(tb_pedidocab.emision,'%d/%m/%Y') AS emision,
                                                        tb_pedidocab.estadodoc,
                                                        LPAD(tb_pedidocab.nrodoc, 6, 0 ) AS pedido,
                                                        UPPER(tb_pedidocab.concepto) AS concepto,
                                                        UPPER(tb_proyectos.cdesproy) AS proyecto,
                                                        tb_parametros.cdescripcion,
                                                        tb_parametros.cabrevia 
                                                    FROM
                                                        tb_pedidocab
                                                        INNER JOIN tb_proyectos ON tb_pedidocab.idcostos = tb_proyectos.nidreg
                                                        INNER JOIN tb_parametros ON tb_pedidocab.estadodoc = tb_parametros.nidreg 
                                                    WHERE
                                                        tb_pedidocab.usuario = :user
                                                    ORDER BY tb_pedidocab.emision DESC");
                $sql->execute(["user"=>$_SESSION['iduser']]);
                $rowcount = $sql->rowcount();

                if ($rowcount > 0) {
                    while ($rs = $sql->fetch()) {
                        $salida .= '<tr>
                                        <td class="textoCentro">'.$rs['pedido'].'</td>
                                        <td class="pl20px">'.$rs['concepto'].'</td>
                                        <td class="textoCentro">'.$rs['emision'].'</td>
                                        <td class="pl20px">'.$rs['proyecto'].'</td>
                                        <td class="textoCentro '.$rs['cabrevia'].'">'.$rs['cdescripcion'].'</td>
                                    </tr>';
                        
                                    if ($rs['estadodoc'] == 49){ //procesando
                                        $proceso++;
                                    }
                                    if($rs['estadodoc'] == 51) { //stock
                                        $consulta++;
                                    }
                                    if($rs['estadodoc'] == 52) { //atendido almacen
                                        $atendido++;
                                    }
                                    if($rs['estadodoc'] == 53) { //aprobacion
                                        $aprobacion++;
                                    }
                                    if($rs['estadodoc'] == 54) { //aprobado
                                        $aprobado++;
                                    }
                                    if($rs['estadodoc'] == 58) { //elaboracion orden
                                        $orden++;
                                    }
                                    if($rs['estadodoc'] == 59) { //firma
                                        $firma++;
                                    }
                                    if($rs['estadodoc'] == 60) { //recepcion
                                        $recepcion++;
                                    }
                                    if($rs['estadodoc'] == 62) { //despacho
                                        $despacho++;
                                    }
                                    if($rs['estadodoc'] == 64) { //asignado
                                        $asignado++;
                                    }
                                    if($rs['estadodoc'] == 65) { //anulado
                                        $anulado++;
                                    } 
                    }
                    
                    $series[] = array("name"=>"Proceso","y"=>$this->seriePie($proceso));
                    $series[] = array("name"=>"Consultas","y"=>$this->seriePie($consulta));
                    $series[] = array("name"=>"Atendidos","y"=>$this->seriePie($atendido));
                    $series[] = array("name"=>"Aprobacion","y"=>$this->seriePie($aprobacion));
                    $series[] = array("name"=>"Aprobados","y"=>$this->seriePie($aprobado));
                    $series[] = array("name"=>"Orden","y"=>$this->seriePie($orden));
                    $series[] = array("name"=>"Firmas","y"=>$this->seriePie($firma));
                    $series[] = array("name"=>"Recepcion","y"=>$this->seriePie($recepcion));
                    $series[] = array("name"=>"Despacho","y"=>$this->seriePie($despacho));
                    $series[] = array("name"=>"Asignacion","y"=>$this->seriePie($asignado));
                    $series[] = array("name"=>"Anulados","y"=>$this->seriePie($anulado));
                }

                return array("contenido"=>$salida,
                              "series" =>$series);
            } catch (PDOException $th) {
                echo $th->getMessage();
                return false;
            } 

        }

        private function seriePie($valor){
            $valor_devuelto = $valor == 0 ? 0:$valor;
            
            return $valor_devuelto;
        }

        public function listarPanelOrdenes(){
            try {
                $valores = [];
                $salida ="";
                $proceso = 0;
                $consulta = 0;
                $atendido = 0;
                $aprobacion = 0;
                $aprobado = 0;
                $cotizando = 0;
                $etiquetas = ["Proceso", "Firmas", "Atendido", "Aprobacion", "Aprobado","Culminados"];


                $sql = $this->db->connect()->query("SELECT
                                                        lg_ordencab.cnumero,
                                                        lg_ordencab.ffechadoc,
                                                        lg_ordencab.ncodpry,
                                                        tb_parametros.cdescripcion,
                                                        tb_parametros.cabrevia,
                                                        UPPER(tb_proyectos.cdesproy) AS proyecto,
                                                        tb_pedidocab.concepto,
                                                        lg_ordencab.nEstadoDoc                                                
                                                    FROM
                                                        lg_ordencab
                                                    INNER JOIN tb_parametros ON lg_ordencab.nEstadoDoc = tb_parametros.nidreg
                                                    INNER JOIN tb_proyectos ON lg_ordencab.ncodcos = tb_proyectos.nidreg
                                                    INNER JOIN tb_pedidocab ON lg_ordencab.id_refpedi = tb_pedidocab.idreg");
                $sql->execute();
                $rowcount = $sql->rowcount();

                if ($rowcount > 0) {
                    while ($rs = $sql->fetch()) {
                        $salida .= '<tr>
                                        <td class="textoCentro">'.$rs['cnumero'].'</td>
                                        <td class="pl20px">'.$rs['concepto'].'</td>
                                        <td class="textoCentro">'.$rs['ffechadoc'].'</td>
                                        <td class="pl20px">'.$rs['proyecto'].'</td>
                                        <td class="textoCentro '.$rs['cabrevia'].'">'.$rs['cdescripcion'].'</td>
                                    </tr>';
                        
                        if ($rs['nEstadoDoc'] == 49){ //procesando
                            $proceso++;
                        }else if($rs['nEstadoDoc'] == 59) {
                            $consulta++;
                        }else if($rs['nEstadoDoc'] == 52) {
                            $atendido++;
                        }else if($rs['nEstadoDoc'] == 53) {
                            $aprobacion++;
                        }else if($rs['nEstadoDoc'] == 54) {
                            $aprobado++;
                        }else if($rs['nEstadoDoc'] == 59) {
                            $cotizando++;
                        }
                        
                    }

                    array_push($valores,$proceso);
                    array_push($valores,$consulta);
                    array_push($valores,$atendido);
                    array_push($valores,$aprobacion);
                    array_push($valores,$aprobado);
                    array_push($valores,$cotizando);
                }

                return array("contenido"=>$salida,
                              "valores"=>$valores,
                              "etiquetas"=>$etiquetas);
            } catch (PDOException $th) {
                echo $th->getMessage();
                return false;
            } 
        }

        public function listarPedidosPendientesAprobacion(){
            try {
                $salida ="";
                $aprobacion = 0;
                $aprobado = 0;
                
                $sql = $this->db->connect()->prepare("SELECT
                                                        tb_costusu.id_cuser,
                                                        tb_costusu.ncodproy,
                                                        LPAD(tb_pedidocab.nrodoc, 6, 0) AS pedido,
                                                        UPPER(tb_pedidocab.concepto) AS concepto,
                                                        UPPER(tb_proyectos.cdesproy) AS proyecto,
                                                        tb_pedidocab.idreg,
                                                        tb_pedidocab.estadodoc,
                                                        tb_pedidocab.emision,
                                                        tb_pedidocab.vence,
                                                        UPPER(
                                                            CONCAT_WS(
                                                                ' ',
                                                                ibis.tb_proyectos.ccodproy,
                                                                ibis.tb_proyectos.cdesproy
                                                            )
                                                        ) AS costos,
                                                        tb_pedidocab.nivelAten,
                                                        estados.cdescripcion AS estado,
                                                        atencion.cdescripcion AS atencion,
                                                        UPPER(estados.cabrevia) AS cabrevia
                                                    FROM
                                                        tb_costusu
                                                    INNER JOIN tb_pedidocab ON tb_costusu.ncodproy = tb_pedidocab.idcostos
                                                    INNER JOIN tb_proyectos ON tb_costusu.ncodproy = tb_proyectos.nidreg
                                                    INNER JOIN tb_parametros AS estados ON tb_pedidocab.estadodoc = estados.nidreg
                                                    INNER JOIN tb_parametros AS atencion ON tb_pedidocab.nivelAten = atencion.nidreg
                                                    WHERE
                                                        tb_costusu.id_cuser = :user
                                                    AND tb_costusu.nflgactivo = 1 
                                                    AND tb_pedidocab.estadodoc BETWEEN 53 AND 54
                                                    ORDER BY emision DESC");
                $sql->execute(["user"=>$_SESSION['iduser']]);
                $rowcount = $sql->rowcount();
                if ($rowcount > 0) {
                    while ($rs = $sql->fetch()) {
                        $salida .= '<tr>
                                        <td class="textoCentro">'.$rs['pedido'].'</td>
                                        <td class="pl20px">'.$rs['concepto'].'</td>
                                        <td class="textoCentro">'.date("d/m/Y", strtotime($rs['emision'])).'</td>
                                        <td class="pl20px">'.$rs['proyecto'].'</td>
                                        <td class="textoCentro '.strtolower($rs['cabrevia']).'">'.$rs['cabrevia'].'</td>
                                    </tr>';
                        
                        if($rs['estadodoc'] == 53) { //aprobacion
                            $aprobacion++;
                        }
                        if($rs['estadodoc'] == 54) { //aprobado
                            $aprobado++;
                        }
                    }

                    $series[] = array("name"=>"Pendientes","y"=>$this->seriePie($aprobacion));
                    $series[] = array("name"=>"Aprobados","y"=>$this->seriePie($aprobado));
                }

                return array("contenido"=>$salida,
                              "series" =>$series);
            } catch (PDOException $th) {
                echo $th->getMessage();
                return false;
            }

        }

        private function conteoPedidosPedientesAprobar(){
            try {
                $sql = $this->db->connect()->prepare("SELECT
                            count(tb_pedidocab.idreg) AS pedidos__pendientes
                        FROM
                            tb_costusu
                        INNER JOIN tb_pedidocab ON tb_costusu.ncodproy = tb_pedidocab.idcostos
                        WHERE
                            tb_costusu.id_cuser = :user
                        AND tb_pedidocab.estadodoc = 49
                        AND tb_costusu.nflgactivo = 1");

                $sql->execute(["user"=>$_SESSION['iduser']]);
                $result = $sql->fetchAll();

                return $result[0]['pedidos__pendientes'];
            } catch (PDOException $th) {
                echo $th->getMessage();
                return false;
            }
        }

        private function conteoPedidosAprobados(){
            try {
                $sql = $this->db->connect()->prepare("SELECT
                            count(tb_pedidocab.idreg) AS pedidos__pendientes
                        FROM
                            tb_costusu
                        INNER JOIN tb_pedidocab ON tb_costusu.ncodproy = tb_pedidocab.idcostos
                        WHERE
                            tb_costusu.id_cuser = :user
                        AND tb_pedidocab.estadodoc BETWEEN 53,62
                        AND tb_costusu.nflgactivo = 1");

                $sql->execute(["user"=>$_SESSION['iduser']]);
                $result = $sql->fetchAll();

                return $result[0]['pedidos__pendientes'];
            } catch (PDOException $th) {
                echo $th->getMessage();
                return false;
            }
        }

        public function resumenCompras() {
            try {
                $compras = $this->pedidosAprobados();
                $ordenes = $this->ordenesCompra();

                return array("aprobados"=>$compras,
                            "ordenes"=>$ordenes);

            } catch (PDOException $th) {
                echo $th->getMessage();
                return false;
            }
        }

        private function pedidosAprobados(){
            try {
                $salida = "";
                $sql = $this->db->connect()->prepare("SELECT
                                                        tb_costusu.id_cuser,
                                                        tb_costusu.ncodproy,
                                                        LPAD(tb_pedidocab.nrodoc, 6, 0) AS pedido,
                                                        UPPER(tb_pedidocab.concepto) AS concepto,
                                                        UPPER(tb_proyectos.cdesproy) AS proyecto,
                                                        tb_pedidocab.idreg,
                                                        tb_pedidocab.estadodoc,
                                                        tb_pedidocab.emision,
                                                        tb_pedidocab.vence,
                                                        UPPER(
                                                            CONCAT_WS(
                                                                ' ',
                                                                ibis.tb_proyectos.ccodproy,
                                                                ibis.tb_proyectos.cdesproy
                                                            )
                                                        ) AS costos,
                                                        tb_pedidocab.nivelAten,
                                                        estados.cdescripcion AS estado,
                                                        atencion.cdescripcion AS atencion,
                                                        estados.cabrevia
                                                    FROM
                                                        tb_costusu
                                                    INNER JOIN tb_pedidocab ON tb_costusu.ncodproy = tb_pedidocab.idcostos
                                                    INNER JOIN tb_proyectos ON tb_costusu.ncodproy = tb_proyectos.nidreg
                                                    INNER JOIN tb_parametros AS estados ON tb_pedidocab.estadodoc = estados.nidreg
                                                    INNER JOIN tb_parametros AS atencion ON tb_pedidocab.nivelAten = atencion.nidreg
                                                    WHERE
                                                        tb_costusu.id_cuser = :user
                                                    AND tb_pedidocab.estadodoc BETWEEN 49 AND 54
                                                    AND tb_costusu.nflgactivo = 1");
                $sql->execute(["user"=>$_SESSION['iduser']]);
                $rowcount = $sql->rowcount();
                if ($rowcount > 0) {
                    while ($rs = $sql->fetch()) {
                        $salida .= '<tr data-id="'.$rs['idreg'].'">
                                        <td class="textoCentro">'.$rs['pedido'].'</td>
                                        <td class="pl20px">'.$rs['concepto'].'</td>
                                        <td class="textoCentro">'.date("d/m/Y", strtotime($rs['emision'])).'</td>
                                        <td class="pl20px">'.$rs['proyecto'].'</td>
                                        <td class="textoCentro '.$rs['cabrevia'].'">'.$rs['estado'].'</td>
                                    </tr>';
                    }
                }

                return $salida;
            } catch (PDOException $th) {
                echo $th->getMessage();
                return false;
            }
        }

        private function ordenesCompra(){
            try {
                $salida = "";
                $sql = $this->db->connect()->prepare("SELECT
                                                        tb_costusu.ncodcos,
                                                        tb_costusu.ncodproy,
                                                        tb_costusu.id_cuser,
                                                        lg_ordencab.id_regmov,
                                                        LPAD(lg_ordencab.cnumero,6,0) as cnumero,
                                                        lg_ordencab.ffechadoc,
                                                        lg_ordencab.nNivAten,
                                                        lg_ordencab.nEstadoDoc,
                                                        lg_ordencab.ncodpago,
                                                        lg_ordencab.nplazo,
                                                        lg_ordencab.cdocPDF,
                                                        UPPER( tb_pedidocab.concepto ) AS concepto,
                                                        UPPER( tb_pedidocab.detalle ) AS detalle,
                                                        UPPER(
                                                        CONCAT_WS( tb_area.ccodarea, tb_area.cdesarea )) AS area,
                                                        UPPER(
                                                        CONCAT_WS( tb_proyectos.ccodproy, tb_proyectos.cdesproy )) AS costos,
                                                        lg_ordencab.nfirmaLog,
                                                        lg_ordencab.nfirmaFin,
                                                        lg_ordencab.nfirmaOpe,
                                                        tb_parametros.cdescripcion AS atencion 
                                                    FROM
                                                        tb_costusu
                                                        INNER JOIN lg_ordencab ON tb_costusu.ncodproy = lg_ordencab.ncodpry
                                                        INNER JOIN tb_pedidocab ON lg_ordencab.id_refpedi = tb_pedidocab.idreg
                                                        INNER JOIN tb_area ON lg_ordencab.ncodarea = tb_area.ncodarea
                                                        INNER JOIN tb_proyectos ON lg_ordencab.ncodpry = tb_proyectos.nidreg
                                                        INNER JOIN tb_parametros ON lg_ordencab.nNivAten = tb_parametros.nidreg 
                                                    WHERE
                                                        tb_costusu.id_cuser = :user 
                                                        AND tb_costusu.nflgactivo = 1
                                                        AND lg_ordencab.nEstadoDoc BETWEEN 49 AND 59
                                                    ORDER BY ffechadoc DESC");
                                                    
                $sql->execute(["user"=>$_SESSION['iduser']]);
                $rowCount = $sql->rowCount();

                if ($rowCount > 0){
                    while ($rs = $sql->fetch()) {

                        $log = is_null($rs['nfirmaLog']) ? '<i class="far fa-square"></i>' : '<i class="far fa-check-square"></i>';
                        $ope = is_null($rs['nfirmaOpe']) ? '<i class="far fa-square"></i>' : '<i class="far fa-check-square"></i>';
                        $fin = is_null($rs['nfirmaFin']) ? '<i class="far fa-square"></i>' : '<i class="far fa-check-square"></i>';

                        $flog = is_null($rs['nfirmaLog']) ? 0 : 1;
                        $fope = is_null($rs['nfirmaOpe']) ? 0 : 1;
                        $ffin = is_null($rs['nfirmaFin']) ? 0 : 1;

                        $estado = $rs['nEstadoDoc'] == 59 ? "resaltado_firma" : "";

                        $salida .='<tr class="pointer '.$estado.'" data-estado="'.$rs['nEstadoDoc'].'">
                                        <td class="textoCentro">'.str_pad($rs['cnumero'],4,0,STR_PAD_LEFT).'</td>
                                        <td class="pl20px">'.$rs['concepto'].'</td>
                                        <td class="textoCentro">'.date("d/m/Y", strtotime($rs['ffechadoc'])).'</td>
                                        <td class="pl20px">'.utf8_decode($rs['costos']).'</td>
                                        <td class="textoCentro '.strtolower($rs['atencion']).'">'.$rs['atencion'].'</td>
                                        <td class="textoCentro">'.$log.'</td>
                                        <td class="textoCentro">'.$ope.'</td>
                                        <td class="textoCentro">'.$fin.'</td>
                                    </tr>';
                    }
                }

                return $salida;
            } catch (PDOException $th) {
                echo $th->getMessage();
                return false;
            }
        }

        public function resumenAlmacenRecepcion(){
            try {
                $ingresos = $this->ingresosAlmacen();
                $ordenes = $this->ordenesCompraCulminadas();

                return array("ingresos"=>$ingresos,
                             "ordenes"=>$ordenes);
            } catch (PDOException $th) {
                echo $th->getMessage();
                return false;
            }
        }

        private function ordenesCompraCulminadas(){
            try {
                $salida = "";
                $sql = $this->db->connect()->prepare("SELECT
                                                        tb_costusu.ncodcos,
                                                        tb_costusu.ncodproy,
                                                        tb_costusu.id_cuser,
                                                        lg_ordencab.id_regmov,
                                                        lg_ordencab.cnumero,
                                                        lg_ordencab.ffechadoc,
                                                        lg_ordencab.nNivAten,
                                                        lg_ordencab.nEstadoDoc,
                                                        lg_ordencab.ncodpago,
                                                        lg_ordencab.nplazo,
                                                        lg_ordencab.cdocPDF,
                                                        UPPER( tb_pedidocab.concepto ) AS concepto,
                                                        UPPER( tb_pedidocab.detalle ) AS detalle,
                                                        UPPER(
                                                        CONCAT_WS( tb_area.ccodarea, tb_area.cdesarea )) AS area,
                                                        UPPER(
                                                        CONCAT_WS( tb_proyectos.ccodproy, tb_proyectos.cdesproy )) AS costos,
                                                        lg_ordencab.nfirmaLog,
                                                        lg_ordencab.nfirmaFin,
                                                        lg_ordencab.nfirmaOpe,
                                                        tb_parametros.cdescripcion AS atencion 
                                                    FROM
                                                        tb_costusu
                                                        INNER JOIN lg_ordencab ON tb_costusu.ncodproy = lg_ordencab.ncodpry
                                                        INNER JOIN tb_pedidocab ON lg_ordencab.id_refpedi = tb_pedidocab.idreg
                                                        INNER JOIN tb_area ON lg_ordencab.ncodarea = tb_area.ncodarea
                                                        INNER JOIN tb_proyectos ON lg_ordencab.ncodpry = tb_proyectos.nidreg
                                                        INNER JOIN tb_parametros ON lg_ordencab.nNivAten = tb_parametros.nidreg 
                                                    WHERE
                                                        tb_costusu.id_cuser = :user 
                                                        AND tb_costusu.nflgactivo = 1
                                                        AND lg_ordencab.nEstadoDoc = 59");
                $sql->execute(["user"=>$_SESSION['iduser']]);
                $rowCount = $sql->rowCount();

                if ($rowCount > 0){
                    while ($rs = $sql->fetch()) {

                        $salida .='<tr class="pointer">
                                        <td class="textoCentro">'.str_pad($rs['cnumero'],4,0,STR_PAD_LEFT).'</td>
                                        <td class="pl20px">'.$rs['concepto'].'</td>
                                        <td class="textoCentro">'.date("d/m/Y", strtotime($rs['ffechadoc'])).'</td>
                                        <td class="pl20px">'.utf8_decode($rs['costos']).'</td>
                                        <td class="textoCentro '.strtolower($rs['atencion']).'">'.$rs['atencion'].'</td>
                                    </tr>';
                    }
                }

                return $salida;
            } catch (PDOException $th) {
                echo $th->getMessage();
                return false;
            }
        }

        private function ingresosAlmacen(){
            try {
                $salida = "";
                $sql = $this->db->connect()->prepare("SELECT
                                                        tb_costusu.id_cuser,
                                                        tb_costusu.ncodproy,
                                                        alm_recepcab.id_regalm,
                                                        alm_recepcab.ncodmov,
                                                        alm_recepcab.nnromov,
                                                        alm_recepcab.nnronota,
                                                        alm_recepcab.cper,
                                                        alm_recepcab.cmes,
                                                        alm_recepcab.ncodalm1,
                                                        alm_recepcab.ffecdoc,
                                                        alm_recepcab.id_centi,
                                                        alm_recepcab.cnumguia,
                                                        alm_recepcab.ncodpry,
                                                        alm_recepcab.ncodcos,
                                                        alm_recepcab.idref_pedi,
                                                        alm_recepcab.idref_abas,
                                                        alm_recepcab.nEstadoDoc,
                                                        UPPER(tb_almacen.cdesalm) AS almacen,
                                                        UPPER(tb_proyectos.cdesproy) AS proyecto,
                                                        lg_ordencab.cnumero AS orden,
                                                        LPAD(tb_pedidocab.nrodoc, 6, 0) AS pedido,
                                                        tb_parametros.cdescripcion,
                                                        tb_parametros.cabrevia
                                                        FROM
                                                        tb_costusu
                                                        INNER JOIN alm_recepcab ON tb_costusu.ncodproy = alm_recepcab.ncodpry
                                                        INNER JOIN tb_almacen ON alm_recepcab.ncodalm1 = tb_almacen.ncodalm
                                                        INNER JOIN tb_proyectos ON alm_recepcab.ncodpry = tb_proyectos.nidreg
                                                        INNER JOIN tb_area ON alm_recepcab.ncodarea = tb_area.ncodarea
                                                        INNER JOIN lg_ordencab ON alm_recepcab.idref_abas = lg_ordencab.id_regmov
                                                        INNER JOIN tb_pedidocab ON alm_recepcab.idref_pedi = tb_pedidocab.idreg
                                                        INNER JOIN tb_parametros ON alm_recepcab.nEstadoDoc = tb_parametros.nidreg
                                                        WHERE
                                                            tb_costusu.id_cuser = :usr
                                                        AND tb_costusu.nflgactivo = 1");
                $sql->execute(["usr"=>$_SESSION['iduser']]);
                $rowCount = $sql->rowcount();
                if ($rowCount > 0){
                    while($rs = $sql->fetch()){
                        $salida.='<tr class="pointer" data-indice="'.$rs['id_regalm'].'">
                                    <td class="textoCentro">'.$rs['cnumguia'].'</td>
                                    <td class="textoCentro">'.date("d/m/Y", strtotime($rs['ffecdoc'])).'</td>
                                    <td class="pl20px">'.$rs['almacen'].'</td>
                                    <td class="pl20px">'.$rs['proyecto'].'</td>
                                    <td class="pl20px">'.$rs['orden'].'</td>
                                    <td class="textoCentro '.$rs['cabrevia'].'">'.$rs['cdescripcion'].'</td>
                                </tr>';
                    }
                }

                return $salida;
            } catch (PDOException $th) {
                echo "Error: " . $th->getMessage();
                return false;
            }
        }

        public function cambiarClave($clave){
            try {
                $ret = false;
                $clave_nueva = $this->encryptPass($clave);
                $sql = $this->db->connect()->prepare("UPDATE tb_user 
                                                    SET cclave =:clave 
                                                    WHERE iduser =:usr");
                $sql->execute(["clave"=>$clave_nueva,
                                        "usr"=>$_SESSION['iduser']]);

                $rowCount = $sql->rowCount();

                if ($rowCount > 0) {
                    $ret = true;
                }

                return $ret;
            } catch (PDOException $th) {
                echo $th->getMessage();
                return false;
            }
        }
    }
?>