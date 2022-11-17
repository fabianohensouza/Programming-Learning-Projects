<?php
use Sisac\Control\Page;
use Sisac\Utils\Convert;
use Sisac\Control\Action;
use Sisac\Database\Criteria;
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
use Sisac\Widgets\Wrapper\DatagridWrapper;

/**
 * Formulário de cooperativas
 */
class CooperativasForm extends Page
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
        $this->form = new FormWrapper(new Form('form_cooperativas'));
        $this->form->setTitle('Cooperativa');
        
        // cria os campos do formulário
        $codigo    = new Entry('id');
        $nome      = new Entry('nome');
        $cidade    = new Combo('id_cidade');
        $ic        = new Combo('ic');
        $responsavel_ic  = new Combo('id_responsavel_ic');
        $equipamentos     = new Number('equipamentos');
        
        // carrega as cidades do banco de dados
        Transaction::open('Sisac');   
        $cidades = Cidade::all();
        $items = array();
        foreach ($cidades as $obj_cidade) {
            $items[$obj_cidade->id] = $obj_cidade->nome;
        }
        $cidade->addItems($items);

        $items = array( "Sim" => "Sim",
                        "Não" => "Não" );
                    
        $ic->addItems($items);

        $responsaveis = Usuario::all();
        $items = array();
        foreach ($responsaveis as $obj_responsaveis) {
            $items[$obj_responsaveis->id] = $obj_responsaveis->nome;
        }
        $responsavel_ic->addItems($items);
        
        $this->form->addField('Código', $codigo, '15%');
        $this->form->addField('Nome', $nome, '50%');
        $this->form->addField('Cidade', $cidade, '50%');
        $this->form->addField('IC', $ic, '15%');
        $this->form->addField('Monitoramento', $responsavel_ic, '50%');
        $this->form->addField('Equipamentos', $equipamentos, '15%');
        
        // define alguns atributos para os campos do formulário     
        if (isset($_GET['id'])) {
            $codigo->setEditable(FALSE);
        }
        
        $this->form->addAction('Salvar', new Action(array($this, 'onSave')));

        parent::add($this->form);

        if (isset($_GET['id'])) {
            $url = http_build_query([
                                        'class'     => 'ContratoFormList',
                                        'method'    => 'onEdit',
                                        'id_coop'   => $_GET['id']
                                    ]);

            $link = new Element('a');
            $link->__set('href', 'index.php?' . $url);
            $link->add('Contrato');
            $link->__set('type', 'button');
            $link->__set('class', 'btn btn-default');

            $painel = new Panel();
            $painel->add($link);
            $painel->add(new Element('hr'));

            $this->datagrid_contrato = new DatagridWrapper(new Datagrid);
    
            $arquivo     = new DatagridColumn('arquivo',       'Arquivo',    'center', '10%');
            $assinatura     = new DatagridColumn('assinatura',       'Assinatura',    'center', '10%');
            $rescisao   = new DatagridColumn('rescisao','Recisão', 'center', '10%');
            $status   = new DatagridColumn('status','Status', 'center', '10%');
            $obs   = new DatagridColumn('obs','Observações', 'center', '55%');
    
            $this->datagrid_contrato->addColumn($arquivo);
            $this->datagrid_contrato->addColumn($assinatura);
            $this->datagrid_contrato->addColumn($rescisao);
            $this->datagrid_contrato->addColumn($status);
            $this->datagrid_contrato->addColumn($obs);
            
            $action = new Action(["ContratoFormList", 'onEdit']);
            $action->setParameter('id_coop', $_GET['id']);
            $this->datagrid_contrato->addAction( 'Editar',  $action, 'id', 'fa fa-edit fa-lg blue');
            
            $painel->add($this->datagrid_contrato);

            parent::add($painel);

            $this->datagrid = new DatagridWrapper(new Datagrid);

            $nome     = new DatagridColumn('nome',       'Nome',    'center', '30%');
            $cargo   = new DatagridColumn('cargo','Cargo', 'center', '30%');
            $email   = new DatagridColumn('email','E-mail', 'center', '40%');

            $this->datagrid->addColumn($nome);
            $this->datagrid->addColumn($cargo);
            $this->datagrid->addColumn($email);
            
            $this->datagrid->addAction( 'Editar',  new Action(["PessoasForm", 'onEdit']), 'id', 'fa fa-edit fa-lg blue');
            
            $box = new VBox;
            $box->style = 'display:block';
            $box->add($this->datagrid);
            
            parent::add($box);        

        }

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
            $pessoa = new Cooperativa; // instancia objeto
            $pessoa->fromArray( (array) $dados); // carrega os dados
            $pessoa->store(); // armazena o objeto no banco de dados
            
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
                $cooperativa = Cooperativa::find($id);
                $this->form->setData($cooperativa); // lança os dados da pessoa no formulário

                $repository = new Repository('Pessoa');
                $criteria = new Criteria;
                $criteria->setProperty('order', 'cod_cargo');
                $criteria->add('id_coop', '=', $id);

                $pessoas = $repository->load($criteria);
                $this->datagrid->clear();
                if ($pessoas)
                {
        
                    $cargos = Cargo::all();
                    $lista_cargos = array();
                    foreach ($cargos as $cargo) {
                        $lista_cargos[$cargo->cod] = $cargo->nome;
                    }
                    
                    foreach ($pessoas as $pessoa)
                    {
                        $pessoa->cod_cargo = (!is_null($pessoa->cod_cargo)) ? $pessoa->cod_cargo : "-";
                        $pessoa->cargo = $lista_cargos[$pessoa->cod_cargo];
                        
                        // adiciona o objeto na Datagrid
                        $this->datagrid->addItem($pessoa);
                    }
                }

                if (isset($param['id']))
                {
                    $repository = new Repository('Contrato');
                    $criteria = new Criteria;
                    $criteria->add('id_coop', '=', $_GET['id']);

                    $contratos = $repository->load($criteria);
                    $this->datagrid_contrato->clear();
                    if ($contratos)
                    {
            
                        foreach ($contratos as $contrato)
                        {
                            $contrato->arquivo = '<a target="_blank" rel="noopener noreferrer" href="' . Anexo::find($contrato->id_anexo)->caminho . '"> Visualizar </a>';
                            $contrato->assinatura   = Convert::dateToPtBr($contrato->assinatura);
                            $contrato->rescisao = isset($contrato->rescisao) ? Convert::dateToPtBr($contrato->rescisao) : '-';
                            $contrato->status = Status::find($contrato->status)->nome;
                            
                            // adiciona o objeto na Datagrid
                            $this->datagrid_contrato->addItem($contrato);//var_dump($contrato);exit;
                        }
                    }

                    Transaction::close(); // finaliza a transação
                }

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