<?php
use Livro\Control\Page;
use Livro\Control\Action;
use Livro\Widgets\Form\Form;
use Livro\Widgets\Form\Entry;
use Livro\Widgets\Container\VBox;
use Livro\Widgets\Datagrid\Datagrid;
use Livro\Widgets\Datagrid\DatagridColumn;
use Livro\Widgets\Wrapper\FormWrapper;
use Livro\Widgets\Wrapper\DatagridWrapper;
use Livro\Widgets\Dialog\Message;
use Livro\Widgets\Dialog\Question;
use Livro\Database\Transaction;
use Livro\Database\Repository;
use Livro\Database\Criteria;

/**
 * Listagem de Funcionários
 */
class FuncionarioBuscaList extends Page
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
        // instancia um formulário
        $this->form = new FormWrapper(new Form('form_busca_funcionarios'));

        // cria os campos do formulário
        $nome = new Entry('nome');
        
        $this->form->addField('Nome', $nome, 300);
        $this->form->addAction('Buscar', new Action(array($this, 'onReload')));
        $this->form->addAction('Novo', new Action(array(new FuncionarioForm, 'onEdit')));
        
        // instancia objeto Datagrid
        $this->datagrid = new DatagridWrapper(new Datagrid);
        
        // instancia as colunas da Datagrid
        $codigo   = new DatagridColumn('id',         'Código', 'right', '10%');
        $nome     = new DatagridColumn('nome',       'Nome',    'left', '30%');
        $endereco = new DatagridColumn('endereco',   'Endereco','left', '30%');
        $email    = new DatagridColumn('email',      'Email',   'left', '30%');

        $codigo_order = new Action(array($this, 'onReload'));
        $codigo_order->setParameter('order', 'id');
        $codigo->setAction( $codigo_order );
        
        $nome_order = new Action(array($this, 'onReload'));
        $nome_order->setParameter('order', 'nome');
        $nome->setAction( $nome_order );
        
        // adiciona as colunas à Datagrid
        $this->datagrid->addColumn($codigo);
        $this->datagrid->addColumn($nome);
        $this->datagrid->addColumn($endereco);
        $this->datagrid->addColumn($email);
        
        // adiciona as ações à Datagrid
        $this->datagrid->addAction('Editar', new Action(array(new FuncionarioForm, 'onEdit')), 'id');
        $this->datagrid->addAction('Deletar', new Action(array($this, 'onDelete')), 'id');

        // monta a página através de uma caixa
        $box = new VBox;
        $box->style = 'display:block; margin: 20px';
        $box->add($this->form);
        $box->add($this->datagrid);
        
        parent::add($box);
    }

    /**
     * Carrega a Datagrid com os objetos do banco de dados
     */
    function onReload( $param = null)
    {
        Transaction::open('livro'); // inicia transação com o BD
        $repository = new Repository('Funcionario');

        // cria um critério de seleção de dados
        $criteria = new Criteria;
        $criteria->setProperty('order', isset($param['order']) ? $param['order'] : 'id');

        // obtém os dados do formulário de buscas
        $dados = $this->form->getData();

        // verifica se o usuário preencheu o formulário
        if ($dados->nome)
        {
            // filtra pelo nome do pessoa
            $criteria->add('nome', 'like', "%{$dados->nome}%");
        }

        // carrega os produtos que satisfazem o critério
        $funcionarios = $repository->load($criteria);
        $this->datagrid->clear();
        if ($funcionarios)
        {
            foreach ($funcionarios as $funcionario)
            {
                // adiciona o objeto na Datagrid
                $this->datagrid->addItem($funcionario);
            }
        }

        // finaliza a transação
        Transaction::close();
        $this->loaded = true;
    }

    /**
     * Pergunta sobre a exclusão de registro
     */
    function onDelete($param)
    {
        $id = $param['id']; // obtém o parâmetro $id
        $action1 = new Action(array($this, 'Delete'));
        $action1->setParameter('id', $id);
        
        new Question('Deseja realmente excluir o registro?', $action1);
    }

    /**
     * Exclui um registro
     */
    function Delete($param)
    {
        try
        {
            $id = $param['id']; // obtém a chave
            Transaction::open('livro'); // inicia transação com o banco 'livro'
            $funcionario = Funcionario::find($id); // busca objeto Funcionario
            if ($funcionario) {
                $funcionario->delete(); // deleta objeto do banco de dados
            }
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
    function show()
    {
         // se a listagem ainda não foi carregada
         if (!$this->loaded)
         {
	        $this->onReload( func_get_args() );
         }
         parent::show();
    }
}
