<?php
include '../config.php';
require_once '../vendor/autoload.php'; // Autoload do Composer

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

// Define o cabeçalho do arquivo Excel
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="dados_motoristas.xlsx"');
header('Cache-Control: max-age=0');

// Cria uma nova planilha
$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();
$sheet->setTitle('Motoristas');

// Define o cabeçalho das colunas
$sheet->setCellValue('A1', 'Placa');
$sheet->setCellValue('B1', 'Hodômetro');
$sheet->setCellValue('C1', 'Data');
$sheet->setCellValue('D1', 'Hora');
$sheet->setCellValue('E1', 'Imagem');
$sheet->setCellValue('F1', 'Localização');

// Inicializa a consulta base
$sql = "SELECT placa, hodometro, imagem, data, hora, latitude, longitude FROM motorista WHERE 1=1";

// Adiciona filtro pelo nome, se selecionado
if (isset($_GET['nome']) && !empty($_GET['nome'])) {
    $nome_selecionado = trim($_GET['nome']);
    $sql .= " AND nome = ?";
}

// Adiciona filtro de datas, se definidos
if (isset($_GET['data_inicio']) && isset($_GET['data_fim']) && !empty($_GET['data_inicio']) && !empty($_GET['data_fim'])) {
    $data_inicio = $_GET['data_inicio'];
    $data_fim = $_GET['data_fim'];
    $sql .= " AND CONCAT(data, ' ', hora) BETWEEN ? AND ?";
}

// Prepara e executa a consulta
$stmt = $conn->prepare($sql);

if ($stmt) {
    $params = [];
    $types = '';

    // Adiciona parâmetros de filtro pelo nome
    if (isset($_GET['nome']) && !empty($_GET['nome'])) {
        $params[] = $nome_selecionado;
        $types .= 's';
    }

    // Adiciona parâmetros de filtro de data
    if (isset($_GET['data_inicio']) && isset($_GET['data_fim']) && !empty($_GET['data_inicio']) && !empty($_GET['data_fim'])) {
        $params[] = $data_inicio . ' 00:00:00'; // Hora de início
        $params[] = $data_fim . ' 23:59:59';   // Hora de fim
        $types .= 'ss';
    }

    if ($types) {
        $stmt->bind_param($types, ...$params);
    }

    $stmt->execute();
    $result = $stmt->get_result();

    // Adiciona os dados na planilha
    $rowNum = 2;
    while ($row = $result->fetch_assoc()) {
        $sheet->setCellValue('A' . $rowNum, $row['placa']);
        $sheet->setCellValue('B' . $rowNum, $row['hodometro']);
        $sheet->setCellValue('C' . $rowNum, $row['data']);
        $sheet->setCellValue('D' . $rowNum, $row['hora']);
        $sheet->setCellValue('E' . $rowNum, $row['imagem'] ? 'Imagem disponível' : 'Sem imagem');
        $sheet->setCellValue('F' . $rowNum, $row['latitude'] && $row['longitude'] ? 'Localização disponível' : 'Sem localização');
        $rowNum++;
    }

    $stmt->close();
} else {
    echo "Erro na preparação da consulta: " . $conn->error;
}

$conn->close();

// Salva o arquivo Excel
$writer = new Xlsx($spreadsheet);
$writer->save('php://output');
exit();
?>
