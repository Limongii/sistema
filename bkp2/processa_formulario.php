<?php
include 'config.php';

// Verifica se o formulário foi enviado
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nome = $_POST['nome'];
    $placa = $_POST['placa'];
    $hodometro = $_POST['hodometro'];
    $posto_id = $_POST['posto'];
    $data = $_POST['data']; // Data no formato yyyy-mm-dd
    $hora = $_POST['hora']; // Hora no formato hh:mm

    // Formata a data e a hora
    $data_formatada = date('d-m-Y', strtotime($data));
    $hora_formatada = date('H:i', strtotime($hora));

    // Processa o upload da imagem
    $imagem = null;
    if (isset($_FILES['imagem']) && $_FILES['imagem']['error'] == UPLOAD_ERR_OK) {
        $imagem = $_FILES['imagem']['name'];
        $imagem_tmp = $_FILES['imagem']['tmp_name'];
        $imagem_destino = 'uploads/' . $imagem;
        move_uploaded_file($imagem_tmp, $imagem_destino);
    }

    // Prepara a inserção no banco de dados
    $sql = "INSERT INTO motorista (nome, placa, hodometro, posto_id, data, hora, imagem) VALUES (?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssssss", $nome, $placa, $hodometro, $posto_id, $data_formatada, $hora_formatada, $imagem);

    if ($stmt->execute()) {
        echo "<script>
                alert('Dados enviados com sucesso!');
                setTimeout(function(){
                    window.location.href = 'bem-vindo.php';
                }, 1000);
              </script>";
    } else {
        echo "Erro: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
}
?>
