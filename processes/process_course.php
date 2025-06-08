<?php 
    include_once('../control.php');

    if(isset($_POST['register'])){
        $name = $_POST['name'];
        $code = $_POST['code'];
        $campus = $_POST['campus'];
        $turn = $_POST['turn'];

        if(!empty($name) && !empty($code) && !empty($campus) && !empty($turn)){
            
            $insert = $conect->prepare("INSERT INTO curso (nome, codigo, campus, turno) VALUES (?, ?, ?, ?)");
            $insert->bind_param("siss", $name, $code, $campus, $turn);
            
            if($insert->execute()){
                header("Location: ../central.php?msg=curso_cadastrado");
                exit();
            } else {
                echo mysqli_errno($conect) . ": " . mysqli_error($conect);
                die();
            }

        } else {
            header('Location: ../forms/form_course.php?msg=1'); // Campo não preenchido
            exit();
        }

    } else if (isset($_POST['edit'])){
        $id_course = $_POST['id'];
        $name = $_POST['name'];
        $code = $_POST['code'];
        $campus = $_POST['campus'];
        $turn = $_POST['turn'];

        if(!empty($name) && !empty($code) && !empty($campus) && !empty($turn)){
            
            $update = $conect->prepare("UPDATE curso SET nome = ?, codigo = ?, campus = ?, turno = ? WHERE id_curso = ?");
            $update->bind_param("sissi", $name, $code, $campus, $turn, $id_course);
            
            if($update->execute()){
                header("Location: ../lists/list_course.php?msg=curso_alterado");
                exit();
            } else {
                echo mysqli_errno($conect) . ": " . mysqli_error($conect);
                die();
            }

        } else {
            header("Location: ../forms/form_course.php?id_course=$id_course&msg=1"); // Campo não preenchido
            exit();
        }

    } else if (isset($_POST['delete'])){
        $id_course = $_POST['id_course'] ?? null;

        if (empty($id_course)) {
            header("Location: ../list/list_course.php?msg=id_n_encontrado");  //ID não encontrado
            exit();
        }

        $delete = $conect->prepare("DELETE FROM curso WHERE id_curso = ?");
        $delete->bind_param("i", $id_course);

        if ($delete->execute()) {
            header("Location: ../list/list_course.php?msg=curso_excluido"); // Exclusão realizada com sucesso
            exit();
        } else {
            echo mysqli_errno($conect) . ": " . mysqli_error($conect);
            die();
        }
    }
?>