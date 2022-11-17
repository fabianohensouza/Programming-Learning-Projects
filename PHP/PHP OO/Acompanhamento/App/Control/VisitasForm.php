<?php
use Sisac\Control\Page;
use Sisac\Control\Action;
use Sisac\Database\Criteria;
use Sisac\Widgets\Form\Date;
use Sisac\Widgets\Form\Form;
use Sisac\Widgets\Form\Text;
use Sisac\Widgets\Form\Combo;
use Sisac\Widgets\Form\Entry;
use Sisac\Database\Repository;
use Sisac\Widgets\Form\Button;
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
use Sisac\Widgets\Form\File;
use Sisac\Widgets\Wrapper\DatagridWrapper;

/**
 * Formulário de cooperativas
 */
class VisitasForm extends Page
{
    private $form;     // formulário de buscas
    private $datagrid; // listagem
    private $datagrid_contrato; // listagem

    /**
     * Construtor da página
     */
    public function __construct()
    {
        parent::__construct();
        // instancia um formulário
        $this->form = new FormWrapper(new Form('form_visita'));
        $this->form->setTitle('Visita');
        
        // cria os campos do formulário
        $id             = new Hidden('id');
        $id_coop        = new Combo('id_coop');
        $data_ida       = new Date('data_ida');
        $data_retorno   = new Date('data_retorno');
        $responsavel_ic = new Combo('id_responsavel_ic');
        $id_tipo_visita = new Combo('id_tipo_visita');
        $obs            = new Text('obs');
        $relatorio      = new File('relatorio');

        $relatorio->setProperty('accept', 'application/pdf');
        
        // carrega as itens do banco de dados
        Transaction::open('Sisac');   
        $coops = Cooperativa::all();
        $items = array();
        foreach ($coops as $obj_coop) {
            $items[$obj_coop->id] = $obj_coop->id;
        }
        $id_coop->addItems($items);

        $responsaveis = Usuario::all();
        $items = array();
        foreach ($responsaveis as $obj_responsaveis) {
            $items[$obj_responsaveis->id] = $obj_responsaveis->nome;
        }
        $responsavel_ic->addItems($items);

        $visitas = TipoVisita::all();
        $items = array();
        foreach ($visitas as $obj_visita) {
            $items[$obj_visita->id] = $obj_visita->nome;
        }
        $id_tipo_visita->addItems($items);
        
        $this->form->addField('', $id, '0%');
        $this->form->addField('Cooperativa', $id_coop, '20%');
        $this->form->addField('Data Ida', $data_ida, '20%');
        $this->form->addField('Data Retorno', $data_retorno, '20%');
        $this->form->addField('Responsável', $responsavel_ic, '50%');
        $this->form->addField('Tipo de Visita', $id_tipo_visita, '20%');
        $this->form->addField('Observações', $obs, '50%');
        $this->form->addField('Relatório', $relatorio, '50%');

        $obs->setSize('50%','150px');
        
        // define alguns atributos para os campos do formulário     
        if (isset($_GET['id'])) {
            $id_coop->setEditable(FALSE);
        }
        
        $this->form->addAction('Salvar', new Action(array($this, 'onSave')));

        parent::add($this->form);  

        Transaction::close();
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

            if($dados->id) {

                $visita = Visita::find($dados->id);

                if ($dados->relatorio != '' && !is_null($visita->id_anexo)) {

                    $anexo = Anexo::find($visita->id_anexo);                    
                }
            }

            if($dados->relatorio != '') {

                $caminho = 'files/relatorios/' . $dados->id_coop .  '-Relatorio-' . date("d-m-Y") .  '.pdf';

                if (move_uploaded_file($dados->relatorio, $caminho)) {
                    $dados_relatorio = new stdClass;
                    $dados_relatorio->caminho = $caminho;

                    $relatorio_anexo = new Anexo(); // instancia objeto
                    $relatorio_anexo->fromArray( (array) $dados_relatorio); // carrega os dados
                    $dados->id_anexo = $relatorio_anexo->store(); // armazena o objeto no banco de dados
                }
            }

            unset($dados->relatorio);

            $visita = new Visita; // instancia objeto 
            $visita->fromArray( (array) $dados); // carrega os dados
            $visita->store(); // armazena o objeto no banco de dados
            
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
            if (isset($param['id']))
            {
                $id = $param['id']; // obtém a chave
                Transaction::open('Sisac');
                $visita = Visita::find($id);
                $this->form->setData($visita); // lança os dados da pessoa no formulário

                Transaction::close();
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