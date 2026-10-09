<?php
require "config.php";
header("Content-Type: application/json; charset=UTF-8");
$rota = $_GET["rota"] ?? "teste";
function listarClientes($con)
{
    $stmt = $con->query("SELECT * FROM Clientes");

    echo json_encode(
        $stmt->fetchAll(PDO::FETCH_ASSOC)
    );
}
function criarCliente($con)
{
    $dados = json_decode(file_get_contents("php://input"), true);
    if (
        !isset($dados["nome"]) ||
        !isset($dados["email"]) ||
        !isset($dados["telefone"])
    ) {
        echo json_encode([
            "erro" => "Nome, email e telefone são obrigatórios."
        ]);
        return;
    }
    try {

        $sql = "INSERT INTO Clientes
                (nome, email, telefone)
                VALUES
                (:nome, :email, :telefone)";
        $stmt = $con->prepare($sql);
        $stmt->execute([
            ":nome" => $dados["nome"],
            ":email" => $dados["email"],
            ":telefone" => $dados["telefone"]
        ]);
        echo json_encode([
            "sucesso" => true,
            "mensagem" => "Cliente criado com sucesso!",
            "id" => $con->lastInsertId()
        ]);
    } catch (PDOException $e) {
        if (isset($e->errorInfo[1]) && $e->errorInfo[1] == 1062) {
            echo json_encode([
                "sucesso" => false,
                "mensagem" => "Este e-mail já está cadastrado."
            ]);
        } else {
            echo json_encode([
                "sucesso" => false,
                "mensagem" => "Erro ao cadastrar cliente."
            ]);
        }
    }
}
switch ($rota) {
    case "clientes":
        listarClientes($con);
        break;
    case "clientes/create":
        criarCliente($con);
        break;
    default:
        echo json_encode([
            "mensagem" => "API LojaTI funcionando!"
        ]);
        break;
}