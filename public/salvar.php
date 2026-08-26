<?php

session_start();

header('Content-Type: application/json');

require_once 'conexao.php';


try {
    if (!isset($_SESSION['usuario_id'])) {
        throw new Exception(
            'Usuário não autenticado.'
        );
    }

    $usuarioId = $_SESSION['usuario_id'];

    $json = file_get_contents("php://input");
    $dados = json_decode(
        $json,
        true
    );

    if (!$dados) {
        throw new Exception(
            'JSON inválido.'
        );
    }

    if (!isset($dados['blocos'])) {
        throw new Exception(
            'Blocos não encontrados.'
        );
    }

    $pdo->beginTransaction();

    $buscarBloco = $pdo->prepare("
        SELECT id
        FROM blocos
        WHERE id = ?
        AND usuario_id = ?
    ");

    $insertBloco = $pdo->prepare("
        INSERT INTO blocos
        (
            usuario_id,
            nome
        )
        VALUES (?, ?)
    ");

    $updateBloco = $pdo->prepare("
        UPDATE blocos
        SET nome = ?
        WHERE id = ?
        AND usuario_id = ?
    ");

    $buscarNota = $pdo->prepare("
        SELECT id
        FROM notas
        WHERE id = ?
        AND bloco_id = ?
    ");

    $insertNota = $pdo->prepare("
        INSERT INTO notas
        (
            bloco_id,
            nome,
            descricao,
            check_in
        )
        VALUES (?, ?, ?, ?)
    ");

    $updateNota = $pdo->prepare("
        UPDATE notas
        SET
            nome = ?,
            descricao = ?,
            check_in = ?
        WHERE id = ?
        AND bloco_id = ?
    ");

    foreach ($dados['blocos'] as &$bloco) {
        $blocoId = $bloco['id'] ?? null;

        $nomeBloco =
            trim($bloco['nome'] ?? 'Novo bloco');

        if ($blocoId !== null) {
            $buscarBloco->execute([
                $blocoId,
                $usuarioId
            ]);

            $existe =
                $buscarBloco->fetch();


            if ($existe) {
                $updateBloco->execute([
                    $nomeBloco,
                    $blocoId,
                    $usuarioId
                ]);
            } else {
                $insertBloco->execute([
                    $usuarioId,
                    $nomeBloco
                ]);

                $blocoId =
                    $pdo->lastInsertId();

                $bloco['id'] =
                    (int) $blocoId;
            }
        } else {
            $insertBloco->execute([
                $usuarioId,
                $nomeBloco
            ]);
            $blocoId =
                $pdo->lastInsertId();
            $bloco['id'] =
                (int) $blocoId;
        }
        if (!isset($bloco['notas'])) {
            $bloco['notas'] = [];
        }
        foreach ($bloco['notas'] as &$nota) {
            $notaId =
                $nota['id'] ?? null;
            $nome =
                trim($nota['nome'] ?? '');
            $descricao =
                $nota['descricao'] ?? '';
            $checkIn =
                $nota['check_in'] ?? 'Não-Concluido';

            if (
                $checkIn !== 'Concluido' &&
                $checkIn !== 'Não-Concluido'
            ) {
                $checkIn =
                    'Não-Concluido';
            }
            if ($notaId !== null) {
                $buscarNota->execute([
                    $notaId,
                    $blocoId
                ]);
                $existe =
                    $buscarNota->fetch();
                if ($existe) {
                    $updateNota->execute([
                        $nome,
                        $descricao,
                        $checkIn,
                        $notaId,
                        $blocoId
                    ]);
                } else {
                    $insertNota->execute([
                        $blocoId,
                        $nome,
                        $descricao,
                        $checkIn
                    ]);
                    $notaId =
                        $pdo->lastInsertId();

                    $nota['id'] =
                        (int) $notaId;
                }
            } else {
                $insertNota->execute([
                    $blocoId,
                    $nome,
                    $descricao,
                    $checkIn
                ]);
                $notaId =
                    $pdo->lastInsertId();
                $nota['id'] =
                    (int) $notaId;
            }
        }
    }

    $pdo->commit();
    echo json_encode([
        'sucesso' => true,
        'mensagem' => 'Dados salvos com sucesso.',
        'dados' => $dados
    ]);
} catch (Throwable $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    http_response_code(500);
    echo json_encode([
        'sucesso' => false,
        'erro' => $e->getMessage()
    ]);
}
