<?php
use Sisac\Control\Page;
use Sisac\Control\Action;
use Sisac\Database\Criteria;
use Sisac\Widgets\Form\Date;
use Sisac\Widgets\Form\File;
use Sisac\Widgets\Form\Form;
use Sisac\Widgets\Form\Text;
use Sisac\Widgets\Form\Combo;
use Sisac\Widgets\Form\Entry;
use Sisac\Database\Repository;
use Sisac\Widgets\Form\Hidden;
use Sisac\Widgets\Form\Number;
use Sisac\Database\Transaction;
use Sisac\Widgets\Base\Element;
use Sisac\Widgets\Container\VBox;
use Sisac\Widgets\Dialog\Message;
use Sisac\Widgets\Container\Panel;
use Sisac\Widgets\Form\CheckGroup;
use Sisac\Widgets\Datagrid\Datagrid;
use Sisac\Widgets\Wrapper\FormWrapper;
use Sisac\Widgets\Datagrid\DatagridColumn;
use Sisac\Widgets\Wrapper\DatagridWrapper;

/**
 * Formulário de pessoas
 */
class ContratoFormList extends Page
{
    private $form;

    /**
     * Construtor da página
     */
    public function __construct()
    {
        parent::__construct();

        $this->form = new FormWrapper(new Form('form_contrato'));
        $this->form->setTitle('Contrato');
        
        $id    = new Hidden('id');
        $id_coop      = new Entry('id_coop');
        $assinatura    = new Date('assinatura');
        $rescisao        = new Date('rescisao');
        $status     = new Combo('status');
        $id_anexo     = new Hidden('id_anexo');
        $arquivo     = new File('arquivo');  
        $obs     = new Text('obs');  

        if (isset($_GET['id_coop'])) {
            $id_coop->setValue($_GET['id_coop']);
        }
        
        $id_coop->setEditable(FALSE);
        $arquivo->setProperty('accept', 'application/pdf');
        
        Transaction::open('Sisac'); 

        $lista_status = Status::all();
        $items = array();
        foreach ($lista_status as $obj_status) {
            $items[$obj_status->id] = $obj_status->nome;
        }
        $status->setValue(1);
        $status->addItems($items);

        Transaction::close();
        
        $this->form->addField('', $id, '0%');
        $this->form->addField('Cooperativa', $id_coop, '50%');
        $this->form->addField('Assinatura', $assinatura, '30%');
        $this->form->addField('Recisão', $rescisao, '30%');
        $this->form->addField('Status', $status, '50%');
        $this->form->addField('', $id_anexo, '0%');
        $this->form->addField('Arquivo', $arquivo, '50%');
        $this->form->addField('Observações', $obs, '50%');

        $obs->setSize('50%','150px');        

        $action = new Action([$this, 'onSave']);
        $action->setParameter('id_coop', $_GET['id_coop']);
        $this->form->addAction('Salvar', $action);

        $action = new Action(['CooperativasForm','onEdit']);
        $action->setParameter('id', $_GET['id_coop']);
        $this->form->addAction('Retornar', $action);
                
        parent::add($this->form);
    }

    /**
     * Salva os dados do formulário
     */
    public function onSave()
    {
        try
        {
            // inicia transação com o BD
            Transaction::open('Sisac');

            $dados = $this->form->getData();
            $this->form->setData($dados);
            
            $nome =$dados->id_coop .  '-contrato-' . date("YmdHi") .  '.pdf';
            $caminho = 'files/contratos/' . $nome;
            
            if($dados->id == '') {
                if (move_uploaded_file($dados->arquivo, $caminho)) {
                    $dados_arquivo = new stdClass;
                    $dados_arquivo->caminho = $caminho;

                    $arquivos_anexos = new Anexo(); // instancia objeto
                    $arquivos_anexos->fromArray( (array) $dados_arquivo); // carrega os dados
                    $dados->id_anexo = $arquivos_anexos->store(); // armazena o objeto no banco de dados
                }
            }
            
            if($dados->rescisao != '') {
                $dados->status = 2;
            } else {
                $dados->status = 1;
            }
            unset($dados->arquivo);

            $contrato = new Contrato; // instancia objeto
            $contrato->fromArray( (array) $dados); // carrega os dados
            $contrato->store(); // armazena o objeto no banco de dados
            Transaction::close(); // finaliza a transação
            new Message('info', 'Dados armazenados com sucesso');
        }
        catch (Exception $e)
        {
            // exibe a mensagem gerada pela exceção
            new Message('error', $e->getMessage());

            // desfaz todas alterações no banco de dados
            Transaction::rollback();
        }
    }
    
    /**
     * Carrega registro para edição
     */
    public function onEdit($param)
    {
        try
        {
            Transaction::open('Sisac');

            if (isset($param['id']))
            {
                $id = $param['id'];
                $dados_contrato = Contrato::find($id);
                $this->form->setData($dados_contrato);
            }
        }
        catch (Exception $e)		    // em caso de exceção
        {
            // exibe a mensagem gerada pela exceção
            new Message('error', $e->getMessage());
            // desfaz todas alterações no banco de dados
            Transaction::rollback();
        }
    }
}
