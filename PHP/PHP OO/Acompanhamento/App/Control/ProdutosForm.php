<?php 
use Sisac\Control\Page;
use Sisac\Control\Action;
use Sisac\Widgets\Form\Form;
use Sisac\Widgets\Dialog\Message;
use Sisac\Widgets\Form\Entry;
use Sisac\Widgets\Form\Combo;
use Sisac\Widgets\Form\RadioGroup;
use Sisac\Database\Transaction;

use Sisac\Widgets\Wrapper\DatagridWrapper;
use Sisac\Widgets\Wrapper\FormWrapper;
use Sisac\Widgets\Container\Panel;

use Sisac\Traits\SaveTrait;
use Sisac\Traits\EditTrait;

/**
 * Cadastro de Produtos
 */
class ProdutosForm extends Page
{
    private $form; // formulário
    private $connection;
    private $activeRecord;
    
    use SaveTrait;
    use EditTrait;
    
    /**
     * Construtor da página
     */
    public function __construct()
    {
        parent::__construct();

        $this->connection = 'livro';
        $this->activeRecord = 'Produto';
        
        // instancia um formulário
        $this->form = new FormWrapper(new Form('form_produtos'));
        $this->form->setTitle('Produto');
        
        // cria os campos do formulário
        $codigo      = new Entry('id');
        $descricao   = new Entry('descricao');
        $estoque     = new Entry('estoque');
        $preco_custo = new Entry('preco_custo');
        $preco_venda = new Entry('preco_venda');
        $fabricante  = new Combo('id_fabricante');
        $tipo        = new RadioGroup('id_tipo');
        $unidade     = new Combo('id_unidade');
        
        // carrega os fabricantes do banco de dados
        Transaction::open('livro');
        $fabricantes = Fabricante::all();
        $items = array();
        foreach ($fabricantes as $obj_fabricante) {
            $items[$obj_fabricante->id] = $obj_fabricante->nome;
        }
        $fabricante->addItems($items);
        
        $tipos = Tipo::all();
        $items = array();
        foreach ($tipos as $obj_tipo) {
            $items[$obj_tipo->id] = $obj_tipo->nome;
        }
        $tipo->addItems($items);
        
        $unidades = Unidade::all();
        $items = array();
        foreach ($unidades as $obj_unidade) {
            $items[$obj_unidade->id] = $obj_unidade->nome;
        }
        $unidade->addItems($items);
        Transaction::close();
        
        // define alguns atributos para os campos do formulário
        $codigo->setEditable(FALSE);
        
        $this->form->addField('Código',    $codigo, '30%');
        $this->form->addField('Descrição', $descricao, '70%');
        $this->form->addField('Estoque',   $estoque, '70%');
        $this->form->addField('Preço custo',   $preco_custo, '70%');
        $this->form->addField('Preço venda',   $preco_venda, '70%');
        $this->form->addField('Fabricante',   $fabricante, '70%');
        $this->form->addField('Tipo',   $tipo, '70%');
        $this->form->addField('Unidade',   $unidade, '70%');
        $this->form->addAction('Salvar', new Action(array($this, 'onSave')));
        
        // adiciona o formulário na página
        parent::add($this->form);
    }
}
