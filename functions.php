<?php
// Global App Functions
function _response($success = false, $msg = '', $data = [], $code = 0) // Responder peticiones AJAX con formato JSON

{
    $response = array(
        'success' => $success,
        'code' => $code,
        'msg' => $msg,
        'data' => $data,
    );
    header("Content-Type: application/json", true);
    echo json_encode($response);
    die();
}

function _val(string $index, $required = true) // Validar variables

{
    /**
     * $index = variable
     * $required = si es un campo requerido por defecto true
     */
    $value = isset($_POST[$index]) ? $_POST[$index] : "";
    if ($required) {
        if (isset($value) && $value != "") {
            return $value;
        } else {
            if (APP_DEBUG) {
                _response(false, "Algunos datos requeridos están vacíos ($index)");
            } else {
                _response(false, 'Algunos datos requeridos están vacíos');
            }
        }
    }
    return $value;
}

function _query($sql, $parameters, $responseType, $operationType = null) // Ejecutar consultas MySQL

{
    switch ($responseType) {
        case 1: //FETCH_ASSOC
            $stm = Conexion::conectar()->prepare($sql);
            $stm->execute($parameters);
            return $stm->fetchAll(PDO::FETCH_ASSOC);
            break;
        case 2: //FETCH_NUM
            $stm = Conexion::conectar()->prepare($sql);
            $stm->execute($parameters);
            return $stm->fetchAll(PDO::FETCH_NUM);
            break;
        case 3: //ROW_COUNT
            $stm = Conexion::conectar()->prepare($sql);
            $stm->execute($parameters);
            return $stm->rowCount();
            break;
        case 4: //LAST_ID
            $con = Conexion::conectar();
            $stm = $con->prepare($sql);
            $stm->execute($parameters);
            return intval($con->lastInsertId());
            break;
        default:
            break;
    }
}

function _countTable(string $tableName) // Contar total de registros en una tabla

{
    $sql = "SELECT COUNT(*) FROM $tableName";
    return _query($sql, [], RES_FETCH_NUM, OPE_READ)[0][0];
}

function _log()
{}

function _isActive($rootLink)
{
    if (App::$url[0] == $rootLink) {
        return 'active';
    }
}

function generateRandomString($length = 10)
{
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
}

function _getFirst($array)
{
    if (count($array) > 0) {
        return $array[0];
    }
    return $array;
}

function _formatPhone($n)
{
    if (strlen($n) == 10) { // Celular
        $nF = substr($n, 0, 3) . ' ';
        $nF .= substr($n, 3, 3) . ' ';
        $nF .= substr($n, 6, 4);
        return $nF;
    } else {
        $nF = substr($n, 0, 3) . ' ';
        $nF .= substr($n, 3, 4);
        return $nF;
    }
}

function _formatDate($d)
{
    return date("g:i a", strtotime($d));
}