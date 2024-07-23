<?php
    error_reporting(0);
    
    include("auxFunctions.php"); include("nexus.php");
    date_default_timezone_set('America/Santiago');

    if (isset($_POST['volver'])){
        echo '
                <script type="text/javascript">
                    window.location.href = "http://localhost/sebad/index.php";
                </script>
            ';
    }

    if(isset($_POST['btnBuscar'])){
        if (!empty($_POST['rut']) && !empty($_POST['dv'])){
            $digito = verificarRut($_POST['rut']);
            $dv = strtoupper($_POST['dv']);
            $rutSinPro = $_POST['rut'];
            if($dv == $digito){
                
                $rut = $rutSinPro."-".$dv;
                $cnn = Conectar();
                $sql = 
                "SELECT  BE.Nombre, BE.Apellido, BH.HoraInicioColacion, BH.HoraFinColacion, RE.CodEntrada, GB.IdGrupo, BE.IdEstadoBeneficio
                FROM beneficiario BE, bloquehorario BH, GrupoBeneficio GB, registroentradarecinto RE, estadobeneficio EB 
                WHERE ((BE.IdEstadoBeneficio = EB.IdEstadoBeneficio )) AND ((BE.GrupoBeneficio = GB.IdGrupo)) AND ((GB.Horarios = BH.Id)) AND ((GB.IdGrupo = RE.IdGrupo)) AND ((BE.Rut ='$rut')) 
                GROUP BY BE.Nombre;";
                
                $get = mysqli_query($cnn, $sql);
                if($row = mysqli_fetch_array($get)){
                    $vaEdoBeneficio = $row[6];
                    if ($vaEdoBeneficio === '1') { /* continue... */ } else {
                        printAlert("ERROR: El beneficio de este usuario se encuentra deshabilitado 🥵");
                        
                        echo '
                                <script type="text/javascript">
                                    window.location.href = "http://localhost/sebad/resources/views/crudUser.php";
                                </script>
                            ';
                    };
                    $vaNom = $row[0];
                    $vaApe = $row[1];
                    $vaHorIn = $row[2];
                    $vaHorFi = $row[3];
                    $vaCodEntry = $row[4];
                    $vaIdGrupo = $row[5];
                    $fechaEntrega = date('Y-m-d');
                    $time = date('H:i:s');
                    //Estas variables deben ser enviadas con PHP a la vista correspondiente.
                    
                    $sqlFiltro="SELECT * FROM registroentregacolacion WHERE (RutBeneficiario ='$rut') AND (FechaDeEntrega = '$fechaEntrega')";
                    $getFiltro = mysqli_query($cnn,$sqlFiltro);
                    if ($rowFiltro = mysqli_fetch_array($getFiltro)){

                        printAlert("ERROR: Usuario ya hizo uso del beneficio el dia de hoy 🥵");
                        
                        echo '
                                <script type="text/javascript">
                                    window.location.href = "http://localhost/sebad/resources/views/crudUser.php";
                                </script>
                            ';
                        
                    }else{
                        $sqlRegistro = "INSERT INTO registroentregacolacion (FechaDeEntrega, RutBeneficiario, IdGrupo, HoraDeEntrega) VALUES ('$fechaEntrega','$rut','$vaIdGrupo','$time');";
                        mysqli_query($cnn,$sqlRegistro);
                        
                        $sqlCodVoucher = "SELECT CodEntrega FROM registroentregacolacion WHERE (RutBeneficiario = '$rut')";
                        $getRegistro = mysqli_query($cnn,$sqlCodVoucher);
                        $arrayRegistro = mysqli_fetch_array($getRegistro);
                        $vaCodVoucher = $arrayRegistro[0];};

                };
            }
            elseif ($dv != $digito) {
                printAlert('ERROR: Verifique el Rut ingresado y reintente nuevamente');
                
                echo 
                '
                <script type="text/javascript">
                    window.location.href = "http://localhost/sebad/resources/views/crudUser.php";
                </script>
                ';
            }
        }else{ printAlert('ERROR: Campo rut vacío');
            
            echo 
            '
            <script type="text/javascript">
                window.location.href = "http://localhost/sebad/resources/views/crudUser.php";
            </script>
            ';
        };
    }

    if(isset($_POST['btnHuella'])) {
    
        $cnn = Conectar();
        $sql = "SELECT Rut FROM beneficiario WHERE IdEstadoBeneficio = '1';";
        $get = mysqli_query($cnn, $sql);
        $row = mysqli_fetch_array($get); // Array con Ruts
        $cantidadArrojada = mysqli_num_rows($get); // Cantidad de indices en el array ya creado

    
        $rut = $row[rand(0, $cantidadArrojada)];


        $cnn = Conectar();
        $sql1 = 
            "SELECT  BE.Nombre, BE.Apellido, BH.HoraInicioColacion, BH.HoraFinColacion, RE.CodEntrada, GB.IdGrupo, BE.IdEstadoBeneficio
            FROM beneficiario BE, bloquehorario BH, GrupoBeneficio GB, registroentradarecinto RE, estadobeneficio EB 
            WHERE ((BE.IdEstadoBeneficio = EB.IdEstadoBeneficio )) AND ((BE.GrupoBeneficio = GB.IdGrupo)) AND ((GB.Horarios = BH.Id)) AND ((GB.IdGrupo = RE.IdGrupo)) AND ((BE.Rut ='$rut')) 
            GROUP BY BE.Nombre;";
        
        $get1 = mysqli_query($cnn, $sql1);
        if(($row1 = mysqli_fetch_array($get1))){
                $vaEdoBeneficio = $row1[6];
            if ($vaEdoBeneficio === '1') { /* continue... */ } else {
                printAlert("ERROR: El beneficio de este usuario se encuentra deshabilitado 🥵");

                echo '
                <script type="text/javascript">
                window.location.href = "http://localhost/sebad/resources/views/crudUser.php";
                </script>
                ';
                };
            $vaNom = $row1[0];
            $vaApe = $row1[1];
            $vaHorIn = $row1[2];
            $vaHorFi = $row1[3];
            $vaCodEntry = $row1[4];
            $vaIdGrupo = $row1[5];
            $fechaEntrega = date('Y-m-d');
            $time = date('H:i:s'); 
            session_start();
            $_SESSION['vaNom'] = $vaNom;
            $_SESSION['vaApe'] = $vaApe;
            $_SESSION['vaHorIn'] = $vaHorIn;
            $_SESSION['vaHorFi'] = $vaHorFi;
            $_SESSION['vaCodEntry'] = $vaCodEntry;
            $_SESSION['vaIdGrupo'] = $vaIdGrupo;
            $_SESSION['fechaEntrega'] = $fechaEntrega;
            $_SESSION['time'] = $time;
            //Estas variables deben ser enviadas con PHP a la vista correspondiente.
            
            $sqlFiltro = "SELECT * 
                        FROM registroentregacolacion 
                        WHERE (RutBeneficiario ='$rut') 
                        AND (FechaDeEntrega = '$fechaEntrega')";

            $getFiltro = mysqli_query($cnn,$sqlFiltro);
            if ($rowFiltro = mysqli_fetch_array($getFiltro)){
                
                printAlert("ERROR: Usuario ya hizo uso del beneficio el dia de hoy 🥵 ");

                echo '
                <script type="text/javascript">
                window.location.href = "http://localhost/sebad/resources/views/crudUser.php";
                </script>
                ';
                
            }else{
                $sqlRegistro = "INSERT INTO registroentregacolacion (FechaDeEntrega, RutBeneficiario, IdGrupo, HoraDeEntrega) VALUES ('$fechaEntrega','$rut','$vaIdGrupo','$time');";
                mysqli_query($cnn,$sqlRegistro);
                
                $sqlCodVoucher = "SELECT CodEntrega FROM registroentregacolacion WHERE (RutBeneficiario = '$rut')";
                $getRegistro = mysqli_query($cnn,$sqlCodVoucher);
                $arrayRegistro = mysqli_fetch_array($getRegistro);
                $vaCodVoucher = $arrayRegistro[0];};
        }
    }  
?>