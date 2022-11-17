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
use Sisac\Widgets\Form\Hidden;
use Sisac\Widgets\Form\Number;
use Sisac\Database\Transaction;
use Sisac\Utils\Convert;
use Sisac\Widgets\Base\Element;
use Sisac\Widgets\Container\VBox;
use Sisac\Widgets\Dialog\Message;
use Sisac\Widgets\Container\Panel;
use Sisac\Widgets\Datagrid\Datagrid;
use Sisac\Widgets\Wrapper\FormWrapper;
use Sisac\Widgets\Datagrid\DatagridColumn;
use Sisac\Widgets\Wrapper\DatagridWrapper;
/**
 * Formulário de cooperativas
 */
class AntivirusForm extends Page
{
    private $form;     // formulário de buscas
    private $datagrid; // listagem

    /**
     * Construtor da página
     */
    public function __construct()
    {
        parent::__construct();

        if(isset($_REQUEST['id']) || $_REQUEST['id_coop'] != 0) {
            
            Transaction::open('Sisac'); 

            //Carregando dados da Cooperativa
            if (isset($_REQUEST['id_coop'])) {
                $coop = $_REQUEST['id_coop'];
            } else {
                $coop = Antivirus::find($_REQUEST['id'])->id_coop;
            }
            $coop =  Cooperativa::find($coop);

            // instancia um formulário
            $this->form = new FormWrapper(new Form('form_antivirus'));
            $this->form->setTitle("Antivírus: $coop->id - $coop->nome");
            
            // cria os campos do formulário
            $id             = new Hidden('id');
            $id_coop        = new Entry('id_coop');
            $id_versao         = new Combo('id_versao');
            $expiracao      = new Date('expiracao');
            $licencas       = new Number('licencas'); 
            $id_servidor    = new Combo('id_servidor');
            $obs            = new Text('obs');
            
            $id_coop->setValue($coop->id);            
            $id_coop->setEditable(FALSE);

            $versoes = AntivirusVersao::all();
            $items = array();
            foreach ($versoes as $obj_versao) {
                $items[$obj_versao->id] = $obj_versao->nome;
            }
            $id_versao->addItems($items);

            $repository = new Repository('Servidor');
            $criteria = new Criteria();
            $criteria->add('id_coop', '=', $coop->id);
            $servidores = $repository->load($criteria);

            $items = array();
            foreach ($servidores as $obj_servidor) {
                $items[$obj_servidor->id] = $obj_servidor->nome;
            }
            $id_servidor->addItems($items);

            $this->form->addField('', $id, '0%');
            $this->form->addField('Cooperativa', $id_coop, '50%');
            $this->form->addField('Versao', $id_versao, '50%');
            $this->form->addField('Expiração', $expiracao, '50%');
            $this->form->addField('Licenças', $licencas, '20%');
            $this->form->addField('Servidor', $id_servidor, '50%');
            $this->form->addField('Observações', $obs, '50%');
            $obs->setSize('50%', '150px');
            
            $this->form->addAction('Salvar', new Action(array($this, 'onSave')));

            parent::add($this->form); 

            //Datagrid para exibição das liberações USB
            $url = http_build_query([
                'class'     => 'LiberacaoForm',
                'method'    => 'onEdit',
                'id_coop'   => $coop->id
            ]);

            $link = new Element('a');
            $link->__set('href', 'index.php?' . $url);
            $link->add('Liberações USB');
            $link->__set('type', 'button');
            $link->__set('class', 'btn btn-default');

            $painel = new Panel();
            $painel->add($link);
            $painel->add(new Element('hr'));

            $this->datagrid = new DatagridWrapper(new Datagrid);

            $id_coop     = new DatagridColumn('id_coop',       'Cooperativa',    'center', '10%');
            $chamado   = new DatagridColumn('chamado','Chamado', 'center', '10%');
            $data   = new DatagridColumn('data','Data', 'center', '10%');
            $equipamento   = new DatagridColumn('equipamento','Equipamento', 'center', '20%');
            $obs     = new DatagridColumn('obs',       'Observações',    'center', '50%');

            $this->datagrid->addColumn($id_coop);
            $this->datagrid->addColumn($chamado);
            $this->datagrid->addColumn($data);
            $this->datagrid->addColumn($equipamento);
            $this->datagrid->addColumn($obs);

            $this->datagrid->addAction( 'Editar',  new Action(["LiberacaoForm", 'onEdit']),   'id', 'fa fa-edit fa-lg blue');
            $this->datagrid->addAction( 'Excluir', new Action([$this, 'onDelete']), 'id', 'fa fa-trash fa-lg red');

            $painel->add($this->datagrid);

            parent::add($painel);  
                
            Transaction::close();

        } else {

            ob_start();
            new Message('error', 'Favor selecionar a Cooperativa na tela anterior');
            $message = ob_get_contents();
            ob_clean();
            parent::add($message); 
        }
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

            $ad = new Antivirus; // instancia objeto
            $ad->fromArray( (array) $dados); // carrega os dados 
            $ad->store(); // armazena o objeto no banco de dados
            
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
        $antivirus = new Antivirus();
        $id_coop = '';

        try
        {

            Transaction::open('Sisac'); // inicia transação com o BD$

            if (isset($_REQUEST['id_coop'])) {

                $id_coop = $_REQUEST['id_coop'];

                $repository = new Repository('Antivirus');
                $criteria = new Criteria();
                $criteria->add('id_coop', '=', $_REQUEST['id_coop']);
                $lista = $repository->load($criteria);
                
                if(!empty($lista)) {
                    foreach ($lista as $obj) {
                        $antivirus = $obj;
                    }

                    $this->form->setData($antivirus);
                }

            } else if (isset($param['id'])) {
                
                $id = $param['id']; // obtém a chave
                $antivirus = Antivirus::find($id);
                $this->form->setData($antivirus);
                
                $id_coop = $antivirus->id_coop;

            }

            $repository = new Repository('LiberaUsb');
            $criteria = new Criteria;
            $criteria->add('id_coop', '=', $id_coop);
            $liberacoes = $repository->load($criteria);

            $this->datagrid->clear();
            if ($liberacoes)
            {
            
                foreach ($liberacoes as $liberacao)
                {        
                    $liberacao->data = Convert::dateToPtBr($liberacao->data);
                           
                    // adiciona o objeto na Datagrid
                    $this->datagrid->addItem($liberacao);
                }
            }

            Transaction::close(); // finaliza a transação

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
