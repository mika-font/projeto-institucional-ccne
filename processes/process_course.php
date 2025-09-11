<?php
include_once(__DIR__ . '/../control.php');

if (!isset($_SESSION['type']) || $_SESSION['type'] != RULE_GERENTE) {
    $_SESSION['alert'] = [
        'type' => 'danger',
        'message' => 'Acesso negado. Você não tem permissão para realizar esta ação.'
    ];
    header('Location: ' . BASE_URL . '/central.php');
    exit();
}

if (isset($_POST['register'])) {
    $name = $_POST['name'];
    $code = $_POST['code'];
    $campus = $_POST['campus'];
    $turn = $_POST['turn'];

    if (!empty($name) && !empty($code) && !empty($campus) && !empty($turn)) {
        $insert = $conect->prepare("INSERT INTO curso (nome, codigo, campus, turno) VALUES (?, ?, ?, ?)");
        $insert->bind_param("siss", $name, $code, $campus, $turn);

        if ($insert->execute()) {
            $_SESSION['alert'] = [
                'type' => 'success',
                'message' => 'Curso cadastrado com sucesso!'
            ];
            header("Location: ../lists/list_course.php");
            exit();
        }
    } else {
        $_SESSION['alert'] = [
            'type' => 'danger',
            'message' => 'Por favor, preencha todos os campos.'
        ];
        header('Location: ../forms/form_course.php');
        exit();
    }
} else if (isset($_POST['edit'])) {
    $id_course = intval($_POST['id_course']);
    $name = $_POST['name'];
    $code = $_POST['code'];
    $campus = $_POST['campus'];
    $turn = $_POST['turn'];

    if (!empty($id_course) && !empty($name) && !empty($code) && !empty($campus) && !empty($turn)) {
        $update = $conect->prepare("UPDATE curso SET nome = ?, codigo = ?, campus = ?, turno = ? WHERE id_curso = ?");
        $update->bind_param("sissi", $name, $code, $campus, $turn, $id_course);

        if ($update->execute()) {
            $_SESSION['alert'] = [
                'type' => 'success',
                'message' => 'Curso alterado com sucesso!'
            ];
            header("Location: ../lists/list_course.php");
            exit();
        }
    } else {
        $_SESSION['alert'] = [
            'type' => 'danger',
            'message' => 'Por favor, preencha todos os campos.'
        ];
        header("Location: ../forms/form_course.php?id_course=$id_course");
        exit();
    }
} else if (isset($_POST['delete'])) {
    $id_course = intval($_POST['id_course'] ?? 0);

    if (empty($id_course)) {
        $_SESSION['alert'] = [
            'type' => 'danger',
            'message' => 'ID do curso inválido para exclusão.'
        ];
        header("Location: ../lists/list_course.php");
        exit();
    }

    try {
        $delete = $conect->prepare("DELETE FROM curso WHERE id_curso = ?");
        $delete->bind_param("i", $id_course);
        $delete->execute();

        $_SESSION['alert'] = [
            'type' => 'success', 
            'message' => 'Curso excluído com sucesso!'
        ];

        header("Location: ../lists/list_course.php");
        exit();
    } catch (mysqli_sql_exception $e) {
        if ($e->getCode() == 1451) { 
            $_SESSION['alert'] = [
                'type' => 'danger',
                'message' => 'Não foi possível excluir o curso, pois ele possui vínculos no sistema (ex: estudantes vinculados).'
            ];
            header("Location: ../lists/list_course.php");
        } else {
            $_SESSION['alert'] = [
                'type' => 'danger',
                'message' => 'Erro no banco de dados ao excluir curso.'
            ];
            header("Location: ../lists/list_course.php");
        }
        exit();
    }
}
