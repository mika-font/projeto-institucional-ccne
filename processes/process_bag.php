<?php 
    include_once('../control.php');

    if(isset($_POST['register'])){
        $name = $_POST['name'];
        $id_sub = $_POST['id_subunit'];
        $id_sub_allocation = $_POST['id_subunit_allocation'];
        $code = $_POST['code'];
        $description = $_POST['description'];
        $workload_limit = $_POST['workload_limit'];
        $modality = $_POST['modality'];
        $situation = $_POST['situation'];
        $link_file = $_POST['file'];

        if(!empty($name) && !empty($id_sub) && !empty($id_sub_allocation) && !empty($code) && !empty($description)
            && !empty($workload_limit) && !empty($modality) && !empty($situation) && !empty($link_file)){
            
            $insert = $conect->prepare("INSERT INTO bolsa (id_subunidade, codigo, nome, descricao, limite_ch, modalidade, situacao, arquivo, id_subunidade_alocacao) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $insert->bind_param("isssisssi", $id_sub, $code, $name, $description, $workload_limit, $modality, $situation, $link_file, $id_sub_allocation);
            
            if($insert->execute()){
                header("Location: ../central.php?msg=bolsa_cadastrada");
                exit();
            } else {
                echo mysqli_errno($conect) . ": " . mysqli_error($conect);
                die();
            }

        } else {
            header('Location: ../forms/form_bag.php?msg=1'); // Campo não preenchido
            exit();
        }

    } else if (isset($_POST['edit'])){
        $id_bag = $_POST['id_bag'];
        $name = $_POST['name'];
        $id_sub = $_POST['id_subunit'];
        $id_sub_allocation = $_POST['id_subunit_allocation'];
        $code = $_POST['code'];
        $description = $_POST['description'];
        $workload_limit = $_POST['workload_limit'];
        $modality = $_POST['modality'];
        $situation = $_POST['situation'];
        $link_file = $_POST['file'];

        if(!empty($id_bag) && !empty($name) && !empty($id_sub) && !empty($id_sub_allocation) && !empty($code) && !empty($description)
            && !empty($workload_limit) && !empty($modality) && !empty($situation) && !empty($link_file)){
            
            $update = $conect->prepare("UPDATE bolsa SET id_subunidade = ?, codigo = ?, nome = ?, descricao = ?, limite_ch = ?, modalidade = ?, situacao = ?, arquivo = ?, id_subunidade_alocacao = ? WHERE id_bolsa = ?");
            $update->bind_param("isssisssii", $id_sub, $code, $name, $description, $workload_limit, $modality, $situation, $link_file, $id_sub_allocation, $id_bag);
            
            if($update->execute()){
                header("Location: ../lists/list_bag.php?msg=bolsa_alterada");
                exit();
            } else {
                echo mysqli_errno($conect) . ": " . mysqli_error($conect);
                die();
            }

        } else {
            header("Location: ../forms/form_bag.php?id_bag=$id_bag&msg=1"); // Campo não preenchido
            exit();
        }

    } else if (isset($_POST['delete'])){
        $id_bag = $_POST['id_bag'] ?? null;

        if (empty($id_bag)) {
            header("Location: ../list/list_bag.php?msg=id_n_encontrado");  //ID não encontrado
            exit();
        }

        $delete = $conect->prepare("DELETE FROM bolsa WHERE id_bolsa = ?");
        $delete->bind_param("i", $id_bag);

        if ($delete->execute()) {
            header("Location: ../lists/list_bag.php?msg=bolsa_excluída"); // Exclusão realizada com sucesso
            exit();
        } else {
            echo mysqli_errno($conect) . ": " . mysqli_error($conect);
            die();
        }
    }
?>