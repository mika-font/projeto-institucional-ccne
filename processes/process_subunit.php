<?php 
    include_once('../control.php');

    if(isset($_POST['register'])){
        $name = $_POST['name'];
        $code = $_POST['code'];

        if(!empty($name) && !empty($code)){
            
            $insert = $conect->prepare("INSERT INTO subunidade (nome, codigo) VALUES (?, ?)");
            $insert->bind_param("ss", $name, $code);
            
            if($insert->execute()){
                header("Location: ../central.php?msg=subunidade_cadastrada");
                exit();
            } else {
                echo mysqli_errno($conect) . ": " . mysqli_error($conect);
                die();
            }

        } else {
            header('Location: ../forms/form_subunit.php?msg=1'); // Campo não preenchido
            exit();
        }

    } else if (isset($_POST['edit'])){
        $id_sub = $_POST['id_sub'];
        $name = $_POST['name'];
        $code = $_POST['code'];

        if(!empty($name) && !empty($code) && !empty($id_sub)){
            
            $update = $conect->prepare("UPDATE subunidade SET nome = ?, codigo = ? WHERE id_subunidade = ?");
            $update->bind_param("ssi", $name, $code, $id_sub);
            
            if($update->execute()){
                header("Location: ../lists/list_subunit.php?msg=subunidade_alterada");
                exit();
            } else {
                echo mysqli_errno($conect) . ": " . mysqli_error($conect);
                die();
            }

        } else {
            header("Location: ../forms/form_subunit.php?id_sub=$id_sub&msg=1"); // Campo não preenchido
            exit();
        }

    } else if (isset($_POST['delete'])){
        $id_sub = $_POST['id_sub'] ?? null;

        if (empty($id_sub)) {
            header("Location: ../list/list_subunit.php?msg=id_n_encontrado");  //ID não encontrado
            exit();
        }

        $delete = $conect->prepare("DELETE FROM subunidade WHERE id_subunidade = ?");
        $delete->bind_param("i", $id_sub);

        if ($delete->execute()) {
            header("Location: ../lists/list_subunidade.php?msg=subunidade_excluida"); // Exclusão realizada com sucesso
            exit();
        } else {
            echo mysqli_errno($conect) . ": " . mysqli_error($conect);
            die();
        }
    }
?>