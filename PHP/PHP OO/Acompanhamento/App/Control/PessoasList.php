<?php
use Sisac\Control\Page;
use Sisac\Control\Action;
use Sisac\Widgets\Form\Form;
use Sisac\Widgets\Form\Entry;
use Sisac\Widgets\Container\VBox;
use Sisac\Widgets\Datagrid\Datagrid;
use Sisac\Widgets\Datagrid\DatagridColumn;
use Sisac\Widgets\Dialog\Message;
use Sisac\Widgets\Dialog\Question;
use Sisac\Widgets\Container\Panel;
use Sisac\Widgets\Wrapper\FormWrapper;
use Sisac\Widgets\Wrapper\DatagridWrapper;
use Sisac\Database\Transaction;
use Sisac\Database\Repository;
use Sisac\Database\Criteria;

/**
 * Listagem de Pessoas
 */
class PessoasList extends Page
{
    private $form;     // formulário de buscas
    private $datagrid; // listagem
    private $loaded;

    /**
     * Construtor da página
     */
    public function __construct()
    {
        parent::__construct();
        
        // instancia um formulário de buscas
        $this->form = new FormWrapper(new Form('form_busca_pessoas'));
        $this->form->setTitle('Pessoas');
        
        $nome = new Entry('nome');
        $this->form->addField('Nome', $nome, '100%');
        $this->form->addAction('Buscar', new Action(array($this, 'onReload')));
        $this->form->addAction('Novo', new Action(array("PessoasForm", 'onEdit')));
        
        // instancia objeto Datagrid
        $this->datagrid = new DatagridWrapper(new Datagrid);

        // instancia as colunas da Datagrid
        $id_coop = new DatagridColumn('id_coop',   'Cooperativa','center', '15%');
        $nome     = new DatagridColumn('nome',       'Nome',    'left', '25%');
        $cargo   = new DatagridColumn('cargo','Cargo', 'center', '25%');
        $email   = new DatagridColumn('email','E-mail', 'left', '35%');

        // adiciona as colunas à Datagrid
        $this->datagrid->addColumn($id_coop);
        $this->datagrid->addColumn($nome);
        $this->datagrid->addColumn($cargo);
        $this->datagrid->addColumn($email);

        $this->datagrid->addAction( 'Editar',  new Action(["PessoasForm", 'onEdit']), 'id', 'fa fa-edit fa-lg blue');
        $this->datagrid->addAction( 'Excluir',  new Action([$this, 'onDelete']),         'id', 'fa fa-trash fa-lg red');
        
        // monta a página através de uma caixa
        $box = new VBox;
        $box->style = 'display:block';
        $box->add($this->form);
        $box->add($this->datagrid);
        
        parent::add($box);
    }

    /**
     * Carrega a Datagrid com os objetos do banco de dados
     */
    public function onReload()
    {
        Transaction::open('Sisac'); // inicia transação com o BD
        $repository = new Repository('Pessoa');

        // cria um critério de seleção de dados
        $criteria = new Criteria;
        $criteria->setProperty('order', 'nome');

        // obtém os dados do formulário de buscas
        $dados = $this->form->getData();

        // verifica se o usuário preencheu o formulário
        if ($dados->nome)
        {
            // filtra pelo nome do pessoa
            $criteria->add('nome', 'like', "%{$dados->nome}%");
        }
        $criteria->add('id_coop', '!=', "0000");

        // carrega os produtos que satisfazem o critério
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

        // finaliza a transação
        Transaction::close();
        $this->loaded = true;
    }

    /**
     * Pergunta sobre a exclusão de registro
     */
    public function onDelete($param)
    {
        $id = $param['id']; // obtém o parâmetro $id
        $action1 = new Action(array($this, 'Delete'));
        $action1->setParameter('id', $id);
        
        new Question('Deseja realmente excluir o registro?', $action1);
    }

    /**
     * Exclui um registro
     */
    public function Delete($param)
    {
        try
        {
            $id = $param['id']; // obtém a chave
            Transaction::open('Sisac'); // inicia transação com o banco 'Sisac'
            $pessoa = Pessoa::find($id);
            $pessoa->delete(); // deleta objeto do banco de dados
            Transaction::close(); // finaliza a transação
            $this->onReload(); // recarrega a datagrid
            new Message('info', "Registro excluído com sucesso");
        }
        catch (Exception $e)
        {
            new Message('error', $e->getMessage());
        }
    }

    /**
     * Exibe a página
     */
    public function show()
    {
         // se a listagem ainda não foi carregada
         if (!$this->loaded)
         {
	        $this->onReload();
         }
         parent::show();
    }
}
