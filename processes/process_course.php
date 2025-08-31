<?php 
    include_once(__DIR__ . '/../control.php');

    if (!isset($_SESSION['type']) || $_SESSION['type'] != RULE_GERENTE) {
        header('Location: ' . BASE_URL . '/central.php?msg=nao_autorizado');
        exit();
    }

    if(isset($_POST['register'])){
        $name = $_POST['name'];
        $code = $_POST['code'];
        $campus = $_POST['campus'];
        $turn = $_POST['turn'];

        if(!empty($name) && !empty($code) && !empty($campus) && !empty($turn)){
            $insert = $conect->prepare("INSERT INTO curso (nome, codigo, campus, turno) VALUES (?, ?, ?, ?)");
            $insert->bind_param("siss", $name, $code, $campus, $turn);
            
            if($insert->execute()){
                header("Location: ../lists/list_course.php?msg=curso_cadastrado");
                exit();
            }
        } else {
            header('Location: ../forms/form_course.php?msg=campos_vazios');
            exit();
        }
    } else if (isset($_POST['edit'])){
        $id_course = intval($_POST['id_course']);
        $name = $_POST['name'];
        $code = $_POST['code'];
        $campus = $_POST['campus'];
        $turn = $_POST['turn'];

        if(!empty($id_course) && !empty($name) && !empty($code) && !empty($campus) && !empty($turn)){
            $update = $conect->prepare("UPDATE curso SET nome = ?, codigo = ?, campus = ?, turno = ? WHERE id_curso = ?");
            $update->bind_param("sissi", $name, $code, $campus, $turn, $id_course);
            
            if($update->execute()){
                header("Location: ../lists/list_course.php?msg=curso_alterado");
                exit();
            }
        } else {
            header("Location: ../forms/form_course.php?id_course=$id_course&msg=campos_vazios");
            exit();
        }
    } else if (isset($_POST['delete'])){
        $id_course = intval($_POST['id_course'] ?? 0);

        if (empty($id_course)) {
            header("Location: ../lists/list_course.php?msg=id_n_encontrado");
            exit();
        }

        try {
            $delete = $conect->prepare("DELETE FROM curso WHERE id_curso = ?");
            $delete->bind_param("i", $id_course);
            $delete->execute();

            header("Location: ../lists/list_course.php?msg=curso_excluido");
            exit();

        } catch (mysqli_sql_exception $e) {
            if ($e->getCode() == 1451) { // Código de erro para violação de chave estrangeira
                header("Location: ../lists/list_course.php?msg=erro_exclusao_vinculo");
            } else {
                header("Location: ../lists/list_course.php?msg=erro_banco");
            }
            exit();
        }
    }
?>