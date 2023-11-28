<?php
    class ContratosModel extends Model{

        public function __construct()
        {
            parent::__construct();
        }

        public function insertarContrato($cabecera,$detalles,$comentarios,$adicionales,$adjuntos,$usuario){
            try {
                $salida = false;
                $respuesta = false;
                $mensaje = "Error en el registro";
                $clase = "mensaje_error";
                $cab = json_decode($cabecera);

                $sql = "SELECT COUNT(lg_ordencab.id_regmov) AS numero FROM lg_ordencab WHERE lg_ordencab.ncodcos = :cod";
                
                $orden = $this->generarNumeroOrden();
                
                $periodo = explode('-',$cab->emision);
                $dias_entrega = intval($cab->dias);

                $sql = $this->db->connect()->prepare("INSERT INTO lg_ordencab SET id_refpedi=:pedi,cper=:anio,cmes=:mes,ntipmov=:tipo,cnumero=:orden,
                                                                                ffechadoc=:fecha,ffechaent=:entrega,id_centi=:entidad,ncodmon=:moneda,ntcambio=:tcambio,
                                                                                nigv=:igv,ntotal=:total,ncodpry=:proyecto,ncodcos=:ccostos,ncodarea=:area,
                                                                                ctiptransp=:transporte,id_cuser=:elabora,ncodpago=:pago,nplazo=:pentrega,cnumcot=:cotizacion,
                                                                                cdocPDF=:adjunto,nEstadoDoc=:est,ncodalm=:almacen,nflgactivo=:flag,nNivAten=:atencion,
                                                                                cverificacion=:verif,cObservacion=:observacion,cReferencia=:referencia,
                                                                                nAdicional=:adicional,lentrega=:lugar");

                $sql ->execute(["pedi"=>$cab->codigo_pedido,
                                "anio"       =>$periodo[0],
                                "mes"        =>$periodo[1],
                                "tipo"       =>$cab->codigo_tipo,
                                "orden"      =>$orden,
                                "fecha"      =>$cab->emision,
                                "entrega"    =>$cab->fentrega,
                                "entidad"    =>$cab->codigo_entidad,
                                "moneda"     =>$cab->codigo_moneda,
                                "tcambio"    =>$cab->tcambio,
                                "igv"        =>$cab->radioIgv,
                                "total"      =>$cab->total_numero,
                                "proyecto"   =>$cab->codigo_costos,
                                "ccostos"    =>$cab->codigo_costos,
                                "area"       =>$cab->codigo_area,
                                "transporte" =>$cab->codigo_transporte,
                                "elabora"    =>$usuario,
                                "pago"       =>$cab->codigo_pago,
                                "pentrega"   =>$dias_entrega,
                                "cotizacion" =>$cab->proforma,
                                "adjunto"    =>$cab->vista_previa,
                                "est"        =>49,
                                "almacen"    =>$cab->codigo_almacen,
                                "flag"       =>1,
                                "atencion"   =>47,
                                "verif"      =>$cab->codigo_verificacion,
                                "cotizacion" =>$cab->ncotiz,
                                "observacion"=>$cab->concepto,
                                "referencia" =>$cab->referencia,
                                "adicional"  =>$cab->total_adicional,
                                "lugar"      =>$cab->lentrega]);
                $rowCount = $sql->rowCount();

                if ($rowCount > 0){
                    $indice = $this->lastInsertOrder();
                    $this->grabarDetalles($indice,$detalles,$cab->codigo_costos,$orden);
                    $this->grabarComentarios($indice,$comentarios,$usuario);
                    $this->grabarAdicionales($indice,$adicionales);
                    $this->actualizarDetallesPedido(84,$detalles,$orden,$cab->codigo_entidad);
                    $this->actualizarCabeceraPedido(58,$cab->codigo_pedido,$orden);
                    $respuesta = true;
                    $mensaje = "Orden Grabada";
                    $clase = "mensaje_correcto";
                }

                $salida = array("respuesta"=>$respuesta,
                                "mensaje"=>$mensaje,
                                "clase"=>$clase,
                                "orden"=>$orden);

            
                return $salida;
            } catch (PDOException $th) {
                echo "Error: ".$th->getMessage();
                return false;
            }    
        }
    }
?>