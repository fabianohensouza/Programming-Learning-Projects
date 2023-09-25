using Store.Domain.Commands;
using Store.Domain.Handlers;
using Store.Domain.Repositories.Interfaces;
using Store.Tests.Repositories;

namespace Store.Tests.Handler
{
    [TestClass]
    public class OrderHandlersTests
    {
        private readonly ICustomerRepository CustomerRepository;
        private readonly IDeliveryFeeRepository DeliveryFeeRepository;
        private readonly IDiscountRepository DiscountRepository;
        private readonly IProductRepository ProductRepository;
        private readonly IOrderRepository OrderRepository;

        public OrderHandlersTests()
        {
            CustomerRepository = new FakeCustomerRepository();
            DeliveryFeeRepository = new FakeDeliveryFeeRepository();
            DiscountRepository = new FakeDiscountRepository();
            ProductRepository = new FakeProductRepository();
            OrderRepository = new FakeOrderRepository();
        }

        

        [TestMethod]
        [TestCategory("Handlers")]
        public void DadoUmClienteInexistenteOPedidoNaoDeveSerGerado()
        {
            Assert.IsTrue(true);
        }

        [TestMethod]
        [TestCategory("Handlers")]
        public void DadoUmCepInvalidoOPedidoDeveSerGeradoNormalmente()
        {
            // TODO: Implementar
            Assert.IsTrue(true);
        }

        [TestMethod]
        [TestCategory("Handlers")]
        public void DadoUmPromocodeInexistenteOPedidoDeveSerGeradoNormalmente()
        {
            // TODO: Implementar
            Assert.IsTrue(true);
        }

        [TestMethod]
        [TestCategory("Handlers")]
        public void DadoUmPedidoSemItensOMesmoNaoDeveSerGerado()
        {
            // TODO: Implementar
            Assert.IsTrue(true);
        }

        [TestMethod]
        [TestCategory("Handlers")]
        public void DadoUmComandoInvalidoOPedidoNaoDeveSerGerado()
        {
            var command = new CreateOrderCommand();
            command.Customer = "";
            command.ZipCode = "13411080";
            command.PromoCode = "12345678";
            command.Items.Add(new CreateOrderItemCommand(Guid.NewGuid(), 1));
            command.Items.Add(new CreateOrderItemCommand(Guid.NewGuid(), 1));
            command.Validate();

            Assert.AreEqual(command.Valid, false);
        }

        [TestMethod]
        [TestCategory("Handlers")]
        public void DadoUmComandoValidoOPedidoDeveSerGerado()
        {
            var command = new CreateOrderCommand();
            command.Customer = "12345678";
            command.ZipCode = "13411080";
            command.PromoCode = "12345678";
            command.Items.Add(new CreateOrderItemCommand(Guid.NewGuid(), 1));
            command.Items.Add(new CreateOrderItemCommand(Guid.NewGuid(), 1));

            var handler = new OrderHandler(
                CustomerRepository,
                DeliveryFeeRepository,
                DiscountRepository,
                ProductRepository,
                OrderRepository);

            handler.Handle(command);
            Assert.AreEqual(handler.Valid, true);
        }
    }
}