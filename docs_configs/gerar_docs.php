<?php
/**
 * Gera documentos PDF (FCB ou PSA) para bolsistas usando Dompdf
 *
 * Parâmetros via GET:
 * - tipo: 'fcb' para Formulário de Inscrição do Bolsista, 'psa' para Plano de Atividades Semestral
 * - id: ID do bolsista
 *
 * @package docs_configs
 */

require_once __DIR__ . '/../vendor/autoload.php';
include_once __DIR__ . '/../control.php';
include_once __DIR__ . '/gerarFCB.php';
include_once __DIR__ . '/gerarPSA.php';

use Dompdf\Dompdf;
use Dompdf\Options;

// Configurações do Dompdf
$options = new Options();
$options->set('isHtml5ParserEnabled', true);
$options->set('isRemoteEnabled', true);
$options->set('defaultFont', 'Arial');
$options->set('chroot', realpath(__DIR__ . '/..'));

$dompdf = new Dompdf($options);

// Verificar tipo de documento solicitado
$tipo_documento = $_GET['tipo'] ?? '';
$id_inscricao = $_GET['id'] ?? null;

if (!$id_inscricao) {
    die('ID do bolsista não informado');
}

$dados_inscricao = dadosInscricao($id_inscricao);

// Gerar documento baseado no tipo
switch ($tipo_documento) {
    case 'fcb':
        $html = gerarFCB($dados_inscricao);
        $nome_arquivo = 'FCB_' . preg_replace('/[^A-Za-z0-9_\-]/', '_', $dados_inscricao['nome']) . '_' . date('Y-m-d') . '.pdf';
        break;
    
    case 'psa':
        $html = gerarPSA($dados_inscricao);
        $nome_arquivo = 'PSA_' . preg_replace('/[^A-Za-z0-9_\-]/', '_', $dados_inscricao['nome']) . '_' . date('Y-m-d') . '.pdf';
        break;
    
    default:
        die('Tipo de documento inválido. Use: fcb ou psa');
}

$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'portrait');
$dompdf->render();
$dompdf->stream($nome_arquivo, ['Attachment' => false]);

function dadosInscricao($id_inscricao) {
    global $conect;
    
    $sql = "
        SELECT 
            i.id_inscricao,
            i.data_inscricao,
            i.situacao AS situacao_inscricao,
            i.disponibilidade,
            
            u.id_usuario,
            u.nome AS nome,
            u.email AS email,
            u.tipo AS tipo_usuario,
            
            e.id_estudante,
            e.matricula,
            e.telefone,
            e.cod_banco,
            e.agencia,
            e.conta,
            
            c.id_curso,
            c.codigo AS codigo_curso,
            c.nome AS curso,
            c.campus,
            c.turno,
            
            b.id_bolsa,
            b.codigo AS codigo_bolsa,
            b.nome AS nome_bolsa,
            b.descricao AS descricao_projeto,
            b.carga_horaria AS carga_horaria,
            b.modalidade,
            b.situacao AS situacao_bolsa,
            b.edital_url,
            
            ori.id_usuario AS id_orientador,
            ori.nome AS orientador,
            ori.email AS email_orientador,
            
            sub_origem.id_subunidade AS id_sub_origem,
            sub_origem.nome AS subunidade_origem,
            sub_origem.codigo AS codigo_sub_origem,
            
            sub_alocacao.id_subunidade AS id_sub_alocacao,
            sub_alocacao.nome AS subunidade_alocacao,
            sub_alocacao.codigo AS codigo_sub_alocacao
            
        FROM inscricao i
        
        INNER JOIN dados_estudante e ON i.id_estudante = e.id_estudante
        INNER JOIN usuario u ON e.id_usuario = u.id_usuario
        INNER JOIN bolsa b ON i.id_bolsa = b.id_bolsa
        LEFT JOIN curso c ON e.id_curso = c.id_curso
        LEFT JOIN usuario ori ON b.id_orientador = ori.id_usuario
        LEFT JOIN subunidade sub_origem ON b.id_sub_origem = sub_origem.id_subunidade
        LEFT JOIN subunidade sub_alocacao ON b.id_sub_alocacao = sub_alocacao.id_subunidade
        
        WHERE i.id_inscricao = ?
        LIMIT 1
    ";
    
    $stmt = $conect->prepare($sql);
    
    if (!$stmt) {
        die('Erro ao preparar consulta: ' . $conect->error);
    }
    
    $stmt->bind_param("i", $id_inscricao);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        $stmt->close();
        return null;
    }
    
    $dados = $result->fetch_assoc();
    $stmt->close();
    
    // Decodificar disponibilidade JSON
    if (!empty($dados['disponibilidade'])) {
        $dados['disponibilidade_array'] = json_decode($dados['disponibilidade'], true);
    }
    
    // Formatar datas
    if (!empty($dados['data_inscricao'])) {
        $dados['data_inscricao_formatada'] = date('d/m/Y H:i', strtotime($dados['data_inscricao']));
    }

    return $dados;
}